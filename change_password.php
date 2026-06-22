<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

include_once "config/core.php";
include_once "config/database.php";

//session_start();

if(!isset($_SESSION['user_id'])){
    die("<div class='alert alert-danger'>Niste ulogovani.</div>");
}

$db = (new Database())->getConnection();

$user_id = $_SESSION['user_id'];

$current = $_POST['current_password'] ?? '';
$new = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';
if(strlen($new) < 8){
    die("<div class='alert alert-danger'>
        Lozinka mora imati minimum 8 karaktera.
    </div>");
}

// ❌ prazno
if(empty($current) || empty($new) || empty($confirm)){
    die("<div class='alert alert-danger'>Sva polja su obavezna.</div>");
}

// ❌ match
if($new !== $confirm){
    die("<div class='alert alert-danger'>Lozinke se ne poklapaju.</div>");
}

// 🔍 uzmi trenutnu lozinku iz baze
$stmt = $db->prepare("SELECT password FROM djaci WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $user_id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$user){
    die("<div class='alert alert-danger'>Korisnik ne postoji.</div>");
}

// ❌ proveri staru lozinku
if(!password_verify($current, $user['password'])){
    die("<div class='alert alert-danger'>Trenutna lozinka nije tačna.</div>");
}

// ❌ nova ista kao stara
if(password_verify($new, $user['password'])){
    die("<div class='alert alert-warning'>Nova lozinka mora biti drugačija.</div>");
}

// 🔐 hash
$hash = password_hash($new, PASSWORD_DEFAULT);

// 💾 update
$upd = $db->prepare("
    UPDATE djaci
    SET password = :password
    WHERE id = :id
");

if($upd->execute([
    ':password' => $hash,
    ':id' => $user_id
])){
    echo "<div class='alert alert-success'>Lozinka uspešno promenjena.</div>";
}else{
    echo "<div class='alert alert-danger'>Greška pri čuvanju.</div>";
}