<head>
    <link rel="shortcut icon" href="images/kalen.png">
	<title> Svi profesori finasije: </title>
</head>
<?php
// core configuration
include_once "config/core.php";

// check if logged in as admin
include_once "login_checker.php";

// include classes
include_once 'config/database.php';
include_once 'objects/user.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// initialize objects
$user = new User($db);

// set page title
$page_title = "<tab style='color:red;'>Naši profesori:</tab>";

// include page header HTML
include_once "layout_head2.php";
echo"<h3 style='color:red;'><u>Naši profesori:</u></h3>";

echo "<div class='col-md-12'>";

// read all users from the database
$stmt = $user->readAllP($from_record_num, $records_per_page);

// count retrieved users
$num = $stmt->rowCount();

// to identify page for paging
$page_url="read_users.php?";

// include products table HTML template
include_once "read_prof_finansije_template.php";

echo "</div>";

// include page footer HTML
include_once "layout_foot.php";
?>

