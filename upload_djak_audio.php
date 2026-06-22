<?php
// Prima djakov audio odgovor (Blob od MediaRecorder-a),
// snima u test-smszv/storage/domaci/djaci/ i upisuje u domaci_audio_odgovori.

include_once 'config/core.php';
include_once 'login_checker.php';
include_once 'config/database.php';
include_once 'config/autoload.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();
$domaci_obj = new domaci_audio($db);

$djak_id   = (int)$_SESSION['user_id'];
$domaci_id = isset($_POST['domaci_id']) ? (int)$_POST['domaci_id'] : 0;

if ($djak_id <= 0 || $domaci_id <= 0) {
    echo json_encode(['error' => 'Niste prijavljeni ili neispravan zadatak.']);
    exit;
}

if (!$domaci_obj->djak_ima_pristup($domaci_id, $djak_id)) {
    echo json_encode(['error' => 'Nemate pristup ovom zadatku.']);
    exit;
}

if ($domaci_obj->je_zakljucan($domaci_id)) {
    echo json_encode(['error' => 'Zadatak je zaključan, predaja nije moguća.']);
    exit;
}

if (!isset($_FILES['audio']) || $_FILES['audio']['error'] !== UPLOAD_ERR_OK) {
    $kod = $_FILES['audio']['error'] ?? -1;
    echo json_encode(['error' => "Upload greška (kod {$kod})."]);
    exit;
}

$file = $_FILES['audio'];

if ($file['size'] > 50 * 1024 * 1024) {
    echo json_encode(['error' => 'Audio fajl je prevelik (max 50MB).']);
    exit;
}

$prijavljeni_mime = trim($file['type']);
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$detektovani_mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

$mime_za_cuvanje = (strpos($prijavljeni_mime, 'audio/') === 0)
    ? $prijavljeni_mime
    : ((strpos($detektovani_mime, 'audio/') === 0) ? $detektovani_mime : $prijavljeni_mime);

$ekstenzija_mapa = [
    'audio/mp4'                => 'mp4',
    'video/mp4'                => 'mp4',
    'audio/mpeg'               => 'mp3',
    'audio/ogg'                => 'ogg',
    'audio/webm'               => 'webm',
    'video/webm'               => 'webm',
    'application/octet-stream' => 'webm',
];

$ext = 'webm';
foreach ($ekstenzija_mapa as $m => $e) {
    if (strpos($mime_za_cuvanje, $m) !== false || strpos($prijavljeni_mime, $m) !== false) {
        $ext = $e;
        break;
    }
}

$upload_dir = rtrim($_ENV['DOCUMENTS_ROOT'], '/') . '/storage/domaci/djaci/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$sacuvan_naziv = 'djak_' . $djak_id . '_' . $domaci_id . '_' . date('Ymd_His') . '_' . uniqid() . '.' . $ext;
$putanja = $upload_dir . $sacuvan_naziv;

if (!move_uploaded_file($file['tmp_name'], $putanja)) {
    echo json_encode(['error' => 'Fajl nije mogao biti sačuvan.']);
    exit;
}

$ok = $domaci_obj->sacuvaj_odgovor($domaci_id, $djak_id, $sacuvan_naziv, $mime_za_cuvanje);

if (!$ok) {
    @unlink($putanja);
    echo json_encode(['error' => 'Greška pri upisu u bazu.']);
    exit;
}

echo json_encode([
    'ok'        => true,
    'filename'  => $sacuvan_naziv,
    'mime_type' => $mime_za_cuvanje,
]);
