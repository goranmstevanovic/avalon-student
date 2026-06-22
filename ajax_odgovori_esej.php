<?php
include_once "config/core.php";
include_once "login_checker.php";
include_once 'config/database.php';
include_once 'config/autoload.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();
$domaci = new domaci_esej($db);

$domaci_id = isset($_POST['domaci_id']) ? (int)$_POST['domaci_id'] : 0;
$tekst     = isset($_POST['tekst'])     ? trim($_POST['tekst'])     : '';

if ($domaci_id <= 0 || $tekst === '') {
    echo json_encode(['error' => 'Tekst ne sme biti prazan.']);
    exit;
}

if (!$domaci->djak_ima_pristup($domaci_id, $_SESSION['user_id'])) {
    echo json_encode(['error' => 'Nemate pristup ovom zadatku.']);
    exit;
}

if ($domaci->je_zakljucan($domaci_id)) {
    echo json_encode(['error' => 'Zadatak je zaključan, predaja nije moguća.']);
    exit;
}

if (mb_strlen($tekst) > 50000) {
    echo json_encode(['error' => 'Tekst je predugačak.']);
    exit;
}

// Dozvoli Quill-generisane tagove, ostalo skini
$tekst_sanit = strip_tags($tekst, '<p><br><strong><em><u><s><ul><ol><li><h1><h2><h3>');
$ok = $domaci->sacuvaj_odgovor($domaci_id, $_SESSION['user_id'], $tekst_sanit);
echo json_encode(['ok' => (bool)$ok, 'datum' => date('d.m.Y')]);
