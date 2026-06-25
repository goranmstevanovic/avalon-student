<head>
    <link rel="shortcut icon" href="images/kalen.png">
	<title> Svi moji djaci u skoli </title>
  <!--  <script type="text/javascript" src="../tabele/jquery-1.12.4.js"></script> 
  <script type="text/javascript" src="../tabele/jquery.dataTables.min.js"></script> -->
    <link rel="stylesheet" type="text/css" href="tabele/jquery.dataTables.min.css" />


   <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"> -->
 <!--  <link rel="stylesheet" type="text/css" href="../DataTables/datatables.css"> -->
    <script type="text/javascript" charset="utf8" src="DataTables/jquery-3.3.1.js"></script>
    <script type="text/javascript" charset="utf8" src="DataTables/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="DataTables/datatables.js"></script>
    <style>
        td {font-size: 12px}
    </style>
</head>
<?php
// core configuration
include_once "config/core.php";

// check if logged in as admin
include_once "login_checker.php";

// include classes
include_once 'config/database.php';
include_once 'objects/djak.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// initialize objects
$djak = new djak($db);
//echo basename(__FILE__, '.php');
// set page title
$page_title = "Moji djaci koji dolaze u školu:";

// include page header HTML
include_once "layout_head2.php";

echo "<div class='col-md-12' style='width: 100%; margin-left:0%;'>";

if (isset($_SESSION["poruka_dodavanje"]))
{
    echo "<div class='alert alert-info'>";
      echo $_SESSION["poruka_dodavanje"];
      unset($_SESSION["poruka_dodavanje"]);
      unset($_SESSION["dodati_drupa"]);
    echo "</div>";
}

if (isset($_SESSION["poruka_izbac"]))
{
    echo "<div class='alert alert-info'>";
      echo $_SESSION["poruka_izbac"];
      unset($_SESSION["poruka_izbac"]);
    // unset($_SESSION["dodati_drupa"]);
    echo "</div>";
}

// read all users from the database
//$stmt = $djak->readAll_djak_skola($from_record_num, $records_per_page);
$stmt = $djak->readAll_djak_skola_skola_prof($_SESSION['user_id']);
echo"<h3><u>Moji djaci koji dolaze u školu:</u></h3>";

// count retrieved users
$num = $stmt->rowCount();
//echo $num;
// to identify page for paging
//$page_url="read_djak.php?";
// include products table HTML template
include_once "read_djak_template.php";
echo "</div>";
// include page footer HTML
include_once "layout_foot1.php";
?>