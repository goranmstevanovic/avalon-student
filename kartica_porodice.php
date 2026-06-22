<html>
<head>
    <title> Kartica djak </title>
    <link rel="shortcut icon" href="images/kalen.png">
    <link rel="stylesheet" type="text/css" href="tigrakal/tcal.css" />
    <script type="text/javascript" src="tigrakal/tcal.js"></script>
   <!-- <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script> 
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>  -->
    <script src="ajax/ajax321jquery.min.js"></script>
    <script type="ajax/jquery15.min.js"></script> 
	<style>
		a,a label {
		cursor: pointer;
	}
	</style>
</head>
<body>
<?php
/**
 * Created by PhpStorm.
 * User: goran
 * Date: 6.11.2019
 * Time: 13:13
 */
ini_set('session.cache_limiter','public');
session_cache_limiter(false); 
if(isset($_GET['id'])){$idd = $_GET['id']; // echo "Radi se o djaku  br: ", $idd;
$aktuelni_djak=$idd;
}
//echo 'Current PHP version: ' . phpversion();
// core configuration
include_once "config/core.php";

// set page title
$page_title = "";

// include login checker
include_once "login_checker.php";

// include classes
include_once 'config/database.php';
include_once 'config/funkcije.php';
include_once 'config/autoload.php';


// include_once 'objects/user.php';
// include_once "libs/php/utils.php";
// include_once 'objects/grupa.php';
// include_once 'objects/djak.php';
// include_once 'objects/zaduzenje.php';
// include_once 'objects/povezivanje.php';
// include_once 'objects/uplata.php';
// include_once 'objects/nivo_znanja.php';
// include_once 'objects/jezik.php';

include_once "layout_head2.php";
$database = new Database();
$db = $database->getConnection();
$grupa = new grupa($db);
$djak = new djak($db);
$profesor = new user($db);
$zaduzenje = new zaduzenje($db);
$povezivanje = new povezivanje($db);
$uplata = new uplata($db);
$nivo_znanja = new nivo_znanja($db);
$jezik = new jezik($db);
$porodica = new porodica($db);
$evidencija = new evidencija($db);
$stmt = $djak->read_one_djak("djaci", $idd);
$row_category_djak = $stmt->fetch(PDO::FETCH_ASSOC);
$cenovnik = new cenovnik($db);
extract($row_category_djak);



// registration form HTML
// code when form was submitted
// if form was posted

?>
<script>
$('textarea').each(function () {
  this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
}).on('input', function () {
  this.style.height = 'auto';
  this.style.height = (this.scrollHeight) + 'px';
});
</script>

<?php 
// provera jeli u nekoj porodici
    $stmt_porodica = $porodica->read_one($idd); 
    $row_porodica = $stmt_porodica->fetch(PDO::FETCH_ASSOC);
?>



    <div class="col-md-12" style='width:90%; margin-left:5%;' id='glavni1' >
        <div class='col-md-12' style = "width:90%; margin-left: 5%; margin-right: 5%;"  >
        <table class="table table-responsive" style="background-color:#F2E2A8;">
        <tr> 
            <th><h3>Kartica porodice:</h3>  </th>
            <th>
                <?php echo $row_porodica['ime'];  ?>
            </th>
            <th class="text-center" >
            <label for="opis">Opis:</label>    
                    <textarea name='opis' readonly class='form-control'> <?php echo  $row_porodica['opis']; ?></textarea>
              
                       
            </th>
            <th class="text-center" >
            <label for="odgovoran">Odgovoran:</label>    
                    <textarea name='odgovoran' readonly class='form-control'> <?php echo  $row_porodica['odgovoran']; ?></textarea>
              
                       
            </th>
        </tr>
    </table>
    <?php 
    $stmt_svi_djaci = $djak->read_all_porodica($idd);
  //  var_dump($stmt_svi_djaci);
    $row_djak = $stmt_svi_djaci->fetchall(PDO::FETCH_ASSOC); 
  //  var_dump($row_djak);
  $id_djaka = array();
    
    ?>
            <table class='table table-bordered text-center'>
               <tr><th>Ime</th><th>Telefon</th><th>Akcija</th></tr>
            <?php foreach($row_djak as $taj_djak){
                $id_djaka[] = $taj_djak['id'];
                ?>
                <tr>
                    <td><?php 
                        echo $taj_djak['firstname'],"&nbsp;",$taj_djak['lastname']; 
                       // var_dump($taj_djak);
                        ?>
                    </td>
                    <td><?=$taj_djak['contact_number'] ?></td>
                    <td><a class="btn btn-outline-danger btn-sm"  href="kartica_djak.php?id=<?php echo $taj_djak['id']; ?>  "  target="_blank"><span class="glyphicon glyphicon-euro"></span> Finasijska kartica djaka</a></td>

                </tr>

                <?php 
            } ?>   
            </table>
            <table class='table table-bordered text-center'>
                <tr>
                <td style='text-align:right;' ><h4><u><b>Ukupni saldo: </b></u></h4> </td>
                <td ><b><u><h4><div id="saldo_gore"></div></h4></u></b></td>
                <td style='color:purple; text-align:right;'><h4><u> <b> Saldo na današnji dan:</b></u></h4></td>
                <td style='color:purple;' ><h4><u><b><div id="danasnji_saldo_gore" ></div></b></u</h4></td>
  
                </tr>

            </table>
            
            
        </div>
        <div class='col-md-12' id="karticatab" style = "width:90%; margin-left: 5%; margin-right: 5%;">
            <?php
            //   $uplata = array();
            // Citamo sve uplate i zaduzenja za zadatog studenta
           // var_dump($row_djak['id']);
            $ssdj = $uplata-> read_all_uplate_porodica($id_djaka);
            $smtp = $zaduzenje -> read_all_zaduzenja_for_porodica($id_djaka);
            $i=-1;
            // while ($row_uplata = $ssdj->fetch(PDO::FETCH_ASSOC)) {
            //     $i++;
            //     $kartica[$i]['id'] = $row_uplata['id'];
            //     $kartica[$i]['fk_djak'] = $row_uplata['fk_djak'];
            //     $kartica[$i]["datum_generisanja"] = $row_uplata["datum_generisanja"];
            //     $kartica[$i]["iznos"]=$row_uplata["iznos"];
            //     $kartica[$i]["fk_grupa"]=$row_uplata["fk_grupa"];
            //     $kartica[$i]["upisao"]=$row_uplata["upisao"];
            //     $kartica[$i]["created"]=$row_uplata["datum_generisanja"];
            //     $kartica[$i]["vrsta"]="uplata";
            //     $kartica[$i]["racun"]=$row_uplata["racun"];
            //     $kartica[$i]["broj_priznanice"]=$row_uplata["broj_priznanice"];
            //     $kartica[$i]["komentar"]=$row_uplata["komentar"];
            // }

            // while ($row_zaduzenje = $smtp->fetch(PDO::FETCH_ASSOC)) {
            //     $i++;
            //     $kartica[$i]["id"]=$row_zaduzenje["id"];
            //     $kartica[$i]["fk_djak"]=$row_zaduzenje["fk_djak"];
            //     $kartica[$i]["fk_grupa"]=$row_zaduzenje["fk_grupa"];
            //     $kartica[$i]["iznos"]=$row_zaduzenje["iznos"];
            //     $kartica[$i]["od_datuma"]=$row_zaduzenje["od_datuma"];
            //     $kartica[$i]["do_datuma"]=$row_zaduzenje["do_datuma"];
            //     $kartica[$i]["zaduzio"]=$row_zaduzenje["zaduzio"];
            //     $kartica[$i]["created"]=$row_zaduzenje["valuta"] ?? null;
            //     $kartica[$i]["vrsta"]="zaduzenje";
            //     $kartica[$i]["komentar"]=$row_zaduzenje["komentar"];

            // }
                $kartica = array();
            // sad za djaku i grupe po casu:
             foreach($id_djaka as $tajj_djak){

              //  echo $tajj_djak,"<br>";
                $idd = $tajj_djak;
                $ssdj_uplata = $uplata-> read_all_uplate_students1($idd);
                //   $smtp = $zaduzenje->read_all_zaduzenja_for_students($idd);
                //  $i=-1;
                while ($row_uplata = $ssdj_uplata->fetch(PDO::FETCH_ASSOC)) {
                    $i++;
                    $kartica[$i]["fk_djak"] =  $tajj_djak;
                    $kartica[$i]['id'] = $row_uplata['id'];
                    $kartica[$i]["datum_generisanja"] = $row_uplata["datum_generisanja"];
                    $kartica[$i]["iznos"]=$row_uplata["iznos"];
                    $kartica[$i]["fk_grupa"]=$row_uplata["fk_grupa"];
                    $kartica[$i]["upisao"]=$row_uplata["upisao"];
                    $kartica[$i]["created"]=$row_uplata["datum_generisanja"];
                    $kartica[$i]["vrsta"]="uplata";
                    $kartica[$i]["racun"]=$row_uplata["racun"];
                    $kartica[$i]["broj_priznanice"]=$row_uplata["broj_priznanice"];
                    $kartica[$i]["komentar"]=$row_uplata["komentar"];
                }
                
                // var_dump($kartica);
                // echo "<hr>";
                
                
                
                $broj_grupa_u_kojima_je = $povezivanje->count_grupa($idd);
                if($broj_grupa_u_kojima_je >= 0)
                {
                    $ttss = $povezivanje->read_all_group_students_za_finasijsku($idd);
                }  $broj_grupa_u_kojima_je = $povezivanje->count_grupa($idd);
              //  $kartica = array();
                if($broj_grupa_u_kojima_je > 0)
                {
                      //  echo"<td> Grupa:</td> ";
                   $ttss = $povezivanje->read_all_group_students_za_finasijsku($idd);
                    // var_dump($ttss);
                    //   $ttss = $evidencija->read_all_grupe_prisustvo_djak($idd);
                    //  var_dump($ttss);
                  //  $i = -1;
                    while ($row_category_grupe = $ttss->fetch(PDO::FETCH_ASSOC)) 
                    {
                       $ta_grupa = $row_category_grupe['fk_grupa'];
                     //  echo "daka:", $idd;
                        $sst = $grupa->read_one_grupa1($row_category_grupe['fk_grupa']);
                        $row_citanje_grupe = $sst->fetch(PDO::FETCH_ASSOC);
                        if($row_citanje_grupe['status'] == false){
                            $status_grupe = 'neaktivna';
                        }else{
                            $status_grupe = 'aktivna';
                        }
                        extract($row_citanje_grupe);
    
                        $stm_jezik = $jezik->read_one_jezik($fk_jezik,'jezik');
                        $row_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC);
                        $fk_jezik = $row_jezik['ime'];
                        
                        $stm_nivo = $nivo_znanja->read_one_nivo($nivo,'nivo_znanja');
                        $row_nivo_znanja = $stm_nivo->fetch(PDO::FETCH_ASSOC);
                        $nivo = $row_nivo_znanja['ime'];
                       
                        if($fk_nacin_zaduzivanja == 1 OR $fk_nacin_zaduzivanja == 2){
                            $vrsta_zaduzenja = "Po kursu";
                          
                            //  $smtp = $zaduzenje->read_all_zaduzenja_for_student_group($idd,$row_category_grupe['fk_grupa']);
                            $smtp = $zaduzenje->read_all_zaduzenja_for_student_group($idd,$row_category_grupe['fk_grupa']);
                           
                            while ($row_zaduzenje = $smtp->fetch(PDO::FETCH_ASSOC)) {
                                $i++;
                                $kartica[$i]["fk_djak"] =  $tajj_djak;
                                $kartica[$i]["id"]=$row_zaduzenje["id"];
                                $kartica[$i]["fk_grupa"]=$row_zaduzenje["fk_grupa"];
                                $kartica[$i]["iznos"]=$row_zaduzenje["iznos"];
                                $kartica[$i]["od_datuma"]=$row_zaduzenje["od_datuma"];
                                $kartica[$i]["do_datuma"]=$row_zaduzenje["do_datuma"];
                                $kartica[$i]["zaduzio"]=$row_zaduzenje["zaduzio"];
                                $kartica[$i]["created"]=$row_zaduzenje["valuta"] ?? null;
                                $kartica[$i]["vrsta"]="zaduzenje po kursu";
                                $kartica[$i]["komentar"]=$row_zaduzenje["komentar"];
                            }
    
                         //   $kartica = array_merge($kartica,$kartica1);
    
                        }
                        if($fk_nacin_zaduzivanja == 2){
                            $vrsta_zaduzenja = "Po času";
                            // echo "ta grupa: ", $ta_grupa;
                            // echo "daka:", $idd;
    
                           // $smtp_zaduzenje = $termin->read_all_termin_grupa_student($idd,$ta_grupa);
                            $smtp_zaduzenje = $evidencija->read_all_all_prisustvo_djak($idd);
                           // var_dump( $smtp_zaduzenje);
                            while ($row_zaduzenje = $smtp_zaduzenje->fetch(PDO::FETCH_ASSOC)) {
                                // var_dump($row_zaduzenje);
                                // echo "<hr>";
                                $i++;
                              //  $kartica[$i]["fk_djak"] =  $tajj_djak;
                                $kartica[$i]["fk_djak"] =  $tajj_djak;
                                $kartica[$i]["id"]=$row_zaduzenje["id"];
                                $kartica[$i]["fk_grupa"]=$row_zaduzenje["fk_grupa"];
                                $zaduzenje_iz_cenovnika = $cenovnik->odredi_zaduzenje($row_zaduzenje);
    
                               // $row_iz_cenovnika = $stmt_zaduzenje_iz_cenovnika->fetch(PDO::FETCH_ASSOC);
                               // var_dump($zaduzenje_iz_cenovnika);
                            //    var_dump($row_zaduzenje  ); 
                            //    echo "<hr>";
                               if($row_zaduzenje["velicina"] == 1 OR $row_zaduzenje["velicina"] == 2 ){
                                if($row_zaduzenje["prisutan"] == 1 OR $row_zaduzenje["opravdao_otsustvo"] == 0){
                                    $kartica[$i]["iznos"] = $zaduzenje_iz_cenovnika["iznos"];
                                }else{
                                    $kartica[$i]["iznos"] = 0;
                                }
                                
                               }else{
                                 $kartica[$i]["iznos"] = $zaduzenje_iz_cenovnika["iznos"];
                               }
                                $kartica[$i]["od_datuma"]= date('d.m.Y H:i', strtotime($row_zaduzenje["start"])); 
                                $kartica[$i]["do_datuma"]= date('H:i', strtotime($row_zaduzenje["end"])); 
                                if($row_zaduzenje["fk_profesor"] != NULL ){
                                    $kartica[$i]["zaduzio"]=$row_zaduzenje["fk_profesor"];
                                }else{
                                    $kartica[$i]["zaduzio"]=$row_zaduzenje["fk_profesor_grupa"];
                                }
                                
                                $kartica[$i]["created"]=substr($row_zaduzenje["start"],0,10);
                                $kartica[$i]["vrsta"]="zaduzenje po casu";
                                $kartica[$i]["komentar"]=$row_zaduzenje["komentar"];
                            }
                            // echo "<pre>";
                            // var_dump($kartica);
                            // echo "</pre>";
    
    
                        }
               
                        // echo "nivo=", $nivo;
                       
    
                    }
                }
             }
                

            // Napunjen niz sa zajenickim podacima iz zaduzenja i uplata
            If($i>=0) {
                foreach ($kartica as $key => $part) {
                    $sort[$key] = strtotime($part['created']);
                }

            //  $created= array_column($kartica, 'created');
                //$city= array_column($data, 'city');
            //  array_multisort($created, SORT_ASC, $kartica);
            array_multisort( array_column($kartica, "created"), SORT_ASC, $kartica );
    // Sortiran niz
                ?>
        <form method="POST" action ="kartica_djak.php?id=<?php echo $idd; ?>"  >
                <table class="table table-bordered table-hover text-center">
                    <tr bgcolor="#BDBDBD" style="text-align:center;">
                        <th style="text-align:center;">Vrsta</th>
                        <th style="text-align:center;">Datum</th>
                        <th style="text-align:center; width: 15em;">Komentar / Vrem. interval </th>
                        <th style="text-align:center;">Evidentirao</th>
                        <th style="text-align:center; width:190px;">Grupa</th>
                        <th style="text-align:center;">Iznos uplata</th>
                        <th style="text-align:center;">Iznos zaduzenja</th>
                        <th style="text-align:center;">Saldo</th>
                        
                    </tr>

                        <?php
                        $saldo = 0;
                        $danasnji_saldo = 0;
                        $suma_uplata = 0 ;
                        $suma_zaduzenja = 0;
                        for ($b = 0; $b <= $i; $b++) {
                           // var_dump($kartica);
                            if ($kartica[$b]["vrsta"] == "uplata") 
                            { 
                                if($kartica[$b]["datum_generisanja"] <= date('Y-m-d')){
                                    $color = "#F2F2F2";
                                }else{
                                    $color = "#FFFFFF";
                                }
                                ?>
                                <tr style='background-color:<?=$color ?>;' ><td> <a href="update_uplata?broj=<?=$kartica[$b]['id'] ?>"> 
                                    Uplata<br>
                                    <?php 
                                    $stmt_citdj = $djak->read_one($kartica[$b]["fk_djak"]);
                                    $row_dj = $stmt_citdj->fetch(PDO::FETCH_ASSOC);
                                    echo "<b style='color:purple;'>",$row_dj['firstname'],"</b>";

                                    ?> </a></td>
                                <?php
                                $karticab = $kartica[$b]["datum_generisanja"] ?? null;
                                $kartica[$b]["datum_generisanja"] = datum_u_nas_datum($kartica[$b]["datum_generisanja"]);
                                
                                echo "<td>", $kartica[$b]["datum_generisanja"], "</td>";
                                echo "<td>";
                                if($kartica[$b]["komentar"] != null){ 
                                    
                                    echo "<b>", str_replace("5555","<br/>", $kartica[$b]["komentar"]); "</b>";
                                }
                                echo "</td>";
                                $iisi = $profesor->read_name_user($kartica[$b]["upisao"],"users");
                                $row_upisao = $iisi->fetch(PDO::FETCH_ASSOC);
                                echo "<td>", $row_upisao["firstname"], "</td>";

                                $ssdk = $grupa->read_one_grupa1($kartica[$b]["fk_grupa"]);
                                $row_citanje_jedne_grupe = $ssdk->fetch(PDO::FETCH_ASSOC);
                                extract($row_citanje_jedne_grupe);
                                // echo "nivo=", $nivo;
                                $stm_jezik = $jezik->read_one_jezik($fk_jezik,'jezik');
                                $row_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC);
                                $fk_jezik = $row_jezik['alias'];


                                
                                $stm_nivo = $nivo_znanja->read_one_nivo($nivo,'nivo_znanja');
                                $row_nivo_znanja = $stm_nivo->fetch(PDO::FETCH_ASSOC);
                                $nivo = $row_nivo_znanja['ime'];

                                echo "<td>",$fk_jezik, "&nbsp;", $nivo, "&nbsp; \n <b> [ ", $alias," ]</b><br/>";
                                echo "<td>", $kartica[$b]["iznos"]; 
                                if($kartica[$b]["racun"] == TRUE){echo "<b style='color: red;'> &nbsp; rač.</b>";}
                                if($kartica[$b]["broj_priznanice"] != null){echo "<br/><b style='color: purple;'> &nbsp; Br.priz.",$kartica[$b]["broj_priznanice"],"</b>";}
                                $suma_uplata = $suma_uplata + $kartica[$b]["iznos"];
                                echo "</td>";
                                echo "<td></td>";
                                $saldo = $saldo + $kartica[$b]["iznos"];
                                if($karticab <= date("Y-m-d")){
                                    $danasnji_saldo = $danasnji_saldo + $kartica[$b]["iznos"];
                                //  echo $kartica[$b]["datum_generisanja"];
                                }
                            // $saldo1 = number_format((float)$saldo, 2, '.', '');
                                ?>
                                <td <?php if ($saldo < 0) {echo "style='color:red;'";} ?> > <?php if ($saldo >= 0){echo "&nbsp;", number_format((float)$saldo, 2, '.', '');} else {echo number_format((float)$saldo, 2, '.', '');} ?></td>
                                <td style="display:none" ><a class="remove" style='color:red;' data-urowid="<?php echo $kartica[$b]['id']; ?>"><span class="glyphicon glyphicon-trash"></span></a></td>
                                        <!-- <button class="btn btn-danger btn-sm remove" ><img style="border:0; outline: none;" src="kanta32.png" width=16 height=16>  </button></td> -->
                                    <?php
                                echo "</tr>";
                            } else 
                            {  
                                if($kartica[$b]["created"] <= date('Y-m-d')){
                                    $color = "#F2F2F2";
                                }else{
                                    $color = "#FFFFFF";
                                }
                                
                                ?>
                                <tr style='background-color:<?=$color ?>;' >
                                    <td> 
                                        <!-- <a href="update_zaduzenje?broj=<?=$kartica[$b]['id'] ?>">Zaduženje</a> -->

                                        <?php 
                                       //  echo $kartica[$b]["vrsta"]; 
                                    if($kartica[$b]["vrsta"] == "zaduzenje po kursu"){?>
                                        <!-- <a href="update_zaduzenje?broj=<?=$kartica[$b]['id'] ?>" disabled >  -->
                                        Zaduženje
                                        <?php
                                    }else{ ?>
                                        <!-- <a class="zaduzenjeLink" href="#" data-id="<?=$kartica[$b]['id']?>" disabled > -->
                                         Zaduženje-čas
                                       
                                   
                                    <?php
                                    }

                                    ?>
                                     <br>
                                        <?php 
                                        $stmt_citdj = $djak->read_one($kartica[$b]["fk_djak"]);
                                        $row_dj = $stmt_citdj->fetch(PDO::FETCH_ASSOC);
                                        echo "<b>",$row_dj['firstname'],"</b>";
                                        ?>
                                    <!-- </a> -->
                                    </td>
                                    <?php
                                    if($kartica[$b]["created"] != null){
                                        $karticab = $kartica[$b]["created"];
                                        $kartica[$b]["created"] =  datum_u_nas_datum($kartica[$b]["created"]);
                                    }
                                
                                echo "<td>", $kartica[$b]["created"], "</td>";
                                $kartica[$b]["od_datuma"] = datum_u_nas_datum($kartica[$b]["od_datuma"]);
                                $kartica[$b]["do_datuma"] = datum_u_nas_datum($kartica[$b]["do_datuma"]);
                                echo "<td>", $kartica[$b]["od_datuma"], "&nbsp; - &nbsp;", $kartica[$b]["do_datuma"];
                                if($kartica[$b]["komentar"] != null){ 
                                    
                                    echo "<br/><b>", str_replace("5555","<br/>", $kartica[$b]["komentar"]); "</b>";
                                }
                                echo "</td>";
                                $iisi = $profesor->read_name_user($kartica[$b]["zaduzio"],"users");
                                $row_upisao = $iisi->fetch(PDO::FETCH_ASSOC);
                                echo "<td>", $row_upisao["firstname"] ?? "", "</td>";
                                $ssdk = $grupa->read_one_grupa1($kartica[$b]["fk_grupa"]);
                                $row_citanje_jedne_grupe = $ssdk->fetch(PDO::FETCH_ASSOC);
                                extract($row_citanje_jedne_grupe);
                                // echo "nivo=", $nivo;
                                $stm_jezik = $jezik->read_one_jezik($fk_jezik,'jezik');
                                $row_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC);
                                $fk_jezik = $row_jezik['alias'];


                                
                                $stm_nivo = $nivo_znanja->read_one_nivo($nivo,'nivo_znanja');
                                $row_nivo_znanja = $stm_nivo->fetch(PDO::FETCH_ASSOC);
                                $nivo = $row_nivo_znanja['ime'];

                                echo "<td>",$fk_jezik, "&nbsp;", $nivo, "&nbsp; <b> [ ", $alias," ]</b><br/>";


                                echo "<td></td>";
                                $iznoszaduznja = $kartica[$b]["iznos"];
                                    $iznoszaduznja= number_format((float)$iznoszaduznja, 2, '.', '');
                                echo "<td style='color:red;'>", $iznoszaduznja, "</td>";
                                $suma_zaduzenja = $suma_zaduzenja + $kartica[$b]["iznos"];
                                $saldo = $saldo - $kartica[$b]["iznos"];
                                if($karticab <= date("Y-m-d")){
                                    $danasnji_saldo = $danasnji_saldo - $kartica[$b]["iznos"];
                                    //echo date("Y-m-d"),"/",$kartica[$b]["created"],"<br/>";

                                }


                                $saldo1 = number_format((float)$saldo, 2, '.', '');
                                ?>
                                
                                <td <?php if ($saldo < 0) {
                                    echo "style='color:red;'";
                                } ?> > <?php if ($saldo >= 0) {
                                        echo "&nbsp;", $saldo1;
                                    } else {
                                        echo $saldo1;
                                    } ?></td>
                                <td style="display:none" >
                                <?php  
                                if($kartica[$b]["vrsta"] == "zaduzenje po kursu"){?>    
                                    <a class="remove"  style='color:red; ' data-zrowid="<?php echo $kartica[$b]['id']; ?>"><span class="glyphicon glyphicon-trash"></span></a>
                                    <?php 
                                } ?>
                                </td>
                                
                                    <!-- <button class="btn btn-danger btn-sm remove" ><img style="border:0; outline: none;" src="kanta32.png" width=16 height=16>  </button></td> -->
                                <?php
                                echo "</tr>";

                            }
                        }
                        ?>
                    <tr><td colspan="5">Ukupno</td><td><?=$suma_uplata ?></td><td style='color:red;'><?=round($suma_zaduzenja, 2 ) ?></td></tr>
                    <tr style='display:none;'><td colspan="6"></td><td id='danasnji_saldo_dole'><?=number_format((float)$danasnji_saldo, 2, '.', ' ') ?></td> <td id='saldo_dole'><?=number_format((float)$saldo, 2, '.', ' ') ?></td></tr>
                </table>
        </form>
    </div>
    </div>
    <script type="text/javascript">
    var firstDivContent = document.getElementById('saldo_dole');
    var secondDivContent = document.getElementById('saldo_gore');
    secondDivContent.innerHTML = firstDivContent.innerHTML;

    var firstDivContent1 = document.getElementById('danasnji_saldo_dole');
    var secondDivContent1 = document.getElementById('danasnji_saldo_gore');
    secondDivContent1.innerHTML = firstDivContent1.innerHTML;


    </script>
     <script>
        $(".zaduzenjeLink").click(function(){
             event.preventDefault();
            var home_url = "<?php echo $home_url; ?>";
            var kartica_id = this.getAttribute('data-id');
            var url = home_url + 'dogadjaj?id=' + kartica_id;
            window.open(url, '_blank');
            event.stopPropagation(); // Zaustavlja dalje širenje događaja na roditelje
        });
    </script>
   
            

            <script type="text/javascript">
			
                $(".remove").click(function(){
						var idiz = $(this).data("zrowid"); 
						var idiu = $(this).data("urowid");
                   /*  var idi = $(this).parents("tr").attr("name");
				    var zzi = $(this).parents("tr").attr("id"); */
                  /*  alert("Zdravo idiz" + idiz + "! Kako si danas?"); 
                    alert("Zdravo idiu" + idiu + "! Kako si danas?"); */
                    if (typeof idiu !== 'undefined')
                    {
						 var row = $(this).closest("tr");
                        if (confirm('Jeste li sigurni da želite da obrišete baš ovu uplatu ?')) 
						{
                            $.ajax({
								type: "POST",
                                url: "deleteuplata.php",
                                data: {
									operation: "remove",
									idiu: idiu
									},
								error: function () {
                                    alert('Nešto nije u redu sa brisanjem, kontaktiraj Goran-a');
                                },
								success: function (data) {
									row.remove();
								/*	alert("Uspešno obrisana uplata"); */
									location.reload(); 
                                }
                            });
                        }
                    }else
					{
						if (typeof idiz !== 'undefined')
						{
							 var row = $(this).closest("tr");
							if (confirm('Jeste li sigurni da želite da obrišete baš ovo zaduzenje ?')) 
							{
								$.ajax({
									type: "POST",
									url: "delete.php",
									data: {
										operation: "remove",
										idiz: idiz
										},
									error: function () {
										alert('Nešto nije u redu sa brisanjem, kontaktiraj Goran-a');
									},
									success: function (data) {
										 row.remove();
									/*	 alert("Uspešno obrisano zaduzenje"); */
										 location.reload(); 
									}
								});
							}
						}
						
					}
                });
			

            </script>
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
		<!--	<script type="text/javascript">
                $(".remove").click(function(){
                   var zidd = $(this).parents("tr").attr("id");
                 /*   alert("Zdravo id" + id + "! Kako si danas?"); */
                    alert("Zdravo zidd" + zidd + "! Kako si danas?"); 
                    if (typeof zidd !== 'undefined')
                    {
                        if (confirm('Jeste li sigurni da želite da obrišete baš ovo zaduzenje ?')) {
                            $.ajax({
                                url: 'delete.php',
                                type: 'GET',
                                data: {dzidd: zidd},
                                error: function () {
                                    alert('Nešto nije u redu sa brisanjem, kontaktiraj Goran-a');
                                },
                                success: function (data) {
                                    $("#"+zidd).remove();
                                    alert("Uspešno obrisano zaduzenje");
                                }
                            });
                        }
                    }
                });


            </script>
			-->


            <?php
        }else{
            echo "<div class='alert alert-info'>";
            echo "Nemamo evidentiranih uplata ni zaduzenja za ucenika";
            echo "</div>";
            
        } ?>


<?php

// include page footer HTML
include_once "layout_foot1.php";
?>



</body>
</html>
