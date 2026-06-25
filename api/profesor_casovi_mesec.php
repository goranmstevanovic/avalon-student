<?php
session_start();

require_once "../../config/database.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Niste ulogovani.']);
    exit;
}

$profesor_id = (int) $_SESSION['user_id'];

$database = new Database();
$db = $database->getConnection();

$sql = "
SELECT 
    godina,
    mesec,
    broj_zakazanih,
    broj_odrzanih
FROM profesor_casovi_mesec
WHERE profesor_id = :profesor_id
AND STR_TO_DATE(CONCAT(godina,'-',LPAD(mesec,2,'0'),'-01'), '%Y-%m-%d') 
    >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 11 MONTH), '%Y-%m-01')
ORDER BY godina, mesec
";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':profesor_id' => $profesor_id
]);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);