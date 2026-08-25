<?php
// AJAX: evidentira da je djak kliknuo na Wordwall Assignment link (bez blokiranja otvaranja u novom tabu).

include_once 'config/core.php';
include_once 'login_checker.php';
include_once 'config/database.php';
include_once 'config/autoload.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();
$domaci_obj = new domaci_wordwall($db);

$djak_id   = (int)$_SESSION['user_id'];
$domaci_id = isset($_POST['domaci_id']) ? (int)$_POST['domaci_id'] : 0;

if ($domaci_id <= 0) {
    echo json_encode(['ok' => false]);
    exit;
}

if (!$domaci_obj->djak_ima_pristup($domaci_id, $djak_id)) {
    echo json_encode(['ok' => false, 'error' => 'Nemate pristup.']);
    exit;
}

$ok = $domaci_obj->oznaci_otvoreno($domaci_id, $djak_id);
echo json_encode(['ok' => (bool)$ok]);
