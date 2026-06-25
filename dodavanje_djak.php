<html>
<head>
    <link rel="shortcut icon" href="images/kalen.png">
    <link href="search/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="search/css/bootstrap-select.min.css" />

<style>
    select.icon-menu option {
background-repeat:no-repeat;
background-position:bottom left;
padding-left:30px;
width: 20px;
}
</style>
</head>
<body>
<?php
/**
 * Created by PhpStorm.
 * User: goran
 * Date: 6.11.2019
 * Time: 13:13
 */
if(isset($_GET['id'])){$idd = $_GET['id']; // echo "RAdi se o djaku  br: ", $idd;
}

// core configuration
include_once "config/core.php";
// set page title
$page_title = "Dodaj djaka u grupu:";
// include login checker
include_once "login_checker.php";
// include classes
include_once 'config/database.php';
include_once 'objects/user.php';
include_once "libs/php/utils.php";
include_once 'objects/grupa.php';
include_once 'objects/djak.php';
include_once 'objects/povezivanje.php';
include_once 'objects/nivo_znanja.php';
include_once 'objects/jezik.php';
include_once 'objects/lokacija.php';
include_once "layout_head2.php";
$database = new Database();
$db = $database->getConnection();
$grupa = new grupa($db);
$djak = new djak($db);
$profesor = new user($db);
$lokacija = new lokacija($db);
$povezivanje = new povezivanje($db);
$stmt = $djak->read_one_djak("djaci", $idd);
$row_category_djak = $stmt->fetch(PDO::FETCH_ASSOC);
extract($row_category_djak);
$broj_lokacija = $lokacija->count_all();



// registration form HTML
// code when form was submitted
// if form was posted
if($_POST){


    // $utils = new Utils();

    // set user email to detect if it already exists
    //  $user->email=$_POST['email'];

    // check if email already exists

    // create user
    // set values to object properties
    $povezivanje->fk_grupa=$_POST['grupa'];
    $povezivanje->fk_djak=$idd;

    //$user->access_level='Customer';

    $povezivanje->status = 1;

// create the user
    if($povezivanje->create()){

        echo "<div class='alert alert-info'>";
        echo "Uspesno ste dodali djaka u grupu.";
        echo "</div>";

        // empty posted values
       // $_POST=array();
       
        $location = $home_url."read_djak";
        
       // echo "prethodna:" , $previous;
        $_SESSION["poruka_dodavanje"] =  "Uspešno ste dodali povezali djaka sa grupom  ";
      //  $_SESSION["dodati_drupa"] = $grupa->ime;
        $_POST=array();
        $previous = "<script>javascript:history.go(-2)</script>";
        header("location: $location");
        //  echo $previous ;
        // header("Location: $previous?message=success");
     //   echo'<script>window.parent.opener.location.reload();</script>';
      //  echo"<script>window.close();</script>";
    }else{
        echo "<div class='alert alert-danger' role='alert'>Neuspešna promena podataka. Molimo Vas da pokušate ponovo.</div>";
    }

}
?>

<div class="col-md-12" style="width:100%; margin-left:0%;" id='glavni' >
<h4>Dodaj djaka u grupu</h4><br/>
    <form action='dodavanje_djak.php?id=<?php echo $id; ?>' method='post' >
    <div class='col-md-4'>
        <table class='table table-responsive'>

            <tr>
                <td class='width-30-percent'>Ime:</td>
                <td> <?php echo $firstname; ?></td>
            </tr>

            <tr>
                <td>Prezime:</td>
                <td><?php echo $lastname; ?></td>
            </tr>

            <tr>
                <td>Broj telefona:</td>
                <td><?php echo $contact_number;  ?></td>
            </tr>

            <tr>
                <td>Adresa:</td>
                <td><?php echo $address;  ?></td>
            </tr>
            <?php if($broj_lokacija > 1){ ?>
                <tr>
                    <td>Lokacija:</td>
                    <td><?php
                    $stmt_lokacija = $lokacija->read_one($fk_lokacija);
                    $row_category_lokacija = $stmt_lokacija->fetch(PDO::FETCH_ASSOC);
                    echo $row_category_lokacija['ime'];  
                    ?></td>
                </tr>
                <?php 
            } ?>

            <tr>
                <td>Email:</td>
                <td><?php echo $email;  ?></td>
            </tr>

            <tr>
                <td>Aktivan</td>
                <td><input type="checkbox" name="status" value="aktivan" <?php if($status==1){echo"checked";}  ?> > </td>
            </tr>




        </table>
    </div>
        <div class='col-md-3' >
        <?php
        $broj_grupa_u_kojima_je = $povezivanje->count_grupa($idd);
       // echo $broj_grupa_u_kojima_je,"<br/>";
       $nivo_znanja = new nivo_znanja($db);
       $jezik = new jezik($db); 
       $osim = array();
        if($broj_grupa_u_kojima_je > 0)
        {
            echo "Djak je vec u grupi:<br/>";
            $ttss = $povezivanje->read_all_group_students($idd);
           
            while ($row_category_grupe = $ttss->fetch(PDO::FETCH_ASSOC)) {
                array_push($osim, $row_category_grupe['fk_grupa']);
                $sst = $grupa->read_one_grupa1($row_category_grupe['fk_grupa']);
                $row_citanje_grupe = $sst->fetch(PDO::FETCH_ASSOC);
                extract($row_citanje_grupe);
                $stm_jezik = $jezik->read_one_jezik($fk_jezik,'jezik');
                $row_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC);
                $stm_nivo = $nivo_znanja->read_one_nivo($nivo,'nivo_znanja');
                $row_nivo_znanja = $stm_nivo->fetch(PDO::FETCH_ASSOC);
                echo $row_jezik['ime'], "&nbsp;", $row_nivo_znanja['ime'], "&nbsp;<br/>";
            }
        } else {echo "Jos nema grupu"; }
        ?>
        </div>

        <div class='col-md-5' >
            <table class='table table-responsive'>
                <tr>
                    <td>
                        <b>  Dodaj u grupu: </b>
                    </td>
                </tr>
                <tr>
                    <td>
                    <?php
                   
                   // var_dump($osim);
                   // echo $fk_lokacija;
                    $stmt = $grupa->read_allgrupa_osim_prof("grupe", $osim, $_SESSION['user_id'] );
                   // var_dump($stmt);
                  //  $stmt = $grupa->read_allgrupa_osim("grupe", $osim );
                  //  var_dump($stmt->fetch(PDO::FETCH_ASSOC));
    
                   // $stmt = $grupa->read_allgrupa("grupe" );

                    // put them in a select drop-down
                    ?>
                  <select   name='grupa' class="selectpicker form-control down" data-show-subtext="true" data-live-search="true" class="icon-menu" required >
                  <option value=''>Odaberi grupu...</option>
                    <?php
                    while ($row_category = $stmt->fetch(PDO::FETCH_ASSOC)){     // Citanje jedne po jedne vrste  iz $smtp i mecanje u $rowcategory
                        extract($row_category); 
                        $stmt1 = $profesor->read_one_profesor($fk_profesor,"users");
                        $row_profesor = $stmt1->fetch(PDO::FETCH_ASSOC);
                        $stm_jezik = $jezik->read_one_jezik($fk_jezik,'jezik');
                        $row_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC);
                        $stm_nivo = $nivo_znanja->read_one_nivo($nivo,'nivo_znanja');
                        $row_nivo_znanja = $stm_nivo->fetch(PDO::FETCH_ASSOC);
                       if($nacin == 2){
                           $slika_nacin = 'images/online1.jpg';
                       }else{
                        $slika_nacin = 'images/room3.jpg';   
                       }
                       
                        echo "<option  style='background-image:url({$slika_nacin}); height: 25px !important; width: 15px !important; background-repeat: no-repeat; padding-left:15px; line-height: 200%; ' value='{$id}'>&nbsp; &nbsp; &nbsp;{$row_jezik['ime']}&nbsp;{$row_nivo_znanja['ime']}&nbsp;{$row_profesor['firstname']}&nbsp;-[{$alias}]";
                        if($nacin == 1 && $broj_lokacija > 1){echo " / ",$ime_lokacije;}
                        echo "</option>";           // vrednost z prenos je $id a prikazuje se $name, to jest ime
                    }
                    ?>
                    </select>
                    
                    </td>
                </tr>
                <script src="search/jquery.min.js"></script>
            <script src="search/bootstrap.min.js"></script>
            <script src="search/bootstrap-select.min.js"></script>
            </table>
        </div>
        <div class='col-md-12' id='glavni3'>

            <table>
                <tr>
                    <td></td>
                    <td>
                        <button type="submit" class="btn btn-danger">
                            <span class="glyphicon glyphicon-record"></span> Snimi izmene
                        </button>
                    </td>
                </tr>
            </table>
        </div>
    </form>
</div>    
<?php



// include page footer HTML
//include_once "layout_foot.php";
?>
</body>
</html>
