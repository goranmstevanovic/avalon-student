<?php
include_once 'config/core.php';
include_once 'login_checker.php';
include_once 'config/database.php';
include_once 'config/autoload.php';

header('Content-Type: application/json');

$fk_djak     = $_SESSION['user_id'];
$fk_profesor = isset($_POST['prof_id'])  ? (int)$_POST['prof_id']  : 0;
$ocena       = isset($_POST['ocena'])    ? (int)$_POST['ocena']    : 0;
$komentar    = isset($_POST['komentar']) ? trim($_POST['komentar']) : '';

if ($fk_profesor <= 0 || $ocena < 1 || $ocena > 5) {
    echo json_encode(['status' => 'greska', 'poruka' => 'Neispravni podaci.']);
    exit;
}

$database = new Database();
$db       = $database->getConnection();
$ocena_obj = new ocena_profesora($db);

if ($ocena_obj->already_rated_today($fk_djak, $fk_profesor)) {
    echo json_encode(['status' => 'greska', 'poruka' => 'Već si ocenio ovog profesora.']);
    exit;
}

$ocena_obj->fk_djak     = $fk_djak;
$ocena_obj->fk_profesor = $fk_profesor;
$ocena_obj->ocena       = $ocena;
$ocena_obj->komentar    = $komentar;

if ($ocena_obj->create()) {
    echo json_encode(['status' => 'ok']);
} else {
    echo json_encode(['status' => 'greska', 'poruka' => 'Greška pri upisu u bazu.']);
}
