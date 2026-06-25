<html>
<head>
    <title> Kartica profesor </title>
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
$iid = $_SESSION['user_id']; //s echo "profesor je br: ", $iid;


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
include_once 'objects/nivo_znanja.php';
include_once 'objects/jezik.php';


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
$jezik = new jezik($db);
$nivo_znanja = new nivo_znanja($db);
// initialize objects
//$user = new User($db);
$stmt = $profesor->read_jedan_profesor($iid,"users");
$row_category_prof = $stmt->fetch(PDO::FETCH_ASSOC);
extract($row_category_prof);

echo "<div class='col-md-12' style='width:100%; margin-left: 0%;'>";



?>
   <table class='table table-bordered'>
        <tr>
		    <td ><h4>Profesor:
                <?php
                echo $firstname,"&nbsp;", $lastname;
                ?>
			</h4>
            </td>
        </tr>
    </table>

</div>
<form action='kartica_profesora.php?id=<?php echo $iid; ?>' method='post' >
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
	$datum_od="01.01.1970";
	$datum_do="31.12.2044";	
		if(isset($_POST['datum_od']) and $_POST['datum_od']!="" ){$datum_od=$_POST['datum_od'];}
		if(isset($_POST['datum_do']) and $_POST['datum_do']!="" ){$datum_do=$_POST['datum_do'];}
						
//	echo "Datum od:",$datum_od,"<br/> Datum do: ",$datum_do;
	$tmp77 = explode (".", $datum_od);
    $datum_od = $tmp77[2] . "-" . $tmp77[1] . "-" . $tmp77[0];
	$tmp777 = explode (".", $datum_do);
    $datum_do = $tmp777[2] . "-" . $tmp777[1] . "-" . $tmp777[0];
//	echo "Datum od:",$datum_od,"<br/> Datum do: ",$datum_do;
?>

<div class='col-md-12' >
    <table class="table table-bordered text-center" style='width:100%; margin-left: 0%;' >
        <tr bgcolor="#E6E6E6" style="text-align:center;">
			<th style="text-align:center;">Red. br.</th>
            <th style="text-align:center;">Ime i prezime</th>
            <th style="text-align:center;">grupa je</th>
            <th style="text-align:center;">U grupi je od</th>
            <th style="text-align:center;">Sve uplate</th>
			<th style="text-align:center;">Sva zaduženja</th>
            <th style="text-align:center;">Saldo djaka</th>


        </tr>
        <?php
        $ssmmjh = $grupa->read_all_grups_profesor($iid,"grupe");
        $ukupna_suma = 0;
		$ukupna_uplata = 0;
		$ukupno_zaduzenje = 0;
		$i=1;
        while ($row_category_grupe_profesori = $ssmmjh->fetch(PDO::FETCH_ASSOC)) {

            $sts = $povezivanje->read_all_students($row_category_grupe_profesori['id']);

            while ($row_category_djaci = $sts->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                $indeks = $row_category_djaci['fk_djak'];
                $ssts = $djak->read_one_djak("djaci", $indeks);
                //echo $row_category_djaci['fk_djak'],  "<br/>";
                $row_category_detalj_djaci = $ssts->fetch(PDO::FETCH_ASSOC);
                if ($row_category_detalj_djaci['status'] == 1)
                {
					echo"<td>",$i,".</td>";
					$i++;
                    echo "<td><a href='kartica_djak.php?id={$row_category_detalj_djaci['id']} '>", $row_category_detalj_djaci['firstname'], "&nbsp;", $row_category_detalj_djaci['lastname'], "</a></td>";
                    $ststmt = $grupa->read_one_grupa($row_category_grupe_profesori['id'],"grupe");
                    $row_category_sve_ogrupi = $ststmt->fetch(PDO::FETCH_ASSOC);
                    extract($row_category_sve_ogrupi);
                    $stm_jezik = $jezik->read_one_jezik($fk_jezik,'jezik');
                    $row_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC);
                    
                    $stm_nivo = $nivo_znanja->read_one_nivo($nivo,'nivo_znanja');
                    $row_nivo_znanja = $stm_nivo->fetch(PDO::FETCH_ASSOC);
                    $fk_jezik = $row_jezik['alias'];
                    $nivo = $row_nivo_znanja['ime'];
                    


                    echo "<td>",$fk_jezik,"&nbsp;",$nivo,"&nbsp;",$alias,"</td>";
                    $sskz = $povezivanje->read_one_student_one_grup($row_category_grupe_profesori['id'], $indeks);
                    $row_category_otkadjeugrupi = $sskz->fetch(PDO::FETCH_ASSOC);
                    $row_category_otkadjeugrupi['created'] = vreme_u_nase_vreme($row_category_otkadjeugrupi['created']);
                 //   echo "<td>", $row_category_otkadjeugrupi['fk_grupa'], "</td>";
                    echo "<td>", $row_category_otkadjeugrupi['created'], "</td>";
                    
                    // zbrajamo sve uplate djaka
                    $uplate_djak = 0;
                    $ddjkm = $uplata->read_all_for_student_interval($indeks, $row_category_grupe_profesori['id'],$datum_od, $datum_do);
                    while ($row_category_medjusuma_uplate = $ddjkm->fetch(PDO::FETCH_ASSOC)) {
                        $uplate_djak = $uplate_djak + $row_category_medjusuma_uplate['iznos'];
                    }
                    echo "<td>", $uplate_djak, "</td>";
					$ukupna_uplata = $ukupna_uplata + $uplate_djak;
					//Zbrajamo sva aktivna zaduzenja za djaka
                    $zaduzenje_djak = 0;
                    $drmk = $zaduzenje->read_all_iznos_for_student_interval($indeks, $row_category_grupe_profesori['id'],$datum_od, $datum_do);
                    while ($row_category_medjusuma = $drmk->fetch(PDO::FETCH_ASSOC)) {
                        $zaduzenje_djak = $zaduzenje_djak + $row_category_medjusuma['iznos'];
                    }
                    $zaduzenje_djak1= number_format($zaduzenje_djak, 2, '.', '');
                    echo "<td>", $zaduzenje_djak1, "</td>";
					$ukupno_zaduzenje = $ukupno_zaduzenje + $zaduzenje_djak;
                    // saldo djaka
                    $saldo_djak = $uplate_djak - $zaduzenje_djak;
                    $saldo_djak1= number_format($saldo_djak, 2, '.', '');

                    ?>
                    <td <?php if ($saldo_djak < 0) {
                        echo "style='color:red;'";
                    } ?>><?php echo $saldo_djak1; ?></td>
                    <?php
                    $ukupna_suma = $ukupna_suma + $saldo_djak;
                }
                echo "</tr>";
            }
        }
        $ukupna_uplata1= number_format($ukupna_uplata, 2, '.', ' ');
        $ukupno_zaduzenje1= number_format($ukupno_zaduzenje, 2, '.', ' ');
        $ukupna_suma1= number_format($ukupna_suma, 2, '.',  ' ');
        ?>
        <tr><td colspan="4" align="right"><b> Ukupan saldo Profesora: </b></td><td><?php echo$ukupna_uplata1;  ?></td><td><?php echo $ukupno_zaduzenje1; ?></td><td <?php if($ukupna_suma < 0 ){echo"style='color:red;'";} ?>  ><?php echo $ukupna_suma1 ?></td></tr>
    </table>



</div>



<?php
	}
// include page footer HTML
include_once "layout_foot.php";
?>
</body>
</html>
