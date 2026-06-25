<html>
<head>
    <link rel="shortcut icon" href="images/kalen.png">
</head>
<body>
<?php
// core configuration
include_once "config/core.php";

// set page title
$page_title = "Dodaj novog saradnika";

// include login checker
include_once "admin/login_checker.php";

// include classes
include_once 'config/database.php';
include_once 'objects/user.php';
include_once "libs/php/utils.php";

// include page header HTML
include_once "layout_head.php";

echo "<div class='col-md-12'>";

// registration form HTML
// code when form was submitted
// if form was posted
if($_POST){

    // get database connection
    $database = new Database();
    $db = $database->getConnection();

    // initialize objects
    $user = new User($db);
    $utils = new Utils();

    // set user email to detect if it already exists
    $user->email=$_POST['email'];

    // check if email already exists
    if($user->emailExists()){
        echo "<div class='alert alert-danger'>";
        echo "The email adresu koju ste naveli vec je registrovana u sistemu pa Vas molimo da navedete drugu  <a href='{$home_url}register'>Pokusajte ponovo</a>";
        echo "</div>";
    }

    else{
        // create user
        // set values to object properties
        $user->firstname=$_POST['firstname'];
        $user->lastname=$_POST['lastname'];
        $user->contact_number=$_POST['contact_number'];
        $user->address=$_POST['address'];
        $user->plata=$_POST['plata'];
        $user->iznos_plate=$_POST['iznos_plate'];
        $user->procentat_za_platu = 50;
        If(isset($_POST['procentat_za_platu'])){
            $user->procentat_za_platu=50;
        }else{$user->procentat_za_platu = NULL;}
        $user->nacin_obracuna=1;
        $user->password=$_POST['password'];
        $user->access_level='Customer';
        $user->color_prof=$_POST['color'];
        $user->status=1;

// create the user
        if($user->create()){

            $stmt=$user->read_last_one_profesor('users');
            $row_category_profesor = $stmt->fetch(PDO::FETCH_ASSOC);
            extract($row_category_profesor);
            $user->insert_procenat($id, $_POST['procentat_za_platu'] );

            echo "<div class='alert alert-info'>";
            echo "Uspesno ste generisali novog saradnika. <a href='{$home_url}register'></a>.";
            echo "</div>";
            $location = $home_url."admin/read_users";
            $_SESSION["poruka_user"] =  "Uspešno ste dodali novog saradnika: ".$user->firstname."&nbsp".$user->lastname;
           // $_SESSION["dodati_jezik"] = $jezik->ime;
            $_POST=array();
            header("Location: $location?message=success");
            // empty posted values
            $_POST=array();
           // echo'<script>window.parent.opener.location.reload();</script>';

           // echo"<script>window.close();</script>";

        }else{
            echo "<div class='alert alert-danger' role='alert'>Neuspešna registracija. Molimo Vas da pokušate ponovo.</div>";
        }
    }
}
?>
    <form action='register.php' method='post' id='register'>

        <table class='table table-responsive'>

            <tr>
                <td class='width-30-percent'>Ime:</td>
                <td><input type='text' name='firstname' class='form-control' required value="<?php echo isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname'], ENT_QUOTES) : "";  ?>" /></td>
            </tr>

            <tr>
                <td>Prezime:</td>
                <td><input type='text' name='lastname' class='form-control' required value="<?php echo isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname'], ENT_QUOTES) : "";  ?>" /></td>
            </tr>

            <tr>
                <td>Broj telefona:</td>
                <td><input type='text' name='contact_number' class='form-control' required value="<?php echo isset($_POST['contact_number']) ? htmlspecialchars($_POST['contact_number'], ENT_QUOTES) : "";  ?>" /></td>
            </tr>

            <tr>
                <td>Adresa:</td>
                <td><textarea name='address' class='form-control' ><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address'], ENT_QUOTES) : "";  ?></textarea></td>
            </tr>
            <script>
             function getval(sel)
            {
                if(sel.value == 2){document.getElementById('fiksna').style.display = 'block';  }
                if(sel.value == 1){document.getElementById('fiksna').style.display = 'none'; }
                
                /*  alert(sel.value); */
            }
            </script>
            <tr>
                <td>Vrsta obracuna plate:</td>
                <td>
                    <select class='form-control' name='plata' onchange="getval(this);">
                        <option value = 1  > Po obracunu </option>
                        <option value = 2  > Fiksno </option>
                    </select>
                </td>
            </tr>
        </table>
        <div id="fiksna" style='display: none ; border: 1px solid #A4A4A4; border-radius:5px;  background-color:lightgray '>
                <table class='table table-responsive' >
                <tr>
                    <td class='width-30-percent' >Iznos plate:</td>
                    <td><input type='number' name='iznos plate' class='form-control' required value="<?php echo $iznos_plate ?? 0;  ?>" /></td>
                </tr>
                </table>
                
            </div>
            <table class='table table-responsive'>  
            
            <tr>
                <td class='width-30-percent' >
                    Boja u rasporedu:
                </td>
                <td>
                    <input type="color" name="color" value="#ff0000">
                </td>
            </tr>

            <tr>
                <td>Email:</td>
                <td><input type='email' name='email' class='form-control' required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES) : "";  ?>" /></td>
            </tr>

            <tr>
                <td>Password (Min 8 znakova):</td>
                <td><input type='password' name='password' class='form-control' required id='passwordInput'></td>
            </tr>

            <tr>
                <td></td>
                <td>
                    <button type="submit" class="btn btn-primary">
                        <span class="glyphicon glyphicon-plus"></span> Register
                    </button>
                </td>
            </tr>

        </table>
    </form>
<?php

echo "</div>";

// include page footer HTML
include_once "layout_foot.php";
?>
</body>
</html>
