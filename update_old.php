
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

echo"<h5><u>Moj nalog :</u></h5><br/>";
// registration form HTML
// code when form was submitted
// if form was posted
if($_POST){
$email = trim($_POST['email']);
if(empty($email)){
    echo "<div class='alert alert-danger'>Email je obavezan.</div>";
    return;
}

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

if($stmt->rowCount() > 0){
    echo "<div class='alert alert-danger'>Email već postoji.</div>";
    return;
}


    $photo_name = $photo; // stara slika
    $djak->photo = $photo_name; // 🔥 UVEK postavi staru sliku

if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0){

    $allowed = ['image/jpeg','image/png','image/jpg','image/webp'];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES['photo']['tmp_name']);
    finfo_close($finfo);;

    if(in_array($mime, $allowed)){

        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);

        $photo_name = "djak_" . $id . "_" . time() . "." . $ext;

        $djak->photo = !empty($photo_name) ? $photo_name : $photo; // overwrite samo ako ima nova

        $upload_path = "assets/uploads/djaci/" . $photo_name;

        move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path);
        if($photo && file_exists("assets/uploads/djaci/".$photo)){
            unlink("assets/uploads/djaci/".$photo);
        }

    } else {
        echo "<div class='alert alert-danger'>Dozvoljene su samo slike</div>";
    }
}
   // $utils = new Utils();

    // set user email to detect if it already exists
  //  $user->email=$_POST['email'];

    // check if email already exists

        // create user
        // set values to object properties
        $djak->firstname=$_POST['firstname'];
        $djak->lastname=$_POST['lastname'];
        $djak->contact_number=$_POST['contact_number'];
        $djak->address=$_POST['address'];
        $djak->email = $email;
       
        //$user->access_level='Customer';
 //   echo '<script type="text/javascript">alert("'.$user->color_prof.'")</script>';
  //  $djak->status = isset($_POST['status']) ? 1 : 0;
    $djak->status = 1;

// create the user
if($djak->update($id)){

    // 🔥 ponovno učitaj podatke
    $stmt = $djak->read_one($id);
    $row_category_profesor = $stmt->fetch(PDO::FETCH_ASSOC);
    extract($row_category_profesor);

    echo "<div class='alert alert-success'>Uspešno sačuvano.</div>";
}else{
            echo "<div class='alert alert-danger' role='alert'>Neuspešna promena podataka. Molimo Vas da pokušate ponovo.</div>";
        }

}
?>
<?php
$avatar = $photo && file_exists("assets/uploads/djaci/".$photo)
    ? "assets/uploads/djaci/".$photo
    : "images/avatar.jpg";
//echo $photo;
?>

<!-- <div style="text-align:center; margin-bottom:20px;">
    <img id="previewImage" src="<?= $avatar ?>" 
         style="width:120px; height:120px; object-fit:cover; border-radius:50%; border:2px solid #ccc;">
</div> -->
<form method="post" enctype="multipart/form-data">

<div class="row">

    <!-- PROFILNA SLIKA -->
    <div class="col-12 text-center mb-4">
        <img id="previewImage" src="<?= $avatar ?>" 
             style="width:120px; height:120px; object-fit:cover; border-radius:50%; border:2px solid #ccc; cursor:pointer;"
             onclick="document.getElementById('photoInput').click();">

        <input type="file" name="photo" id="photoInput" class="form-control mt-3" accept="image/*">
        <div id="photoError" style="color:red; margin-top:5px;"></div>
    </div>

    <!-- IME -->
    <div class="col-md-6 mb-3">
        <label class="form-label">Ime</label>
        <input type="text" name="firstname" value="<?= $firstname ?>" class="form-control" required>
    </div>

    <!-- PREZIME -->
    <div class="col-md-6 mb-3">
        <label class="form-label">Prezime</label>
        <input type="text" name="lastname" value="<?= $lastname ?>" class="form-control" required>
    </div>

    <!-- TELEFON -->
    <div class="col-md-6 mb-3">
        <label class="form-label">Broj telefona</label>
        <input type="text" name="contact_number" value="<?= $contact_number ?>" class="form-control" required>
    </div>

    <!-- EMAIL -->
    <div class="col-md-6 mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" value="<?= $email ?>" class="form-control" required>
    </div>

    <!-- ADRESA -->
    <div class="col-12 mb-3">
        <label class="form-label">Adresa</label>
        <textarea name="address" class="form-control" rows="3" required><?= $address ?></textarea>
    </div>

    <!-- BUTTON -->
    <div class="col-12 text-end">
        <button type="submit" class="btn btn-danger">
            <i class='bi bi-record'></i> Snimi izmene
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
document.getElementById("photoInput").addEventListener("change", function(e){
    const file = e.target.files[0];
    if(file){
        const reader = new FileReader();
        reader.onload = function(e){
            document.getElementById("previewImage").src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>
<script>
const input = document.getElementById("photoInput");
const preview = document.getElementById("previewImage");
const errorDiv = document.getElementById("photoError");

const allowedTypes = ["image/jpeg", "image/png", "image/jpg", "image/webp"];
const maxSize = 2 * 1024 * 1024; // 2MB

input.addEventListener("change", function(e){

    errorDiv.innerHTML = "";
    const file = e.target.files[0];

    if(!file) return;

    // ✅ TIP
    if(!allowedTypes.includes(file.type)){
        errorDiv.innerHTML = "Dozvoljeni formati: JPG, PNG, WEBP";
        input.value = "";
        return;
    }

    // ✅ VELIČINA
    if(file.size > maxSize){
        errorDiv.innerHTML = "Slika ne sme biti veća od 2MB";
        input.value = "";
        return;
    }

    // ✅ PREVIEW
    const reader = new FileReader();
    reader.onload = function(e){
        preview.src = e.target.result;
    }
    reader.readAsDataURL(file);
});
</script>
<script>
document.querySelector("form").addEventListener("submit", function(e){
    if(errorDiv.innerHTML !== ""){
        e.preventDefault();
        alert("Molimo ispravite grešku sa slikom pre snimanja.");
    }
});
</script>