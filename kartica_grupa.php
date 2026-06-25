<html>
<head>
<title>Fuinansijska kartica grupe</title>
    <link rel="shortcut icon" href="images/kalen.png">
</head>
<body>
<?php
error_reporting(E_ALL);
ini_set("display_errors",1);
ini_set('memory_limit', '-1');
/**
 * Created by PhpStorm.
 * User: goran
 * Date: 25.10.2019
 * Time: 12:36
 */
if(isset($_GET['id'])){$iid = $_GET['id']; //echo "Grupa je br: ", $iid;
}

// core configuration
include_once "config/core.php";

// set page title
$page_title = "<tab style='color:red;'>Finasije studentske grupe:</tab>";

// include login checker
include_once "login_checker.php";

// include classes
include_once 'config/database.php';
include_once 'config/funkcije.php';
include_once 'config/autoload.php';

// include_once 'objects/user.php';
// include_once 'objects/grupa.php';
// include_once 'objects/djak.php';
// include_once "libs/php/utils.php";
// include_once 'objects/povezivanje.php';
// include_once 'objects/zaduzenje.php';
// include_once 'objects/uplata.php';
// include_once 'objects/jezik.php';
// include_once 'objects/nivo_znanja.php';


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
$jezik = new jezik($db);
$nivo_znanja = new nivo_znanja($db);
// initialize objects
//$user = new User($db);
$stmt = $grupa->read_one_grupa($iid,"grupe");
$row_category_grupa = $stmt->fetch(PDO::FETCH_ASSOC);
extract($row_category_grupa);

echo "<div class='col-md-12'>";



?>

    <form action='update_grupa.php?id=<?php echo $id; ?>' method='post' >
        <h3 style='margin-left: 5%;'>Kartica grupe:</h3>

        <table class='table table-responsive' style='width:90%; margin: auto;'>

            <tr>
            <?php 
            $fk_jezik = $row_category_grupa['fk_jezik'];
            $fk_profesor = $row_category_grupa['fk_profesor'];
            //echo  'Fk profesor: ', $fk_profesor,"<br/>";
            $stm_jezik = $jezik->read_one_jezik($fk_jezik,'jezik');
            $row_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC);
            $nivo = $row_category_grupa['nivo'];
            $stm_nivo = $nivo_znanja->read_one_nivo($nivo,'nivo_znanja');
            $row_nivo_znanja = $stm_nivo->fetch(PDO::FETCH_ASSOC);
            
            $fk_jezik = $row_jezik['ime'];
            $nivo = $row_nivo_znanja['ime'];
            
            ?>
                <td class='width-20-percent'>Jezik: <?=$fk_jezik ?></td>
                <td>

                        <?php
                       
                        echo "&nbsp;&nbsp; Nivo: ",$nivo;
                        $nndj = $profesor->read_name_user($fk_profesor,"users");
                        $row_category_profesor = $nndj->fetch(PDO::FETCH_ASSOC); ?>
                </td><td>
                <?php         
                        echo "&nbsp;&nbsp;", $row_category_profesor['firstname'],"&nbsp;",$row_category_profesor['lastname'];
                        echo "&nbsp;&nbsp; [ ", $alias, " ]";
                        ?>
                </td>
            </tr>
            <tr>
                <td>Komentar:</td>
                <td colspan='2'><textarea name='komentar' readonly class='form-control' required> <?php echo $row_category_grupa['komentar'];  ?></textarea></td>
            </tr>
        </table>
    </form>
</div>

<div class='col-md-12'>
<table class="table table-bordered table-hover text-center" style='width:90%; margin: auto;'>
        <tr bgcolor="#BDBDBD" style="text-align:center;">
            <th style="text-align:center;">Ime i prezime</th>
            <th style="text-align:center;">U grupi je od</th>
            <th style="text-align:center;">Sva zaduženja</th>
            <th style="text-align:center;">Sve uplate</th>
            <th style="text-align:center;">Saldo djaka</th>


        </tr>
<?php
$sts = $povezivanje->read_all_students($iid);
$ukupna_suma = 0;
while ($row_category_djaci = $sts->fetch(PDO::FETCH_ASSOC))
{
    echo"<tr>";
    $indeks = $row_category_djaci['fk_djak'];
    $ssts = $djak->read_one_djak("djaci", $indeks );
    //echo $row_category_djaci['fk_djak'],  "<br/>";
    $row_category_detalj_djaci = $ssts->fetch(PDO::FETCH_ASSOC);
    if($row_category_detalj_djaci['status'] == 1) {

        echo "<td><a href='kartica_djak.php?id={$row_category_detalj_djaci['id']} '>",$row_category_detalj_djaci['firstname'], "&nbsp;", $row_category_detalj_djaci['lastname'],"</a></td>";

        $sskz = $povezivanje->read_one_student_one_grup($iid, $indeks);
        $row_category_otkadjeugrupi = $sskz->fetch(PDO::FETCH_ASSOC);
        $row_category_otkadjeugrupi['created'] = vreme_u_nase_vreme($row_category_otkadjeugrupi['created']);
        echo "<td>",$row_category_otkadjeugrupi['created'],"</td>";
        //Zbrajamo sva aktivna zaduzenja za djaka
        $zaduzenje_djak=0;
        $drmk = $zaduzenje->read_all_iznos_for_student_do_danas($indeks, $iid );
        while ($row_category_medjusuma = $drmk->fetch(PDO::FETCH_ASSOC)){
            $zaduzenje_djak = $zaduzenje_djak + $row_category_medjusuma['iznos'];
        }
        echo "<td>",$zaduzenje_djak,"</td>";
        // zbrajamo sve uplate djaka
        $uplate_djak = 0;
        $ddjkm = $uplata->read_all_for_student_do_danas($indeks, $iid );
        while ($row_category_medjusuma_uplate = $ddjkm->fetch(PDO::FETCH_ASSOC)){
            $uplate_djak = $uplate_djak + $row_category_medjusuma_uplate['iznos'];
        }
        echo "<td>",$uplate_djak,"</td>";
        // saldo djaka
        $saldo_djak = $uplate_djak - $zaduzenje_djak; ?>
        <td <?php if($saldo_djak < 0 ){echo"style='color:red;'";} ?>><?php echo $saldo_djak; ?></td>
        <?php
       $ukupna_suma = $ukupna_suma + $saldo_djak;
    }
    echo"</tr>";
}
?>
<tr><td colspan="4" align="right">Ukupan saldo grupe: </td><td <?php if($ukupna_suma < 0 ){echo"style='color:red;'";} ?>  ><?php echo $ukupna_suma ?></td></tr>
</table>



</div>



<?php
// include page footer HTML
include_once "layout_foot.php";
?>
</body>
</html>
