<html>
<head>
    <link rel="shortcut icon" href="images/kalen.png">
    <title> Dnevni izvestaj: </title>

    <link rel="stylesheet" type="text/css" href="tigrakal/tcal.css" />
    <script type="text/javascript" src="tigrakal/tcal.js"></script>
    <!-- <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script> -->
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
if(isset($_GET['admin'])){$zadmin = $_GET['admin']; // echo "Radi se o djaku  br: ", $idd;
   // echo $zadmin,"<br/>";
 $dan1 = substr($zadmin, 0, 10);
// echo $dan1,"<br/>";
    $prof = substr($zadmin, strpos($zadmin, "/") + 1);
  //  echo $prof;
     $tmp77 = explode ("-", $dan1);
    $dan2 = $tmp77[2] . "." . $tmp77[1] . "." . $tmp77[0];
}

if(isset($_GET['admin2'])){$admin2 = $_GET['admin2']; // echo "Radi se o djaku  br: ", $idd;
   // echo $admin2,"<br/>";
    $dan1 = substr($admin2, 0, 10);
   $dan2 = substr($admin2, 0, 7);

 //   echo 'admin2';
// echo $dan1,"<br/>";
    $prof = substr($admin2, strpos($admin2, "/") + 1);
    //  echo $prof;
    $za_prenos = $dan2."/".$prof;
  //  echo $za_prenos;
    $tmp77 = explode ("-", $dan1);
    $dan2 = $tmp77[2] . "." . $tmp77[1] . "." . $tmp77[0];
}

// core configuration
include_once "config/core.php";

// set page title
$page_title = "";

// include login checker
include_once "login_checker.php";

// include classes
include_once 'config/database.php';
include_once 'objects/user.php';
include_once "libs/php/utils.php";
include_once 'objects/grupa.php';
include_once 'objects/djak.php';
include_once 'objects/tok_novca.php';
include_once 'objects/uplata.php';
include_once 'objects/povezivanje.php';
include_once "layout_head1.php";
$database = new Database();
$db = $database->getConnection();
$grupa = new grupa($db);
$djak = new djak($db);
$profesor = new user($db);
$tok_novca = new tok_novca($db);
$uplata = new uplata($db);


//$dan = date("Y-m-d");
//echo $dan;
$stmt = $tok_novca->read_one_day_one_profesor($dan1, $_SESSION['user_id']);
$row_category_dnevni_izvestaj = $stmt->fetch(PDO::FETCH_ASSOC);
//var_dump($row_category_dnevni_izvestaj);
if($row_category_dnevni_izvestaj != FALSE ){
    extract($row_category_dnevni_izvestaj);
}




// registration form HTML
// code when form was submitted
// if form was posted
if($_POST){

    // set values to object properties

 //   $tmp77 = explode (".", $_POST['datum_generisanja']);
  //  $_POST['datum_generisanja'] = $tmp77[2] . "-" . $tmp77[1] . "-" . $tmp77[0];

    $tok_novca->dan = $dan1;
    $tok_novca->fk_profesor = $prof;

    $tok_novca->iznos_predao=$_POST['iznos_predao'];
    $tok_novca->komentar_iznos_predao=$_POST['komentar_iznos_predao'];
    $tok_novca->iznos_primio=$_POST['iznos_primio'];
    $tok_novca->iznos_primio2=$_POST['iznos_primio2'];
    $tok_novca->komentar_iznos_primio=$_POST['komentar_iznos_primio'];
    $tok_novca->primio_racun=$_POST['primio_racun'];
    $tok_novca->primio_racun2=$_POST['primio_racun2'];
    $tok_novca->upisao=$_SESSION['user_id'];
    $tok_novca->aktivan = 1;

// create the user
    if($tok_novca->create($dan1, $prof)){

        echo "<div class='alert alert-info'>";
        echo "Uspesno ste upisali uplate i isplate za dan";
        // empty posted values

      //   echo'<script>window.parent.opener.location.reload();</script>';
    //    $_POST=array();
        if( isset($admin2) )
        {
            header("Location: mesecna_statistika_profesor.php?admin=$za_prenos", true, 303);
          /*  echo $zadmin;
            echo $admin2; */

        }else
            {
                echo $zadmin;
                echo $admin2;
                header("Location: tok_novca_profesora.php?id=$prof", true, 303);
             }


      // echo"<script>window.close();</script>";

    }else{
        echo "<div class='alert alert-danger' role='alert'>Neuspešna promena podataka. Molimo Vas da pokušate ponovo.</div>";
    }

}
$stmt4 = $profesor->read_jedan_profesor($prof,"users");
$row_category_profesor = $stmt4->fetch(PDO::FETCH_ASSOC);


?>
<h3 style="margin-left: 10%;">Evidencija uplata na dan <?php echo $dan2; ?> :
    
</h3>
<?php if( isset($zadmin) )
{ ?>
<!--<form action='dnevni_izvestaj.php?admin=<?php // echo $dan1."/".$prof; ?>' method='post' > -->
    <?php }else { ?>
 <!--   <form action='dnevni_izvestaj.php?admin2=<?php // echo  $dan1."/".$prof; ?>' method='post' > -->
        <?php } ?>
    <div class='col-md-12' style = "width : 100%; margin-left : 0;">
    <div class='col-md-10' style="border-style: solid; border-color: #F2F2F2; padding : 10px; ">
        <p><h4>Suma današnjih uplata:</h4></p>
        <table class='table table-bordered'>
         <tr><th class="text-center">Djak:</th><th class="text-center">Iznos:</th></tr>
        <?php
        //$dan1 = date("Y-m-d");
      //  $iid = $_SESSION['user_id'];
        $suma_danasnjih_uplata =0;
        $stmt = $uplata->read_all_uplate_na_dan($dan1, $prof);
        while ($row_uplata = $stmt->fetch(PDO::FETCH_ASSOC)){
            $stmt1 = $djak->read_one_djak("djaci", $row_uplata['fk_djak']);
            $row_category_djak = $stmt1->fetch(PDO::FETCH_ASSOC);
            extract($row_category_djak);
            $suma_danasnjih_uplata = $suma_danasnjih_uplata + $row_uplata['iznos'];
            ?>
         <tr><td><?php echo $firstname,"&nbsp;",$lastname; ?></td><td class="text-center" ><?php echo $row_uplata['iznos']; ?> </td></tr>
        <?php } ?>
            <tr><td><b>Suma uplata</b></td><td class="text-center"><b><?php echo number_format($suma_danasnjih_uplata,2,'.',''); ?></b></td></td></tr>
        </table>
    </div>
    <!-- style="opacity: 0;  transition: all ease 0.8s;" -->
    <div class='col-md-1' style="border-style: solid; border-color: #F2F2F2; padding : 10px; display:none "   >
    <table class="table table-responsive">
        <tr>
            <td><b> Uplaceno Vojayer-u: </b></td>
        </tr>
        <tr>
            <td>
                <input type="number" class="form-control"  required name="iznos_predao" min="0" <?php if(isset($iznos_predao)){echo "value=",$iznos_predao; } else { ?> value="0" <?php } ?> step="0.01" title="Currency" required >
            </td>
        </tr>
        <tr>
            <td>komenatr:</td>
        </tr>
        <tr>
            <td>
                <textarea type="number" class="form-control"   name="komentar_iznos_predao"  ><?php if(isset($komentar_iznos_predao)){echo $komentar_iznos_predao; } ?> </textarea>
            </td>
        </tr>
    </table>

    </div>


    <div class='col-md-1' style="border-style: solid; border-color: #F2F2F2; padding : 10px; display:none; " >
        <table class='table table-responsive'>
            <tr>
                <td><b> Voyager uplatio meni:</b></td>
                <td> <b>  Vrsta uplate: </b></td>
            </tr>
            <tr>
                <td>
                    <input  type="number" class="form-control"  required name="iznos_primio" min="0"   <?php if(isset($iznos_primio)){echo "value=",$iznos_primio; } else { ?> value="0" <?php } ?>   step="0.01" title="Currency" required >
                </td>
                <td>
                    <select class='form-control' name='primio_racun' required>
                        <option value='0' <?php if(isset($primio_racun) && $primio_racun == 0){echo "selected";} ?>>cash</option>
                        <option value='1' <?php if(isset($primio_racun) && $primio_racun == 1){echo "selected";} ?> >račun</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <input  type="number" class="form-control"  required name="iznos_primio2" min="0"   <?php if(isset($iznos_primio2)){echo "value=",$iznos_primio2; } else { ?> value="0" <?php } ?>   step="0.01" title="Currency" required >
                </td>
                <td>
                    <select class='form-control' name='primio_racun2' required>
                        <option value='0' <?php if(isset($primio_racun2) && $primio_racun2 == 0){echo "selected";} ?>>cash</option>
                        <option value='1' <?php if(isset($primio_racun2) && $primio_racun2 == 1){echo "selected";} ?> >račun</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2">komenatr:</td>
            </tr>
            <tr>
                <td colspan="2">
                    <textarea type="number" class="form-control"   name="komentar_iznos_primio"  ><?php if(isset($komentar_iznos_primio)){echo $komentar_iznos_primio; } ?></textarea>
                </td>
            </tr>
        </table>

    </div>
    <div class='col-md-12' >
    <script>
        function goBack() {
        window.history.back();
        }
    </script>
        <table>
            <tr>
                <td></td>
                <td>
                    <button  class="btn btn-primary"  onclick="goBack()">
                        <span class="glyphicon glyphicon-step-backward"></span> Vrati se na prethodnu stranu
                    </button>
                </td>
            </tr>
        </table>
    </div>
    </div>
</form>
<?php



// include page footer HTML
include_once "layout_foot.php";
?>



</body>
</html>
