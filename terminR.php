<head>
<head>
    <link rel="stylesheet" type="text/css" href="tigrakal/tcal.css" />
    <script type="text/javascript" src="tigrakal/tcal.js"></script>
    <link href="search/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="search/css/bootstrap-select.min.css" />
  <!--  <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> -->
    <script>

        function getval(sel)
        {
            if(sel.value == 1){document.getElementById('slobodan').style.display = 'none'; document.getElementById('termin').style.display = 'block';}
            if(sel.value == 2){ document.getElementById('termin').style.display = 'none'; document.getElementById('slobodan').style.display = 'block';}
            /* alert(sel.value); */
        }
    </script>
</head>
<table class="table table-responsive" style="width: 50%; " bgcolor="#00FF00">
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

// set page title
$page_title = "Termin zakazivanje";

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

echo "<div id='termin' class='col-md-12'>";
$database = new Database();
$db = $database->getConnection();
$profesor = new User($db);
$djak = new djak($db);
$grupa = new grupa($db);
$jezik = new jezik($db);
$nivo_znanja = new nivo_znanja($db);
$ucionica = new ucionica($db);
// registration form HTML
// code when form was submitted
// if form was posted
if(isset($_POST['skola'])){

    // get database connection


    // initialize objects
    $termin = new kalendar($db);
    $utils = new Utils();

    // set user email to detect if it already exists


    // check if email already exists
    $tmp77 = explode (".", $_POST['datum_termina']);
    $_POST['datum_termina'] = $tmp77[2] . "-" . $tmp77[1] . "-" . $tmp77[0];
    // create termin
    // set values to object properties

    $termin->ucionica=$_POST['ucionica'];
    echo "Ucionica je br: ",$termin->ucionica;
    $termin->grupa=$_POST['grupa'];
    $termingrua=$termin->grupa;
    $termin->zoom_skype = $_POST['zoom_skype'];
    //  echo '<script type="text/javascript">alert("'.$termingrua.'");</script>';
    $stts = $grupa->read_one_grupa($termin->grupa,"grupe");
    $row_category_analiza_grupe = $stts->fetch(PDO::FETCH_ASSOC);
    $grupni_profesor = $row_category_analiza_grupe['fk_profesor'];
    // echo '<script type="text/javascript">alert("'.$grupni_profesor.'");</script>';
    $smstat = $profesor->read_one_profesor($grupni_profesor,"users");
    $row_category_tarzenje_boje = $smstat->fetch(PDO::FETCH_ASSOC);
   // $boja = $row_category_tarzenje_boje['color_prof'];
    $stmt_ucionica = $ucionica->read_one_ucionica($_POST['ucionica'],'ucionice');
    $row_ucionica = $stmt_ucionica->fetch(PDO::FETCH_ASSOC);
    $boja = $row_ucionica['color_room'] ?? 0;
    $termin->color = $boja;
    echo $boja;
    // echo '<script type="text/javascript">alert("'.$boja.'");</script>';
    $termin->komentar=$_POST['komentar'];
    $termin->pocetak=$_POST['datum_termina'].' '.$_POST['sati_od'].":".$_POST['minuti_od'].":"."00";
    $termin->kraj=$_POST['datum_termina'].' '.$_POST['sati_do'].":".$_POST['minuti_do'].":"."00";
    $termin->status = 1;
    
    if( $boja == 0  && $termin->ucionica == 0 && $row_category_analiza_grupe['nacin'] == 1 )
    {
        echo "<div class='alert alert-danger'>";
                    echo "Ako je nastava u skoli morate selektovati ucionicu";
                //    echo'<script>window.parent.opener.location.reload();</script>';
                //    echo"<script>window.close();</script>";
                    echo "</div>";

    }else{
        if($row_category_analiza_grupe['nacin'] == 2 ){
            $termin->ucionica = 2000;
            $termin->color =  "#C8FE2E";
        }





                
            // create the user
                if($termin->create_termin()){

                    echo "<div class='alert alert-info'>";
                    echo "Uspešno ste zakazali termin proverite u kalendaru";
                //    echo'<script>window.parent.opener.location.reload();</script>';
                    echo"<script>window.close();</script>";
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
?>
<form action='termin.php' method='post' id='skola'>

    
    <?php 
    $stmt33_ucionica = $ucionica->read_All(); 

    
    ?>

    <div id = "ucionica" style="display : block;">
    <table class='table table-responsive'>
    
        <tr >
            <td class='width-30-percent'>Učionica:</td>
            <td>
                <select class='form-control' name='ucionica' >
                <option value="0">Odaberi učionicu</option>
                <?php while ($row_ucionica = $stmt33_ucionica->fetch(PDO::FETCH_ASSOC)){ ?>
                    <option value=<?php echo $row_ucionica['id'];  ?>  "> 
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
                $stmt = $grupa->read_allgrupa("grupe");
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
                            echo "<option value='{$id}'  style='background-image:url(images/online1.jpg); height: 25px !important; background-repeat: no-repeat'>";
                     
                        }else{
                            echo "<option value='{$id}'  style='background-image:url(images/room3.jpg); height: 25px !important; background-repeat: no-repeat'>";
                   
                        }
                        
                        echo"&nbsp; &nbsp;{$fk_jezik}&nbsp;{$nivo}&nbsp;{$row_profesor['firstname']}&nbsp;-[{$alias}]</option>";           // vrednost z prenos je $id a prikazuje se $name, to jest ime
                    }
                    echo "</select>";
                    ?>
                    <script type="text/javascript">
                    document.getElementById('grupa_ucenika').value = "<?php echo $_POST['grupa_ucenika'];?>";
                    </script>
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
            <td class='width-30-percent'>Komentar:</td>
            <td><textarea name='komentar' class='form-control' ></textarea></td>
        </tr>
    </table>
    <table>
        <tr>
            <td style="width: 20%;">Dana:</td>
            <td>
                <input type="text"  name="datum_termina" <?php if(isset($datum)){echo"value='$datum'";} ?> required STYLE="background-color:white; padding: 6px;" size="10" class="tcal" />
            </td>
            <td align="right" style="width: 10%">OD:</td>
            <td style="width: 10%"> <select id="sata_od"  class='form-control' name="sati_od">
                    <option value="8" <?php if(isset($sati_sat) && $sati_sat==8){echo "selected";} ?> >8</option>
                    <option value="9" <?php if(isset($sati_sat) && $sati_sat==9){echo "selected";} ?> >9</option>
                    <option value="10" <?php if(isset($sati_sat) && $sati_sat==10){echo "selected";}  ?> >10</option>
                    <option value="11" <?php if(isset($sati_sat) && $sati_sat==11){echo "selected";}  ?> >11</option>
                    <option value="12" <?php if(isset($sati_sat) && $sati_sat==12){echo "selected";}  ?> >12</option>
                    <option value="13" <?php if(isset($sati_sat) && $sati_sat==13){echo "selected";}  ?> >13</option>
                    <option value="14" <?php if(isset($sati_sat) && $sati_sat==14){echo "selected";}  ?> >14</option>
                    <option value="15" <?php if(isset($sati_sat) && $sati_sat==15){echo "selected";}  ?> >15</option>
                    <option value="16" <?php if(isset($sati_sat) && $sati_sat==16){echo "selected";}  ?> >16</option>
                    <option value="17" <?php if(isset($sati_sat) && $sati_sat==17){echo "selected";}  ?> >17</option>
                    <option value="18" <?php if(isset($sati_sat) && $sati_sat==18){echo "selected";}  ?> >18</option>
                    <option value="19" <?php if(isset($sati_sat) && $sati_sat==19){echo "selected";}  ?> >19</option>
                    <option value="20" <?php if(isset($sati_sat) && $sati_sat==20){echo "selected";}  ?> >20</option>
                    <option value="21" <?php if(isset($sati_sat) && $sati_sat==21){echo "selected";}  ?> >21</option>
                    <option value="22" <?php if(isset($sati_sat) && $sati_sat==22){echo "selected";}  ?> >22</option>
                    <option value="23" <?php if(isset($sati_sat) && $sati_sat==23){echo "selected";}  ?> >23</option>
                </select>
                <script type="text/javascript">
                    document.getElementById("sati_od").value = "<?php echo $_POST["sati_od"];?>";
                </script>
            </td>
            <td style="width: 10%">
                <select id="minuta_od" class='form-control' name="minuti_od">
                    <option value="00" <?php if(isset($sati_min) && $sati_min==0){echo "selected";} ?> >00</option>
                    <option value="15" <?php if(isset($sati_min) && $sati_min==15){echo "selected";} ?> >15</option>
                    <option value="30" <?php if(isset($sati_min) && $sati_min==30){echo "selected";} ?> >30</option>
                    <option value="45" <?php if(isset($sati_min) && $sati_min==45){echo "selected";} ?> >45</option>
                </select>
                <script type="text/javascript">
                    document.getElementById("minuti_od").value = "<?php echo $_POST["minuti_od"];?>";
                </script>


            </td>
            <td align="right" style="width: 10%">DO:</td>
            <td align="center" style="width: 10%">
                <select id="sata_do" class='form-control' name="sati_do">

                    <option value="8" >8</option>
                    <option value="9" <?php if(isset($sati_sat) && $sati_sat==8){echo "selected";} ?> >9</option>
                    <option value="10" <?php if(isset($sati_sat) && $sati_sat==9){echo "selected";} ?> >10</option>
                    <option value="11" <?php if(isset($sati_sat) && $sati_sat==10){echo "selected";} ?> >11</option>
                    <option value="12" <?php if(isset($sati_sat) && $sati_sat==11){echo "selected";} ?> >12</option>
                    <option value="13" <?php if(isset($sati_sat) && $sati_sat==12){echo "selected";} ?> >13</option>
                    <option value="14" <?php if(isset($sati_sat) && $sati_sat==13){echo "selected";} ?> >14</option>
                    <option value="15" <?php if(isset($sati_sat) && $sati_sat==14){echo "selected";} ?> >15</option>
                    <option value="16" <?php if(isset($sati_sat) && $sati_sat==15){echo "selected";} ?> >16</option>
                    <option value="17" <?php if(isset($sati_sat) && $sati_sat==16){echo "selected";} ?> >17</option>
                    <option value="18" <?php if(isset($sati_sat) && $sati_sat==17){echo "selected";} ?> >18</option>
                    <option value="19" <?php if(isset($sati_sat) && $sati_sat==18){echo "selected";} ?> >19</option>
                    <option value="20" <?php if(isset($sati_sat) && $sati_sat==19){echo "selected";} ?> >20</option>
                    <option value="21" <?php if(isset($sati_sat) && $sati_sat==20){echo "selected";} ?> >21</option>
                    <option value="22" <?php if(isset($sati_sat) && $sati_sat==21){echo "selected";} ?> >22</option>
                    <option value="23" <?php if(isset($sati_sat) && $sati_sat==22){echo "selected";} ?> >23</option>
                    <option value="24" <?php if(isset($sati_sat) && $sati_sat==23){echo "selected";} ?> >24</option>
                </select>
                <script type="text/javascript">
                    document.getElementById("sati_do").value = "<?php echo $_POST["sati_do"];?>";
                </script>
            </td>
            <td>
                <select id="minuta_do" class='form-control' name="minuti_do">

                    <option value="00" <?php if(isset($sati_min) && $sati_min==0){echo "selected";} ?> >00</option>
                    <option value="15" <?php if(isset($sati_min) && $sati_min==15){echo "selected";} ?> >15</option>
                    <option value="30" <?php if(isset($sati_min) && $sati_min==30){echo "selected";} ?> >30</option>
                    <option value="45" <?php if(isset($sati_min) && $sati_min==45){echo "selected";} ?> >45</option>
                </select>
                <script type="text/javascript">
                    document.getElementById("minuti_do").value = "<?php echo $_POST["minuti_do"]; ?>";                  _do"];?>";
                </script>
            </td>
        </tr>
        <tr class="blank_row">
            <td  colspan="4">&nbsp;</td>
        </tr>
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
<?php

echo "</div>";

echo "<div id='slobodan' class='col-md-12' style = 'display: none;'>";
$database = new Database();
$db = $database->getConnection();
$profesor = new User($db);
$djak = new djak($db);
$grupa = new grupa($db);
// registration form HTML
// code when form was submitted
// if form was posted
if(isset($_POST['slobodan'])){

    $termin = new kalendar($db);
    $utils = new Utils();

    // check if email already exists
    $tmp77 = explode (".", $_POST['datum_termina']);
    $_POST['datum_termina'] = $tmp77[2] . "-" . $tmp77[1] . "-" . $tmp77[0];

    $termin->color = "#DBA901";

    echo $boja;
    $termin->grupa=9177;

    $termin->komentar=$_SESSION['firstname'].": ".$_POST['komentar'];
    $termin->pocetak=$_POST['datum_termina'].' '.$_POST['sati_od'].":".$_POST['minuti_od'].":"."00";
    $termin->kraj=$_POST['datum_termina'].' '.$_POST['sati_do'].":".$_POST['minuti_do'].":"."00";
    $termin->fk_profesor = $_SESSION['user_id'];
    $termin->status = 1;


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
<form action='termin.php' method='post' id='register'>

    <table class='table table-responsive'>




            <td>Opis termina:</td>
            <td><textarea name='komentar' class='form-control' ></textarea></td>
        </tr>
    </table>
    <table>
        <tr>
            <td style="width: 20%;">Dana:</td>
            <td>
                <input type="text"  name="datum_termina" <?php if(isset($datum)){echo"value='$datum'";} ?> required STYLE="background-color:white; padding: 6px;" size="10" class="tcal" />
            </td>
            <td align="right" style="width: 10%">OD:</td>
            <td style="width: 10%"> <select id="sata_od"  class='form-control' name="sati_od">
                    <option value="8" <?php if(isset($sati_sat) && $sati_sat==8){echo "selected";} ?> >8</option>
                    <option value="9" <?php if(isset($sati_sat) && $sati_sat==9){echo "selected";} ?> >9</option>
                    <option value="10" <?php if(isset($sati_sat) && $sati_sat==10){echo "selected";}  ?> >10</option>
                    <option value="11" <?php if(isset($sati_sat) && $sati_sat==11){echo "selected";}  ?> >11</option>
                    <option value="12" <?php if(isset($sati_sat) && $sati_sat==12){echo "selected";}  ?> >12</option>
                    <option value="13" <?php if(isset($sati_sat) && $sati_sat==13){echo "selected";}  ?> >13</option>
                    <option value="14" <?php if(isset($sati_sat) && $sati_sat==14){echo "selected";}  ?> >14</option>
                    <option value="15" <?php if(isset($sati_sat) && $sati_sat==15){echo "selected";}  ?> >15</option>
                    <option value="16" <?php if(isset($sati_sat) && $sati_sat==16){echo "selected";}  ?> >16</option>
                    <option value="17" <?php if(isset($sati_sat) && $sati_sat==17){echo "selected";}  ?> >17</option>
                    <option value="18" <?php if(isset($sati_sat) && $sati_sat==18){echo "selected";}  ?> >18</option>
                    <option value="19" <?php if(isset($sati_sat) && $sati_sat==19){echo "selected";}  ?> >19</option>
                    <option value="20" <?php if(isset($sati_sat) && $sati_sat==20){echo "selected";}  ?> >20</option>
                    <option value="21" <?php if(isset($sati_sat) && $sati_sat==21){echo "selected";}  ?> >21</option>
                    <option value="22" <?php if(isset($sati_sat) && $sati_sat==22){echo "selected";}  ?> >22</option>
                    <option value="23" <?php if(isset($sati_sat) && $sati_sat==23){echo "selected";}  ?> >23</option>
                </select>
                <script type="text/javascript">
                    document.getElementById("sati_od").value = "<?php echo $_POST["sati_od"];?>";
                </script>
            </td>
            <td style="width: 10%">
                <select id="minuta_od" class='form-control' name="minuti_od">
                    <option value="00" <?php if(isset($sati_min) && $sati_min==0){echo "selected";} ?> >00</option>
                    <option value="15" <?php if(isset($sati_min) && $sati_min==15){echo "selected";} ?> >15</option>
                    <option value="30" <?php if(isset($sati_min) && $sati_min==30){echo "selected";} ?> >30</option>
                    <option value="45" <?php if(isset($sati_min) && $sati_min==45){echo "selected";} ?> >45</option>
                </select>
                <script type="text/javascript">
                    document.getElementById("minuti_od").value = "<?php echo $_POST["minuti_od"];?>";
                </script>


            </td>
            <td align="right" style="width: 10%">DO:</td>
            <td align="center" style="width: 10%">
                <select id="sata_do" class='form-control' name="sati_do">

                    <option value="8" >8</option>
                    <option value="9" <?php if(isset($sati_sat) && $sati_sat==8){echo "selected";} ?> >9</option>
                    <option value="10" <?php if(isset($sati_sat) && $sati_sat==9){echo "selected";} ?> >10</option>
                    <option value="11" <?php if(isset($sati_sat) && $sati_sat==10){echo "selected";} ?> >11</option>
                    <option value="12" <?php if(isset($sati_sat) && $sati_sat==11){echo "selected";} ?> >12</option>
                    <option value="13" <?php if(isset($sati_sat) && $sati_sat==12){echo "selected";} ?> >13</option>
                    <option value="14" <?php if(isset($sati_sat) && $sati_sat==13){echo "selected";} ?> >14</option>
                    <option value="15" <?php if(isset($sati_sat) && $sati_sat==14){echo "selected";} ?> >15</option>
                    <option value="16" <?php if(isset($sati_sat) && $sati_sat==15){echo "selected";} ?> >16</option>
                    <option value="17" <?php if(isset($sati_sat) && $sati_sat==16){echo "selected";} ?> >17</option>
                    <option value="18" <?php if(isset($sati_sat) && $sati_sat==17){echo "selected";} ?> >18</option>
                    <option value="19" <?php if(isset($sati_sat) && $sati_sat==18){echo "selected";} ?> >19</option>
                    <option value="20" <?php if(isset($sati_sat) && $sati_sat==19){echo "selected";} ?> >20</option>
                    <option value="21" <?php if(isset($sati_sat) && $sati_sat==20){echo "selected";} ?> >21</option>
                    <option value="22" <?php if(isset($sati_sat) && $sati_sat==21){echo "selected";} ?> >22</option>
                    <option value="23" <?php if(isset($sati_sat) && $sati_sat==22){echo "selected";} ?> >23</option>
                    <option value="24" <?php if(isset($sati_sat) && $sati_sat==23){echo "selected";} ?> >24</option>
                </select>
                <script type="text/javascript">
                    document.getElementById("sati_do").value = "<?php echo $_POST["sati_do"];?>";
                </script>
            </td>
            <td>
                <select id="minuta_do" class='form-control' name="minuti_do">

                    <option value="00" <?php if(isset($sati_min) && $sati_min==0){echo "selected";} ?> >00</option>
                    <option value="15" <?php if(isset($sati_min) && $sati_min==15){echo "selected";} ?> >15</option>
                    <option value="30" <?php if(isset($sati_min) && $sati_min==30){echo "selected";} ?> >30</option>
                    <option value="45" <?php if(isset($sati_min) && $sati_min==45){echo "selected";} ?> >45</option>
                </select>
                <script type="text/javascript">
                    document.getElementById("minuti_do").value = "<?php echo $_POST["minuti_do"]; ?>";                  _do"];?>";
                </script>
            </td>
        </tr>
        <tr class="blank_row">
            <td  colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td></td>
            <td>
                <button type="submit" name="slobodan"  value="slobodan" class="btn btn-danger">
                    <span class="glyphicon glyphicon-plus"></span> snimi i izadji
                </button>
            </td>
        </tr>

    </table>
</form>
<?php

echo "</div>";



// include page footer HTML
//include_once "layout_foot.php";
?>