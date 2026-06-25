<?php
if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
error_reporting(E_ALL);
ini_set("display_errors",1);
include_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();


if(isset($_POST["idiu"]))
{
	echo $_POST["idiu"];
    $idi=$_POST["idiu"];
    $deleted = date('Y-m-d H:i:s');
    $brisac = $_SESSION['user_id'];
    $stmt = $db->prepare("UPDATE `uplate` SET `aktivan` = FALSE, `deleted` = '$deleted', `obrisao` = '$brisac'   WHERE `id` = '$idi'");
      if($stmt->execute()){echo'ZAPISANO';}else{echo' NIJE ZAPISANO';}
    $row = $stmt->fetch();
    echo 'Deleted successfully.';
}

// make sql query to remove element with given id
echo'alert("Treba nesto napisati u poruku da bi je pamtili....!!");   	';

