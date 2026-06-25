<head>
    <link rel="shortcut icon" href="images/kalen.png">
	<title> Sve grupe finasije: </title>
</head>
<?php
// core configuration
include_once "config/core.php";

// check if logged in as admin
include_once "login_checker.php";

// include classes
include_once 'config/database.php';
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
$page_title = "Finasije mojih studentskih grupa:";

// include page header HTML
include_once "layout_head2.php";
echo"<h3 style='color:red;'><u>Finasije mojih studentskih grupa:</u></h3>";

echo "<div class='col-md-12'>";

// read all users from the database
$stmt33 = $grupa->read_allgrupa_profesor("grupe", $_SESSION['user_id']);
//$stmt33 = $grupa->read_Allgrupa("grupe");

// count retrieved users
$num = 5;

// to identify page for paging
$page_url="read_grupa_finasije.php?";

// include products table HTML template
include_once "read_grupa_finasije_template.php";

echo "</div>";

// include page footer HTML
include_once "layout_foot.php";
?>