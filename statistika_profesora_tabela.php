<?php
error_reporting(E_ALL);
ini_set("display_errors",1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once "config/core.php";
include_once "login_checker.php";
include_once "config/database.php";
include_once "config/autoload.php";

$database = new Database();
$db = $database->getConnection();

$iid = $_SESSION['user_id'];

$od = $_POST['od'] ?? '';
$do = $_POST['do'] ?? '';

if(!$od || !$do){
    echo "<div class='alert alert-warning'>Niste odabrali interval.</div>";
    exit;
}


// include page header HTML
//include_once "layout_head2.php";
// get database connection
// $database = new Database();
// $db = $database->getConnection();
$profesor = new user($db);
$grupa = new grupa($db);
$povezivanje = new povezivanje($db);
$djak = new djak($db);
$zaduzenje = new zaduzenje($db);
$uplata = new uplata($db);
$termin = new kalendar($db);
$cenovnik = new cenovnik($db);
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



        <table class="table table-bordered table-hover table-striped text-center" style=" width: 100%; margin-top : 30px;">
            <tr>
                <th class="text-center">Mesec:</th>
                <th class="text-center">Broj grupa:</th>
                <th class="text-center">Broj djaka:</th>
                <th class="text-center">Održanih časova</th>
                <th>Skup zaduženja za djake </th>
                <th class="text-center">Skup uplata:</th>
                <th class="text-center">Plata profesora: </th>
                <!-- <th>Dosad isplaćeno:</th><th>Mes. Saldo:</th><th>Kumulativ. saldo:</th> -->
            </tr>
            <?php
            $i = date("Ym", strtotime($od));
            $doYm = date("Ym", strtotime($do));
            $ukupan_saldo = 0;
            $kumulaticni_saldo = 0;
            $ddd = 0;
            while($i <= date("Ym", strtotime($do)))
            {
                $ddd++;
                $mesec = substr($i,-2,2);
                $godina = substr($i,0,4);
                $operativni_datum = $godina."-".$mesec;
                //echo $operativni_datum;
                $stttam1 = $termin->read_all_termin_month_profesor($operativni_datum,$iid); //svi termini profesora za taj mesec 1. deo
                $row_all_broj_casova = $stttam1->fetchall(PDO::FETCH_ASSOC);
                //  $stttam2 = $termin->read_all_termin_month_profesor_2($operativni_datum,$iid); //svi termini profesora za taj mesec
                $bg_color = $ddd % 2 === 0 ? "#FFFFFF" : "#E6E6E6";
                ?>
                <tr>
                    <td class="text-center"><a
                            href='mesecna_statistika_profesor.php?admin=<?=$operativni_datum."_".$iid."_1"; ?>'>
                            <?php  echo $mesec,".",$godina; ?> </a></td>
                    <?php
                        $sati=0;
                        $minuti=0;
                        // brojimo casove za svaki mesec
                        $sve_grupe_profesor= array();
                        $broj_djaka_profesor = 0;
                        $sati =0;
                        foreach ($row_all_broj_casova as $row_broj_casova ) // brojim grupe
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
                                            
                        }
                    
                        ?>
                    <td class="text-center"><?php echo  /* var_dump($sve_grupe_profesor), */ count( $sve_grupe_profesor); ?>
                    </td>
                    <td class="text-center"><?php echo $broj_djaka_profesor; ?></td>
                    <td class="text-center"><?php echo $sati; ?></td>
                    <td>
                        <?php 
                        $niz_upisana_zaduzenja_po_kursu = array();
                        // $id_djaka_array = array_column($$upisana_zaduzenja_po_kursu, 'id_djaka');
                        // $mesec_array = array_column($upisana_zaduzenja_po_kursu, 'mesec');
                        $suma_zaduzenja_za_mesec = 0; 
                        $suma_za_platu_po_zaduzenju = 0;
                        foreach ($row_all_broj_casova as $jedan_cas ) // brojim grupe
                        {
                        //  var_dump($jedan_cas);
                            // echo "<br>Id grupe: ",$jedan_cas['fk_grupa'];
                            // echo "<br>Nacin zaduzivanja: ",$jedan_cas['nacin_zaduzivanja_grupe'];
                            // echo"<hr>";
                        
                            $osnovno_zaduzenje = $cenovnik->odredi_zaduzenje($jedan_cas);
                            // var_dump($osnovno_zaduzenje);
                            // echo "<br> za jedan cas", $osnovno_zaduzenje['iznos'], "<br>";
                            // echo"<hr>";
                            // svi djaci koji u toj grupi u tom trenutku
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
                                            $zaduzenje_od_casa = $zaduzenje_od_casa + $osnovno_zaduzenje['iznos'] ?? 0;
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
                        //  echo "<br>Stvarno zaduzenje od casa: ", $zaduzenje_od_casa,"<br>";
                            // procenat za profesora:
                        //  $procenat_za_platu = $termin->procenat_za_platu( $jedan_cas, $iid);
                        //  echo "<br> Procenat od ovog casa: ",$procenat_za_platu;
                        //  echo "<br> Za platu: ",$zaduzenje_od_casa * $procenat_za_platu/100;    
                        // var_dump($row_all_djaci_cas);
                        //  $suma_za_platu_po_zaduzenju = $suma_za_platu_po_zaduzenju + $zaduzenje_od_casa * $procenat_za_platu/100 ;
                        // echo "<br> suma Za platu: ",$suma_za_platu_po_zaduzenju;    
                        $suma_zaduzenja_za_mesec = $suma_zaduzenja_za_mesec + $zaduzenje_od_casa;
                        // echo "<br> suma zaduzenja: ",$suma_zaduzenja_za_mesec;  
                        // echo"<b><hr><hr></b>";
                        // echo "<div style='border-top: 3px solid #000; margin: 20px 0;'></div>";
                        }
                        //  echo "<pre>";
                        //  var_dump($niz_upisana_zaduzenja_po_kursu);
                        //  echo "</pre>";
                        echo number_format($suma_zaduzenja_za_mesec, 2, ',', ' '); 
                        
                        ?>
                    </td>
                
                    <td class="text-center">
                        <?php
                        // Sve uplate za grupe koje drzi profesor sabiramo
                        $niz_djaci_da_se_ne_ponavljaju = array();
                        $suma_uplata_djaka_mesec_prof = 0;
                        $suma_za_platu_od_uplata = 0;
                        foreach ($row_all_broj_casova as $jedan_cas ){ // brojim grupe
                            $stmt_djaci = $djak->read_all_djak_grupa_cas($jedan_cas["fk_grupa"], $jedan_cas['start'], $jedan_cas['id'] );
                            while($row_all_djaci_cas = $stmt_djaci->fetch(PDO::FETCH_ASSOC)){
                            
                                if (!in_array($row_all_djaci_cas['id'],  $niz_djaci_da_se_ne_ponavljaju)  ) {
                                if( ($jedan_cas['nacin_zaduzivanja_grupe'] == 1 AND ($jedan_cas['fk_profesor'] == null OR  $jedan_cas['fk_profesor'] == 0 OR ($jedan_cas['fk_profesor'] == $jedan_cas['fk_profesor_grupa']) )) OR ($jedan_cas['nacin_zaduzivanja_grupe'] == 2) ){
                                        // echo "<br>",$jedan_cas['id'],"/",$row_all_djaci_cas['id']," / ",$row_all_djaci_cas['firstname']," ",$row_all_djaci_cas['lastname'], " // ";
                                        $niz_djaci_da_se_ne_ponavljaju[] = $row_all_djaci_cas['id'];   
                                    //  $stmt_uplata_djak_mesec = $uplata->sum_all_for_student_interval_mesec($row_all_djaci_cas['id'], $jedan_cas["fk_grupa"], $operativni_datum); 
                                        $stmt_uplata_djak_mesec = $uplata->sum_all_for_student_prof_interval_mesec($row_all_djaci_cas['id'], $jedan_cas["fk_grupa"], $operativni_datum, $iid); 
                                    
                                        $suma_uplata_taj_djak = $stmt_uplata_djak_mesec->fetch(PDO::FETCH_ASSOC);
                                        // echo "Uplatio: ", $suma_uplata_taj_djak['suma_iznosa'] ;
                                        $suma_uplata_djaka_mesec_prof = $suma_uplata_djaka_mesec_prof + $suma_uplata_taj_djak['suma_iznosa'];
                                        // $procenat_za_platu = $termin->procenat_za_platu( $jedan_cas, $iid);
                                        //  $iznos_za_platu_djak =  $suma_uplata_taj_djak['suma_iznosa'] * $procenat_za_platu/100;
                                        //  $suma_za_platu_od_uplata = $suma_za_platu_od_uplata + $iznos_za_platu_djak;
                                    // echo " %: ",$procenat_za_platu;
                                }
                                }
                                
                            }
                        } 
                    //  echo "<hr>";
                        echo number_format($suma_uplata_djaka_mesec_prof, 2, ',', ' ');    
                    ?>
                    </td>

                    <td class="text-center">
                        <?php
                            $suma_za_isplatu_profesoru_mesec =  0;
                            foreach ($row_all_broj_casova as $jedan_cas ){ 
                                $suma_sa_jednog_casa = $termin->plata($jedan_cas, $iid);
                                //echo $suma_sa_jednog_casa, "/";
                                $suma_za_isplatu_profesoru_mesec = $suma_za_isplatu_profesoru_mesec + $suma_sa_jednog_casa;
                            } 
                            echo number_format($suma_za_isplatu_profesoru_mesec, 2, ',', ' ');    
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

include_once "layout_foot.php";
?>
    </div>
</body>

</html>