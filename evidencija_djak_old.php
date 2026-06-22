
<?php
if(isset($_GET['id'])){$idd = $_GET['id']; // echo "Radi se o djaku br:" , $idd;
}

error_reporting(E_ALL);
ini_set("display_errors",1);
ini_set('memory_limit', '-1');
//echo "broj=",$idd,"<br/>";
// core configuration
include_once "config/core.php";
//include_once "admin/login_checker.php";

// set page title
$page_title = "Evidencija dolaska na nastavu za djaka";

// include login checker
 include_once "login_checker.php";


// include classes
include_once 'config/database.php';
include_once 'config/autoload.php';
// include_once 'objects/user.php';
// include_once 'objects/kalendar.php';
include_once 'config/funkcije.php';

// include_once 'objects/djak.php';
// include_once 'objects/grupa.php';
// include_once 'objects/povezivanje.php';
// include_once 'objects/evidencija.php';
// include_once 'objects/jezik.php';
// include_once 'objects/nivo_znanja.php';

// include page header HTML
//include_once "layout_head2.php";

//$id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');


$database = new Database();
$db = $database->getConnection();
$profesor = new user($db);
$djak = new djak($db);
$termin = new kalendar($db);
$grupa = new grupa($db);
$jezik = new jezik($db);
$nivo_znanja = new nivo_znanja($db);
$povezivanje = new povezivanje($db);
$evidencija = new evidencija($db);
$stmt = $djak->read_one_djak("djaci", $idd);
$row_category_aktuelni_djak = $stmt->fetch(PDO::FETCH_ASSOC);
extract($row_category_aktuelni_djak);
?>
<div class='row' id='glavni1' >
<div class='row' style="border-style: solid; border-color: #F2F2F2; margin-left: 6px; " id='glavni' >
    <div class="col-md-7">
        <h5 class="text-danger" style=' padding: 10px;' style="display : inline-block" >Evidencija dolaska na nastavu za djaka: <?php echo $firstname,"&nbsp;",$lastname; ?> </h5>
    </div>        
    <div class="col-md-4">
        <button style="display : none" class="btn btn-danger  pull-right" ><a style="color:white;" href = "kartica_djak.php?id=<?php echo $idd; ?> " target='_blank'>Finasijska kartica djaka</a></button>
    </div>
</div>
<div class='col-md-12' style="margin-top : 20px;" >
    <table class="table table-bordered">
    <tr ><th class="text-center" >Datum i vreme</th><th class="text-center" >grupa</th><th class="text-center" >prisutan</th><th class="text-center" >Evidentirao</th><th>Komentar djak</th><th>Komentar čas</th></tr>
    <?php  $tstmttt = $evidencija->read_all_prisustvo_djak($idd);
    while($row_category_sva_prisustva = $tstmttt->fetch(PDO::FETCH_ASSOC)){

         $termin->readOne($row_category_sva_prisustva['termin_id']);
      //  $row_category_termin = $smjka->fetch(PDO::FETCH_ASSOC);

       // echo $row_category_sva_prisustva['prisutan'],"/",$row_category_sva_prisustva['termin_id'],"/",$row_category_sva_prisustva['evidentirao'],"/",$row_category_sva_prisustva['evidentirano'],"<br/>";
    ?>
    <tr>
        <td class="text-center">
           <!-- <a href='dogadjaj.php?id=<?php echo $row_category_sva_prisustva['termin_id']; ?>  '  target="_blank"> -->
            <a onclick="open_in_new_tab_and_reload('dogadjaj.php?id=<?=$row_category_sva_prisustva['termin_id'] ?>')" href="#">
            <script>
            function open_in_new_tab_and_reload(url)
            {
            //Open in new tab
            window.open(url, '_blank');
            //focus to thet window
            window.focus();
            //reload current page
            location.reload();
            }
            </script>
            <?php
                $datum = substr($termin->start,0,10);
                $datum = datum_u_nas_datum($datum);

                $broj_casova = $termin->count_broj_casova_grupa_vreme($termin->fk_grupa,$termin->start);

                $od = substr($termin->start, -8,5);
                $do = substr($termin->kraj, -8,5);
                echo $datum,"<br/>",$od,"&nbsp;-&nbsp;", $do,"<br/><tab style='color:purple;'><b> Cas po redu: ",$broj_casova+1 ,"</b></tab>";
                ?>
            </a>
        </td>
        <td class="text-center">
            <?php
            $stmm = $grupa->read_one_grupa1($termin->fk_grupa);
            $row_category_grupa = $stmm->fetch(PDO::FETCH_ASSOC);
            $fk_jezik = $row_category_grupa['fk_jezik'];
            $fk_profesor = $row_category_grupa['fk_profesor'];
            $nivo = $row_category_grupa['nivo'];
            $alias = $row_category_grupa['alias'];
            //echo $fk_profesor;
            $stm_jezik = $jezik->read_one_jezik($fk_jezik,'jezik');
            $row_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC);
            $fk_jezik = $row_jezik['alias']; 

            $stm_nivo = $nivo_znanja->read_one_nivo($nivo,'nivo_znanja');
            $row_nivo_znanja = $stm_nivo->fetch(PDO::FETCH_ASSOC);
            $nivo = $row_nivo_znanja['ime'];
            echo $fk_jezik,"&nbsp;",$nivo,"<br/>",$alias;


          
            ?>
        </td>
        <td class="text-center">
            <input type="checkbox"    <?php  if(isset($row_category_sva_prisustva['prisutan']) && $row_category_sva_prisustva['prisutan'] == 1) {echo "checked"; }  ?> >
        </td>
        <td class="text-center">
            <?php
            $bnbsp = $profesor->read_name_user($row_category_sva_prisustva['evidentirao'],"users");
            $row_category_user = $bnbsp->fetch(PDO::FETCH_ASSOC);
            $row_category_sva_prisustva['evidentirano'] = vreme_u_nase_vreme($row_category_sva_prisustva['evidentirano']);
                echo $row_category_user['firstname'],"&nbsp;",$row_category_user['lastname'],"<br/>",$row_category_sva_prisustva['evidentirano'];
            ?>
        </td>
        <td style='width: 25%;'><?=$row_category_sva_prisustva['komentar'] ?></td>
        <td style='width: 25%;'><?=$termin->komentar ?></td>
    </tr>
        <?php
    }


    ?>

</table>
</div>
</div>

<?php
include_once "layout_foot.php";

?>