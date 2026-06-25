

<?php
// header("Cache-Control: no-cache, must-revalidate");
/**
 * Created by PhpStorm.
 * User: goran
 * Date: 21 Feb 2020
 * Time: 11:54
 */

if(isset($_GET['admin'])){$admin = $_GET['admin'];  // echo "Radi se o profesoru br: ", $idd;
  //  echo $admin,"<br/>";
   $pom_prenos = explode("_", $admin);
   $godina_mesec = $pom_prenos['0'];
   $prof = $pom_prenos['1'];
   $iid = $prof;
   $deo_meseca = $pom_prenos['2'];


  //  $godina_mesec = substr($admin, 0, 7);
  //  echo $godina_mesec,"<br/>";
  //  $prof = substr($admin, strpos($admin, "/") + 1);
  //  echo $prof,"<br/>";
 //   $iid = $prof;
  //  echo "profesor: ", $iid;
  //  $deo_meseca = substr($admin, -1);
  //  echo "deo meseca", $deo_meseca;

  //  echo "Radi se o profesoru br: ", $iid,"<br/>";
  //  echo "Godina-mesec: ", $godina_mesec,"<br/>";


    $date_za_prikaz = DateTime::createFromFormat('Y-m', $godina_mesec);
  //  echo $date_za_prikaz->format('F.Y');


/*
    $tmp77 = explode ("-", $dan1);
    $dan2 = $tmp77[2] . "." . $tmp77[1] . "." . $tmp77[0]; */
}


// ini_set('session.cache_limiter','public');
// session_cache_limiter(false);
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
include_once 'config/autoload.php';
include_once 'config/funkcije.php';
// include_once 'objects/user.php';
// include_once 'objects/grupa.php';
// include_once 'objects/djak.php';
// include_once "libs/php/utils.php";
// include_once 'objects/povezivanje.php';
// include_once 'objects/zaduzenje.php';
// include_once 'objects/kalendar.php';
// include_once 'objects/uplata.php';

// include_once 'objects/tok_novca.php';
// include_once 'objects/isplate_profesoru.php';
// include_once 'objects/nivo_znanja.php';
// include_once 'objects/isplata.php';
// include_once 'objects/jezik.php';
// include_once 'objects/velicina.php';
// include_once 'objects/uzrast.php';


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
$termin = new kalendar($db);
$tok_novca = new tok_novca($db);
$uplata = new uplata($db);
$isplate_profesoru = new isplate_profesoru($db);
$isplata = new isplata($db);
$jezik = new jezik($db);
$velicina1 = new velicina($db);
$nivo_znanja = new nivo_znanja($db);
$uzrast1 = new uzrast($db);
$cenovnik = new cenovnik($db);
// initialize objects
//$user = new User($db);
$stmt = $profesor->read_jedan_profesor($prof,"users");
$row_category_prof = $stmt->fetch(PDO::FETCH_ASSOC);
extract($row_category_prof);
//echo"<script> window.location=window.location; </script>";
?>
<div class='col-md-12' style='width:96%; margin-left: 2%;' >

<table class='table table-bordered'>
    <tr>
             <td>
        <a href="<?= htmlspecialchars($back_url) ?>" class="btn btn-danger">
            <span class="glyphicon glyphicon-arrow-left"></span> Nazad
        </a>
        </td>
        <td ><h4>Statistika za profesora: <b> <?php echo $firstname,"&nbsp;", $lastname; ?></b>, za mesec: <?php  echo $date_za_prikaz->format('F.Y'); ?>  </h4> </td>
    </tr>
</table>


<div class='col-md-8' style='margin-left: 0; border: 1px solid linen;'>
    <p class = "h4" style="padding : 10px;" > Studentske grupe u ovom mesecu:</p>
    <table class="table table-bordered table-hover"  >
        <tr><th>Red. br.</th><th class="text-center">Ime grupe</th><th class="text-center">Broj djaka u grupi</th><th class="text-center">Održanih časova:</th></tr>
        <?php
        
        $stttam = $termin->read_all_termin_month_profesor($godina_mesec,$prof);
        $operativni_datum = $godina_mesec;
        $sati=0;
        $minuti=0;
        // brojimo casove za svaki mesec
        $sve_grupe_profesor= array();
        $broj_djaka_profesor = 0;
        while ($row_broj_casova = $stttam->fetch(PDO::FETCH_ASSOC))
        {
          //  if( ( ($row_broj_casova['fk_profesor'] == null OR  $row_broj_casova['fk_profesor'] == 0 OR ($row_broj_casova['fk_profesor'] == $row_broj_casova['fk_profesor_grupa']) ))  ){
                             
                // punimo niz sa grupama
                if(!in_array($row_broj_casova['fk_grupa'], $sve_grupe_profesor)){
                    $sve_grupe_profesor[]=$row_broj_casova['fk_grupa'];
                //  $broj_djaka_grupe = $povezivanje->count_student($row_broj_casova['fk_grupa']);
                //  $broj_djaka_profesor = $broj_djaka_profesor + $broj_djaka_grupe;

                }
          //  }
            $datetime1 = new DateTime($row_broj_casova['start']);
            $datetime2 = new DateTime($row_broj_casova['end']);
            $interval = $datetime1->diff($datetime2);
            $sati = $sati + $interval->format('%h');
            $minuti = $minuti + $interval->format('%i');
            //   echo $interval->format('%h')." Hours ".$interval->format('%i')." Minuta","|-->";
            //  echo $row_broj_casova['start'],$row_broj_casova['end'],"<br/>";

        }

        $ukupno_sati = 0;
        $ukupno_minuti = 0;
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

                    $stm_jezik = $jezik->read_one_jezik($fk_jezik,'jezik');
                    $row_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC);
                    $fk_jezik = $row_jezik['alias'];
                
                    $stm_nivo = $nivo_znanja->read_one_nivo($nivo,'nivo_znanja');
                    $row_nivo_znanja = $stm_nivo->fetch(PDO::FETCH_ASSOC);
                    $nivo = $row_nivo_znanja['ime'];



                    echo "<a href='update_grupa_uvid?id={$id} '>";
                    echo $fk_jezik, "&nbsp;", $nivo, "&nbsp;[ ", $alias," ]</a>";
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
                    $stttam = $termin->read_all_termin_month_grupa_profesor($godina_mesec,$broj_grupe,$iid  );
                    $sati = 0;
                    $minuti = 0;
                    $brojac_casova = 0;
                    while ($row_grupa_broj_casova = $stttam->fetch(PDO::FETCH_ASSOC))
                    {
                        $datetime1 = new DateTime($row_grupa_broj_casova['start']);
                        $datetime2 = new DateTime($row_grupa_broj_casova['end']);
                        $interval = $datetime1->diff($datetime2);
                        $sati = $sati + $interval->format('%h');
                        $minuti = $minuti + $interval->format('%i');
                        // echo ++$brojac_casova ,"<br>";
                        // echo $interval->format('%h')." Hours ".$interval->format('%i')." Minuta","|-->";
                        // $sati = $sati + $interval->format('%h');
                        // echo $row_broj_casova['start'],$row_broj_casova['end'],"<br/>";
                        //  var_dump($row_grupa_broj_casova);
                        //  $sati++;
                        if ($minuti >= 60) {
                            $sati += floor($minuti / 60);
                            $minuti = $minuti % 60;
                        }
                    }
                    // $sati1 = $sati + $minuti/60;
                    echo  $sati," sati ", $minuti, " minuta ";
                    $ukupno_sati = $ukupno_sati + $sati;
                    $ukupno_minuti =  $ukupno_minuti + $minuti;


                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
        <tr><td colspan="2" class = "text-center" ><b>&Sigma;</b></td>

            <td class = "text-center">
                <b>
                    <?php echo $broj_djaka_profesor;  ?>
                </b>
            </td>
            <td class="text-center">
                <b>
                    <?php 
                    $pretvoreno_sati = floor($ukupno_sati + $ukupno_minuti / 60); // Zaokruživanje na manji ceo broj
                    $pretvoreni_minuti = $ukupno_minuti % 60; // Ostatak minutii
                    echo " $pretvoreno_sati sati $pretvoreni_minuti minuta";
                    
                    
                    //echo $ukupno_sati;
                     ?>
                </b>
            </td>

        </tr>
    </table>

</div>
    <div class='col-md-4' style='margin-right: 0; border: 1px solid linen;'>
        <h4 style="padding : 10px;">Sve uplate djaka:</h4>
        <?php
        $suma_svih_uplata=0;
        $query_date = $godina_mesec ;
        $mesec_godina = $godina_mesec ;
        $prvi_dan = new DateTime($mesec_godina . '-01');
        $poslednji_dan = new DateTime($prvi_dan->format('Y-m-t'));
        
        // Petlja za prolazak kroz sve dane meseca
        $datum = clone $prvi_dan;
        // while ($datum <= $poslednji_dan) {
        //     echo $datum->format('Y-m-d') . "<br>";
        //     $datum->modify('+1 day');
        // }
        ?>
        <table class="table table-bordered" style=" width: 100%; margin-left:0%; margin-top : 10px;">
            <tr class="text-center"><th class="text-center">Datum:</th><th class="text-center" >Uplate na dan:</th>
         <!--   <th class="text-center" >Predat novac Voyager-u:</th><th class="text-center" >Primljen novac od Voyager-a</th> -->
            </tr>
            <?php  
            while ($datum <= $poslednji_dan) 
            {                
                $dan1 = $datum->format('Y-m-d');
                   //  echo $dan," / " ,$dan1," / ",$iid, "<br/>";
                $stmt = $uplata->sum_all_uplate_na_dan_profesor_prof($dan1, $sve_grupe_profesor, $prof );
                    // var_dump( $stmt);
                    // echo "<hr>";
                    // $suma_uplata = 0;
                $row_uplata = $stmt->fetch(PDO::FETCH_ASSOC);
                // $stmt = $tok_novca->read_one_day_one_profesor($dan1, $iid);
                // $row_category_dnevni_izvestaj = $stmt->fetch(PDO::FETCH_ASSOC);
                if( (isset($row_uplata['iznos']) && $row_uplata['iznos'] > 0 )   )
                { ?>
                    <tr><td align="center" ><a href='dnevni_izvestaj.php?admin2=<?php echo $dan1."/".$iid; ?> '> <?php  echo  $dan1; ?></a> </td>
                        <td align="center" >
                            <?php
                            $suma_svih_uplata = $suma_svih_uplata + $row_uplata['iznos'];
                            echo $row_uplata['iznos'];
                            ?>
                        </td>
                    
                    </tr>
                    <?php
                }
                $datum->modify('+1 day');
            } ?>
            <tr>
                <td class="text-center" ><b>SUM:</b></td>
                <td class="text-center" ><b><?php echo $suma_svih_uplata; ?></b></td>
              
            </tr>
        </table>
    </div>


    <div class='col-md-12' style=' margin: auto;'>
        <p class = "h4"> Analitika plate za ovaj deo meseca:</p>
        <table class="table table-bordered table-hover text-center" style=' margin: auto;' >
            <tr bgcolor="#E6E6E6" style="text-align:center;">
                <th style="text-align:center;">Red. br.</th>
                <th style="text-align:center;">Čas (datum/vreme)</th>
                <th style="text-align:center;">Grupa</th>
                <th style="text-align:center;">Zaduženje</th>
                <th style="text-align:center;">Nadoknada</th>
               
               



            </tr>
            <?php
         //  $ssmmjh = $grupa->read_all_grups_profesor($prof,"grupe");
            $ukupna_suma = 0;
            $ukupna_uplata = 0;
            $ukupno_zaduzenje = 0;
            $ukupna_plata = 0;
            $i=1;
            //$stttam_all_prof_month = $termin->read_all_termin_month_profesor($godina_mesec,$iid);

           
                $stttam_all_prof_month = $termin->read_all_termin_month_profesor($godina_mesec,$iid);
            
                $niz_upisana_zaduzenja_po_kursu = array();
                $suma_zaduzenja_za_mesec = 0;
            while ($row_svi_casovi_month_prof = $stttam_all_prof_month->fetch(PDO::FETCH_ASSOC)) {
                echo"<tr><td>",$i++,"</td>";
                echo "<td>";
                $datetime1_cas = new DateTime($row_svi_casovi_month_prof['start']);
                $datetime2_cas = new DateTime($row_svi_casovi_month_prof['end']);
                $interval_casa = $datetime1_cas->diff($datetime2_cas);
               // $sati = $sati + $interval_casa->format('%h');
               // $minuti = $minuti + $interval_casa->format('%i');
                // echo "sat: ",$interval_casa->format('%h'),"minuta: ",$interval_casa->format('%i'),"<br/>";
                $sum_interval = $interval_casa->format('%h')*60 + $interval_casa->format('%i');
              //  echo $sum_interval;
               
                $datum_za_upis = datum_u_nas_datum(substr($row_svi_casovi_month_prof['start'], 0, 10));
                echo  $datum_za_upis,"&nbsp; od ", substr($row_svi_casovi_month_prof['start'], 11, 5),"&nbsp; do ", substr($row_svi_casovi_month_prof['end'], 11, 5);
                echo "</td><td>";


                 $stmt_grupa = $grupa->read_one_grupa($row_svi_casovi_month_prof['fk_grupa'],"grupe");
                 $row_category_aktulna_grupa = $stmt_grupa->fetch(PDO::FETCH_ASSOC);
                    extract($row_category_aktulna_grupa);
                // echo "nivo=", $nivo;

                $stm_jezik = $jezik->read_one_jezik($fk_jezik,'jezik');
                $row_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC);
                $fk_jezik = $row_jezik['ime'];
            
                $stm_nivo = $nivo_znanja->read_one_nivo($nivo,'nivo_znanja');
                $row_nivo_znanja = $stm_nivo->fetch(PDO::FETCH_ASSOC);
                $nivo = $row_nivo_znanja['ime'];

                if($fk_nacin_zaduzivanja == 1){
                    $nnacin_zad = "po kursu";
                } else{
                    $nnacin_zad = "po času";
                }

                echo "<a href='update_grupa_uvid?id={$id} '>";
                echo $fk_jezik, "&nbsp;", $nivo, "&nbsp;[ ", $alias," ] - <b style='color:black;' >",$nnacin_zad ,"</b></a>";
                echo "</td>";
                echo "<td>";
               
                  //  var_dump($row_svi_casovi_month_prof);
                    $jedan_cas = $row_svi_casovi_month_prof;
                    $osnovno_zaduzenje = $cenovnik->odredi_zaduzenje($row_svi_casovi_month_prof);
                    $stmt_djaci = $djak->read_all_djak_grupa_cas($jedan_cas["fk_grupa"], $jedan_cas['start'], $jedan_cas['id'] );
                    //  var_dump($stmt_djaci);
                    $zaduzenje_od_casa = 0; 
                    while($row_all_djaci_cas = $stmt_djaci->fetch(PDO::FETCH_ASSOC)){
                        $bio_prisutan = intval($row_all_djaci_cas['bio_prisutan']);
                        $opravdao_otsustvo= intval($row_all_djaci_cas['opravdao_otsustvo']);
                       
                      //  echo "<br>",$row_all_djaci_cas['id']," / ",$row_all_djaci_cas['firstname']," ",$row_all_djaci_cas['lastname']," / ",$bio_prisutan," / ",$opravdao_otsustvo," // ";
                        if($jedan_cas['velicina'] == 1 OR $jedan_cas['velicina'] == 2 ){ // individual
                            if($bio_prisutan == 1 OR $opravdao_otsustvo == 0 ){
                                if($jedan_cas['nacin_zaduzivanja_grupe'] == 2 ){
                                    $zaduzenje_od_casa = $zaduzenje_od_casa + $osnovno_zaduzenje['iznos'];
                                }
                                
                            }
                        }else{
                            if($jedan_cas['nacin_zaduzivanja_grupe'] == 2 ){
                                $zaduzenje_od_casa = $zaduzenje_od_casa + $osnovno_zaduzenje['iznos'];
                            }
                        }
                        // po kursu ali samo ako nije zamneski profesor
                        if($jedan_cas['nacin_zaduzivanja_grupe'] == 1 AND ($jedan_cas['fk_profesor'] == null OR  $jedan_cas['fk_profesor'] == 0 OR ($jedan_cas['fk_profesor'] == $jedan_cas['fk_profesor_grupa']) )){
                       // if($jedan_cas['nacin_zaduzivanja_grupe'] == 1 ){
                      
                            if (!in_array($row_all_djaci_cas['id'],  $niz_upisana_zaduzenja_po_kursu)) {
                                $niz_upisana_zaduzenja_po_kursu[] = $row_all_djaci_cas['id'];    
                            // prvo da proverim dal sam vec upisao zaduzenja za taj mesec
                            $stmt_suma_zaduzenja_za_studenta_mesec = $zaduzenje->sum_all_all_zaduzenja_for_student_month($row_all_djaci_cas['id'], $operativni_datum); 
                            $row_all_zaduzenja_po_kursu_mesec_djak = $stmt_suma_zaduzenja_za_studenta_mesec->fetch(PDO::FETCH_ASSOC);
                            //    echo "<br> Zaduzenje po kursu: ";
                            //    var_dump($row_all_zaduzenja_po_kursu_mesec_djak['iznos']);
                            $zaduzenje_od_casa = $zaduzenje_od_casa + $row_all_zaduzenja_po_kursu_mesec_djak['iznos'];
                           // echo "<br>";
                            }
                        }

                    }
                    echo $zaduzenje_od_casa;
                    $suma_zaduzenja_za_mesec = $suma_zaduzenja_za_mesec + $zaduzenje_od_casa;
                
                    echo "</td>";
               
      
                echo"<td>";
                $za_platu_od_casa = 0;

      
                $za_platu_od_casa = $termin->plata($row_svi_casovi_month_prof, $iid);
               echo $za_platu_od_casa; // Ovo se prikazuje
               $ukupna_plata = $ukupna_plata + $za_platu_od_casa;

                echo "</td>";
                
                echo"</tr>";
            }
           ?>
        <tr><td colspan="3">Zarada za navedeni deo meseca</td><td><?=number_format($suma_zaduzenja_za_mesec, 2, '.', ' ') ?></td><td><?php echo number_format($ukupna_plata, 2, '.', ' '); ?></td></tr>    

            
            
         <?php   
        //    var_dump($svi_djaci_gde_je_bilo_ili_uplata_ili_zaduzenja);
            // U niz stavljenis vi djaci koji su imali uplate ili zaduzenja u tekucem mesecu i njihov id je $index
         //   $prolaz = 0;
            
           // }
		/*   $ukupna_uplata88 = $ukupna_uplata/2; 
		   $ukupna_uplata77 =  number_format($ukupna_uplata88, 2, '.', ' ');
		   $ukupno_zaduzenje77 = number_format($ukupno_zaduzenje/2, 2, '.', ' ');
		   $ukupna_plata77 = number_format($ukupna_plata/2, 2, '.', ' '); */
            ?>
        </table>

    </div>



</div>




