<html>
<head>
    <title> Tok novca profesor </title>
    <link rel="shortcut icon" href="images/kalen.png">
    <link rel="stylesheet" type="text/css" href="tigrakal/tcal.css" />
    <script type="text/javascript" src="tigrakal/tcal.js"></script>
</head>
<body>
<?php
ini_set('session.cache_limiter','public');
session_cache_limiter(false);
error_reporting(E_ALL);
ini_set("display_errors",1);
ini_set('memory_limit', '-1');
/**
 * Created by PhpStorm.
 * User: goran
 * Date: 25.10.2019
 * Time: 12:36
 */



// core configuration
include_once "config/core.php";
$_POST['btnSub'] = TRUE;
$iid = $_SESSION['user_id']; // echo "profesor je br: ", $iid;

// set page title
$page_title = "<tab style='color:red;'>Finasije profesora :</tab>";

// include login checker
include_once "login_checker.php";

// include classes
include_once 'config/database.php';
include_once 'config/funkcije.php';
include_once 'objects/user.php';
include_once 'objects/grupa.php';
include_once 'objects/djak.php';
include_once "libs/php/utils.php";
include_once 'objects/povezivanje.php';
include_once 'objects/zaduzenje.php';
include_once 'objects/uplata.php';
include_once 'objects/tok_novca.php';


// include page header HTML
include_once "layout_head2.php";
// get database connection
$database = new Database();
$db = $database->getConnection();
$profesor = new User($db);
$grupa = new grupa($db);
$povezivanje = new povezivanje($db);
$djak = new djak($db);
$zaduzenje = new zaduzenje($db);
$uplata = new uplata($db);
$tok_novca = new tok_novca($db);
// initialize objects
//$user = new User($db);
$stmt = $profesor->read_jedan_profesor($iid,"users");
$row_category_prof = $stmt->fetch(PDO::FETCH_ASSOC);
extract($row_category_prof);
//echo"<script> window.location=window.location; </script>";
echo "<div class='col-md-12' style='width:90%; margin-left: 5%;'>";



?>
<table class='table table-bordered'>
    <tr>
        <td ><h4>Profesor: <?php echo $firstname,"&nbsp;", $lastname; ?> </h4> </td>
    </tr>
</table>

</div>
<form action='tok_novca_profesora.php?id=<?php echo $iid; ?>' method='post' >
    <table align="center" style='border:none'>
        <tr>
            <td>Odaberi vremsnki interval za pregled: </td>
            <td style="padding-left:10px ; padding-right:10px; border:none">
                <b>	od: </b><input type="text" <?php if (isset($_POST['datum_od'])){echo'value="'.$_POST['datum_od'].'"';}?> name="datum_od" STYLE="background-color:white;  padding: 6px; border-radius: 3px;" size="12" class="tcal" />
            </td><td style="padding-left:10px ; padding-right:10px; border:none " >
                <b>	do: </b><input type="text" <?php if (isset($_POST['datum_do'])){echo'value="'.$_POST['datum_do'].'"';}?> name="datum_do" STYLE="background-color:white;  padding: 6px; border-radius: 3px; " size="12" class="tcal" />

            </td>
            <td style="border:0;"><input type='submit'  value='PRIKAŽI' name='btnSub' class='btn btn-primary' ></td>
        </tr>
    </table>
    <table align="center" >
        <tr border="0">

        </tr>
    </table>


</form>
<?php
if(isset($_POST['btnSub']))
{
  //  $datum_od = "01.01.1970";
   // $danas = date("d.m.Y");
    $datum_od = date('d.m.Y', strtotime('-1 months'));

    $datum_do =  date("d.m.Y");
    if (isset($_POST['datum_od']) and $_POST['datum_od'] != "") {
        $datum_od = $_POST['datum_od'];
    }
    if (isset($_POST['datum_do']) and $_POST['datum_do'] != "") {
        $datum_do = $_POST['datum_do'];
    }

//	echo "Datum od:",$datum_od,"<br/> Datum do: ",$datum_do;
    $tmp77 = explode(".", $datum_od);
    $datum_od = $tmp77[2] . "-" . $tmp77[1] . "-" . $tmp77[0];
    $tmp777 = explode(".", $datum_do);
    $datum_do = $tmp777[2] . "-" . $tmp777[1] . "-" . $tmp777[0];
//	echo "Datum od:",$datum_od,"<br/> Datum do: ",$datum_do;
/*
    $start = '$datum_od';
    $end = '$datum_do';
*/

    function daysBetween($start, $end){
   $dates = array();
   while($start <= $end)
   {
       array_push(
           $dates,
           date(
               'd.m.Y',
               $end
           )
       );
       $end -= 86400;
   }
   return $dates;
}

$start    = strtotime($datum_od);
$end    = strtotime($datum_do);
$dana_izmedju = daysBetween($start,$end);
//var_dump(daysBetween($start,$end));




    ?>

    <table class="table table-bordered" style=" width: 80%; margin-left:10%; margin-top : 30px;">
        <tr class="text-center"><th class="text-center">Datum:</th><th class="text-center" >Uplate na dan:</th><th class="text-center" >Predat novac Voyager-u:</th><th class="text-center" >Primljen novac od Voyager-a</th></tr>
        <?php  foreach($dana_izmedju as $dan)
        {

            $tmp77 = explode(".", $dan);
            $dan1 = $tmp77[2] . "-" . $tmp77[1] . "-" . $tmp77[0];

            $stmt = $uplata->read_all_uplate_na_dan($dan1, $iid);
            $suma_uplata = 0;
            while ($row_uplata = $stmt->fetch(PDO::FETCH_ASSOC))
            {
              //  echo $row_uplata['iznos'];
                $suma_uplata = $suma_uplata + $row_uplata['iznos'];
            }

            $stmt = $tok_novca->read_one_day_one_profesor($dan1, $iid);
            $row_category_dnevni_izvestaj = $stmt->fetch(PDO::FETCH_ASSOC);

            if($suma_uplata ?? 0 > 0 || $row_category_dnevni_izvestaj['iznos_predao'] > 0 || $row_category_dnevni_izvestaj['iznos_primio'] > 0 )
            { ?>
                <tr><td align="center" > <!-- <a href='dnevni_izvestaj.php?admin=<?php /* echo $dan1."/".$iid; */ ?>'   > --> <?php  echo  $dan; ?></a> </td><td align="center" ><?php echo $suma_uplata; ?></td>
                    <td class="text-center">
                        <?php echo $row_category_dnevni_izvestaj['iznos_predao'] ?? ""; ?>
                    </td><td class="text-center">
                        <?php
                        if(isset($row_category_dnevni_izvestaj['iznos_primio']) && $row_category_dnevni_izvestaj['iznos_primio'] > 0 ){
                            echo $row_category_dnevni_izvestaj['iznos_primio'];
                            if ($row_category_dnevni_izvestaj['primio_racun'] == 1) {
                                echo "<tab style='color:red'> &nbsp; Na račun</tab>";
                            }else{ echo "<tab style='color:red'> &nbsp; Cash</tab>";}

                        }
                        if(isset($row_category_dnevni_izvestaj['iznos_primio2']) && $row_category_dnevni_izvestaj['iznos_primio2'] > 0 ){
                            echo "<br/>",$row_category_dnevni_izvestaj['iznos_primio2'];
                            if ($row_category_dnevni_izvestaj['primio_racun2'] == 1) {
                                echo "<tab style='color:red'> &nbsp; Na račun</tab>";
                            }else{ echo "<tab style='color:red'> &nbsp; Cash</tab>";}

                        }


                        ?>
                    </td></tr>
                <?php
            }
        } ?>
    </table>
<?php




}
include_once "layout_foot.php";
    ?>
</body>
</html>
