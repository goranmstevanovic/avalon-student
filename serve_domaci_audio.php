<?php
// Servira audio fajlove iz test-smszv storage-a, sa provjerom pristupa djaka.
// ?file=naziv.mp4&tip=profesor|djaci

include_once 'config/core.php';
include_once 'login_checker.php';
include_once 'config/database.php';
include_once 'config/autoload.php';

$database = new Database();
$db = $database->getConnection();
$domaci_obj = new domaci_audio($db);

$djak_id = (int)$_SESSION['user_id'];

$dozvoljeni_tipovi = ['profesor', 'djaci'];
$tip  = isset($_GET['tip'])  && in_array($_GET['tip'], $dozvoljeni_tipovi) ? $_GET['tip'] : '';
$file = isset($_GET['file']) ? basename($_GET['file']) : '';

if (!$tip || !$file) {
    http_response_code(400);
    exit('Neispravan zahtev.');
}

// Baza za storage je uvek u test-smszv projektu
$storage_root = dirname(__DIR__) . '/test-smszv/storage/domaci/';
$putanja = $storage_root . $tip . '/' . $file;

if (!file_exists($putanja)) {
    http_response_code(404);
    exit('Fajl nije pronađen.');
}

$ext_mapa = [
    'mp4'  => 'audio/mp4',
    'webm' => 'audio/webm',
    'ogg'  => 'audio/ogg',
    'mp3'  => 'audio/mpeg',
];

$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
$content_type = $ext_mapa[$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $content_type);
header('Content-Length: ' . filesize($putanja));
header('Accept-Ranges: bytes');
header('Cache-Control: private, max-age=3600');

readfile($putanja);
exit;
