<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors",1);
include_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();


if(isset($_POST['idiz']))
{
	echo "UUUUUUUUUU", $_POST['idiz'];
    $zzi=$_POST['idiz'];
    $deleted = date("Y-m-d H:i:s");
    $brisac = $_SESSION["user_id"];
    $stmt = $db->prepare("UPDATE `zaduzenja` SET `aktivan` = FALSE, `deleted` = '$deleted', `obrisao` = '$brisac'   WHERE `id` = '$zzi'");
    if($stmt->execute()){echo'ZAPISANO';}else{echo' NIJE ZAPISANO';}
    //$row = $stmt->fetch();
	echo 'Deleted successfully.';
}
?>