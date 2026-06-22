<?php
include_once "config/database.php";

$db = (new Database())->getConnection();

$token = $_GET['token'] ?? '';

if(!$token){
    die("Nevalidan link.");
}

$stmt = $db->prepare("
SELECT id FROM djaci 
WHERE verification_token=:token
");

$stmt->execute([':token'=>$token]);

if($stmt->rowCount() == 1){

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $db->prepare("
    UPDATE djaci 
    SET email_verified_at = NOW(), verification_token = NULL
    WHERE id=:id
    ")->execute([':id'=>$row['id']]);

    header("Location: login.php?action=email_verified");
    exit;

}else{
    echo "Link nije validan ili je već iskorišćen.";
}