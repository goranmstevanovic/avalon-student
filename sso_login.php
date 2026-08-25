<?php
include_once "config/core.php";
include_once "config/database.php";
include_once "objects/djak.php";

$token = $_GET['token'] ?? '';

if (empty($token)) {
    header("Location: {$home_url}login?action=please_login");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare("SELECT fk_djak, created_by FROM sso_tokens WHERE token = :token AND used_at IS NULL AND expires_at > NOW()");
$stmt->bindParam(':token', $token);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    header("Location: {$home_url}login?action=please_login");
    exit;
}

$user = new djak($db);
$stmt_user = $user->read_one((int)$row['fk_djak']);
$user_row = $stmt_user->fetch(PDO::FETCH_ASSOC);

if (!$user_row || (int)$user_row['status'] !== 1) {
    header("Location: {$home_url}login?action=please_login");
    exit;
}

// token je jednokratan — odmah ga oznaci kao iskoriscen, bez obzira na ishod ispod
$stmt_use = $db->prepare("UPDATE sso_tokens SET used_at = NOW() WHERE token = :token");
$stmt_use->bindParam(':token', $token);
$stmt_use->execute();

session_regenerate_id(true);

$_SESSION['login_attempts'] = 0;
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = $user_row['id'];
$_SESSION['role'] = $user_row['role'];
$_SESSION['firstname'] = htmlspecialchars($user_row['firstname'], ENT_QUOTES, 'UTF-8');
$_SESSION['lastname'] = $user_row['lastname'];

// admin koji je pokrenuo "Uđi u nalog" — omogucava "Nazad na admin nalog" dugme
if (!empty($row['created_by'])) {
    $_SESSION['impersonating_admin_id'] = (int)$row['created_by'];
} else {
    unset($_SESSION['impersonating_admin_id']);
}

header("Location: {$home_url}index?action=login_success");
exit;
