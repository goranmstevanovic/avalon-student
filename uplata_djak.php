<html>
<head>
    <link rel="shortcut icon" href="images/kalen.png">
    <link rel="stylesheet" type="text/css" href="tigrakal/tcal.css" />
    <script type="text/javascript" src="tigrakal/tcal.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script> 
    <!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script> -->
     <!--   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
	<!--	 <script src="ajax321jquery.min.js"></script> -->
    <script type="ajax/jquery15.min.js"></script>
</head>
<body>
<?php
/**
 * Created by PhpStorm.
 * User: goran
 * Date: 6.11.2019
 * Time: 13:13
 */
if(isset($_GET['id'])){$idd = $_GET['id']; // echo "Radi se o djaku  br: ", $idd;
}

// core configuration
include_once "config/core.php";

// set page title
$page_title = "<h3 style='color:red' >Evidentiraj uplatu djaka:</h3>";

// include login checker
include_once "login_checker.php";

// include classes
include_once 'config/database.php';
include_once 'objects/user.php';
include_once "libs/php/utils.php";
include_once 'objects/grupa.php';
include_once 'objects/djak.php';
include_once 'objects/zaduzenje.php';
include_once 'objects/uplata.php';
include_once 'objects/povezivanje.php';
include_once 'objects/nivo_znanja.php';
include_once 'objects/jezik.php';
include_once "layout_head2.php";
$database = new Database();
$db = $database->getConnection();
$grupa = new grupa($db);
$djak = new djak($db);
$profesor = new user($db);
$zaduzenje = new zaduzenje($db);
$povezivanje = new povezivanje($db);
$uplata = new uplata($db);
$nivo_znanja = new nivo_znanja($db);
$jezik = new jezik($db);


$stmt = $djak->read_one_djak("djaci", $idd);
$row_category_djak = $stmt->fetch(PDO::FETCH_ASSOC);
extract($row_category_djak);



// registration form HTML
// code when form was submitted
// if form was posted
if($_POST){

    // set values to object properties

    $tmp77 = explode (".", $_POST['datum_generisanja']);
    $_POST['datum_generisanja'] = $tmp77[2] . "-" . $tmp77[1] . "-" . $tmp77[0];


    $uplata->datum_generisanja=$_POST["datum_generisanja"];

    $uplata->iznos=$_POST['iznos'];
    $uplata->komentar=$_POST['komentar'];
    $uplata->upisao=$_SESSION['user_id'];
    $uplata->fk_grupa=$_POST['grupa'];
    $uplata->fk_djak=$idd;
    $uplata->racun=$_POST['racun'];
    $uplata->aktivan = 1;
    $uplata->broj_priznanice = $_POST['broj_priznanice'] ?? null;

// create the user
    if($uplata->create()){

        echo "<div class='alert alert-info'>";
        echo "Uspesno ste upisali uplatu";


        // empty posted values
        $_POST=array();
       // echo'<script>window.parent.opener.location.reload();</script>';
	   header("Location: kartica_djak.php?id=$idd", true, 303);

       // echo"<script>window.close();</script>";

    }else{
        echo "<div class='alert alert-danger' role='alert'>Neuspešna promena podataka. Molimo Vas da pokušate ponovo.</div>";
    }

}
?>
<div class="col-md-12" style='width:90%; margin-left: 5%; background-color: #FAFAFA;' id='glavni'>
<h4><b>Evidentiraj uplatu đaka:</b></h4><br>
<form action='uplata_djak.php?id=<?php echo $id; ?>' method='post' >
    <div class='col-md-4' id='glavni2' style='background-color: #FFFFFF;' >
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
                <td>Komentar</td>
                <td style="width:100%;"><textarea class='form-control' disabled ><?php echo $komentar;  ?></textarea></td>
            </tr>
        </table>
    </div>
    <!-- style="opacity: 0;  transition: all ease 0.8s;" -->
    <div class='col-md-4' style="border-style: solid; border-color: #F2F2F2; padding : 10px; "   >
        <?php
        $broj_grupa_u_kojima_je = $povezivanje->count_grupa($idd);
        // echo $broj_grupa_u_kojima_je,"<br/>";
        $osim = array();
        if($broj_grupa_u_kojima_je > 0)
        {
            echo "Djak je u grupi:<br/><hr>";

            $ttss = $povezivanje->read_all_group_students_za_finasijsku($idd);
            while ($row_category_grupe = $ttss->fetch(PDO::FETCH_ASSOC))
            {

                array_push($osim, $row_category_grupe['fk_grupa']);
                $sst = $grupa->read_one_grupa1($row_category_grupe['fk_grupa']);
                $row_citanje_grupe = $sst->fetch(PDO::FETCH_ASSOC);
                extract($row_citanje_grupe);
                // echo "nivo=", $nivo;
                $stm_jezik = $jezik->read_one_jezik($fk_jezik,'jezik');
                $row_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC);
                $fk_jezik = $row_jezik['ime'];


                   
                $stm_nivo = $nivo_znanja->read_one_nivo($nivo,'nivo_znanja');
                $row_nivo_znanja = $stm_nivo->fetch(PDO::FETCH_ASSOC);
                $nivo = $row_nivo_znanja['ime'];

                echo $fk_jezik, "&nbsp;", $nivo, "&nbsp;<b> [ ", $alias, " ]</b><br/>";

                echo "U grupi je od: ", $row_category_grupe['created'], "<br/>";
                echo"<hr>";
                $broj_uplata_dosad = $uplata->count_uplata($idd, $row_category_grupe['fk_grupa']);
                if($broj_uplata_dosad > 0)
                {
                    echo "<b>Dosadasnje Uplate:</b> <br/>";
                    echo"<table class='table table-bordered p-5' >"; ?>
                    <tr ><th>Datum:</th><th>Iznos</th></tr>
                    <?php  $nsbp = $uplata->read_all_uplate_students($idd, $row_category_grupe['fk_grupa']);
                    while ($row_category_uplate = $nsbp->fetch(PDO::FETCH_ASSOC))
                    {
                        $tmp77 = explode ("-", $row_category_uplate['datum_generisanja']);
                        $row_category_uplate['datum_generisanja'] = $tmp77[2] . "." . $tmp77[1] . "." . $tmp77[0];
                        $id=$row_category_uplate['id']; ?>
                        <tr id="<?php echo $id; ?>"><?php echo" <td>{$row_category_uplate['datum_generisanja']}</td><td>{$row_category_uplate['iznos']}</td>"; ?>
                         <!--   <td style="border:none;" ><button class="btn btn-danger btn-sm remove" ><img style="border:0; outline: none;" src="kanta32.png" width=16 height=16>  </button></td></tr> -->
                        <?php
                        //  echo "<b>Od:</b> ",$row_category_zaduzenje['od_datuma'],"&nbsp;"," <b> Do:</b> ", $row_category_zaduzenje['do_datuma'],"&nbsp;[ ",$row_category_zaduzenje['iznos']," din. ]<br/>";
                    }
                    echo"</table>";



                }else{echo"Jos nema evidentiranih uplata";}
                echo"<hr>";



            }
            } else {echo "Jos nema grupu";  };





        ?>
    </div>

   


    <div class='col-md-4' id='glavni3' style='background-color: #FFFFFF;' >
        <table class='table table-responsive'>
            <tr>
                <td colspan="2">
                    <b>  Uplata vezana za grupu: </b>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <?php
                    if($broj_grupa_u_kojima_je > 1){

                        // read the scool boards from the database
                        $stmt = $grupa->read_allgrupa_samo("grupe", $osim);

                        // put them in a select drop-down
                        echo "<select class='form-control' name='grupa' required> ";
                        echo "<option value=''>Odaberi grupu...</option>";

                        while ($row_category = $stmt->fetch(PDO::FETCH_ASSOC)) {     // Citanje jedne po jedne vrste  iz $smtp i mecanje u $rowcategory
                            extract($row_category);                       //ekstrakovanje na pojedniacne zapise unutar vrste
                            $stmt1 = $profesor->read_one_profesor($fk_profesor, "users");
                            $row_profesor = $stmt1->fetch(PDO::FETCH_ASSOC);
                            $stm_jezik = $jezik->read_one_jezik($fk_jezik,'jezik');
                            $row_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC);
                            $fk_jezik = $row_jezik['ime'];
            
            
                               
                            $stm_nivo = $nivo_znanja->read_one_nivo($nivo,'nivo_znanja');
                            $row_nivo_znanja = $stm_nivo->fetch(PDO::FETCH_ASSOC);
                            $nivo = $row_nivo_znanja['ime'];
                            echo "<option value='{$id}'>{$fk_jezik}&nbsp;{$nivo}&nbsp;{$row_profesor['firstname']}&nbsp;-[{$alias}]</option>";           // vrednost z prenos je $id a prikazuje se $name, to jest ime
                        }

                        echo "</select>";
                    } elseif($broj_grupa_u_kojima_je == 1) { 
						$stmt = $grupa->read_allgrupa_samo("grupe", $osim);
						$row_category = $stmt->fetch(PDO::FETCH_ASSOC);
					?>
                        <input type="hidden" name="grupa" value = " <?php echo $row_category['id']; ?> " />
                        <?php   echo "{$fk_jezik}&nbsp;{$row_category['nivo']}&nbsp;&nbsp;-[{$alias}]";
                    }else{
                        echo "<div class='alert alert-info'>";
                        echo "Djak nema grupa";
                        echo "</div>"; ?>
                        <script>
                            $(document).ready(function () {
                               
                                    $("#register2").attr("disabled", true);
                                    return true;
                                
                            });
                            </script>
                    <?php        
                    }
                    ?>
                </td>
            </tr>


           
              

            <tr>
           
            <td><b>  Iznos uplate: </b></td>        
                <td>
                    <input class='form-control' type="number" placeholder="2700.00" required name="iznos" min="1" value="2700" step="0.01" title="Currency" required >
                </td>
               
            </tr>
            <tr>
            <td> <b>  Vrsta uplate: </b></td>
                <td>
                    <select class='form-control' name='racun' onchange="getval(this);" required>
                        <option value='0'>cash</option>
                        <option value='1'>račun</option>
                    </select>
                </td>
               
            </tr>
           
            <tr id='br_priznanice' >
                <td><b>  Broj priznanice: </b></td>
                <td><input  type="text" class='form-control' name = "broj_priznanice"  required /></td>
            </tr>        
            
            
                

            <tr>
                <td >
                    <b>  Datum uplate: </b>
                </td>
                <td style='text-align: right;'>
                      <input  type="text"  name = "datum_generisanja"  required style = "background-color:white; padding: 4px; border-radius: 5px;" size="10" class="tcal" />


                </td>
            </tr>
            <tr>
               
            </tr>
            <tr>
                <td colspan="2">
                    <b>  Komentar: </b>
                </td>
            </tr>
            <tr>

                <td colspan="2"><textarea name='komentar' class='form-control' ></textarea></td>

            </tr>


        </table>
    </div>
    <div class='col-md-12' >

        <table>
            <tr>
                <td></td>
                <td>
                    <button type="submit" id='register2'  class="btn btn-danger">
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
include_once "layout_foot.php";
?>
<script>
function getval(sel)
        {
            console.log(sel.value);
            if(sel.value == '0'){document.getElementById('br_priznanice').style.visibility = 'visible'; 
                $("#br_priznanice").find("select, input, textarea, button, select").removeAttr("disabled");
            }
            if(sel.value == '1'){
                document.getElementById('br_priznanice').style.visibility = 'hidden'; 
                $('#br_priznanice').find('input, textarea, button, select').attr('disabled','disabled');
            }

            console.log(document.getElementById('br_priznanice').style.display);
            /* alert(sel.value); */
        }
</script>



</body>
</html>
