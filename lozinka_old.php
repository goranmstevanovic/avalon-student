
<?php
/**
 * Created by PhpStorm.
 * User: goran
 * Date: 25.10.2019
 * Time: 12:36
 */
include_once "config/core.php";
include_once "config/autoload.php";
// if(isset($_GET['id'])){
//     $id = $_GET['id']; //echo "Doktur je br: ", $id;
//     if($id != $_SESSION['user_id']){
//         echo"<script> alert('Nemate privilegiju da vidite ovaj sadrzaj !!!'); window.history.back(); </script>"; 
//         exit;  
//     }
// }
 
$id = $_SESSION['user_id'];


// get database connection
$database = new Database();
$db = $database->getConnection();
$djak = new djak($db);
// initialize objects
//$user = new User($db);


echo "<div class='col-md-12' id='glavni' >";
$stmt = $djak->read_one($id);
$row_category_profesor = $stmt->fetch(PDO::FETCH_ASSOC);
extract($row_category_profesor);


// registration form HTML
// code when form was submitted
// if form was posted


// ❌ FORMAT
if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    echo "<div class='alert alert-danger'>Email nije validan.</div>";
    return;
}

// ❌ DUPLIKAT (ali ignoriše trenutnog korisnika)
$query = "SELECT id FROM djaci WHERE email = :email AND id != :id LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(":email", $email);
$stmt->bindParam(":id", $id);
$stmt->execute();

?>
<h5><u>Promena lozinke:</u></h5>

<div id="passwordMessage"></div>

<form id="changePasswordForm">

    <div class="row">

        <div class="col-md-4 mb-3">
            <label>Trenutna lozinka</label>
            <input type="password" name="current_password" class="form-control" required>
        </div>

        <div class="col-md-4 mb-3">
            <label>Nova lozinka</label>
            <input type="password" name="new_password" class="form-control" required>
        </div>

        <div class="col-md-4 mb-3">
            <label>Ponovi lozinku</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>

        <div class="col-12 text-end">
            <button type="submit" class="btn btn-warning">
                Promeni lozinku
            </button>
        </div>

    </div>

</form>

<?php

echo "</div>";

// include page footer HTML
include_once "layout_foot.php";
?>
<script>
document.getElementById("changePasswordForm").addEventListener("submit", function(e){
    e.preventDefault();

    const formData = new FormData(this);

    fetch("change_password.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        document.getElementById("passwordMessage").innerHTML = data;
    })
    .catch(err => {
        console.error(err);
    });
});
</script>
