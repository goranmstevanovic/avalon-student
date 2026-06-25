<html>
<head>
    <title> Mesecna statistika profesor </title>
    <link rel="shortcut icon" href="images/kalen.png">
  <!--  <link rel="stylesheet" type="text/css" href="../tigrakal/tcal.css" />
    <script type="text/javascript" src="../tigrakal/tcal.js"></script> -->
    <style>
       .table-bordered{
            font-size: 14px;
        }
    </style>
</head>
<body>

<?php
/**
 * Created by PhpStorm.
 * User: goran
 * Date: 21 Feb 2020
 * Time: 11:54
 */

if(isset($_GET['admin'])){$admin = $_GET['admin']; // echo "Radi se o djaku  br: ", $idd;
  //  echo $admin,"<br/>";

    $godina_mesec = substr($admin, 0, 7);
  //  echo $godina_mesec,"<br/>";
    $prof = substr($admin, strpos($admin, "/") + 1);
   // echo $prof,"<br/>";
    $iid = $prof;

    $date_za_prikaz = DateTime::createFromFormat('Y-m', $godina_mesec);
  //  echo $date_za_prikaz->format('F.Y');


/*
    $tmp77 = explode ("-", $dan1);
    $dan2 = $tmp77[2] . "." . $tmp77[1] . "." . $tmp77[0]; */
}


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
// if(isset($_GET['id'])){$iid = $_GET['id'];  echo "profesor je br: ", $iid;}

// core configuration
include_once "config/core.php";

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
include_once 'objects/kalendar.php';
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
$termin = new kalendar($db);
$tok_novca = new tok_novca($db);
$uplata = new uplata($db);
// initialize objects
//$user = new User($db);
$stmt = $profesor->read_jedan_profesor($prof,"users");
$row_category_prof = $stmt->fetch(PDO::FETCH_ASSOC);
extract($row_category_prof);
//echo"<script> window.location=window.location; </script>";
?>
<div class='col-md-12' style='width:96%; margin-left: 2%;'>

<table class='table table-bordered'>
    <tr>
        <td ><h4>Statistika za profesora:<b> <?php echo $firstname,"&nbsp;", $lastname; ?></b>, za mesec: <?php  echo $date_za_prikaz->format('F.Y'); ?>  </h4> </td>
    </tr>
</table>


<div class='col-md-6' style='margin-left: 0; border: 1px solid linen;'>
    <p class = "h4"> Studentske grupe u ovom mesecu:</p>
    <table class="table table-bordered"  >
        <tr><th>Red. br.</th><th class="text-center">Ime grupe</th><th class="text-center">broj djaka u grupi</th><th class="text-center">Odrzanih sati nastave:</th></tr>
        <?php
        $stttam = $termin->read_all_termin_month_profesor($godina_mesec,$prof);
        $sati=0;
        $minuti=0;
        // brojimo casove za svaki mesec
        $sve_grupe_profesor= array();
        $broj_djaka_profesor = 0;
        while ($row_broj_casova = $stttam->fetch(PDO::FETCH_ASSOC))
        {
            // punimo niz sa grupama
            if(!in_array($row_broj_casova['fk_grupa'], $sve_grupe_profesor)){
                $sve_grupe_profesor[]=$row_broj_casova['fk_grupa'];
              //  $broj_djaka_grupe = $povezivanje->count_student($row_broj_casova['fk_grupa']);
              //  $broj_djaka_profesor = $broj_djaka_profesor + $broj_djaka_grupe;

            }


            $datetime1 = new DateTime($row_broj_casova['start']);
            $datetime2 = new DateTime($row_broj_casova['end']);
            $interval = $datetime1->diff($datetime2);
            $sati = $sati + $interval->format('%h');
            $minuti = $minuti + $interval->format('%i');
            //   echo $interval->format('%h')." Hours ".$interval->format('%i')." Minuta","|-->";
            //  echo $row_broj_casova['start'],$row_broj_casova['end'],"<br/>";

        }

        $ukupno_sati = 0;
        foreach ($sve_grupe_profesor as $key => $broj_grupe) {


            ?>
            <tr class = "text-center">
                <td  class = "text-center" ><?php echo $key+1; ?></td>
                <td  class = "text-center" ><?php
                  //  echo $broj_grupe;
                    $sst = $grupa->read_one_grupa1($broj_grupe);
                    $row_citanje_grupe = $sst->fetch(PDO::FETCH_ASSOC);
                    extract($row_citanje_grupe);
                    // echo "nivo=", $nivo;
                    if ($fk_jezik == 1) {$fk_jezik = "Eng ";} else {$fk_jezik = "Nem ";}
                    switch ($nivo) {
                        case 1:
                            $nivo="A1";
                            break;
                        case 2:
                            $nivo="A1.1";
                            break;
                        case 3:
                            $nivo="A1.2";
                            break;
                        case 4:
                            $nivo="A2";
                            break;
                        case 5:
                            $nivo="A2.1";
                            break;
                        case 6:
                            $nivo="A2.2";
                            break;
                        case 7:
                            $nivo="B1";
                            break;
                        case 8:
                            $nivo="B1.1";
                            break;
                        case 9:
                            $nivo="B1.2";
                            break;
                        case 10:
                            $nivo="B2";
                            break;
                        case 11:
                            $nivo="B2.1";
                            break;
                        case 12:
                            $nivo="B2.2";
                            break;
                        case 13:
                            $nivo="C1";
                            break;
                        case 14:
                            $nivo="C1.1";
                            break;
                        case 15:
                            $nivo="C1.2";
                            break;
                        case 16:
                            $nivo="C2";
                            break;
                        case 17:
                            $nivo="C2.1";
                            break;
                        case 18:
                            $nivo="C2.2";
                            break;
                    }
                    echo $fk_jezik, "&nbsp;", $nivo, "&nbsp;[ ", $alias," ]";
                    ?>

                    </td>
                <td class="text-center">
                    <?php
                        $num_students = $povezivanje->count_student($broj_grupe);
                        echo $num_students;
                        $broj_djaka_profesor = $broj_djaka_profesor + $num_students;
                       // echo $broj_djaka_profesor;
                    ?>
                </td>
                <td>
                    <?php
                  //  echo "<br/>",$godina_mesec,"--",$broj_grupe,"<br/>";
                    $stttam = $termin->read_all_termin_month_grupa($godina_mesec,$broj_grupe);
                    $sati = 0;
                    $minuti = 0;
                    while ($row_grupa_broj_casova = $stttam->fetch(PDO::FETCH_ASSOC))
                    {
                       // echo "--",$row_grupa_broj_casova['start'],"-->",$row_grupa_broj_casova['end'],"<br/>";
                        $datetime1 = new DateTime($row_grupa_broj_casova['start']);
                        $datetime2 = new DateTime($row_grupa_broj_casova['end']);
                        $interval = $datetime1->diff($datetime2);
                        $sati = $sati + $interval->format('%h');
                        $minuti = $minuti + $interval->format('%i');
                        //   echo $interval->format('%h')." Hours ".$interval->format('%i')." Minuta","|-->";
                        //  echo $row_broj_casova['start'],$row_broj_casova['end'],"<br/>";
                    }
                    $sati1 = $sati + $minuti/60;
                    echo $sati1 /* ," = ", $sati," sati ", $minuti, " minuti " */;
                    $ukupno_sati = $ukupno_sati + $sati1;
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
        <tr><td colspan="2"></td>

            <td class = "text-center">
                <b>
                    <?php echo $broj_djaka_profesor;  ?>
                </b>
            </td>
            <td class="text-center">
                <b>
                    <?php echo $ukupno_sati; ?>
                </b>
            </td>

        </tr>
    </table>

</div>
    <div class='col-md-6' style='margin-right: 0; border: 1px solid linen;'>
        <h4 >Sve isplate profesoru za ovaj mesec:</h4>
        <?php
        $suma_svih_uplata=0;
            $suma_predatog_novca = 0;
        $suma_primljenog_novca = 0;
        $query_date = $godina_mesec ;
        $date = new DateTime($query_date);
        //First day of month
        $date->modify('first day of this month');
        $firstday= $date->format('Y-m-d');
        //Last day of month
        $date->modify('last day of this month');
        $lastday= $date->format('Y-m-d');
       /* echo "prvi: ",$firstday,"<br/>";
        echo $lastday;
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

        $start    = strtotime($firstday);
        $end    = strtotime($lastday);
        $dana_izmedju = daysBetween($start,$end);
        ?>
        <table class="table table-bordered" style=" width: 100%; margin-left:0%; margin-top : 10px;">
            <tr class="text-center"><th class="text-center">Datum:</th><th class="text-center" >Uplate na dan:</th><th class="text-center" >Predat novac Voyager-u:</th><th class="text-center" >Primljen novac od Voyager-a</th></tr>
            <?php  foreach($dana_izmedju as $dan)
            {

                $tmp77 = explode(".", $dan);
                $dan1 = $tmp77[2] . "-" . $tmp77[1] . "-" . $tmp77[0];
              //  echo $dan," / " ,$dan1," / ",$iid, "<br/>";
                $stmt = $uplata->read_all_uplate_na_dan($dan1, $iid);
                $suma_uplata = 0;
                while ($row_uplata = $stmt->fetch(PDO::FETCH_ASSOC))
                {
                    //  echo $row_uplata['iznos'];
                    $suma_uplata = $suma_uplata + $row_uplata['iznos'];
                }

                $stmt = $tok_novca->read_one_day_one_profesor($dan1, $iid);
                $row_category_dnevni_izvestaj = $stmt->fetch(PDO::FETCH_ASSOC);

                if($suma_uplata > 0 || $row_category_dnevni_izvestaj['iznos_predao'] > 0 || $row_category_dnevni_izvestaj['iznos_primio'] > 0 )
                { ?>
                    <tr><td align="center" > <!-- <a href='dnevni_izvestaj.php?admin2=<?php /*  echo $dan1."/".$iid; */ ?> '> --> <?php  echo  $dan; ?> <!-- </a> --> </td>
                        <td align="center" >
                            <?php
                            $suma_svih_uplata = $suma_svih_uplata + $suma_uplata;
                            echo $suma_uplata;
                            ?>
                        </td>
                        <td class="text-center">
                            <?php
                            $suma_predatog_novca = $suma_predatog_novca + $row_category_dnevni_izvestaj['iznos_predao'];
                            echo $row_category_dnevni_izvestaj['iznos_predao'];
                            ?>
                        </td>
                        <td class="text-center">
                            <?php
                            if(isset($row_category_dnevni_izvestaj['iznos_primio']) && $row_category_dnevni_izvestaj['iznos_primio'] > 0 ){
                                $suma_primljenog_novca = $suma_primljenog_novca +  $row_category_dnevni_izvestaj['iznos_primio'];
                                echo $row_category_dnevni_izvestaj['iznos_primio'];
                                if ($row_category_dnevni_izvestaj['primio_racun'] == 1) {
                                    echo "<tab style='color:red'> &nbsp; Na račun</tab>";
                                }else{ echo "<tab style='color:red'> &nbsp; Cash</tab>";}

                            }
                            if(isset($row_category_dnevni_izvestaj['iznos_primio2']) && $row_category_dnevni_izvestaj['iznos_primio2'] > 0 ){
                                $suma_primljenog_novca = $suma_primljenog_novca +  $row_category_dnevni_izvestaj['iznos_primio2'];
                                echo "<br/>",$row_category_dnevni_izvestaj['iznos_primio2'];
                                if ($row_category_dnevni_izvestaj['primio_racun2'] == 1) {
                                    echo "<tab style='color:red'> &nbsp; Na račun</tab>";
                                }else{ echo "<tab style='color:red'> &nbsp; Cash</tab>";}

                            }


                            ?>
                        </td>
                    </tr>
                    <?php
                }
            } ?>
            <tr>
                <td class="text-center" ><b>SUM:</b></td>
                <td class="text-center" ><b><?php echo $suma_svih_uplata; ?></b></td>
                <td class="text-center" ><b><?php echo $suma_predatog_novca; ?></b></td>
                <td class="text-center" ><b><?php echo $suma_primljenog_novca; ?></b></td>
            </tr>
        </table>



    </div>
    <div class='col-md-12' style='width:100%; margin-left: 0;'>
        <p class = "h4"> Zaduženja, uplate i obračun plate u ovom mesecu:</p>
        <table class="table table-bordered text-center" style='width:100%; margin-left: 0;' >
            <tr bgcolor="#E6E6E6" style="text-align:center;">
                <th style="text-align:center;">Red. br.</th>
                <th style="text-align:center;">Ime i prezime</th>
                <th style="text-align:center;">grupa je</th>
                <th style="text-align:center;">generisana</th>
                <th style="text-align:center;">U grupi je od</th>
                <th style="text-align:center;">Sve uplate</th>
                <th style="text-align:center;">Sva zaduženja</th>
                <th style="text-align:center;">% za platu</th>
                <th style="text-align:center;">Plata</th>



            </tr>
            <?php
         //  $ssmmjh = $grupa->read_all_grups_profesor($prof,"grupe");
            $ukupna_suma = 0;
            $ukupna_uplata = 0;
            $ukupno_zaduzenje = 0;
            $ukupna_plata = 0;
            $i=1;


            $svi_djaci_gde_je_bilo_uplate = array();
            $svi_djaci_gde_je_bilo_ili_uplata_ili_zaduzenja = array();
        /*    $sjdjjd = $uplata->read_all_uplate_month_profesor($godina_mesec,$prof );
            while ($row_category_sve_grupe_suplate = $sjdjjd->fetch(PDO::FETCH_ASSOC)){
                if(!in_array($row_category_sve_grupe_suplate['fk_grupa'], $sve_grupe_gde_je_bilo_uplate)){
                    $sve_grupe_gde_je_bilo_uplate[]=$row_category_sve_grupe_suplate['fk_grupa'];}

            }

            foreach ($sve_grupe_gde_je_bilo_uplate as $key => $broj_grupe)
            {

                $sts = $povezivanje->read_all_students($broj_grupe); */

            $sjdjjd = $uplata->read_all_uplate_month_profesor($godina_mesec,$prof );
            while ($row_category_djaci_uplate = $sjdjjd->fetch(PDO::FETCH_ASSOC)){
                if(!in_array($row_category_djaci_uplate['fk_djak'], $svi_djaci_gde_je_bilo_ili_uplata_ili_zaduzenja)){
                    $svi_djaci_gde_je_bilo_ili_uplata_ili_zaduzenja[]=$row_category_djaci_uplate['fk_djak'];

                }

            }

            $ssddrmk = $zaduzenje->read_all_zaduzenja_month_profesor1($godina_mesec,$prof);
            while ($row_category_djaci_zaduzenja = $ssddrmk->fetch(PDO::FETCH_ASSOC)){
                if(!in_array($row_category_djaci_zaduzenja['fk_djak'], $svi_djaci_gde_je_bilo_ili_uplata_ili_zaduzenja)){
                    $svi_djaci_gde_je_bilo_ili_uplata_ili_zaduzenja[]=$row_category_djaci_zaduzenja['fk_djak'];

                }
            }
        //    var_dump($svi_djaci_gde_je_bilo_ili_uplata_ili_zaduzenja);
            // U niz stavljenis vi djaci koji su imali uplate ili zaduzenja u tekucem mesecu i njihov id je $index
         //   $prolaz = 0;
            foreach ($svi_djaci_gde_je_bilo_ili_uplata_ili_zaduzenja as $key => $indeks)
            {
                     //   $broj_grupe = $row_category_djaci['fk_grupa'];
                     //   $indeks = $row_category_djaci['fk_djak'];
                     //  $svi_djaci_gde_je_bilo_uplate[]=$row_category_djaci['fk_djak'];
                    //   echo $row_category_djaci['fk_djak'],  "<br/>";
                $prolaz=array();
              //  $prolaz2=0;
                        $ddkkakakk = $povezivanje->read_allall_group_students($indeks);
                        while( $row_category_sve_grupe_djak = $ddkkakakk->fetch(PDO::FETCH_ASSOC)  )
                        {
                            $broj_grupe = $row_category_sve_grupe_djak['fk_grupa'];
                            // nedam mu da istu grupu i istog djaka ponovi u pregledu da ne narusimo tacnost uplata
                            $prolaz_test = $indeks.$broj_grupe;
                            if(!in_array($prolaz_test, $prolaz)  )
                            {
                                $prolaz[] = $prolaz_test;
                                echo "<tr>";
                                $ssts = $djak->read_one_djak("djaci", $indeks); // sve o jednom djaku koji dolazi iz niza
                                $row_category_detalj_djaci = $ssts->fetch(PDO::FETCH_ASSOC);
                                if ($row_category_detalj_djaci['status'] >= 0) // nebitno jeli aktivan
                                {

                                    echo"<td>",$i, /* "/",$indeks,"/",$broj_grupe, */ "</td>";
                                    $i++;
                                    echo "<td><a href='kartica_djak.php?id={$row_category_detalj_djaci['id']} '>", $row_category_detalj_djaci['firstname'], "&nbsp;", $row_category_detalj_djaci['lastname'], "</a>";
                                    if($row_category_detalj_djaci['prevod'] == TRUE){echo "&nbsp; <tab style='color:red;'>Prevod</tab>";}
                                    echo"</td>";
                                    $ststmt = $grupa->read_one_grupa($broj_grupe,"grupe"); // vidimo o kojoj grupi se radi i citamo sve o njoj
                                    $row_category_sve_ogrupi = $ststmt->fetch(PDO::FETCH_ASSOC);
                                    extract($row_category_sve_ogrupi);
                                    if($fk_jezik==1){$fk_jezik = "Eng."; }else{$fk_jezik = "Nem.";}
                                    switch ($nivo) {
                                        case 1:
                                            $nivo="A1";
                                            break;
                                        case 2:
                                            $nivo="A1.1";
                                            break;
                                        case 3:
                                            $nivo="A1.2";
                                            break;
                                        case 4:
                                            $nivo="A2";
                                            break;
                                        case 5:
                                            $nivo="A2.1";
                                            break;
                                        case 6:
                                            $nivo="A2.2";
                                            break;
                                        case 7:
                                            $nivo="B1";
                                            break;
                                        case 8:
                                            $nivo="B1.1";
                                            break;
                                        case 9:
                                            $nivo="B1.2";
                                            break;
                                        case 10:
                                            $nivo="B2";
                                            break;
                                        case 11:
                                            $nivo="B2.1";
                                            break;
                                        case 12:
                                            $nivo="B2.2";
                                            break;
                                        case 13:
                                            $nivo="C1";
                                            break;
                                        case 14:
                                            $nivo="C1.1";
                                            break;
                                        case 15:
                                            $nivo="C1.2";
                                            break;
                                        case 16:
                                            $nivo="C2";
                                            break;
                                        case 17:
                                            $nivo="C2.1";
                                            break;
                                        case 18:
                                            $nivo="C2.2";
                                            break;
                                    }


                                    $row_category_sve_ogrupi['created'] = substr($row_category_sve_ogrupi['created'],0,10);
                                    $row_category_sve_ogrupi['created'] = datum_u_nas_datum($row_category_sve_ogrupi['created']);

                                    echo "<td><a href='update_grupa.php?id={$row_category_sve_ogrupi['id']} ' target='_blank'>",$fk_jezik,"&nbsp;",$nivo,"&nbsp;",$alias,"</td><td class='text-center'>",$row_category_sve_ogrupi['created'],"</a></td>";
                                    $sskz = $povezivanje->read_one_student_one_grup($broj_grupe, $indeks);
                                    $row_category_otkadjeugrupi = $sskz->fetch(PDO::FETCH_ASSOC);
                                    if(isset($row_category_otkadjeugrupi['created'])){
                                        $row_category_otkadjeugrupi['created'] = vreme_u_nase_vreme($row_category_otkadjeugrupi['created']);

                                    }
                                    // $row_category_otkadjeugrupi['created'] = vreme_u_nase_vreme($row_category_otkadjeugrupi['created']);
                                    //   echo "<td>", $row_category_otkadjeugrupi['fk_grupa'], "</td>";
                                    echo "<td>", substr($row_category_otkadjeugrupi['created'],0,10), "</td>";

                                    // zbrajamo sve uplate djaka
                                    $uplate_djak = 0;
                                    $ddjkm = $uplata->read_all_for_student_interval_mesec_prof($indeks, $broj_grupe,$godina_mesec,$prof);
                                    while ($row_category_medjusuma_uplate = $ddjkm->fetch(PDO::FETCH_ASSOC)) {
                                        $uplate_djak = $uplate_djak + $row_category_medjusuma_uplate['iznos'];
                                    }

                                    echo "<td>", $uplate_djak, "</td>";

                                    $ukupna_uplata = $ukupna_uplata + $uplate_djak;
                                    //Zbrajamo sva aktivna zaduzenja za djaka za interval
                                    $zaduzenje_djak = 0;
                                    $drmk = $zaduzenje->read_all_iznos_for_student_interval_mesec($indeks, $broj_grupe,$godina_mesec);
                                   echo"<td>";
                                    while ($row_category_medjusuma = $drmk->fetch(PDO::FETCH_ASSOC)) {
                                     //   echo $row_category_medjusuma['iznos']," --> ",$row_category_medjusuma['fk_djak']," --> ",$row_category_medjusuma['fk_grupa'],"<br/>";
                                        $zaduzenje_djak = $zaduzenje_djak + $row_category_medjusuma['iznos'];
                                    }

                                    $drannaj = $grupa->read_one_grupa($broj_grupe,"grupe");
                                    $row_category_sve_o_grupi2 = $drannaj->fetch(PDO::FETCH_ASSOC);

                                    if($row_category_sve_o_grupi2['procenat_za_platu'] == NULL){
                                        $jsjsjsj = $profesor->read_jedan_profesor($prof,"users");
                                        $row_info_profesor = $jsjsjsj->fetch(PDO::FETCH_ASSOC);
                                        $procenat_za_racunanje_plate = $row_info_profesor['procentat_za_platu'];
                                    }else{
                                        $procenat_za_racunanje_plate = $row_category_sve_o_grupi2['procenat_za_platu'];
                                    }
                                    echo  $zaduzenje_djak,"</td>";


                                    $ukupno_zaduzenje = $ukupno_zaduzenje + $zaduzenje_djak;
                                    // saldo djaka
                                    // $saldo_djak = $uplate_djak - $zaduzenje_djak;
                                    if(isset($nacin_obracuna) && $nacin_obracuna == 1)
                                    {
                                        $periodicno_za_platu = $zaduzenje_djak * $procenat_za_racunanje_plate/100;
                                    } else
                                        {
                                            $periodicno_za_platu = $uplate_djak * $procenat_za_racunanje_plate/100;
                                        }
                                    ?>
                                    <td><?php echo $procenat_za_racunanje_plate; ?> % </td>
                                    <td><?php echo $periodicno_za_platu; ?></td>
                                    <?php
                                    $ukupna_plata =  $ukupna_plata + $periodicno_za_platu;
                                    // $ukupna_suma = $ukupna_suma + $saldo_djak;
                                }
                                echo "</tr>";


                            }
                        }
                }
           // }
            ?>
            <tr><td colspan="5" align="right"><b> Ukupan saldo Profesora: </b></td><td><?php echo $ukupna_uplata;  ?></td><td><?php echo $ukupno_zaduzenje; ?></td><td></td><td> <?php echo $ukupna_plata; ?></td></tr>
        </table>

    </div>



</div>




<?php
include_once "layout_foot.php";
?>
</body>
</html>