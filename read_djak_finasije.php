<head>
    <link rel="shortcut icon" href="images/kalen.png">
	<title> Svi djaci finsije: </title>
    <!--  <script type="text/javascript" src="../tabele/jquery-1.12.4.js"></script>
      <script type="text/javascript" src="../tabele/jquery.dataTables.min.js"></script> -->
    <link rel="stylesheet" type="text/css" href="tabele/jquery.dataTables.min.css" />


    <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"> -->
   <!-- <link rel="stylesheet" type="text/css" href="DataTables/datatables.css"> -->
    <script type="text/javascript" charset="utf8" src="DataTables/jquery-3.3.1.js"></script>
    <script type="text/javascript" charset="utf8" src="DataTables/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="DataTables/datatables.js"></script>

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
$page_title = "<tab style='color:red;'>Finansije naših djaka:</tab>";

// include page header HTML
include_once "layout_head2.php";

echo"<h3 style='color:red;'><u>Finansije naših djaka:</u></h3>";

echo "<div class='col-md-12'>";

// read all users from the database
$stmt = $djak->readAll_djak($from_record_num, $records_per_page);

// count retrieved users
$num = $stmt->rowCount();

// to identify page for paging
//$page_url="read_djak.php?";

// include products table HTML template
include_once "read_djak_finasije_template.php";

echo "</div>";

// include page footer HTML
include_once "layout_foot1.php";
?>