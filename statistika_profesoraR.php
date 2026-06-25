<html>
<head>
    <title> Pregled plata profesora </title>
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
if(isset($_GET['id'])){$iid = $_GET['id']; // echo "profesor je br: ", $iid;
}

// core configuration
include_once "config/core.php";
$iid = $_SESSION['user_id'];
// set page title
$page_title = "<tab style='color:red;'>Finasije profesora :</tab>";

// include login checker
include_once "login_checker.php";

// include classes
include_once 'config/database.php';
include_once 'config/autoload.php';

include_once 'config/funkcije.php';



// include page header HTML
include_once "layout_head2.php";
// get database connection
$database = new Database();
$db = $database->getConnection();
$profesor = new user($db);
$grupa = new grupa($db);
$povezivanje = new povezivanje($db);
$djak = new djak($db);
$zaduzenje = new zaduzenje($db);
$uplata = new uplata($db);
$termin = new kalendar($db);
// $tok_novca = new tok_novca($db);
// $isplate_profesoru = new isplate_profesoru($db);
// $isplata = new isplata($db);
// $nivo_znanja = new nivo_znanja($db);
// $duzina_casa = new duzina_casa($db);
// initialize objects
$uzrast = new uzrast($db);
$stmt = $profesor->read_jedan_profesor($iid,"users");
$row_category_prof = $stmt->fetch(PDO::FETCH_ASSOC);

extract($row_category_prof);
//echo"<script> window.location=window.location; </script>";





?>
<div class="col-md-12" id='glavni' style='width: 94%; margin-left:3%; ' >
<div class='col-md-12' style='width:100%; '>
    <table class='table table-bordered'>
        <tr>
            <td ><h4>Obračun plate za profesora: <?php echo $firstname,"&nbsp;", $lastname; ?> </h4> </td>
        </tr>
    </table>
</div>
<div  class='col-md-12'>
    <?php
 //   $pocetak = "2019-11";
    $pocetak =  substr($created,0,7);
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
                <?php 
                    $pocetak1 =  substr($created,0,7);  
                    $pocetak1 = date("Ym", strtotime($pocetak1));
                ?>
            </td><td style="padding-left:10px ; padding-right:10px; border:none " >
                <b>	do: </b> </td><td>
                <select class = "selectpicker form-control" name="do" id="do"   >
                    <?php
                    while($pocetak1 <= date("Ym", strtotime($sada)))
                    {
                        $mesec = substr($pocetak1,-2,2);
                        $godina = substr($pocetak1,0,4);
                        $operativni_datum = $godina."-".$mesec;
                        $date_za_prikaz = DateTime::createFromFormat('Y-m', $operativni_datum);
                        // echo $pocetak," *--* ", $operativni_datum,' ------ Za prikaz:',$date_za_prikaz->format('F.Y'),"<br/>";
                        ?>
                        <option value=" <?php echo $operativni_datum;  ?>" ><?php echo $date_za_prikaz->format('F.Y'); ?></option>";
                        <?php
                        if(substr($pocetak1, -2, 2) == "12")
                            $pocetak1 = (date("Y", strtotime($pocetak1."01")) + 1)."01";
                        else
                            $pocetak1++;
                    }
                    ?>
                </select>
                <script type="text/javascript">
                    document.getElementById('do').value = "<?php echo $_POST['do'];?>";
                </script>
            </td>
            <td style="border:0; "><input type='submit' style="margin-left : 20px;" value='PRIKAŽI ' name='btnSub' class='btn btn-primary' ></td>
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
    //var_dump($i);
     
    ?>
    <table class="table table-bordered" style=" width: 90%; margin-left:5%; margin-top : 30px;">
        <tr><th class="text-center">Mesec:</th><th class="text-center">Broj grupa:</th><th class="text-center">Broj djaka:</th><th class="text-center">Održanih časova</th><th class="text-center">Skup uplata:</th><th class="text-center" >Plata profesora: </th>
       <!-- <th>Dosad isplaćeno:</th><th>Mes. Saldo:</th><th>Kumulativ. saldo:</th> -->
        </tr>
        <?php
        $ukupan_saldo = 0;
        $kumulaticni_saldo = 0;
        $ddd = 0;
        while($i <= date("Ym", strtotime($do)))
        {
            $ddd++;
            $mesec = substr($i,-2,2);
            $godina = substr($i,0,4);
            $operativni_datum = $godina."-".$mesec;
           // echo $operativni_datum;
            $stttam1 = $termin->read_all_termin_month_profesor_1($operativni_datum,$iid); //svi termini profesora za taj mesec 1. deo
            $stttam2 = $termin->read_all_termin_month_profesor_2($operativni_datum,$iid); //svi termini profesora za taj mesec
            $bg_color = $ddd % 2 === 0 ? "#FFFFFF" : "#E6E6E6";
            ?>
            <tr style='background-color:<?=$bg_color ?>' ><td class="text-center" ><a href='mesecna_statistika_profesor.php?admin=<?=$operativni_datum."_".$iid."_1"; ?>' >
                <?php  echo $mesec,".",$godina; ?> / I </a></td>
                     <?php
                     $sati=0;
                     $minuti=0;
                     // brojimo casove za svaki mesec
                     $sve_grupe_profesor= array();
                     $broj_djaka_profesor = 0;
                     $sati =0;
                     while ($row_broj_casova = $stttam1->fetch(PDO::FETCH_ASSOC)) // brojim grupe
                     {
                         $sati++;
                      //  echo "<pre>" . var_dump($row_broj_casova) . "</pre>";
                         // punimo niz sa grupama
                         if(!in_array($row_broj_casova['fk_grupa'], $sve_grupe_profesor))
                         {
                             $sve_grupe_profesor[]=$row_broj_casova['fk_grupa'];
                           //  echo $operativni_datum,"*", $row_broj_casova['fk_grupa'],"/ <br/>";
                            //if($operativni_datum = "2020-1"){var_dump($sve_grupe_profesor);}
                             $broj_djaka_grupe = $povezivanje->count_student($row_broj_casova['fk_grupa']);
                             $broj_djaka_profesor = $broj_djaka_profesor + $broj_djaka_grupe; // brojim djake

                         }
                        
                      //   echo $interval->format('%h')." Hours ".$interval->format('%i')." Minuta","|-->";
                      //  echo $row_broj_casova['start'],$row_broj_casova['end'],"<br/>";
                     }
                  //   if($operativni_datum = "2020-1"){var_dump($sve_grupe_profesor);}
                     ?>
                <td class="text-center" ><?php echo  /* var_dump($sve_grupe_profesor), */ count( $sve_grupe_profesor); ?></td>
                <td class="text-center" ><?php echo $broj_djaka_profesor; ?></td>
                <td class="text-center" ><?php echo $sati; ?></td>

                <td class="text-center">
                    <?php
                    // Sve uplate za grupe koje drzi profesor sabiramo
                    $srraff = $uplata->read_all_uplate_month_profesor1($operativni_datum,$iid);
                    $ukupno_uplate = 0;
                    $platau = 0;
                    while ($row_suma_uplata = $srraff->fetch(PDO::FETCH_ASSOC))
                    {
                         $jeli_u_grupi=$povezivanje->count_one_student_one_grup($row_suma_uplata['fk_grupa'], $row_suma_uplata['fk_djak']);
                        if($jeli_u_grupi >= 0 )
                        {
                            $ukupno_uplate = $ukupno_uplate + $row_suma_uplata['iznos'];
                        }
   
                    }
                //    echo "Tot: ";
                    echo round($ukupno_uplate, 2);
                    ?>
                </td>
               
                 <td class="text-center"  >
                    <?php
                 //   echo "Op datum",$operativni_datum,"<br/>";
                     $stttam_all_prof_month = $termin->read_all_termin_month_profesor_1($operativni_datum,$iid);
                     $plata_za_navedeni_mesec = 0;
                     $i_brojac = 0;
                     while ($row_svi_casovi_month_prof = $stttam_all_prof_month->fetch(PDO::FETCH_ASSOC)) // brojim grupe
                     {
                       $za_platu_od_casa = 0;
                        //  echo "<pre>";
                        //  var_dump($row_svi_casovi_month_prof);
                        //  echo "</pre>";
                       $i_brojac++;
                        //  //   echo $i_brojac,'/Cas je od: ',$row_svi_casovi_month_prof['start'],"/ do: ",$row_svi_casovi_month_prof['end']," Grupa: ",$row_svi_casovi_month_prof['fk_grupa'],/*"Status:",$row_svi_casovi_month_prof['status'] ,*/"<br/>";
                        //  $datetime1_cas = new DateTime($row_svi_casovi_month_prof['start']);
                        //  $datetime2_cas = new DateTime($row_svi_casovi_month_prof['end']);
                        //  $interval_casa = $datetime1_cas->diff($datetime2_cas);
                        //  // $sum_interval je duzina trajanja casova u minutima
                        //  $sum_interval = $interval_casa->format('%h') * 60 + $interval_casa->format('%i'); // duzina casa u minutima
                        //  echo "Sum interval / cas traje minuta : ", $sum_interval,"<br/>";
                        // //  $stmt_grupa = $grupa->read_one_grupa($row_svi_casovi_month_prof['fk_grupa'],"grupe");
                        // //  $row_category_aktulna_grupa = $stmt_grupa->fetch(PDO::FETCH_ASSOC); // grupa koja ima cas
                        //  // PRovera da li se profesor isplacuje fiksno na osnovu zabelezbe o grupi ne vezano od duzine casa
                        //  if($row_svi_casovi_month_prof['isplata_profesoru'] != null && $row_svi_casovi_month_prof['isplata_profesoru'] != 0 ){
                        //         $za_platu_od_casa = $row_svi_casovi_month_prof['isplata_profesoru'];
                        //  }else{
                        //     $id_duzine_casa = $duzina_casa->read_one_duzina($sum_interval);
                        //     // ako nije fiksna nadoknada prvo proeravamo u profesorovoj tabeli:
                        //         $stmt_uzrast = $uzrast->read_one($row_svi_casovi_month_prof['uzrast']);
                        //         $row_uzrast = $stmt_uzrast->fetch(PDO::FETCH_ASSOC);
                        //         $stmt_isplate = $isplate_profesoru->read_one_isplata_profesor_vreme_last($iid, $row_svi_casovi_month_prof['velicina'], $row_uzrast['fk_platni_razred'],$id_duzine_casa,$row_svi_casovi_month_prof['start']);
                        //         $row_isplta_profesoru = $stmt_isplate->fetch(PDO::FETCH_ASSOC);
                        //         if($row_isplta_profesoru != false){ 
                        //             // Ima posebnosti za profesora
                        //            // include ('matematika_duzine_trajanja_casa.php');
                        //             $za_platu_od_casa = $row_isplta_profesoru['iznos'];
                        //         }else{ // nema posebnosti za profesora
                        //             // echo "<p style='color: blue;'>NEMA SPECIFICNOSTI i CITAMO IZ OPSTE TABELE</p><br/>";
                        //             $smtp_opsta_isplata = $isplata->read_one( $row_svi_casovi_month_prof['velicina'], $row_uzrast['fk_platni_razred'],$id_duzine_casa);
                        //             $row_isplta_profesoru = $smtp_opsta_isplata->fetch(PDO::FETCH_ASSOC);
                        //             // echo "<p style='color:orange'>isplata:<pre> ",var_dump($row_isplta_profesoru),"</pre></p><br/>";
                        //             $za_platu_od_casa = $row_isplta_profesoru['iznos'];
                        //             //include ('matematika_duzine_trajanja_casa.php');
                        //              $row_isplta_profesoru = array();
                        //         }
                        //         // echo "Od ovog casa profesoru se iplacuje: <b style='color:orange;'>", $za_platu_od_casa,"</b><br/>";
                        //     }        
                        // echo "<b style='color:green;'>Za platu sa ovog casa: ",$za_platu_od_casa,"</b><br/>"; 
                        $za_platu_od_casa = $termin->plata($row_svi_casovi_month_prof, $iid);
                        $plata_za_navedeni_mesec = $plata_za_navedeni_mesec + $za_platu_od_casa;
                        
                     }
                     if($row_category_prof['plata'] == 2){
                         echo "fiksno: ",$row_category_prof['iznos_plate'];
                     }else{
                        echo $plata_za_navedeni_mesec;      
                     }
                    ?>
                </td>
            </tr>

            <tr  style='background-color:<?=$bg_color ?>' ><td class="text-center" ><a href='mesecna_statistika_profesor.php?admin=<?php echo $operativni_datum."_".$iid."_2"; ?>' >
                <?php  echo $mesec,".",$godina; ?> / II </a></td>
                <?php
                     $sati=0;
                     $minuti=0;
                     // brojimo casove za svaki mesec
                     $sve_grupe_profesor= array();
                     $broj_djaka_profesor = 0;
                     $sati =0;
                     while ($row_broj_casova = $stttam2->fetch(PDO::FETCH_ASSOC)) // brojim grupe
                     {
                         $sati++;
                      //  echo "<pre>" . var_dump($row_broj_casova) . "</pre>";
                         // punimo niz sa grupama
                         if(!in_array($row_broj_casova['fk_grupa'], $sve_grupe_profesor))
                         {
                             $sve_grupe_profesor[]=$row_broj_casova['fk_grupa'];
                           //  echo $operativni_datum,"*", $row_broj_casova['fk_grupa'],"/ <br/>";
                            //if($operativni_datum = "2020-1"){var_dump($sve_grupe_profesor);}
                             $broj_djaka_grupe = $povezivanje->count_student($row_broj_casova['fk_grupa']);
                             $broj_djaka_profesor = $broj_djaka_profesor + $broj_djaka_grupe; // brojim djake

                         }
                        
                      //   echo $interval->format('%h')." Hours ".$interval->format('%i')." Minuta","|-->";
                      //  echo $row_broj_casova['start'],$row_broj_casova['end'],"<br/>";
                     }
                  //   if($operativni_datum = "2020-1"){var_dump($sve_grupe_profesor);}
                     ?>
                <td class="text-center" ><?php echo  /* var_dump($sve_grupe_profesor), */ count( $sve_grupe_profesor); ?></td>
                <td class="text-center" ><?php echo $broj_djaka_profesor; ?></td>
                <td class="text-center" ><?php echo $sati; ?></td>
                <td class="text-center">
                    <?php
                    // Sve uplate za grupe koje drzi profesor sabiramo
                    $srraff = $uplata->read_all_uplate_month_profesor2($operativni_datum,$iid);
                    $ukupno_uplate = 0;
                    $platau = 0;
                    while ($row_suma_uplata = $srraff->fetch(PDO::FETCH_ASSOC))
                    {
                         $jeli_u_grupi=$povezivanje->count_one_student_one_grup($row_suma_uplata['fk_grupa'], $row_suma_uplata['fk_djak']);
                        if($jeli_u_grupi >= 0 )
                        {
                            $ukupno_uplate = $ukupno_uplate + $row_suma_uplata['iznos'];
                        }
   
                    }
                //    echo "Tot: ";
                    echo round($ukupno_uplate, 2);
                    ?>
                </td>

                <td class="text-center">
                <?php
                 //   echo "Op datum",$operativni_datum,"<br/>";
                     $stttam_all_prof_month = $termin->read_all_termin_month_profesor_2($operativni_datum,$iid);
                     $plata_za_navedeni_mesec = 0;
                     $i_brojac = 0;
                     while ($row_svi_casovi_month_prof = $stttam_all_prof_month->fetch(PDO::FETCH_ASSOC)) // brojim grupe
                     {
                       $za_platu_od_casa = 0;
                        //  echo "<pre>";
                        //  var_dump($row_svi_casovi_month_prof);
                        //  echo "</pre>";
                       $i_brojac++;
                         //   echo $i_brojac,'/Cas je od: ',$row_svi_casovi_month_prof['start'],"/ do: ",$row_svi_casovi_month_prof['end']," Grupa: ",$row_svi_casovi_month_prof['fk_grupa'],/*"Status:",$row_svi_casovi_month_prof['status'] ,*/"<br/>";
                        //  $datetime1_cas = new DateTime($row_svi_casovi_month_prof['start']);
                        //  $datetime2_cas = new DateTime($row_svi_casovi_month_prof['end']);
                        //  $interval_casa = $datetime1_cas->diff($datetime2_cas);
                        //  // $sum_interval je duzina trajanja casova u minutima
                        //  $sum_interval = $interval_casa->format('%h') * 60 + $interval_casa->format('%i'); // duzina casa u minutima
                        //  echo "Sum interval / cas traje minuta : ", $sum_interval,"<br/>";
                        // //  $stmt_grupa = $grupa->read_one_grupa($row_svi_casovi_month_prof['fk_grupa'],"grupe");
                        // //  $row_category_aktulna_grupa = $stmt_grupa->fetch(PDO::FETCH_ASSOC); // grupa koja ima cas
                        //  // PRovera da li se profesor isplacuje fiksno na osnovu zabelezbe o grupi ne vezano od duzine casa
                        //  if($row_svi_casovi_month_prof['isplata_profesoru'] != null && $row_svi_casovi_month_prof['isplata_profesoru'] != 0 ){
                        //         $za_platu_od_casa = $row_svi_casovi_month_prof['isplata_profesoru'];
                        //  }else{
                        //     $id_duzine_casa = $duzina_casa->read_one_duzina($sum_interval);
                        //     var_dump($id_duzine_casa);
                        //     // ako nije fiksna nadoknada prvo proeravamo u profesorovoj tabeli:
                        //         $stmt_uzrast = $uzrast->read_one($row_svi_casovi_month_prof['uzrast']);
                        //         $row_uzrast = $stmt_uzrast->fetch(PDO::FETCH_ASSOC);
                        //         $stmt_isplate = $isplate_profesoru->read_one_isplata_profesor_vreme_last($iid, $row_svi_casovi_month_prof['velicina'], $row_uzrast['fk_platni_razred'],$id_duzine_casa,$row_svi_casovi_month_prof['start']);
                        //         $row_isplta_profesoru = $stmt_isplate->fetch(PDO::FETCH_ASSOC);
                        //         if($row_isplta_profesoru != false){ 
                        //             // Ima posebnosti za profesora
                        //            // include ('matematika_duzine_trajanja_casa.php');
                        //             $za_platu_od_casa = $row_isplta_profesoru['iznos'];
                        //         }else{ // nema posebnosti za profesora
                        //             // echo "<p style='color: blue;'>NEMA SPECIFICNOSTI i CITAMO IZ OPSTE TABELE</p><br/>";
                        //             $smtp_opsta_isplata = $isplata->read_one( $row_svi_casovi_month_prof['velicina'], $row_uzrast['fk_platni_razred'],$id_duzine_casa);
                        //             $row_isplta_profesoru = $smtp_opsta_isplata->fetch(PDO::FETCH_ASSOC);
                        //             // echo "<p style='color:orange'>isplata:<pre> ",var_dump($row_isplta_profesoru),"</pre></p><br/>";
                        //             $za_platu_od_casa = $row_isplta_profesoru['iznos'];
                        //             //include ('matematika_duzine_trajanja_casa.php');
                        //              $row_isplta_profesoru = array();
                        //         }
                        //         // echo "Od ovog casa profesoru se iplacuje: <b style='color:orange;'>", $za_platu_od_casa,"</b><br/>";
                        //     }        
                        // echo "<b style='color:green;'>Za platu sa ovog casa: ",$za_platu_od_casa,"</b><br/>"; 
                        $za_platu_od_casa = $termin->plata($row_svi_casovi_month_prof, $iid);
                        
                        $plata_za_navedeni_mesec = $plata_za_navedeni_mesec + $za_platu_od_casa;
                        
                     }
                     if($row_category_prof['plata'] == 2){
                         echo "fiksno: ",$row_category_prof['iznos_plate'];
                     }else{
                        echo $plata_za_navedeni_mesec;      
                     }
                    ?>
                </td>
            </tr>        

            <?php    
                if(substr($i, 4, 2) == "12"){
                    $i = (date("Y", strtotime($i."01")) + 1)."01";
                }else{
                    $i++;
                }
                    
        }

        ?>
   
    </table>
    <?php
}
include_once "layout_foot.php";
?>
</div>
</body>
</html>
