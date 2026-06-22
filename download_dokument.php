<?php
// ini_set('display_errors', 1);
// 		ini_set('display_startup_errors', 1);
// 		error_reporting(E_ALL);  
// 🔴 NEMA error_reporting OVDE
// 🔴 NEMA echo
// 🔴 NEMA HTML

// =====================
// BUFFER CLEAN START
// =====================
while (ob_get_level()) {
    ob_end_clean();
}

// =====================
// CORE
// =====================
include_once "config/core.php";
include_once "config/database.php";
include_once __DIR__ . '/config/require_login.php';

//session_start();

// =====================
// INPUT
// =====================
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    exit;
}

// =====================
// DB
// =====================
$db = (new Database())->getConnection();

$stmt = $db->prepare("
    SELECT putanja, originalni_naziv, ekstenzija 
    FROM dokumenti 
    WHERE id = ? AND aktivan = 1
");
$stmt->execute([$id]);

$dokument = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dokument) {
    http_response_code(404);
    exit;
}

// =====================
// PATH
// =====================
$path = rtrim($_ENV['DOCUMENTS_ROOT'], '/\\') . '/' . $dokument['putanja'];
//echo $path;
//exit;
if (!file_exists($path)) {
    http_response_code(404);
    exit;
}

// =====================
// MIME (sigurno)
// =====================
$ext = strtolower($dokument['ekstenzija']);

switch($ext){
    case 'jpg':
    case 'jpeg':
        $contentType = 'image/jpeg';
        break;
    case 'png':
        $contentType = 'image/png';
        break;
    case 'pdf':
        $contentType = 'application/pdf';
        break;
    case 'doc':
        $contentType = 'application/msword';
        break;
    case 'docx':
        $contentType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        break;
    case 'xls':
        $contentType = 'application/vnd.ms-excel';
        break;
    case 'xlsx':
        $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        break;
    default:
        $contentType = 'application/octet-stream';
}

// =====================
// HEADERS
// =====================
header('Content-Type: ' . $contentType);
header('Content-Length: ' . filesize($path));

// preview za slike i pdf
if (in_array($ext, ['jpg','jpeg','png','pdf'])) {
    header('Content-Disposition: inline; filename="'.basename($dokument['originalni_naziv']).'"');
} else {
    header('Content-Disposition: attachment; filename="'.basename($dokument['originalni_naziv']).'"');
}

// =====================
// OUTPUT
// =====================
readfile($path);
exit;