<?php
require_once "../../config/database.php";

$database = new Database();
$db = $database->getConnection();

$sql = "
SELECT 
    godina,
    mesec,
    ukupno_uplate,
    broj_zakazanih,
    broj_odrzanih
FROM uplate_casovi_mesec
WHERE STR_TO_DATE(CONCAT(godina,'-',mesec,'-01'), '%Y-%m-%d') 
      >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 11 MONTH), '%Y-%m-01')
ORDER BY godina, mesec
";

$stmt = $db->prepare($sql);
$stmt->execute();

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);