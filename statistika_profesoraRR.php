<html>
<head>
    <title> Tok novca profesor </title>
    <link rel="shortcut icon" href="../images/kalen.png">
    <link rel="stylesheet" type="text/css" href="../tigrakal/tcal.css" />
    <script type="text/javascript" src="../tigrakal/tcal.js"></script>
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
if(isset($_GET['id'])){$iid = $_GET['id']; // echo "profesor je br: ", $iid;
}

// core configuration
include_once "/config/core.php";

// set page title
$page_title = "<tab style='color:red;'>Moje finasije :</tab>";

// include login checker
include_once "login_checker.php";

// include classes
include_once '/config/database.php';
include_once '/config/funkcije.php';
include_once '/objects/user.php';
include_once '/objects/grupa.php';
include_once '/objects/djak.php';
include_once "/libs/php/utils.php";
include_once '/objects/povezivanje.php';
include_once '/objects/uplata.php';
include_once '/objects/zaduzenje.php';
include_once '/objects/kalendar.php';
include_once '/objects/tok_novca.php';


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
$termin = new kalendar($db);
$tok_novca = new tok_novca($db);
// initialize objects
//$user = new User($db);
$stmt = $profesor->read_jedan_profesor($iid,"users");
$row_category_prof = $stmt->fetch(PDO::FETCH_ASSOC);
extract($row_category_prof);
//echo"<script> window.location=window.location; </script>";





?>
<div class='col-md-12' style='width:90%; margin-left: 5%;'>
    <table class='table table-bordered'>
        <tr>
            <td ><h4>Profesor: <?php echo $firstname,"&nbsp;", $lastname; ?> </h4> </td>
        </tr>
    </table>
</div>
<div  class='col-md-12'>
    <?php
    $pocetak = "2019-11";
    $sada = date('Y-m'); // echo "<br/> Sad:",$sada,"<br/>";
    $pocetak = date("Ym", strtotime($pocetak));
   //  echo $pocetak;


    ?>

</div>


<form action='statistika_profesora.php?id=<?php echo $iid; ?>' method='post' >
    <table align="center" style='border:none'>
        <tr>
            <td>Odaberi vremsnki interval za pregled: </td>
            <td style="padding-left:10px ; padding-right:10px; border:none">
                <b>	od:</b> </td><td>
                <select class = "selectpicker form-control" name="od"  id="od" >
                    <?php
                    while($pocetak <= date("Ym", strtotime($sada)))
                    {
                        $mesec = substr($pocetak,-2,2);
                        $godina = substr($pocetak,0,4);
                        $operativni_datum = $godina."-".$mesec;
                        $date_za_prikaz = DateTime::createFromFormat('Y-m', $operativni_datum);
                        // echo $pocetak," *--* ", $operativni_datum,' ------ Za prikaz:',$date_za_prikaz->format('F.Y'),"<br/>";
                        ?>
                       <option value=" <?php echo $operativni_datum;  ?>" ><?php echo $date_za_prikaz->format('F.Y'); ?></option>";
                        <?php
                        if(substr($pocetak, 4, 2) == "12")
                                $pocetak = (date("Y", strtotime($pocetak."01")) + 1)."01";
                            else
                                $pocetak++;
                    }
                    ?>
                </select>
                <script type="text/javascript">
                    document.getElementById('od').value = "<?php echo $_POST['od'];?>";
                </script>
                <?php $pocetak = "2019-11";  $pocetak = date("Ym", strtotime($pocetak));?>
            </td><td style="padding-left:10px ; padding-right:10px; border:none " >
                <b>	do: </b> </td><td>
                <select class = "selectpicker form-control" name="do" id="do"   >
                    <?php
                    while($pocetak <= date("Ym", strtotime($sada)))
                    {
                        $mesec = substr($pocetak,-2,2);
                        $godina = substr($pocetak,0,4);
                        $operativni_datum = $godina."-".$mesec;
                        $date_za_prikaz = DateTime::createFromFormat('Y-m', $operativni_datum);
                        // echo $pocetak," *--* ", $operativni_datum,' ------ Za prikaz:',$date_za_prikaz->format('F.Y'),"<br/>";
                        ?>
                        <option value=" <?php echo $operativni_datum;  ?>" ><?php echo $date_za_prikaz->format('F.Y'); ?></option>";
                        <?php
                        if(substr($pocetak, -2, 2) == "12")
                            $pocetak = (date("Y", strtotime($pocetak."01")) + 1)."01";
                        else
                            $pocetak++;
                    }
                    ?>
                </select>
                <script type="text/javascript">
                    document.getElementById('do').value = "<?php echo $_POST['do'];?>";
                </script>
            </td>
            <td style="border:0; "><input type='submit' style="margin-left : 20px;" value='PRIKAŽI' name='btnSub' class='btn btn-primary' ></td>
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
  //  $datum_od = date('d.m.Y', strtotime('-3 months'));

    $do =  date("M.Y");
    if (isset($_POST['od']) and $_POST['od'] != "") {
        $od = $_POST['od'];
    }
    if (isset($_POST['do']) and $_POST['do'] != "") {
        $do = $_POST['do'];
    }



    $i = date("Ym", strtotime($od));
 /*   while($i <= date("Ym", strtotime($do))){
        echo $i,"<br/>";
        if(substr($i, 4, 2) == "12")
            $i = (date("Y", strtotime($i."01")) + 1)."01";
        else
            $i++;
    } */

?>
<div class='col-md-6' >
<?php
    $termin_procenat = $profesor->read_all_promene_procenat_jedan_profesor($iid,'vremenski_intervali_procenat_plata');

    $pomocna = '0000-00-00';
    $pomocna2 = '2080-12-31';
   /*
    $len = $termin_procenat->fetchColumn();
  //  var_dump($termin_procenat);
    $brojac = 0;
    var_dump($len);
    echo "ima clanova: ", $len,"<br/>";
    foreach( $termin_procenat as $valvue){
        if($brojac == $len){
            echo "8";
        }else{
            echo "procenat je: ", $valvue['procenat_za_platu'],"&nbsp; od datuma: ",$pomocna, " &nbsp; Do datuma ",  $valvue['generete_date'],"<br/>" ;
        }
        $pomocna = $valvue['generete_date'];
        $brojac++;
        echo "brojac ",$brojac;
    }
    echo  "procenat je: ", $valvue['procenat_za_platu'],"&nbsp; od datuma: ",$valvue['generete_date'], " &nbsp; Do datuma ",  $pomocna2,"<br/>" ;
    echo "<br/>";
    echo "<br/>----------------<br/>";
   */
  /*  $count = $termin_procenat->fetchColumn();
    echo "Ima ih: ",$count,"<br/>"; */
	?>
	 <table class="table table-bordered" style=" width: 80%; margin-left:10%; margin-top : 15px;">
	<?php 
     foreach( $termin_procenat as $valvue){
		 echo"<tr><td>";

        echo "Procenat je: </td><td> ", $valvue['procenat_za_platu'],"</td><td> Od datuma:</td><td> ",$pomocna, " </td><td> Do datuma: </td><td>",  $valvue['generete_date'],"</td></tr>" ;
        $pomocna = $valvue['generete_date'];
    }
    echo  "<tr><td>Procenat je: </td><td> ", $valvue['procenat_za_platu'],"</td><td> Od datuma: </td><td> ",$valvue['generete_date'], " </td><td> Do datuma: </td><td>",  $pomocna2,"</td></tr>" ;
  
	echo"</table>";
echo"</div>";
echo"<div class='col-md-6' >";
    $termin_obracun = $profesor->read_all_promene_nacin_obracuna_jedan_profesor($iid,'vremenski_intervali_vrsta_obracuna');
    //$ssss=0;
	?>
	 <table class="table table-bordered" style=" width: 100%; margin-left:1%; margin-top : 15px;">
	<?php
    $pomocna = '0000-00-00';
    foreach($termin_obracun as $obracun){
        echo "<tr><td> Način obračuna: </td><td> ";
        if($obracun['nacin_obracuna']==0){echo"Po naplati";}else{echo "Po zaduženju";}
        echo "</td><td> Od datuma: </td><td>",$pomocna,"</td><td> Do datuma: </td><td>" , $obracun['generete_date'],"</td></tr>" ;
        $pomocna = $obracun['generete_date'];
    }
    echo "<tr><td> Način obracuna: </td><td>";
    if($obracun['nacin_obracuna']==0){echo"Po naplati";}else{echo "Po zaduženju";}
    echo "</td><td> Od datuma: </td><td>",$obracun['generete_date'],"</td><td> Do datuma: </td><td>" , $pomocna2,"</td></tr>" ;
   echo"</table>";

echo"</div>";
    ?>
    <table class="table table-bordered" style=" width: 80%; margin-left:10%; margin-top : 30px;">
        <tr><th class="text-center">Mesec:</th><th class="text-center">Broj grupa:</th><th class="text-center">Broj djaka:</th><th class="text-center">Održanih sati nastave</th><th class="text-center">Skup uplata:</th><th class="text-center">Skup zaduženja:</th><th class="text-center" >Plata profesora: </th><th>Dosad isplaćeno:</th><th>Mes. Saldo:</th><th>Kumulativ. saldo:</th></tr>
        <?php
        $ukupan_saldo = 0;
        $kumulaticni_saldo = 0;
        while($i <= date("Ym", strtotime($do)))
        {
            $mesec = substr($i,-2,2);
            $godina = substr($i,0,4);
            $operativni_datum = $godina."-".$mesec;
           // echo $operativni_datum;
            $stttam = $termin->read_all_termin_month_profesor($operativni_datum,$iid);
            ?>
            <tr><td class="text-center" ><a href='mesecna_statistika_profesor.php?admin=<?php echo$operativni_datum."/".$iid; ?>'   ><?php  echo $mesec,".",$godina; ?></a></td>
                     <?php
                     $sati=0;
                     $minuti=0;
                     // brojimo casove za svaki mesec
                     $sve_grupe_profesor= array();
                     $broj_djaka_profesor = 0;
                     while ($row_broj_casova = $stttam->fetch(PDO::FETCH_ASSOC))
                     {
                         // punimo niz sa grupama
                         if(!in_array($row_broj_casova['fk_grupa'], $sve_grupe_profesor))
                         {
                             $sve_grupe_profesor[]=$row_broj_casova['fk_grupa'];
                           //  echo $operativni_datum,"*", $row_broj_casova['fk_grupa'],"/ <br/>";
                            // if($operativni_datum = "2020-1"){var_dump($sve_grupe_profesor);}
                             $broj_djaka_grupe = $povezivanje->count_student($row_broj_casova['fk_grupa']);
                             $broj_djaka_profesor = $broj_djaka_profesor + $broj_djaka_grupe;

                         }
                         $datetime1 = new DateTime($row_broj_casova['start']);
                         $datetime2 = new DateTime($row_broj_casova['end']);
                         $interval = $datetime1->diff($datetime2);
                         $sati = $sati + $interval->format('%h');
                         $minuti = $minuti + $interval->format('%i');
                      //   echo $interval->format('%h')." Hours ".$interval->format('%i')." Minuta","|-->";
                      //  echo $row_broj_casova['start'],$row_broj_casova['end'],"<br/>";
                     }
                  //   if($operativni_datum = "2020-1"){var_dump($sve_grupe_profesor);}
                     $sati = $sati + $minuti/60; ?>
                <td class="text-center" ><?php echo  /* var_dump($sve_grupe_profesor), */ count( $sve_grupe_profesor); ?></td>
                <td class="text-center" ><?php echo $broj_djaka_profesor; ?></td>
                <td class="text-center" ><?php echo $sati; ?></td>

                <td class="text-center">
                    <?php
                    // Sve uplate za grupe koje drzi profesor sabiramo
                    $srraff = $uplata->read_all_uplate_month_profesor($operativni_datum,$iid);
                    $ukupno_uplate = 0;
                    $platau = 0;
                    while ($row_suma_uplata = $srraff->fetch(PDO::FETCH_ASSOC))
                    {
                      //  $platau = 0;
                        // proveravamo jeli djak aktivan u grupi i ako niej ne brojimo ga
                        $jeli_u_grupi=$povezivanje->count_one_student_one_grup($row_suma_uplata['fk_grupa'], $row_suma_uplata['fk_djak']);
                        if($jeli_u_grupi >= 0 )
                        {
                            If($row_suma_uplata['iznos'] > 0 /* && in_array($row_suma_uplata['fk_grupa'], $sve_grupe_profesor) */ )
                            {
                                                           $ukupno_uplate = $ukupno_uplate + $row_suma_uplata['iznos'];
                                $swajja = $grupa->read_one_grupa($row_suma_uplata['fk_grupa'],"grupe");
                                $row_info_grupa = $swajja->fetch(PDO::FETCH_ASSOC);                 // koliko je zapisano za grupu da je procenat za platu
                                if(  $row_info_grupa['procenat_za_platu'] == 0 )              // ako nije definisano za samu grupu onda se povlaci od profesora
                                {
                              //     echo "Uplata je od: ",$row_suma_uplata['datum_generisanja'],"<br/> Iznos uplate: ",$row_suma_uplata['iznos'],"<br/> Za djaka:", $row_suma_uplata['fk_djak'];
                                    $jsjsjsj1 = $profesor->read_jedan_profesor($iid,"users");
                                    $row_info_profesor = $jsjsjsj1->fetch(PDO::FETCH_ASSOC);
                                    $procenat_za_racunanje_plate = $row_info_profesor['procentat_za_platu'];
                                //    $jsjsjsj = $profesor->read_jedan_profesor($iid,"users");        // radi se o profesoru $iid
                                //    $row_info_profesor = $jsjsjsj->fetch(PDO::FETCH_ASSOC);
                                   $procenat_za_racunanje_plate = $row_info_profesor['procentat_za_platu'];
                                    $pomocna = "0000-00-00";
                                    $termin_procenat = $profesor->read_all_promene_procenat_jedan_profesor($iid,'vremenski_intervali_procenat_plata');
                                    foreach( $termin_procenat as $valvue)
                                    {
                                     //   echo "procenat je: ", $valvue['procenat_za_platu'],"&nbsp; od datuma: ",$pomocna, " &nbsp; Do datuma",  $valvue['generete_date'],"<br/>" ;
                                        //echo
                                    //    echo "88",$procenat_za_racunanje_plate,"<br/>";
                                        if( ( strtotime($pomocna) <= strtotime($row_suma_uplata['datum_generisanja']))  && (strtotime($row_suma_uplata['datum_generisanja'])  < strtotime($valvue['generete_date']))  )
                                        {
                                            $procenat_za_racunanje_plate = $valvue['procenat_za_platu']; //echo "<b>88</b>",$valvue['procenat_za_platu'],"<br/>";
                                        }
                                        $pomocna = $valvue['generete_date'];
                                    }
                                    if( strtotime($row_suma_uplata['datum_generisanja'])  >= strtotime($valvue['generete_date'])  )
                                        {
                                            $procenat_za_racunanje_plate = $valvue['procenat_za_platu']; //echo "<b>88</b>",$valvue['procenat_za_platu'],"<br/>";
                                        }
                                          // echo "<br/><b>Procen prof</b>  ", $procenat_za_racunanje_plate," % <br/>";
                                }else
                                {
                                    $procenat_za_racunanje_plate = $row_info_grupa['procenat_za_platu'];
                                  //  echo "<br/><b>Procen Grup</b>  ", $procenat_za_racunanje_plate," % <br/>";
                                }
                                $termin_obracun = $profesor->read_all_promene_nacin_obracuna_jedan_profesor($iid,'vremenski_intervali_vrsta_obracuna');
                                //$ssss=0;
                                $pomocna = '0000-00-00';
                                foreach($termin_obracun as $obracun){
                                //    echo "NAcin obracuna je: ", $obracun['nacin_obracuna'],"&nbsp; od datuma: ",$pomocna,"&nbsp; Do datuma: " , $obracun['generete_date'],"<br/>" ;
                                    if( ( $pomocna <= $row_suma_uplata['datum_generisanja']) && ($row_suma_uplata['datum_generisanja'] < $obracun['generete_date']) &&   $obracun['nacin_obracuna'] == 0 ){
                                        $platau = $platau + $row_suma_uplata['iznos'] * $procenat_za_racunanje_plate/100;
                                   //     echo "<br/><b style='color:green;'> Dod za platu 364:",$row_suma_uplata['iznos']* $procenat_za_racunanje_plate/100,"</b><br/>";
                                    //    echo"<p style='font-size:160%'>U-plata:", $platau,"</p>";
                                       // echo "NAcin obracuna je: ", $obracun['nacin_obracuna'],"&nbsp; od datuma: ",$pomocna,"&nbsp; Do datuma: " , $obracun['generete_date'],"<br/>" ;
                                     //   echo "<br/><b style='color:green;'> Način obr. : po uplatama (",$obracun['nacin_obracuna'],") <br/> Za uplatu profesoru je: " ,  $row_suma_uplata['iznos']* $procenat_za_racunanje_plate/100,"</b><br/>";
                                    //    echo "<br/><b>Po procenatu: </b>  ", $procenat_za_racunanje_plate," % <br/>";
                                    }
                                    $pomocna = $obracun['generete_date'];
                                }
                                if( ($row_suma_uplata['datum_generisanja'] >= $obracun['generete_date']) &&  $obracun['nacin_obracuna'] == 0 ){
                                    $platau = $platau + $row_suma_uplata['iznos'] * $procenat_za_racunanje_plate/100;
                                //    echo "<br/><b style='color:indigo;'> Dodato za platu je 373: ",$row_suma_uplata['iznos']* $procenat_za_racunanje_plate/100,"</b><br/>";
                                //    echo"<p style='font-size:160%'>U-plata 375:", $platau,"</p>";
                                    //echo "NAcin obracuna je: ", $obracun['nacin_obracuna'],"&nbsp; od datuma: ", $obracun['generete_date'],"<br/>" ;
                                 //   echo "<br/><b style='color:green;'> Nacin obr.: po uplatama1 <br/> Za uplatu profesoru je:" ,  $row_suma_uplata['iznos']* $procenat_za_racunanje_plate/100,"</b><br/>";
                                //    echo "<br/><b>Po procentu1 </b>  ", $procenat_za_racunanje_plate," % <br/>";
                                }
                                //    echo "NAcin obracuna je: ", $obracun['nacin_obracuna'],"&nbsp; od datuma: ",$obracun['generete_date'],"&nbsp; Do datuma: " , $pomocna2,"<br/>" ;
                           //     echo"<br/>";
                            }
                        //    echo"<br/>------------------------------<br/>";
                        }
                 //    echo"-----------<br/>";
                    }
                //    echo "Tot: ";
                    echo round($ukupno_uplate, 2);
                    ?>
                </td>
                 <td class="text-center">
                    <?php
                    // Sva zaduzenja za grupe koje drzi profesor sabiramo
                    $srraff = $zaduzenje->read_all_zaduzenja_month_profesor1($operativni_datum,$iid);
                    $ukupno_zasuzenje = 0;
                    $plata = 0;
                    while ($row_suma_zaduzenja = $srraff->fetch(PDO::FETCH_ASSOC))
                    {
                        // proveravamo jeli djak aktivan u grupi i ako nije ipak ga brojimo jer moze biti i naknadnih uplata
                       $jeli_u_grupi=$povezivanje->count_one_student_one_grup($row_suma_zaduzenja['fk_grupa'], $row_suma_zaduzenja['fk_djak']);
                        if($jeli_u_grupi >= 0 )
                        {
                            If($row_suma_zaduzenja['iznos'] > 0 /* && in_array($row_suma_zaduzenja['fk_grupa'], $sve_grupe_profesor) */ )
                            {
                             //   echo $row_suma_zaduzenja['iznos']," --> ",$row_suma_zaduzenja['fk_grupa'],"<br/>";
                                $ukupno_zasuzenje = $ukupno_zasuzenje + $row_suma_zaduzenja['iznos'];
                            //    echo "Suma: ",$row_suma_zaduzenja['iznos'],"<br/> Od datuma: ", $row_suma_zaduzenja['od_datuma'],"<br/> Do datuma: ",$row_suma_zaduzenja['do_datuma'],"<br/>";
                                $swajja = $grupa->read_one_grupa($row_suma_zaduzenja['fk_grupa'],"grupe");
                                $row_info_grupa = $swajja->fetch(PDO::FETCH_ASSOC);
                                if($row_info_grupa['procenat_za_platu'] == 0)
                                {
                                    $termin_procenat = $profesor->read_all_promene_procenat_jedan_profesor($iid,'vremenski_intervali_procenat_plata');
                                    $pomocna = '0000-00-00';
                                    $pomocna2 = '2080-12-31';
                                    foreach( $termin_procenat as $valvue)
                                    {
                                        //  echo "procenat je: ", $valvue['procenat_za_platu'],"&nbsp; od datuma: ",$pomocna, " &nbsp; Do datuma",  $valvue['generete_date'],"<br/>" ;
                                        //echo
                                        //   echo "88",$procenat_za_racunanje_plate,"<br/>";
                                        if( strtotime($pomocna) <= strtotime($row_suma_zaduzenja['od_datuma'])  && strtotime($row_suma_zaduzenja['od_datuma'])  < strtotime($valvue['generete_date'])  )
                                        {
                                            $procenat_za_racunanje_plate = $valvue['procenat_za_platu']; // echo "<br/><b>%</b>",$valvue['procenat_za_platu'],"<br/>";
                                        }
                                        $pomocna = $valvue['generete_date'];
                                    }
                                    if( strtotime($row_suma_zaduzenja['od_datuma'])  >= strtotime($valvue['generete_date'])  )
                                    {
                                        $procenat_za_racunanje_plate = $valvue['procenat_za_platu']; // echo "<b>99</b>",$valvue['procenat_za_platu'],"<br/>";
                                    }

                                    $jsjsjsj = $profesor->read_jedan_profesor($iid,"users");
                                    $row_info_profesor = $jsjsjsj->fetch(PDO::FETCH_ASSOC);
                                 //   $procenat_za_racunanje_plate = $row_info_profesor['procenat_za_platu'];

                                }else
                                    {
                                        $procenat_za_racunanje_plate = $row_info_grupa['procenat_za_platu'];
                                    }

                                $termin_obracun = $profesor->read_all_promene_nacin_obracuna_jedan_profesor($iid,'vremenski_intervali_vrsta_obracuna');
                                //$ssss=0;
                                $pomocna = '0000-00-00';
                                foreach($termin_obracun as $obracun){
                                    //   echo "NAcin obracuna je: ", $obracun['nacin_obracuna'],"&nbsp; od datuma: ",$pomocna,"&nbsp; Do datuma: " , $obracun['generete_date'],"<br/>" ;
                                    if( $pomocna <= $row_suma_zaduzenja['od_datuma'] && $row_suma_zaduzenja['od_datuma'] < $obracun['generete_date'] &&  $obracun['nacin_obracuna'] == 1 )
                                    {
                                        //   $procenat_za_racunanje_plate = $obracun['procenat_za_platu'];
                                        //     echo "Po proccc: ",  $procenat_za_racunanje_plate," % <br/>";
                                        //     echo "Za zaduzenja za period <br/> Od: ", $row_suma_zaduzenja['od_datuma'],"&nbsp, do: ",$row_suma_zaduzenja['do_datuma'],"<br/>";
                                        $platau = $platau + $row_suma_zaduzenja['iznos'] * $procenat_za_racunanje_plate/100;
                                        //     echo"<p style='font-size:160%'>U-plata 406:", $platau,"</p>";
                                        //    echo "<br/><p style='color:red' >Dodat iznos 407: ", $row_suma_zaduzenja['iznos']* $procenat_za_racunanje_plate/100,"</p><br/>";
                                        //      echo"<p style='font-size:160%'>U-plata 452:", $platau,"</p>";
                                    }
                                    $pomocna = $obracun['generete_date'];
                                }
                                if(  $row_suma_zaduzenja['od_datuma'] >= $obracun['generete_date'] &&  $obracun['nacin_obracuna'] == 1 ){
                                    //          $procenat_za_racunanje_plate = $obracun['procenat_za_platu'];
                                    //             echo "Proccc:",  $procenat_za_racunanje_plate,"<br/>";
                                    //             echo "Za zaduzenja za period <br/> Od: ", $row_suma_zaduzenja['od_datuma'],"&nbsp, do: ",$row_suma_zaduzenja['do_datuma'],"<br/> NNacinn obracuna je: ", $obracun['nacin_obracuna'],"<br/>";
                                    //       echo "Po proccc: ",  $procenat_za_racunanje_plate," % <br/>";
                                    $platau = $platau + $row_suma_zaduzenja['iznos'] * $procenat_za_racunanje_plate/100;
                                    //       echo"<p style='font-size:160%'>U-plata 417:", $platau,"</p>";
                                    //       echo "<br/><p style='color:red' >Dodat iznos 418: ", $row_suma_zaduzenja['iznos']* $procenat_za_racunanje_plate/100,"</p><br/> Od datuma: ",$row_suma_zaduzenja['od_datuma'],"<br/>";
                                    //  echo "<p style='font-size:160%'> 463 ",$platau,"</p>";
                                    //       echo "<b>Nacin obracuna je: ";
                                    //       If($obracun['nacin_obracuna']==0){echo "po naplati";}else{ echo "po zaduzenju";}
                                    //       echo"</b>";
                                }
                                //   echo "<br/> Interval: ",$obracun['generete_date']," - " , $pomocna2,"<br/>" ;
                                //  echo"<br/>";
                                //  $platau = $platau + $row_suma_zaduzenja['iznos'] * $procenat_za_racunanje_plate/100;
                            }

                        }
                   //     echo "<p style='color:red;'>-----------------</p>";
                    //    echo"<br/>------------------------------<br/>";
                    }
                //    echo "<p style='color:red;'>SUM:</p>";
                       echo round($ukupno_zasuzenje, 2);
                    ?>
                 </td><td class="text-center" >
                    <?php

                      echo round($platau, 2);
                      //$platau=0;

                    ?>
                </td>
                <td class="text-center" >
                    <?php
                    $sidjip = $tok_novca->read_all_prijem_novca_profesor_month($iid,$operativni_datum);
                    $mesecni_primljeni_novac = 0;
                    while ($row_suma_primljenog_novca_mesec = $sidjip->fetch(PDO::FETCH_ASSOC))
                    {
                        $mesecni_primljeni_novac = $mesecni_primljeni_novac
                            + $row_suma_primljenog_novca_mesec['iznos_primio'] + $row_suma_primljenog_novca_mesec['iznos_primio2'];
                    }
                    echo round($mesecni_primljeni_novac, 2);
                    ?>
                </td>
                <td class="text-center" >
                    <?php
                        $saldo = $mesecni_primljeni_novac - $platau;
                 //   $saldo = $mesecni_primljeni_novac - $plata;
                    $ukupan_saldo= $ukupan_saldo + $saldo;
                    if($saldo <= 0){echo "<tab style='color:red;'>";}
                    echo round($saldo, 2);
                    if($saldo <= 0){echo "</tab >";}
                    ?>
                </td>
                <td class="text-center">
                    <b>
                        <?php
                        $kumulaticni_saldo = $kumulaticni_saldo + $saldo;
                        if($kumulaticni_saldo <= 0){echo "<tab style='color:red;'>";}
                        echo round($kumulaticni_saldo, 2);
                        if($kumulaticni_saldo <= 0){echo "</tab >";}
                        ?>
                    </b>
                </td>
            </tr>
            <?php    if(substr($i, 4, 2) == "12")
                     $i = (date("Y", strtotime($i."01")) + 1)."01";
                    else
                    $i++;
        }

        ?>
        <tr><td colspan="8" class="text-right"><b>Ukupan saldo profesora za navedeni period:</b> </td>
        <td class="text-center" ><b><?php
          //  echo $ukupan_saldo;
            if($ukupan_saldo <= 0){echo "<tab style='color:red;'>";}
            echo round($ukupan_saldo, 2);
            if($ukupan_saldo <= 0){echo "</tab >";}
            ?>
            </b>
        </td>

    </tr>
    </table>
    <?php
}
include_once "layout_foot.php";
?>
</body>
</html>
