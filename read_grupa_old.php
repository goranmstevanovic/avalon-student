<head>
    <link rel="shortcut icon" href="images/kalen.png">
    <link rel="stylesheet" type="text/css" href="tabele/jquery.dataTables.min.css" />
   <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"> -->
 <!--  <link rel="stylesheet" type="text/css" href="../DataTables/datatables.css"> -->
    <script type="text/javascript" charset="utf8" src="DataTables/jquery-3.3.1.js"></script> 
    <script type="text/javascript" charset="utf8" src="DataTables/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="DataTables/datatables.js"></script>
	<title> Sve grupe: </title>
</head>
<?php
/
// include classes
include_once 'config/database.php';
include_once 'config/autoload.php';
include_once 'objects/grupa.php';
include_once 'objects/user.php';
include_once 'objects/povezivanje.php';

// get database connection
$database = new Database();
$db = $database->getConnection();


// initialize objects
$grupa = new grupa($db);
$povezivanje = new povezivanje($db);
//echo basename(__FILE__, '.php');
// set page title
$page_title = "Moje studentske grupe:";

// include page header HTML
include_once "layout_head2.php";
echo"<h3><u>Moje studentske grupe:</u></h3>";

echo "<div class='col-md-12'>";
if (isset($_SESSION["poruka_grupa_dodavanje"]))
{
    echo "<div class='alert alert-info'>";
    echo $_SESSION["poruka_grupa_dodavanje"];
    unset($_SESSION["poruka_grupa_dodavanje"]);
    unset($_SESSION["dodat_grupa"]);
    echo "</div>";
}

if (isset($_SESSION["poruka_grupa"]))
{
    echo "<div class='alert alert-info'>";
    echo $_SESSION["poruka_grupa"];
    unset($_SESSION["poruka_grupa"]);
    unset($_SESSION["dodati_jezik"]);
    echo "</div>";
}


// read all users from the database
$stmt33 = $grupa->read_allgrupa_profesor("grupe", $_SESSION['user_id']);
//$stmt33 = $grupa->read_Allgrupa("grupe");

// count retrieved users
$num = 5;

// to identify page for paging
$page_url="read_grupa.php?";

// include products table HTML template
include_once "read_grupa_template.php";

echo "</div>";

// include page footer HTML
include_once "layout_foot1.php";
?>