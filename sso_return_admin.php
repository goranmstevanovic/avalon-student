<?php
include_once "config/core.php";
include_once "config/database.php";

if (empty($_SESSION['impersonating_admin_id'])) {
    header("Location: {$home_url}index");
    exit;
}

$database = new Database();
$db = $database->getConnection();

// usput pociscenje starih isteklih tokena (samoodrzavanje, bez potrebe za cron-om)
$db->exec("DELETE FROM sso_return_tokens WHERE expires_at < NOW() - INTERVAL 1 DAY");

$token = bin2hex(random_bytes(32));
$fk_admin_user = (int)$_SESSION['impersonating_admin_id'];
$fk_djak = (int)($_SESSION['user_id'] ?? 0);

$stmt = $db->prepare("INSERT INTO sso_return_tokens (token, fk_admin_user, fk_djak, created_at, expires_at)
    VALUES (:token, :admin, :djak, NOW(), DATE_ADD(NOW(), INTERVAL 60 SECOND))");
$stmt->bindParam(':token', $token);
$stmt->bindParam(':admin', $fk_admin_user);
$stmt->bindParam(':djak', $fk_djak);
$stmt->execute();

// izloguj polaznika pre povratka u admin nalog
session_unset();
session_destroy();

$admin_app_url = $_ENV['ADMIN_APP_URL'] ?? 'http://localhost/avalon/';
header("Location: " . $admin_app_url . "admin/sso_login_return.php?token=" . $token);
exit;
