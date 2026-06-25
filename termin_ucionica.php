<head>
<head>
    <link rel="stylesheet" type="text/css" href="tigrakal/tcal.css" />
    <meta http-equiv='cache-control' content='no-cache'>
    <meta http-equiv='expires' content='0'>
    <meta http-equiv='pragma' content='no-cache'>
    <script type="text/javascript" src="tigrakal/tcal.js"></script>
    <link href="search/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="search/css/bootstrap-select.min.css" />
  <!--  <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> -->
    <script>

        
    </script>
</head>
<body  onload="ponistavanje()">
<table class="table table-responsive" style="width: 50%; background-color: #f5f5f5;">
<tr>
    <td class='width-35-percent'>Odaberi vrstu termina: </td>
    <td>
        <select id="jedina" name="jedina" style="padding: 7px"  onchange="getval(this);">
            <option value="1">Termin u skoli</option>
            <option value="2">Slobodan termin</option>
        </select>
    </td>
</tr>
</table>

<?php
error_reporting(E_ALL); 
ini_set("display_errors",1);
ini_set('memory_limit', '-1'); 
// core configuration
//session_start();
include_once "config/core.php";
include_once "config/funkcije.php";

// set page title
$page_title = "Zakaži čas:";

// include login checker
if( $_SESSION['access_level'] == "Customer"){
    include_once "login_checker.php";
}else{ include_once "admin/login_checker.php";}

// include classes
include_once 'config/database.php';
include_once 'objects/user.php';
include_once 'objects/kalendar.php';
include_once "libs/php/utils.php";
include_once 'objects/djak.php';
include_once 'objects/grupa.php';
include_once 'objects/jezik.php';
include_once 'objects/nivo_znanja.php';
include_once 'objects/ucionica.php';

// include page header HTML
include_once "layout_head.php";

if(isset($_GET['vreme'])){
    $vreme = $_GET['vreme'];
    $pomocna = explode("/",$vreme);
    $vreme = $pomocna[0];
    $uciona = $pomocna[1];
  //  echo "uciona = ", $uciona;
    $datum = substr($vreme,0,10);
    $p77 = explode ("-", $datum);
    $datum = $p77[2] . "." . $p77[1] . "." . $p77[0];
	$sati_sat=substr($vreme,11,2);
    $sati_min=substr($vreme,14,2);
	/*
	echo"<br/>Vreme je:",$vreme,"<br/>";
    echo"<br/>DAtum:",$datum,"<br/>";
    echo"<br/>Sati:",$sati_sat,"<br/>";
    echo"<br/>Sati:",$sati_min,"<br/>";
    */

}

echo "<div id='termin' class='col-md-12' >";
$database = new Database();
$db = $database->getConnection();
$profesor = new User($db);
$djak = new djak($db);
$grupa = new grupa($db);
$jezik = new jezik($db);
$nivo_znanja = new nivo_znanja($db);
$ucionica = new ucionica($db);
$termin = new kalendar($db);

$stmt_lokacija = $ucionica->read_one_ucionica1($uciona);
$row_ucionica = $stmt_lokacija->fetch(PDO::FETCH_ASSOC);
$loc = $row_ucionica['fk_lokacija'];
//echo "lokacija", $loc,"<br/>";
// registration form HTML
// code when form was submitted
// if form was posted
if(isset($_POST['skola'])){

    // get database connection


    // initialize objects
   
    $utils = new Utils();

    // set user email to detect if it already exists
    $periodicni = $_POST['periodicni'];
    echo "periodicni:",$periodicni;
    if($periodicni == 2){
        $datum_od = $_POST['datum_od'];
        $datum_do = $_POST['datum_do'];
        $tmp77 = explode (".", $_POST['datum_od']);
        $_POST['datum_od'] = $tmp77[2] . "-" . $tmp77[1] . "-" . $tmp77[0];
        $tmp77 = explode (".", $_POST['datum_do']);
        $_POST['datum_do'] = $tmp77[2] . "-" . $tmp77[1] . "-" . $tmp77[0];
        $datum_od = $_POST['datum_od'];
        $datum_do = $_POST['datum_do'];
        $termin->grupa=$_POST['grupa'];


        $termin->zoom_skype = $_POST['zoom_skype'] ?? 1;

        $datum_termina = $_POST['datum_termina'];
        $termin->ucionica=$_POST['ucionica'] ?? null;
        $termin->grupa = $_POST['grupa'] ?? null;
         $stts = $grupa->read_one_grupa($termin->grupa,"grupe");
        $row_category_analiza_grupe = $stts->fetch(PDO::FETCH_ASSOC);
        $grupni_profesor = $row_category_analiza_grupe['fk_profesor'];
        // echo '<script type="text/javascript">alert("'.$grupni_profesor.'");</script>';
        $smstat = $profesor->read_one_profesor($grupni_profesor,"users");
        $row_category_tarzenje_boje = $smstat->fetch(PDO::FETCH_ASSOC);
       // $boja = $row_category_tarzenje_boje['color_prof'];
        $stmt_ucionica = $ucionica->read_one_ucionica($_POST['ucionica'],'ucionice');
        $row_ucionica = $stmt_ucionica->fetch(PDO::FETCH_ASSOC);
        $boja = $row_category_tarzenje_boje['color_prof'] ?? 0;
        $termin->color = $boja;
        $termin->komentar=$_POST['komentar'];
        $termin->status = 1;
        $termin->fk_lokacija = $loc;

        echo "<br/>datum od: ", $datum_od, "  Datum do: ",$datum_do,"<br/>";
       

        $begin = new DateTime( $_POST['datum_od'] );
        $end   = new DateTime( $_POST['datum_do'] );

        $myDate =  $_POST['datum_od'];
        $next_monday = date('Y-m-d', strtotime("next monday", strtotime($myDate)));
        echo "prvi ponedeljak: ", $next_monday;

        $next_monday_start_end = new DateTime(  $next_monday );
        $radni_dani = array('Mon','Tue','Wed','Thu','Fri','Sat');

                $isto_naizmenicno = $_POST['pre_posle_1'];
                echo "Isto = 1, naizmenicno =2 : ",$isto_naizmenicno;
                $dan_za_cas = $_POST['dan_1'];
                echo "<br/> Dan kada ce biti cas: ", $dan_za_cas,"<br/>";
                $postojanje_rasporeda = $_POST['postojanje_rasporeda'];
                  echo "Poztojanje rasporeda : ",$postojanje_rasporeda;

                if($postojanje_rasporeda != 4){
                    if($isto_naizmenicno == 1){ // uvek isto
                        $sati_od = $_POST['sati_od_1'];
                        $minuti_od = $_POST['minuti_od_1'];
                        $sati_do = $_POST['sati_do_1'];
                        $minuti_do = $_POST['minuti_do_1'];
                          //  echo "Od: ",$sati_od,":",$minuti_od," /DO: ",$sati_do,":",$minuti_do,"<br/>";
                        for($i = $begin; $i < $end; $i->modify('+1 day')){
                            echo "<br/>",$i->format("Y-m-d")," i to je dan: ";
                            $timestamp = strtotime($i->format("Y-m-d"));
                            $day = date('D', $timestamp);
                            echo $day,"  /  ";
                            if($day == $dan_za_cas){
                               include 'zakazivanje_periodicno_prvi.php';
                            }
                        }
                    }else{
                        $sati_od = $_POST['sati_od_1_n'];
                        $minuti_od = $_POST['minuti_od_1_n'];
                        $sati_do = $_POST['sati_do_1_n'];
                        $minuti_do = $_POST['minuti_do_1_n'];
    
                        $sati_od12 = $_POST['sati_od_12_n'];
                        $minuti_od12 = $_POST['minuti_od_12_n'];
                        $sati_do12 = $_POST['sati_do_12_n'];
                        $minuti_do12 = $_POST['minuti_do_12_n'];
                        $prvo = 1;
                        for($i = $begin; $i < $end; $i->modify('+1 day')){
                            echo "<br/>",$i->format("Y-m-d")," i to je dan: ";
                            $timestamp = strtotime($i->format("Y-m-d"));
                            $day = date('D', $timestamp);
                            echo $day,"  /  ";
                            if($day == $dan_za_cas  ){
                                if($prvo == 1){
                                    echo " Pisemo Od: ",$sati_od,":",$minuti_od," /DO: ",$sati_do,":",$minuti_do,"<br/>";
                                    include 'zakazivanje_periodicno_prvi.php';
                                    $prvo = 2;
                                }else{
                                    echo " Pisemo Od: ",$sati_od12,":",$minuti_od12," /DO: ",$sati_do12,":",$minuti_do12,"<br/>";
                                    include 'zakazivanje_periodicno_drugi.php';
                                    $prvo = 1;
                                }
                            }
                
                        }
                    }

                }else{
                    echo "Ke vidime sta ce radime";
                   
                    $stmt_brisi_termine_grupa_interval = $termin->brisi_termine_grupa_interval($datum_od, $datum_do , $termin->grupa); 
                    var_dump($stmt_brisi_termine_grupa_interval);            
                }
                  
               

            
            
                        /*
                                for($i = $begin; $i < $end; $i->modify('+1 day')){
                                    echo "<br/>",$i->format("Y-m-d")," i to je dan: ";
                                    $timestamp = strtotime($i->format("Y-m-d"));
                                    $day = date('D', $timestamp);
                                    echo $day,"  /  ";

                                }
                        */        
    }else{
      // jedan termin - zakazan cas  
        $tmp77 = explode (".", $_POST['datum_termina']);
        $_POST['datum_termina'] = $tmp77[2] . "-" . $tmp77[1] . "-" . $tmp77[0];
        $datum_termina = $_POST['datum_termina'];
        $termin->ucionica=$_POST['ucionica'];
        echo "Ucionica je br: ",$termin->ucionica;
        $termin->grupa=$_POST['grupa'];
      //  $termingrua=$termin->grupa;
        $termin->zoom_skype = $_POST['zoom_skype'] ?? 1;
        $stts = $grupa->read_one_grupa($termin->grupa,"grupe");
        $row_category_analiza_grupe = $stts->fetch(PDO::FETCH_ASSOC);
        $grupni_profesor = $row_category_analiza_grupe['fk_profesor'];
        // echo '<script type="text/javascript">alert("'.$grupni_profesor.'");</script>';
        $smstat = $profesor->read_one_profesor($grupni_profesor,"users");
        $row_category_tarzenje_boje = $smstat->fetch(PDO::FETCH_ASSOC);
       // $boja = $row_category_tarzenje_boje['color_prof'];
        $stmt_ucionica = $ucionica->read_one_ucionica($_POST['ucionica'],'ucionice');
        $row_ucionica = $stmt_ucionica->fetch(PDO::FETCH_ASSOC);
        $boja = $row_category_tarzenje_boje['color_prof'] ?? 0;
        $termin->color = $boja;
        echo $boja;
        $termin->komentar=$_POST['komentar'];
        $termin->pocetak=$_POST['datum_termina'].' '.$_POST['sati_od'].":".$_POST['minuti_od'].":"."00";
        $termin->kraj=$_POST['datum_termina'].' '.$_POST['sati_do'].":".$_POST['minuti_do'].":"."00";
        $termin->status = 1;
        $termin->fk_lokacija = $loc;

        if( $boja == 0  && $termin->ucionica == 0 && $row_category_analiza_grupe['nacin'] == 1 )
        {
            echo "<div class='alert alert-danger'>";
                        echo "Ako je nastava u skoli morate selektovati ucionicu";
            echo "</div>";
    
        }else{
            if($row_category_analiza_grupe['nacin'] == 2 ){
                $termin->ucionica = 2000;
                $termin->color =  $boja;
            }
    
    
    
            $krk=2;
            if($krk == 2){
                 // create the user
                 if($termin->create_termin()){
    
                    echo "<div class='alert alert-info'>";
                    echo "Uspešno ste zakazali termin proverite u kalendaru";
                //    echo'<script>window.parent.opener.location.reload();</script>';
                 //   echo"<script>window.close();</script>";
                    echo "</div>";
    
                    // empty posted values
                    $_POST=array();
    
                }else{
                    echo "<div class='alert alert-danger' role='alert'>";
                    echo "\nPDO::errorInfo():\n";
                print_r($db->errorInfo());
                //  error_log('query failed: ERROR['.$db->errorCode().':'.print_r($db->errorInfo(), true).'] QUERY['.$query.']');
    
                echo "</div>";
                }
            }
        }
    }
    echo"<script>window.close();</script>";
}
?>
<form  method='post' id='skola'>
    <?php 
        $stmt33_ucionica = $ucionica->read_All_lokacija($loc); 
    ?>
    <div id = "ucionica" style="display : block;">
    <table class='table table-responsive'>
        <tr >
            <td class='width-30-percent'>Učionica:</td>
            <td>
                <select class='form-control' name='ucionica' >
                <option value="1" >Odaberi ucionicu...</option>
                <?php while ($row_ucionica = $stmt33_ucionica->fetch(PDO::FETCH_ASSOC)){ ?>
                    <option value="<?php echo $row_ucionica['id'];  ?>" <?php if(isset($uciona) && $uciona == $row_ucionica['id'] ){ echo "selected";}  ?> > 
                      <?php  echo  $row_ucionica['ime'];  ?></option>
          
                <?php } ?>    
                </select>
            </td>
        </tr>
    </table>    
    </div>  
    <table class='table table-responsive'>  
            <?php 
             $stmt_online_grupa = $grupa->read_all_online();
             $online_grupe = array();
             while ($row_category_online_grupa = $stmt_online_grupa->fetch(PDO::FETCH_ASSOC)){
                $online_grupe[] = $row_category_online_grupa['id'];
             }
             $stmt_skola_grupa = $grupa->read_all_skola();
             $skola_grupe = array();
             while ($row_category_skola_grupa = $stmt_skola_grupa->fetch(PDO::FETCH_ASSOC)){
                $skola_grupe[] = $row_category_skola_grupa['id'];
             }

            // echo var_dump($online_grupe);
            ?>
            <script type="text/javascript">
            var jArray = <?php echo json_encode($online_grupe); ?>;
            var skolaArray = <?php echo json_encode($skola_grupe); ?>;
            function vataj_online(sel)
            {
                if( jArray.includes(sel.value)){document.getElementById('online_grupa').style.display = 'block'; }
                if( jArray.includes(sel.value)){document.getElementById('ucionica').style.display = 'none'; }
                if( skolaArray.includes(sel.value)){document.getElementById('online_grupa').style.display = 'none'; }
                if( skolaArray.includes(sel.value)){document.getElementById('ucionica').style.display = 'block'; }
            }

          /*  for(var i=0; i<jArray.length; i++){
                alert(jArray[i]);
            } */
            </script>
        <tr>
            <td class='width-30-percent'>Grupa:</td>
            <td>
                <?php
                // read the scool boards from the database
                if($_SESSION['access_level'] == 'Customer'){
                    $stmt = $grupa->read_allgrupa_profesor_lokacija('grupe', $_SESSION['user_id'], $loc);
                }else{
                    $stmt = $grupa->read_allgrupa_lokacija($loc);
                }
                ?>
                <select  name='grupa' id='grupa_ucenika' class="selectpicker form-control down" data-show-subtext="true" data-live-search="true" onchange="vataj_online(this);"  required>
                    <option value="" >Odaberi grupu...</option>
                    <?php
                    while ($row_category = $stmt->fetch(PDO::FETCH_ASSOC)){     // Citanje jedne po jedne vrste  iz $smtp i mecanje u $rowcategory
                        extract($row_category);                       //ekstrakovanje na pojedniacne zapise unutar vrste
                        $stmt1 = $profesor->read_one_profesor($fk_profesor,"users");
                        $row_profesor = $stmt1->fetch(PDO::FETCH_ASSOC);
 
                        $stm_jezik = $jezik->read_one_jezik($fk_jezik,'jezik');
                        $row_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC);
                        
                        $stm_nivo = $nivo_znanja->read_one_nivo($nivo,'nivo_znanja');
                        $row_nivo_znanja = $stm_nivo->fetch(PDO::FETCH_ASSOC);

                        $fk_jezik = $row_jezik['alias'];
                        $nivo = $row_nivo_znanja['ime'];
                        if($nacin == 2){
                            echo "<option value='{$id}'   style='background-image:url(images/online1.jpg); height: 25px !important; background-repeat: no-repeat'>";
                     
                        }else{
                            echo "<option value='{$id}'  style='background-image:url(images/room3.jpg); height: 25px !important; background-repeat: no-repeat'>";
                   
                        }
                        
                        echo"&nbsp; &nbsp;{$fk_jezik}&nbsp;{$nivo}&nbsp;{$row_profesor['firstname']}&nbsp;-[{$alias}]</option>";           // vrednost z prenos je $id a prikazuje se $name, to jest ime
                    }
                    echo "</select>";
                    ?>
                    
            </td>  
            <script src="search/jquery.min.js"></script>
            <script src="search/bootstrap.min.js"></script>
            <script src="search/bootstrap-select.min.js"></script>
            </tr>
        </table>
    <div id="online_grupa"  style = " display: none " >
        <table class='table table-responsive'>
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
        </table>
    </div>
    
    <table class='table table-responsive'>
            <tr>
                    <td class='width-30-percent'>Periodicni zakazivanja casa</td>
                    <td>      
                        <select class='form-control' name='periodicni' onchange="open_periodicni(this);" >
                            <option value="1"> Jednokratni </option>
                            <option value="2"> Periodican </option>
                        </select>
                    </td>
            </tr>
    </table>
    
    <div id="vreme_jednokratno">
        <table>
            <tr>
                <td style="width: 20%;">Dana:</td>
                <td>
                    <input type="text"  name="datum_termina" <?php if(isset($datum)){echo"value='$datum'";} ?>  STYLE="background-color:white; padding: 6px;" size="10" class="tcal" />
                </td>
                <?php
                $stmt_sati = $termin->read_all_sati();
                
                ?>
                <td align="right" style="width: 10%">OD:</td>
                <td style="width: 10%">
                    <select   class='form-control' name="sati_od">
                        <?php while($row_category_sati = $stmt_sati->fetch(PDO::FETCH_ASSOC)){ ?>
                                <option value = "<?php echo $row_category_sati['sat'] ?>" <?php if( $row_category_sati['sat'] == $sati_sat){echo 'selected';} ?> ><?php echo $row_category_sati['sat']; ?></option>
                            <?php 
                        } ?>
                    </select>
                        
                </td>
                <td style="width: 10%">
                <?php 
                    $stmt_minuti = $termin->read_all_minuti();
                ?>    
                    <select  class='form-control' name="minuti_od">
                    <?php while($row_category_minuti = $stmt_minuti->fetch(PDO::FETCH_ASSOC)){ ?>
                        <option value = "<?php echo $row_category_minuti['minut'] ?>" <?php if( $sati_min == $row_category_minuti['minut']){echo "selected='selected'";} ?> ><?php echo $row_category_minuti['minut']; ?></option>
                        <?php } ?>
                    
                    </select>
                    
                </td>
                <td align="right" style="width: 10%">DO:</td>
                <?php
                    $stmt_sati_do = $termin->read_all_sati();
                    $sati_do = ($sati_sat ?? 0) + 1 ;  
                ?>
                <td align="center" style="width: 10%">
                    <select  class='form-control' name="sati_do">
                    <?php while($row_category_sati_do = $stmt_sati_do->fetch(PDO::FETCH_ASSOC)){ ?>
                        <option value = "<?php echo $row_category_sati_do['sat'] ?>" <?php if( $row_category_sati_do['sat'] == $sati_do){echo 'selected';} ?> ><?php echo $row_category_sati_do['sat']; ?></option>
                        <?php } ?>
                    
                    </select>
                    
                </td>
                <?php 
                    $stmt_minuti = $termin->read_all_minuti();
                ?>    
                <td>
                    <select  class='form-control' name="minuti_do">
                    <?php while($row_category_minuti = $stmt_minuti->fetch(PDO::FETCH_ASSOC)){ ?>
                        <option value = "<?php echo $row_category_minuti['minut'] ?>" <?php if( $sati_min == $row_category_minuti['minut']){echo "selected='selected'";} ?> ><?php echo $row_category_minuti['minut']; ?></option>
                        <?php } ?>    
                    </select>
                    
                </td>
            </tr>
            <tr class="blank_row">
                <td  colspan="4">&nbsp;</td>
            </tr>
        </table>
    </div>
    <div id="vreme_periodicno" style="display:none; background-color: #F2F2F2;">
        <table class='table table-responsive'>
            <tr style="background-color : #BDBDBD;">
                <td class='width-30-percent'>Interval zakazaivanja</td>
                
                <td style="background-color : #BDBDBD;" >
                Od:   <input type="text"  name="datum_od" <?php if(isset($datum)){echo"value='$datum'";} ?>  STYLE="background-color:white; padding: 6px; margin-right: 20px; border: 1px solic gray; border-radius : 3px; " size="10" class="tcal" />
                Do:  <input type="text"  name="datum_do" <?php if(isset($datum)){echo"value='$datum'";} ?>  STYLE="background-color:white;  padding: 6px; border: 1px solic gray; border-radius : 3px; " size="10" class="tcal" />
                </td>
            </tr>
            <tr>
                <td class='width-30-percent' style="background-color: #CECEF6;">Ukoliko postoji raspored za odredjeni dan</td>
                <td style="background-color: #CECEF6;">
                    <select class='form-control' name='postojanje_rasporeda'  >
                        <option value="1"> Ignorisati (zakazati novi i obrisati sve stare na taj dan) </option>
                        <option value="2"> Preskočiti taj dani </option>
                        <option value="3"> Dodati još jedan čas za taj dan </option>
                        <option value="4"> Obrisati sve zakazane termine u navedenom terminu za djaka </option>
                    </select>
                </td>
            </tr>
        </table>
        <div id="jednom_nedeljno"  >
            <table class='table table-responsive'>
            <tr>
                <td class='width-30-percent'>Prvi dan kojim bi se cas odrzavao:</td>
                <td>
                    <select class='form-control' name='dan_1'  >
                        <option value="Mon"> Ponedeljak </option>
                        <option value="Tue"> Utorak </option>
                        <option value="Wed"> Sreda </option>
                        <option value="Thu"> Cetvrtak </option>
                        <option value="Fri"> Petak </option>
                        <option value="Sat"> Subota </option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class='width-30-percent'>Način periodičnog zakazivanja</td>
                <td>
                    <select class='form-control' name='pre_posle_1' onchange="open_naizmenicno1(this);" >
                        <option value="1"> Uvek isto  </option>
                        <option value="2"> Naizmenicno </option>
                    </select>
                </td>
            </tr>
            </table>   
            <div id = "isto_1">
                <?php include_once "isto1.php" ?>
            </div> 
            <div id = "naizmenicno_1" style="display:none;">
                <?php include_once "naizmenicno1.php" ?>
            </div>                 
        </div>
        
        
    </div>                
    <table>
    <table class='table table-responsive'>            
        <tr>
            <td class='width-30-percent'>Komentar:</td>
            <td><textarea name='komentar' class='form-control' ></textarea></td>
        </tr>
    </table>
    <tr>
            <td></td>
            <td>
                <button type="submit" name="skola" value="skola" class="btn btn-danger">
                    <span class="glyphicon glyphicon-plus"></span> snimi i izadji
                </button>
            </td>
        </tr>
    </table>
</form>
</div>




<div id='slobodan' class='col-md-12' style = 'display: none;'>
       
        <?php
        $database = new Database();
        $db = $database->getConnection();
        $profesor = new User($db);
        $djak = new djak($db);
        $grupa = new grupa($db);
        // registration form HTML
        // code when form was submitted
        // if form was posted
        if(isset($_POST['slobodan1'])){
            $termin = new kalendar($db);
            $utils = new Utils();

            // check if email already exists
            $tmp77 = explode (".", $_POST['datum_termina_s']);
            $_POST['datum_termina_s'] = $tmp77[2] . "-" . $tmp77[1] . "-" . $tmp77[0];
            $termin->color = "#DBA901";
            echo $boja;
            $termin->grupa=9177;
            $termin->komentar=$_SESSION['firstname'].": ".$_POST['komentar_s'];
            $termin->pocetak=$_POST['datum_termina_s'].' '.$_POST['sati_od_s'].":".$_POST['minuti_od_s'].":"."00";
            $termin->kraj=$_POST['datum_termina_s'].' '.$_POST['sati_do_s'].":".$_POST['minuti_do_s'].":"."00";
            $termin->fk_profesor = $_SESSION['user_id'];
            $termin->status = 1;
            $termin->fk_lokacija = $loc;
            /* $vreme_od=$sati_od.":".$minuti_od.":"."00";
                        $odd= $datum_termina.' '.$vreme_od;  */
            // create the user
            if($termin->create_termin_slobodan()){
                echo "<div class='alert alert-info'>";
                echo "Uspešno ste zakazali termin proverite u kalendaru";
                    //  sleep(1);
                //  echo'<script>window.parent.opener.location.reload();</script>';
                echo"<script>window.close();</script>";
                echo "</div>";
                // empty posted values
                $_POST=array();
            }else{
                echo "<div class='alert alert-danger' role='alert'>Nije bilo moguce upisati termin. Please try again.</div>";
            }
        }
        ?>
            <form  method='post' id='slobodan1'>
                <table class='table table-responsive'>
                        <td>Opis termina:</td>
                        <td><textarea name='komentar_s' class='form-control' ></textarea></td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td style="width: 20%;">Dana:</td>
                        <td>
                            <input type="text"  name="datum_termina_s" <?php if(isset($datum)){echo"value='$datum'";} ?>  STYLE="background-color:white; padding: 6px;" size="10" class="tcal" />
                        </td>
                        <td align="right" style="width: 10%">OD: </td>
                        <td style="width: 10%"> 
                            
                            <select  class='form-control form-control-lg' name="sati_od_s"  >
                                <?php 
                                $stmt_sati = $termin->read_all_sati();
                                while($row_category_sati = $stmt_sati->fetch(PDO::FETCH_ASSOC)){ ?>
                                <option value = "<?php echo $row_category_sati['sat'] ?>" <?php if(isset($sati_sat) && $sati_sat==$row_category_sati['sat']){echo "selected";} ?> ><?php echo $row_category_sati['sat']; ?></option>
                                <?php } ?>
                            </select>
                            
                        </td>
                        <td style="width: 10%">
                           
                            <select class='form-control form-control-sm' name="minuti_od_s">
                            <?php $stmt_minuti = $termin->read_all_minuti();
                                while($row_category_minuti = $stmt_minuti->fetch(PDO::FETCH_ASSOC)){ ?>
                                <option value = "<?php echo $row_category_minuti['minut'] ?>" <?php if(isset($sati_min) && $sati_min == $row_category_minuti['minut'] ){echo "selected";} ?> ><?php echo $row_category_minuti['minut']; ?></option>
                                <?php } ?>
                            </select>
                            


                        </td>
                        <td align="right" style="width: 10%">DO:</td>
                        <td align="center" style="width: 10%">
                            
                            <select  class='form-control form-control-lg' name="sati_do_s"  >
                                <?php 
                                $stmt_sati = $termin->read_all_sati();
                                while($row_category_sati = $stmt_sati->fetch(PDO::FETCH_ASSOC)){ ?>
                                <option value = "<?php echo $row_category_sati['sat'] ?>" <?php if(isset($sati_sat) && $sati_sat + 1 == $row_category_sati['sat']){echo "selected";} ?> ><?php echo $row_category_sati['sat']; ?></option>
                                <?php } ?>
                            </select>
                           
                        </td>
                        <td>
                            
                            <select class='form-control form-control-sm' name="minuti_do_s">
                            <?php $stmt_minuti = $termin->read_all_minuti();
                                while($row_category_minuti = $stmt_minuti->fetch(PDO::FETCH_ASSOC)){ ?>
                                <option value = "<?php echo $row_category_minuti['minut'] ?>" <?php if(isset($sati_min) && $sati_min == $row_category_minuti['minut'] ){echo "selected";} ?> ><?php echo $row_category_minuti['minut']; ?></option>
                                <?php } ?>
                            </select>
                           
                        </td>
                    </tr>
                    <tr class="blank_row">
                        <td  colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <button type="submit" name="slobodan1"  value="slobodan1" class="btn btn-danger">
                                <span class="glyphicon glyphicon-plus"></span> snimi i izadji
                            </button>
                        </td>
                    </tr>

                </table>
            </form>
</div>
<?php

//echo "";



// include page footer HTML
//include_once "layout_foot.php";
?>
<script>

function ponistavanje() {
   
 //  $('#vreme_periodicno').find('input, textarea, button, select').attr('disabled','disabled');
 //  $('#vreme_jednokratno').find('input, textarea, button, select').attr('disabled','disabled');
 //  $('#dva_puta_nedeljno').find('input, textarea, button, select').attr('disabled','disabled');
 //  $('#tri_puta_nedeljno').find('input, textarea, button, select').attr('disabled','disabled');
 
   
   
  
//$(document).one('ready',function(){
  
  

};

function open_periodicni(sel)
{
    if(sel.value == 1){
        document.getElementById('vreme_jednokratno').style.display = 'block';
     //   $("#vreme_jednokratno").find("select, input, textarea, button, select").removeAttr("disabled"); 
        document.getElementById('vreme_periodicno').style.display = 'none';
     //   $('#vreme_periodicno').find('input, textarea, button, select').attr('disabled','disabled');
        
        }
    if(sel.value == 2){ 
        document.getElementById('vreme_jednokratno').style.display = 'none';
     //   $('#vreme_jednokratno').find('input, textarea, button, select').attr('disabled','disabled');
        document.getElementById('vreme_periodicno').style.display = 'block';
     //   $("#vreme_periodicno").find("select, input, textarea, button, select").removeAttr("disabled");
        
    }
    /* alert(sel.value); */
}
function open_broj_termina(sel)
{
    if(sel.value == 1){
        document.getElementById('dva_puta_nedeljno').style.display = 'none'; 
        document.getElementById('tri_puta_nedeljno').style.display = 'none';
        }
    if(sel.value == 2){ 
        document.getElementById('dva_puta_nedeljno').style.display = 'block'; 
        document.getElementById('tri_puta_nedeljno').style.display = 'none';
    }
    if(sel.value == 3){ 
        document.getElementById('dva_puta_nedeljno').style.display = 'block'; 
        document.getElementById('tri_puta_nedeljno').style.display = 'block';
    }
    /* alert(sel.value); */
}

function open_naizmenicno1(sel)
{
    if(sel.value == 1){
        document.getElementById('isto_1').style.display = 'block'; 
        document.getElementById('naizmenicno_1').style.display = 'none';
        }
    if(sel.value == 2){ 
        document.getElementById('isto_1').style.display = 'none'; 
        document.getElementById('naizmenicno_1').style.display = 'block';
    }
    
    /* alert(sel.value); */
}

function open_naizmenicno2(sel)
{
    if(sel.value == 1){
        document.getElementById('isto_2').style.display = 'block'; 
        document.getElementById('naizmenicno_2').style.display = 'none';
        }
    if(sel.value == 2){ 
        document.getElementById('isto_2').style.display = 'none'; 
        document.getElementById('naizmenicno_2').style.display = 'block';
    }
    
    /* alert(sel.value); */
}

function open_naizmenicno3(sel)
{
    if(sel.value == 1){
        document.getElementById('isto_3').style.display = 'block'; 
        document.getElementById('naizmenicno_3').style.display = 'none';
        }
    if(sel.value == 2){ 
        document.getElementById('isto_3').style.display = 'none'; 
        document.getElementById('naizmenicno_3').style.display = 'block';
    }
    
    /* alert(sel.value); */
}

function getval(sel)
        {
            if(sel.value == 1){document.getElementById('slobodan').style.display = 'none'; document.getElementById('termin').style.display = 'block';}
            if(sel.value == 2){ document.getElementById('termin').style.display = 'none'; document.getElementById('slobodan').style.display = 'block';}
            /* alert(sel.value); */
        }
</script>
</body>