<head>
    <link rel="shortcut icon" href="images/kalen.png">
	<title> Dobrodosli: </title>
</head>


<?php
// core configuration
include_once "config/core.php";

// set page title
$page_title="";

// include login checker
$require_login=true;
include_once "login_checker.php";

// include page header HTML
include_once 'layout_head1.php';

echo "<div class='col-md-12'>";

// to prevent undefined index notice
$action = isset($_GET['action']) ? $_GET['action'] : "";

// if login was successful
if($action=='login_success'){
    echo "<div class='alert alert-success'>";
    echo "<strong> Zdravo 	&nbsp;" . $_SESSION['firstname'] . ", dobro nam došli!!</strong>";
    echo "<br/><p>Ovo je stranica namenjena Vama. Svaka Vaša sugestija će nam pomoći da unapredimo aplikaciju <br/>
Ideje slati na email: <b> goranmstevanovic@gmail.com   </b>.</p> ";;
    echo "</div>";
}

// if user is already logged in, shown when user tries to access the login page
else if($action=='already_logged_in'){
    echo "<div class='alert alert-success'>";
    echo "<strong>Logovani ste kao profesor...</strong>";
    echo "</div>";
}

// content once logged in
echo "<div class='alert alert-success' style='background-color: white !important;'>";
echo "<div class='alert alert-success'>";
echo "<strong>Logovani ste kao profesor...</strong>";
echo "</div>";

echo "</div>";

echo "</div>";

// footer HTML and JavaScript codes
include 'layout_foot.php';
?>