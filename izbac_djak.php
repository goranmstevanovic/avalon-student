<?php
/**
 * Created by PhpStorm.
 * User: goran
 * Date: 6.11.2019
 * Time: 13:13
 */
if(isset($_GET['id'])){
    $idd = $_GET['id']; 
 //   echo "RAdi se o djaku  br: ", $idd; 
}

// core configuration
include_once "config/core.php";

// set page title
$page_title = "Izbaci djaka iz grupe:";

// include login checker
include_once "login_checker.php";

// include classes
include_once 'config/database.php';
include_once 'objects/user.php';
include_once "libs/php/utils.php";
include_once 'objects/grupa.php';
include_once 'objects/djak.php';
include_once 'objects/povezivanje.php';
include_once "layout_head2.php";
$database = new Database();
$db = $database->getConnection();
$grupa = new grupa($db);
$djak = new djak($db);
$profesor = new user($db);
$povezivanje = new povezivanje($db);

$stmt = $djak->read_one_djak("djaci", $idd);
$row_category_djak = $stmt->fetch(PDO::FETCH_ASSOC);
extract($row_category_djak);



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

    $povezivanje->status = 0;

// create the user
    if($povezivanje->delete($idd, $povezivanje->fk_grupa )){

        echo "<div class='alert alert-info'>";
        echo "Uspesno ste izbacili djaka iz grupe. ";
        echo "</div>";

        // empty posted values
        $location = $home_url."admin/read_djak";
        $_SESSION["poruka_izbac"] =  "Uspešno ste izbacili djaka iz grupe  ";
     //   $_SESSION["dodati_drupa"] = $jezik->ime;
        $_POST=array();
        $previous = "<script>javascript:history.go(-2)</script>";
        echo $previous ;
      //  header("Location: $location?message=success");
      //  echo'<script>window.parent.opener.location.reload();</script>';

       // echo"<script>window.close();</script>";

    }else{
        echo "<div class='alert alert-danger' role='alert'>Neuspešna promena podataka. Molimo Vas da pokušate ponovo.</div>";
    }

}
?>
<div class="col-md-12" id='glavni'>
    <h4 style='padding:10px;'>Izbaci đaka iz grupe</h4>
    <form action='izbac_djak.php?id=<?php echo $id; ?>' method='post' >
        <div class='col-md-5'>
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
            $osim = array();
            if($broj_grupa_u_kojima_je > 0)
            {
                if($broj_grupa_u_kojima_je ==1)
                {echo "Djak se nalazi u grupi:<br/>";}else{echo "Djak se nalazi u grupama:<br/>";}

                $ttss = $povezivanje->read_all_group_students($idd);
                while ($row_category_grupe = $ttss->fetch(PDO::FETCH_ASSOC)) {

                    array_push($osim, $row_category_grupe['fk_grupa']);
                    $sst = $grupa->read_one_grupa1($row_category_grupe['fk_grupa']);
                 //  var_dump($sst);
                    $row_citanje_grupe = $sst->fetch(PDO::FETCH_ASSOC);
                  // var_dump($row_citanje_grupe);
                    extract($row_citanje_grupe);
                    // echo "nivo=", $nivo;
                  

                    echo $ime_jezika, "&nbsp;", $ime_nivoa, "&nbsp;<b>", $alias,"</b><br/>";
                }
            } else {echo "Jos nema grupu"; }
            ?>
        </div>

        <div class='col-md-4' >
            <table class='table table-responsive'>
                <tr>
                    <td>
                        <b>  Izbaci iz grupe: </b>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php
                        // read the scool boards from the database
                      // $stmt = $grupa->read_allgrupa_osim("grupe", $osim );
                        $stmt = $grupa->read_allgrupa_samo("grupe", $osim );
                       // var_dump($stmt);
                        // put them in a select drop-down
                        echo "<select class='form-control' name='grupa' required> ";
                        echo "<option value=''>Odaberi grupu...</option>";

                        while ($row_category = $stmt->fetch(PDO::FETCH_ASSOC)){     // Citanje jedne po jedne vrste  iz $smtp i mecanje u $rowcategory
                            extract($row_category);                       //ekstrakovanje na pojedniacne zapise unutar vrste
                            $stmt1 = $profesor->read_one_profesor($fk_profesor,"users");
                            $row_profesor = $stmt1->fetch(PDO::FETCH_ASSOC);
                           
                  

                            echo "<option value='{$id}'>{$ime_jezika}&nbsp;{$ime_nivoa}&nbsp;{$row_profesor['firstname']}&nbsp;-[{$alias}]</option>";           // vrednost z prenos je $id a prikazuje se $name, to jest ime
                        }

                        echo "</select>";
                        ?>
                    </td>
                </tr>
            </table>
        </div>
        <div class='col-md-12'>

            <table>
                <tr>
                    <td></td>
                    <td>
                        <button type="submit" class="btn btn-primary">
                            <span class="glyphicon glyphicon-plus"></span> Snimi izmene
                        </button>
                    </td>
                </tr>
            </table>
        </div>
    </form>
<?php



// include page footer HTML
include_once "layout_foot.php";
?>