<head>
<title>Detalji casa</title>
<link rel="shortcut icon" href="images/kalen.png">
    <link rel="stylesheet" type="text/css" href="tigrakal/tcal.css" />
    <script type="text/javascript" src="tigrakal/tcal.js"></script>
    <script src="assets/js/jquery.min.js"></script>
    <!-- make sure the src path points to your copied ckeditor folder -->
    <style>
        .checkbox-lg {
    width: 28px;
    height: 28px;
    cursor: pointer;
}
    </style>

</head>
<?php
 if(isset($_GET['id'])){
    $idd = $_GET['id']; // echo "Radi se o terminu br:" , $idd;
    $termin_id = $idd; 
 }

error_reporting(E_ALL);
ini_set("display_errors",1);
ini_set('memory_limit', '-1');
//echo "broj=",$idd,"<br/>";
// core configuration
include_once "config/core.php";
//include_once "admin/login_checker.php";

// set page title
$page_title = "Detalji termina";

// include login checker
if( $_SESSION['access_level'] == "Customer"){
    include_once "login_checker.php";
} else { include_once "admin/login_checker.php";}


// include classes
include_once 'config/database.php';
include_once 'config/autoload.php';
// include_once 'objects/user.php';
// include_once 'objects/kalendar.php';
// include_once "libs/php/utils.php";
// include_once 'objects/djak.php';
// include_once 'objects/grupa.php';
// include_once 'objects/povezivanje.php';
// include_once 'objects/evidencija.php';
// include_once 'objects/jezik.php';
// include_once 'objects/nivo_znanja.php';
// include_once 'objects/ucionica.php';

// include page header HTML
//include_once "layout_head.php";
if($_SESSION['access_level']  == "Customer"){
    //include_once "layout_head2.php";
}else{ 
    // include_once "admin/layout_head4.php";
    }

//$id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: missing ID.');


$database = new Database();
$db = $database->getConnection();
$profesor = new user($db);
$djak = new djak($db);
$termin = new kalendar($db);
$grupa = new grupa($db);
$povezivanje = new povezivanje($db);
$evidencija = new evidencija($db);
$jezik = new jezik($db);
$nivo_znanja = new nivo_znanja($db);
$ucionica = new ucionica($db);
$program_rada_nastavna_jedinica = new program_rada_nastavna_jedinica($db);
$program_rada = new  program_rada($db);

$termin->readOne($idd);
// echo "Grupa je: ",$termin->fk_grupa;


echo "<div class='row' id='glavni1'>";

if($_SESSION['access_level'] != "Customer"){
   
}
echo"<h3> <u> Detalji termina: </u> </h3>";
 if($termin->fk_grupa == 9177)
    {
       
        if(isset($_POST['slobodan'])){
                  // $utils = new Utils();
                    $termin->komentar = $_POST['komentar'];
                    $termin->status = $_POST['status'];
                    $termin->color = "#DBA901";
                    // create the user
                    if ($termin->update_slobodan_termin($idd)) {
                        echo "<div class='alert alert-info'>";
                        echo "Uspesno ste promenili stavke termina";
                        echo "</div>";

                        // empty posted values
                        $_POST = array();
                        // echo '<script>window.parent.opener.location.reload();</script>';
                        echo "<script>window.close();</script>";
                    } else {
                        echo "<div class='alert alert-danger' role='alert' > Doslo je do greske.</div>";
                    }

        }
        ?>
        <form action="dogadjaj.php?id=<?php echo $idd; ?>" method="post" >
            <table class='table table-responsive'>

                    <td>Komentar:</td>
                    <td><textarea name='komentar' class='form-control'><?php echo $termin->komentar; ?></textarea></td>
                </tr>
               

                <tr>

                    <td>Dana:</td>
                    <td>
                        <table style="border-spacing: 15px; border-collapse: separate;">
                            <tr>
                                <td style="color:#585858;">
                                    <?php
                                    $vreme = $termin->start;
                                    $datum = substr($vreme, 0, 10);

                                    $p77 = explode("-", $datum);
                                    $datum = $p77[2] . "." . $p77[1] . "." . $p77[0];
                                    echo $datum;

                                    ?>
                                </td>


                                <td style="color:#585858;">OD:&nbsp;
                                    <?php
                                    $sati_min_od = substr($vreme, 11, 5);
                                    $vreme_do = $termin->kraj;
                                    $sati_min_do = substr($vreme_do, 11, 5);
                                    echo $sati_min_od;

                                    ?>
                                </td>

                                <td style="color:#585858;">DO:&nbsp;
                                    <?php echo $sati_min_do; ?>
                                </td>

                            </tr>
                        </table>
                    </td>

                </tr>
                
                <tr>
                    <td>Status termina:</td>

                    <td>
                        <select class='form-control' name='status'>
                            <option value=1 <?php if ($termin->status == 1){echo "selected";} ?> >U kalendaru</option>
                            <option value=4 <?php if ($termin->status == 4){echo "selected";} ?> >Obriši ga</option>
                        </select>
                    </td>
                    </td>
                </tr>

                <tr class="blank_row">
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <button type="submit" name="slobodan"  value="slobodan" class="btn btn-primary">
                            <span class="glyphicon glyphicon-plus"></span>Snimi i izadji
                        </button>
                    </td>
                </tr>

            </table>

        </form>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
      
        <?php

       


    }else {
        $stmm = $grupa->read_one_grupa1($termin->fk_grupa);
        $row_category_grupa = $stmm->fetch(PDO::FETCH_ASSOC);
        $fk_jezik = $row_category_grupa['fk_jezik'];
        $fk_profesor = $termin->fk_profesor_grupa;

        //var_dump($row_category_grupa);

        //echo  'Fk profesor: ', $fk_profesor,"<br/>";
        $stm_jezik = $jezik->read_one_jezik($fk_jezik,'jezik');
        $row_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC);
        $nivo = $row_category_grupa['nivo'];
          $fk_program_rada = $row_category_grupa['fk_program_rada'];
        $stm_nivo = $nivo_znanja->read_one_nivo($nivo,'nivo_znanja');
        $row_nivo_znanja = $stm_nivo->fetch(PDO::FETCH_ASSOC);

        $fk_jezik = $row_jezik['ime'];
        $nivo = $row_nivo_znanja['ime'];
        
        $broj_casova = $termin->count_broj_casova_grupa_vreme($row_category_grupa['id'],$termin->start);
        //echo "broj casova: ",$broj_casova;
        //echo "profesor je id: ",$row_category_grupa['fk_profesor'];
        if($_SESSION['access_level'] == "Customer" && $_SESSION['user_id'] != $fk_profesor and $_SESSION['user_id'] != $termin->fk_profesor){
            echo "ovo ne bi trebalo da se vidi";
            echo"<script> alert('Nemate privilegiju da vidite ovaj sadrzaj !!!'); window.close(); </script>"; 
            exit;  
        }

        ?>

        <table class='table table-hover table-responsive table-bordered'>
        <tr><th>Jezik: <?=$fk_jezik ?></th><th>Nivo: <?= $nivo ?></th><th>Grupa: <?=$row_category_grupa['alias'] ?></th>
        <th>Redni broj časa: <?=$broj_casova+1 ?></th>
             <th>Plan rada: 
            <?php 
                if($fk_program_rada == null || $fk_program_rada == 0 ){
                    echo "Nema"; 
                }else{
                    $stmt_program_rada = $program_rada->read_one($fk_program_rada);
                    $row_category_program_rada = $stmt_program_rada->fetch(PDO::FETCH_ASSOC);
                    echo $row_category_program_rada['ime'] ?? "";
                } 
                ?>
        </th>
        </tr>
        </table>
             <?php 
            if($fk_program_rada == null || $fk_program_rada == 0 ){
                
            }else{?>
                <div class="col-md-12" style='background-color: #f6f4f3; padding:15px; ' >
                    <div class="col-md-3">
                        Plan rada:
                    </div>
                    
                    
                        <div class="col-md-10">
                            <div class="row">    
                                <?php 
                                    $stmt_program_rada_nastavna_jedinica = $program_rada_nastavna_jedinica->read_nekoliko($broj_casova+1, $fk_program_rada);
                                 //var_dump($stmt_program_rada_nastavna_jedinica);
                                    $row_category_program_rada_nastavna_jedinica = $stmt_program_rada_nastavna_jedinica->fetchall(PDO::FETCH_ASSOC);
                                // echo "<pre>",var_dump($row_category_program_rada_nastavna_jedinica ),"</pre>";
                                    if($row_category_program_rada_nastavna_jedinica !== []){
                                        foreach($row_category_program_rada_nastavna_jedinica  as $nastavna_jedinica){?>

                                            <div class="col-md-4" style=' border-radius : 5px;  padding:3px;'>
                                                <textarea class='form-control' readonly style="width:100%;" ><?php echo "Čas redni br: ",$nastavna_jedinica['redni_br'],"\n", $nastavna_jedinica['nastavna_jedinica'] ?></textarea>
                                            </div>
                                            <!-- <div class="col-md-1"></div>  -->
                                            <?php
                                        }
                                    }
                                
                                ?>
                            </div>
                        </div>        
                    <div class="col-md-9">

                    </div>
                    <?php if($row_category_program_rada_nastavna_jedinica !== []){ ?>
                    <div class="col-md-3">
                        <button id="toggle-btn" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye"></i> Vidi sve
                    </button> 
                    </div>
                    <?php } ?>
                    <div class="col-md-12" style="margin-top: 15px;">           
                        <div class="ekstenzija" style='display:none; margin-bottom:10px !important; padding:0px; border:none;' >
                            <!-- Ovo je sadržaj div-a koji će se otvarati/zatvarati. -->
                            <div class="row">
                            <?php 
                                $stmt_program_rada_nastavna_jedinica_do_kraja = $program_rada_nastavna_jedinica->read_nekoliko_do_kraja($broj_casova+2, $fk_program_rada);
                            // var_dump($stmt_program_rada_nastavna_jedinica_do_kraja);
                                $row_category_program_rada_nastavna_jedinica_do_kraja = $stmt_program_rada_nastavna_jedinica_do_kraja->fetchall(PDO::FETCH_ASSOC);
                                //  echo "<pre>",var_dump($row_category_program_rada_nastavna_jedinica_do_kraja ),"</pre>";
                                foreach($row_category_program_rada_nastavna_jedinica_do_kraja  as $nastavna_jedinica_do_kraja){?>
                                    <div class="col-md-3" style=' height: 60px !important; '>
                                        <div class="col-md-12" style='padding: 5px; border-radius : 5px; width: 100%; margin-left: 2%; height: 85%; margin-bottom:10%;   background-color: white; border: solid 1px red;' >
                                            <?php echo "Čas redni br: ",$nastavna_jedinica_do_kraja['redni_br'],"<br>", $nastavna_jedinica_do_kraja['nastavna_jedinica'] ?>
                                        </div>
                                        <!-- <textarea class='form-control' style=' height: 300px !important;' rows="3" readonly style="width:100%;" ></textarea> -->
                                    
                                    
                                    </div>
                                    <!-- <div class="col-md-1"></div>  -->
                                    <?php
                                }
                            
                            
                            ?>
                            </div>
                        </div>
                    </div>
                    <script>
                    $(document).ready(function() {
                    $('#toggle-btn').click(function() {
                        $('.ekstenzija').toggle();
                    });
                    });
                    </script>

                    
                </div>
                    <?php 
            }
        ?>
        <div class='col-md-12'>
            <?php 
        
            if(isset($_POST['skola'])){
                  //  $utils = new Utils();
                    // set values to object properties
                    //  $termin->profesor=$_POST['profesor'];
                    $termin->ucionica = $_POST['ucionica'] ?? 2000;
                    $termin->komentar = $_POST['komentar'];
                    $termin->status = $_POST['status'];
                    $status_termina = $termin->status;
                    if($termin->status != 2){
                        $termin->fk_profesor_grupa = $row_category_grupa['fk_profesor'];
                    }
                   
                    
                    
                    $stmt_ucionica = $ucionica->read_one_ucionica1($termin->ucionica);
                    $row_ucionica = $stmt_ucionica->fetch(PDO::FETCH_ASSOC);
                    echo "termin-profesor1: ",$termin->fk_profesor,"<br/>";
                    $termin->fk_profesor = $_POST['fk_profesor'] ?? null ; 
                    echo "termin-profesor2: ",$termin->fk_profesor,"<br/>";

                    if($termin->fk_profesor == null || $termin->fk_profesor == 0 ){
                        $predavac = $profesor->read_one_profesor($termin->fk_profesor_grupa, "users");
                    }else{
                        $predavac = $profesor->read_one_profesor($termin->fk_profesor, "users");
                    }
                    $row_category_predavac = $predavac->fetch(PDO::FETCH_ASSOC);
                     

                    if(  $row_category_predavac['id'] != $fk_profesor ){
                        if($_SESSION['access_level'] != "Customer"){
                            $termin->color =  $row_category_predavac['color_prof'];
                        }else{
                            $termin->color = $_POST['color'];    
                        }
                    
                    }else{
                        $termin->color = $_POST['color'];    
                    }
                
                    
                    
                     // $termin->zoom_skype = $_POST['zoom_skype'];
                    if(isset($_POST['zoom_skype'])){
                        $termin->zoom_skype = $_POST['zoom_skype'];
                    }else{
                        $termin->zoom_skype = 0; 
                    }

                   
                    if ($termin->status == 3) {
                        $termin->color = '#BDBDBD';
                    }

                    // periodicno brisanje

                    if(isset($_POST['vrsta_brisanja22']) && $_POST['vrsta_brisanja22'] == 2  ){
                        $datum_od = $_POST['pocetak_brisanja_datum'];
                        $datum_do = $_POST['kraj_brisanja_datum'];
                       
                        $pocetak_brisanja_datum = date("Y-m-d", strtotime($datum_od));
                        $kraj_brisanja_datum = date("Y-m-d", strtotime($datum_do));
                        $grupa_za_brisanje =  $termin->fk_grupa;

                        $stmt_brisi_termine_grupa_interval = $termin->brisi_termine_grupa_interval($pocetak_brisanja_datum, $kraj_brisanja_datum , $grupa_za_brisanje); 

                    }

                    // var_dump($_SESSION);
                    if($_SESSION['access_level'] == 'Customer'){
                        if ($termin->update_termin_profesor($idd)) {
                            $evidencija->termin_id = $idd;
                            $evidencija->evidentirao = $_SESSION['user_id'] ;
                            $ssttmp = $povezivanje->read_all_students($termin->fk_grupa);
                            //$prisutan = array();
                            $index_komentar = 0;
                            //  var_dump($_POST['prisutan'] ?? null);
                            $prisutni = $_POST['prisutan'] ?? array();
                            $opravdao_otsustvo = $_POST['opravdao_otsustvo'] ?? array();
                            var_dump($opravdao_otsustvo );
                            while ($row_category_djaci = $ssttmp->fetch(PDO::FETCH_ASSOC)) {
                                if (in_array($row_category_djaci['fk_djak'], $prisutni)) {
                                //  echo $row_category_djaci['fk_djak'], "<br/>";
                                    $evidencija->prisutan = TRUE;
                                } else {
                                    $evidencija->prisutan = FALSE;
                                }
                                $evidencija->djak_id = $row_category_djaci['fk_djak'];
                                
                                $evidencija->komentar = $_POST['komentar1'][$index_komentar];
                                $index_komentar++;
                                var_dump($evidencija->komentar);
                                if ($evidencija->upisi($row_category_djaci['fk_djak'], $idd )) {
                                    echo "<div class='alert alert-info'>";
                                    echo "Uspesno ste evidentirali prisustvo djaka";
                                    echo "</div>";
                                }
                            }

                            if($opravdao_otsustvo != array()){
                                foreach($opravdao_otsustvo as $taj_djak){
                                    var_dump($taj_djak);
                                   // var_dump($evidencija->update_opravdanje_dolaska($taj_djak , $termin_id));
                                    if($evidencija->update_opravdanje_dolaska($taj_djak , $idd)){
                                        echo "Izmenjeno";
                                    }
                                }

                            }else{
                                echo "<br> Nema opravdanja";
                            }
                            echo "<div class='alert alert-info'>";
                            echo "Uspesno ste promenili stavke termina aaaaaaa";
                            echo "</div>";


                            // deo vezan za stored proceduru i racunanje salda
                            // POSLE upisi + update_opravdanje_dolaska, pre echo success/script close:
                            if($termin->status == 2){
                                    $fk_grupa = (int)$termin->fk_grupa;
                                    $datum_danas = date('Y-m-d'); // ili bolje: substr($termin->start, 0, 10)

                                    $sql = "
                                        UPDATE djaci d
                                        JOIN povezivanje p ON p.fk_djak = d.id
                                        JOIN grupe g ON g.id = p.fk_grupa
                                        SET d.saldo_danas = izracunaj_saldo_djak_fn(d.id, :datum)
                                        WHERE p.fk_grupa = :fk_grupa
                                        AND p.status = 1
                                        AND g.fk_nacin_zaduzivanja = 2
                                    ";
                                    $stmtSaldo = $db->prepare($sql);
                                    $stmtSaldo->execute([
                                        ':datum' => $datum_danas,
                                        ':fk_grupa' => $fk_grupa
                                    ]);

                            }
                        

                            // empty posted values
                            $_POST = array(); ?>
                            
                            <script>
                                //window.parent.opener.document.location.reload();
                                var substring = "dnevnik_grupa";
                                var substring2 = "evidencija_djak";
                                var substring3 = "kartica_djak";
                                console.log(window.opener.location.toString().includes(substring) );
                                if(window.opener.location.toString().includes(substring) == true){
                                    window.parent.opener.document.location.reload();
                                }
                                if(window.opener.location.toString().includes(substring2) == true){
                                    window.parent.opener.document.location.reload();
                                }
                                if(window.opener.location.toString().includes(substring3) == true){
                                    window.parent.opener.document.location.reload();
                                }
                                console.log(window.opener.document.location);
                                window.close(); 
                            </script>
                            
                            
                            
                        <?php         
                        } else {  
                                echo "<div class='alert alert-danger' role='alert' >";
                                echo "Doslo je do problema prilikom evidentiranja prisutnosti djaka";
                                $iii=1;
                                    echo var_dump($_POST['prisutan']),"<br/>";
                                    foreach ($_POST['prisutan'] as $key => $val)
                                    {
                                        if(isset($_POST['prisutan'])){
                                        $statuss[$iii] = 1;
                                        }else { $statuss[$iii] = 0;}
                                        echo $iii,"Vrednost:", $statuss[$iii],"--> Key",$key, "--> VAlue:", $val,"<br/>";
                                        //$age = $_POST['age'][$i];
                                    $iii++;
                                    }
                            echo"</div>";
                        }

                    }else{  // iz admin naloga
                        if ($termin->update_termin($idd)) {
                            $evidencija->termin_id = $idd;
                            $evidencija->evidentirao = $_SESSION['user_id'] ;
                            $ssttmp = $povezivanje->read_all_students($termin->fk_grupa);
                            //$prisutan = array();
                            $index_komentar = 0;
                            //  var_dump($_POST['prisutan'] ?? null);
                            $prisutni = $_POST['prisutan'] ?? array();
                            $opravdao_otsustvo = $_POST['opravdao_otsustvo'] ?? array();
                            var_dump($opravdao_otsustvo);
                            while ($row_category_djaci = $ssttmp->fetch(PDO::FETCH_ASSOC)) {
                                if (in_array($row_category_djaci['fk_djak'], $prisutni)) {
                                //  echo $row_category_djaci['fk_djak'], "<br/>";
                                    $evidencija->prisutan = TRUE;
                                } else {
                                    $evidencija->prisutan = FALSE;
                                                      
                                }
                                $evidencija->djak_id = $row_category_djaci['fk_djak'];
                                
                                $evidencija->komentar = $_POST['komentar1'][$index_komentar];
                                $index_komentar++;
                                echo "<br> Komentar:";
                                var_dump($evidencija->komentar);
                                if ($evidencija->upisi($row_category_djaci['fk_djak'], $idd )) {
                                    echo "<div class='alert alert-info'>";
                                    echo "Uspesno ste evidentirali prisustvo djaka";
                                    echo "</div>";
                                }
                            }
                            if($opravdao_otsustvo != array()){
                                foreach($opravdao_otsustvo as $taj_djak){
                                    var_dump($taj_djak);
                                   // var_dump($evidencija->update_opravdanje_dolaska($taj_djak , $termin_id));
                                    if($evidencija->update_opravdanje_dolaska($taj_djak , $idd)){
                                        echo "Izmenjeno";
                                    }
                                }

                            }else{
                                echo "<br> Nema opravdanja";
                            }
                           
                            echo "<div class='alert alert-info'>";
                            echo "Uspesno ste promenili stavke termina aaaaaaa";
                            echo "</div>";

                            // deo vezan za stored proceduru i racunanje salda
                            // POSLE upisi + update_opravdanje_dolaska, pre echo success/script close:

                            if($termin->status == 2){
                                    $fk_grupa = (int)$termin->fk_grupa;
                                    $datum_danas = date('Y-m-d'); // ili bolje: substr($termin->start, 0, 10)

                                    $sql = "
                                        UPDATE djaci d
                                        JOIN povezivanje p ON p.fk_djak = d.id
                                        JOIN grupe g ON g.id = p.fk_grupa
                                        SET d.saldo_danas = izracunaj_saldo_djak_fn(d.id, :datum)
                                        WHERE p.fk_grupa = :fk_grupa
                                        AND p.status = 1
                                        AND g.fk_nacin_zaduzivanja = 2
                                    ";
                                    $stmtSaldo = $db->prepare($sql);
                                    $stmtSaldo->execute([
                                        ':datum' => $datum_danas,
                                        ':fk_grupa' => $fk_grupa
                                    ]);

                            }



                            // empty posted values
                            $_POST = array(); ?>
                            
                            <script>
                                //window.parent.opener.document.location.reload();
                                var substring = "dnevnik_grupa";
                                var substring2 = "evidencija_djak";
                                var substring3 = "kartica_djak";
                                console.log(window.opener.location.toString().includes(substring) );
                                if(window.opener.location.toString().includes(substring) == true){
                                    window.parent.opener.document.location.reload();
                                }
                                if(window.opener.location.toString().includes(substring2) == true){
                                    window.parent.opener.document.location.reload();
                                }
                                if(window.opener.location.toString().includes(substring3) == true){
                                    window.parent.opener.document.location.reload();
                                }
                                console.log(window.opener.document.location);
                                window.close(); 
                            </script>
                            
                            
                            
                            <?php         
                        }else{
                                echo "<div class='alert alert-danger' role='alert' >";
                                echo "Doslo je do problema prilikom evidentiranja prisutnosti djaka";
                                $iii=1;
                                echo var_dump($_POST['prisutan']),"<br/>";
                                foreach ($_POST['prisutan'] as $key => $val)
                                {
                                            if(isset($_POST['prisutan'])){
                                            $statuss[$iii] = 1;
                                            }else { $statuss[$iii] = 0;}
                                            echo $iii,"Vrednost:", $statuss[$iii],"--> Key",$key, "--> VAlue:", $val,"<br/>";
                                            //$age = $_POST['age'][$i];
                                        $iii++;
                                }
                                echo"</div>";
                        }

                    }
               
                

                }
        ?>
        <form action="dogadjaj.php?id=<?php echo $idd; ?>" method="post">
         <table class='table table-responsive'>
             <tr>
                 <td class='width-20-percent' style='width: 25%;'>Profesor:</td>
                 <td style="color:#585858;">
                     <?php
                     //  echo $row_category_grupa['fk_profesor'];
                     if($termin->fk_profesor == null || $termin->fk_profesor == 0 ){
                        if($termin->status != 2){
                            $predavac = $profesor->read_one_profesor($row_category_grupa['fk_profesor'], "users");   
                        }else{
                            $predavac = $profesor->read_one_profesor($termin->fk_profesor_grupa, "users");
                        }
                        
                       // $predavac = $profesor->read_one_profesor($termin->fk_profesor_grupa, "users");
                     } else{
                        $predavac = $profesor->read_one_profesor($termin->fk_profesor, "users");
                     }
                     $row_category_predavac = $predavac->fetch(PDO::FETCH_ASSOC);
                    // var_dump($row_category_predavac);
                     if($_SESSION['access_level'] == "Customer" ){
                          echo "&nbsp;", $row_category_predavac['firstname'], "&nbsp;", $row_category_predavac['lastname']; ?>
                         <input  type='number' style="display:none" name='fk_profesor' value="null" class='form-control'    />  
                     <?php  
                     }elseif($row_category_predavac['status'] == false){
                        echo "&nbsp;", $row_category_predavac['firstname'], "&nbsp;", $row_category_predavac['lastname']; ?>
                                <input  type='number' style="display:none" name='fk_profesor' value="null" class='form-control'    />  
                
                     <?php 
                     }else{
                        $stmt_profesor = $profesor->read_All_profesor(); ?>
                         <select class='form-control' name='fk_profesor'>
                                <?php while ($row_profesor = $stmt_profesor->fetch(PDO::FETCH_ASSOC)){ ?>
                                    <option value="<?=$row_profesor['id'] ?>" <?php if ($row_category_predavac['id'] == $row_profesor['id']) { echo "selected";} ?> ><?=$row_profesor['firstname'],"&nbsp;",$row_profesor['lastname'] ?> </option>


                            <?php    
                            }
                     }    

                     ?>
                 </td>

             </tr>
             <?php 
             
           //  echo $termin->fk_ucionica;
             if($termin->fk_ucionica != 2000){
                        ?>
                        <tr>
                        <?php 
                            $stmt33_ucionica = $ucionica->read_All(); 
                        ?>
                            <td>Ucionica:</td>
                            <td>
                            
                                <select class='form-control' name='ucionica'>
                                <?php while ($row_ucionica = $stmt33_ucionica->fetch(PDO::FETCH_ASSOC)){ ?>
                                    <option value=<?php echo $row_ucionica['id'];  ?> <?php if ($termin->fk_ucionica == $row_ucionica['id']) {
                                        echo "selected";
                                    } ?> ><?php echo $row_ucionica['ime'];  ?>
                                    </option>
                                    
                                
                                <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <?php 
             }else{ ?>
                <tr>
                    <td class='width-30-percent'>Vrsta online komunikacije</td>
                    <td>
        
                            <select class='form-control' name='zoom_skype' >
                                <option value="0">Odaberi vrstu Online komunikacije...</option>
                                <option value="1"> Zoom </option>
                                <option value="2"> Skype </option>
                            </select>
                    </td>
                </tr>
            
                <?php
            }
             
             ?>
             <tr>

                 <td colspan='4'>Djaci:</td>
                </tr><tr>
                 <td colspan='4' style="color:#585858;">
                 <table class='table table-hover table-responsive table-bordered' style='width:100%;'>
                    <tr class="text-center" ><th>Prisutan:</th><th>Djak:</th><th >Komentar:</th></tr>
                     <?php
                   
                     $ssttmp = $povezivanje->read_all_students($termin->fk_grupa);
                     //$prisutan = array();
                     while ($row_category_djaci = $ssttmp->fetch(PDO::FETCH_ASSOC)) { ?>
                        <tr> 
                         <?php   
                         $stmt = $djak->read_one_djak("djaci", $row_category_djaci['fk_djak']);
                         $row_category_one_djak = $stmt->fetch(PDO::FETCH_ASSOC);
                         $skskmsj = $evidencija->read_one_prisustvo($row_category_djaci['fk_djak'],$idd);
                         $row_category_evidencija = $skskmsj->fetch(PDO::FETCH_ASSOC);
                        // var_dump( $row_category_evidencija);
                      
                       
                            if($row_category_grupa["velicina"] == 1 OR $row_category_grupa["velicina"] == 2  ){
                                if($row_category_evidencija  != false){
                                    if(isset($row_category_evidencija['prisutan']) AND ( $row_category_evidencija['prisutan'] == 0 OR $row_category_evidencija['prisutan'] == null ) ){
                                        $vidljivo_opravdanje = 'inline-block';
                                    }else{
                                        $vidljivo_opravdanje = 'none';
                                    }
                                }else{
                                  //  echo "nema dosad";
                                    $vidljivo_opravdanje = 'inline-block';    
                                }    
                            }else{
                                $vidljivo_opravdanje = 'none';
                            }
                        
                         ?>
                        <td style='width:20%;'>
                            <input class='form-check-input checkbox-lg' type="checkbox" name="prisutan[]" id="prisutan_<?php echo $row_category_one_djak['id']; ?>" value="<?php echo $row_category_one_djak['id'] ?>" onchange="toggleOpravdanje(<?php echo $row_category_one_djak['id']; ?>)" <?php if (isset($row_category_evidencija['prisutan']) && $row_category_evidencija['prisutan'] == 1) { echo "checked"; } ?>>
                            <span id="opravdanjeContainer_<?php echo $row_category_one_djak['id']; ?>" class="opravdanje-container" style="display: <?=$vidljivo_opravdanje ?>;">
                                <!-- Ovde stavite drugo polje koje se pojavljuje kada je checkbox nije čekiran -->
                                <br/><br>
                                <input type="checkbox" name="opravdao_otsustvo[]" id="opravdao_otsustvo_<?php echo $row_category_one_djak['id']; ?>" value="<?php echo $row_category_one_djak['id'] ?>"   <?php if (isset($row_category_evidencija['opravdao_otsustvo']) && $row_category_evidencija['opravdao_otsustvo'] == 1) { echo "checked"; } ?> >
                                <label for="opravdao_otsustvo_<?php echo $row_category_one_djak['id']; ?>">Opravdano odsustvo</label>
                            </span>
                        </td>
                        <td style='width : 15em;'><?=$row_category_one_djak ['firstname'], "&nbsp;", $row_category_one_djak ['lastname'] ?></td>
                        
                        <td style='margin-left:none; width: 25em;'> <textarea name='komentar1[]' id='komentar' class='form-control' ><?php if( isset( $row_category_evidencija['komentar'] ) ){ echo $row_category_evidencija['komentar']; } ?></textarea></td>
                        </tr>
                     <?php 
                     }

                     if($row_category_grupa["velicina"] == 1 OR $row_category_grupa["velicina"] == 2 ){
                        ?>
                        <script>
                            function toggleOpravdanje(id) {
                                var prisutanCheckbox = document.getElementById('prisutan_' + id);
                                var opravdanjeContainer = document.getElementById('opravdanjeContainer_' + id);
                                
                                if (prisutanCheckbox.checked) {
                                    opravdanjeContainer.style.display = 'none';
                                } else {
                                    opravdanjeContainer.style.display = 'inline-block';
                                }
                            }
                        </script>
                        <?php 
                    } ?>
                    </table>
                 </td>

             </tr>


             <tr>
                 <td>Opis Časa:</td>
                 <td><textarea name='komentar' class='form-control'><?php echo $termin->komentar; ?></textarea></td>
             </tr>
             <script>
                const tx = document.getElementsByTagName("textarea");
                for (let i = 0; i < tx.length; i++) {
                    tx[i].setAttribute("style", "height:" + (tx[i].scrollHeight) + "px;overflow-y:hidden;");
                    tx[i].addEventListener("input", OnInput, false);
                }
                function OnInput() {
                    this.style.height = "auto";
                    this.style.height = (this.scrollHeight) + "px";
                }
             </script>

             <tr>

                 <td>Dana:</td>
                 <td>
                     <table style="border-spacing: 15px; border-collapse: separate;">
                         <tr>
                             <td style="color:#585858;">
                                 <?php
                                 $vreme = $termin->start;
                                 $datum = substr($vreme, 0, 10);

                                 $p77 = explode("-", $datum);
                                 $datum = $p77[2] . "." . $p77[1] . "." . $p77[0];
                                 echo $datum;

                                 ?>
                             </td>


                             <td style="color:#585858;">OD:&nbsp;
                                 <?php
                                 $sati_min_od = substr($vreme, 11, 5);
                                 $vreme_do = $termin->kraj;
                                 $sati_min_do = substr($vreme_do, 11, 5);
                                 echo $sati_min_od;

                                 ?>
                             </td>

                             <td style="color:#585858;">DO:&nbsp;
                                 <?php echo $sati_min_do; ?>
                             </td>

                         </tr>
                     </table>
                 </td>

             </tr>
			 <tr>
                <td>Boja :</td>
                <td> <input type="color" name="color" value="<?php echo $termin->color; ?>"></td>
            </tr>
            <?php if($row_category_grupa['nacin'] == 1 ){ ?>
                <tr style="display: none;">
                 <td>Nacin Slusanja:</td>

                 <td>
                     <select class='form-control' name='zoom_skype'>
                         <option value=1 <?php if ($termin->zoom_skype == 1) {echo "selected"; } ?> >Zoom </option>
                         <option value=2 <?php if ($termin->zoom_skype == 2) {echo "selected"; } ?> >Skype </option>
                     </select>
                 </td>
                 </td>
             </tr>

            <?php } ?>
            
             <tr>
                 <td>Status termina:</td>

                 <td>
                     <select class='form-control' name='status' onchange="vrsta_brisanja(this)">
                         <option value=1 <?php if ($termin->status == 1) { echo "selected";} ?> >U kalendaru </option>
                         <option value=2 <?php if ($termin->status == 2) { echo "selected";} ?> >Odrzan </option>
                         <option value=3 <?php if ($termin->status == 3) { echo "selected";} ?> >Otkazan </option>
                         <option value=4 <?php if ($termin->status == 4) { echo "selected";} ?> >Obriši ga </option>
                     </select>
                 </td>
                 </td>
             </tr>
            </table> 
            
            <div class="col-md-12"  id="nacin_brisanja" style="display : none; background-color: #F2F2F2; border-radius: 10px; border: 1px solid silver;" >
                <table class='table table-bordered;' >
                    <tr>
                        <td  >Način brisanja:</td>
                        <td colspan="2" >
                            <select class='form-control' name='vrsta_brisanja22' onchange="vrsta_brisanja2(this)">
                                <option value=1  >Samo ovaj čas </option>
                                <option value=2 >Sve časove ove grupe u intervalu </option>
                                
                            </select>
                        </td>
                    </tr>  
                <table>
            </div>  
                    <script>
                        function vrsta_brisanja(selectObject) {
                            var value = selectObject.value;  
                            console.log(value);
                            if(value == 4){
                            //  alert ("Selektovana vrednost je: "+ value);
                                document.getElementById("nacin_brisanja").style.display = 'block';
                                $('#interval_za_brisanje').find('input, textarea, button, select').attr('disabled',false);
                                event.preventDefault();
                                return false;

                            }else{
                                document.getElementById("nacin_brisanja").style.display = 'none';
                                $('#interval_za_brisanje').find('input, textarea, button, select').attr('disabled','disabled');
                                event.preventDefault();
                                return false;

                            }
                    
                        }      
                        </script>
            <div class="col-md-12" >
            
                <table class='table table-responsive table-bordered; ' id="interval_za_brisanje" style="display: none;  width:100%;" >
                    <tr>
                        <td style="width:30em" >Unesi interval za brisanje</td>
                        <td style="width : 30em">
                            Od:        <input type="text"   name="pocetak_brisanja_datum" <?php if(isset($datum)){echo"value='$datum'";} ?>  STYLE="background-color:white; padding: 6px; border: 1px solid silver; border-radius: 4px;" size="10" class="tcal" />
                        </td>
                        <td style="width: 30em">
                            Do:        <input type="text"   name="kraj_brisanja_datum" <?php if(isset($datum)){echo"value='$datum'";} ?>  STYLE="background-color:white; padding: 6px; border: 1px solid silver; border-radius: 4px;" size="10" class="tcal" />
             
                        </td>

                    </tr>
                </table>
            </div>    
                  
            
            <script>

                function vrsta_brisanja2(selectObject11) {
                    var value11 = selectObject11.value;  
                    console.log(value11);
                    if(value11 == 2){
                       // alert ("Selektovana vrednost je: "+ value);
                        document.getElementById("interval_za_brisanje").style.display = 'block';
                        $('#interval_za_brisanje').find('input, textarea, button, select').attr('disabled',false);
                                event.preventDefault();
                                return false;


                    }else{
                        document.getElementById("interval_za_brisanje").style.display = 'none';
                        $('#interval_za_brisanje').find('input, textarea, button, select').attr('disabled','disabled');
                                event.preventDefault();
                                return false;
                    }
               
                }

               

             </script>
         <table class='table table-responsive'> 
            
             <tr>
                 <td></td>
                 <td>
                     <button type="submit" name="skola" value="skola" class="btn btn-danger">
                         <i class='bi bi-record'></i> Snimi i izadji
                     </button>
                 </td>
             </tr>

         </table>

        </form>
     <?php

     echo "</div>";
    }
    echo "</div>";
// include page footer HTML
//include_once "layout_foot.php";

?>