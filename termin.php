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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>  -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="search/bootstrap-select.min.js"></script>
    <script src="search/bootstrap.min.js"></script>
</head>
<!-- <body  onload="ponistavanje()"> -->
<body>
        
   
<table class="table table-responsive" style="width: 50%; background-color: #f5f5f5; display:none;" >
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
include_once 'config/autoload.php';

// include_once 'objects/user.php';
// include_once 'objects/kalendar.php';
// include_once "libs/php/utils.php";
// include_once 'objects/djak.php';
// include_once 'objects/grupa.php';
// include_once 'objects/jezik.php';
// include_once 'objects/nivo_znanja.php';
// include_once 'objects/ucionica.php';

// include page header HTML
include_once "layout_head.php";

if(isset($_GET['vreme'])){
    $vreme = $_GET['vreme'];
    $vreme_pomocni = explode('_', $vreme);
    $vreme = $vreme_pomocni[0];
    $profa = $vreme_pomocni[1];
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
$profesor = new user($db);
$djak = new djak($db);
$grupa = new grupa($db);
$jezik = new jezik($db);
$nivo_znanja = new nivo_znanja($db);
$ucionica = new ucionica($db);
$termin = new kalendar($db);
$lokacija = new lokacija($db);


if(isset($_GET['vreme_resurs'])){
    $vreme = $_GET['vreme_resurs'];
    $vreme_pomocni = explode('_', $vreme);
    $vreme = $vreme_pomocni[0];
    $profa = $vreme_pomocni[1] ?? 0;
    $datum = substr($vreme,0,10);
    $p77 = explode ("-", $datum);
    $datum = $p77[2] . "." . $p77[1] . "." . $p77[0];
	$sati_sat=substr($vreme,11,2);
    $sati_min=substr($vreme,14,2);
    $stjmmj = $profesor->read_all_prof();
    // var_dump($stjmmj);
    $zz=0;
    //$slovo = 'a';
    $profesor_ime = array();
    while($row = $stjmmj->fetch(PDO::FETCH_ASSOC)){
       $profesor_ime[]=array('id' => $zz, 'ime' => $row['firstname'], 'profa_id'=> $row['id']);
        //  $slovo++;
        $zz++;
         // echo  $row['firstname'];
        //var_dump($row);
    }
    $profa = $profesor_ime[$profa]['profa_id'];
}

if(isset($_GET['vreme_lokacija_resurs'])){
    $vreme = $_GET['vreme_lokacija_resurs'];
    $vreme_pomocni = explode('/', $vreme);
    $vreme = $vreme_pomocni[0];
    $loc = $vreme_pomocni[1]; 
    $lokac = $loc;
    $resurs = $vreme_pomocni[2];
    $datum = substr($vreme,0,10);
    $p77 = explode ("-", $datum);
    $datum = $p77[2] . "." . $p77[1] . "." . $p77[0];
	$sati_sat=substr($vreme,11,2);
    $sati_min=substr($vreme,14,2);
    $stmt_lokacija = $lokacija->read_one($loc);
    $row_category_lokacija = $stmt_lokacija->fetch(PDO::FETCH_ASSOC);
    // echo "Lokacija: ", $row_category_lokacija['ime'];
    
    $stjmmj = $ucionica->read_all_lokacija($lokac);
        $zz=0;
        // $slovo = 'a';
        $radnici_ime = array();
        while($row = $stjmmj->fetch(PDO::FETCH_ASSOC)){
                $radnici_ime[]=array('id' => $zz, 'ime' => $row['ime'], 'radnik_id'=> $row['id']);
            // $slovo++;
            $zz++;
        }
     //   echo "soba je: ",$radnici_ime[$resurs]['ime'];
     $ta_soba = $radnici_ime[$resurs]['radnik_id'] ?? 0;
}


if(isset($_GET['vreme_online'])){
    $vreme = $_GET['vreme_online'];
    $datum = substr($vreme,0,10);
    $p77 = explode ("-", $datum);
    $datum = $p77[2] . "." . $p77[1] . "." . $p77[0];
	$sati_sat=substr($vreme,11,2);
    $sati_min=substr($vreme,14,2);
    $loc = 1;
    $lokac = $loc;
	/*
	echo"<br/>Vreme je:",$vreme,"<br/>";
    echo"<br/>DAtum:",$datum,"<br/>";
    echo"<br/>Sati:",$sati_sat,"<br/>";
    echo"<br/>Sati:",$sati_min,"<br/>";
    */

}

if(isset($_GET['vreme_ucionica'])){
    $vreme = $_GET['vreme_ucionica'];
    $pomocna = explode("/",$vreme);
    $vreme = $pomocna[0];
    $ta_soba = $pomocna[1];
    $stmt_lokacija_soba = $ucionica->read_one($ta_soba);
    $row_ta_ucionica = $stmt_lokacija_soba->fetch(PDO::FETCH_ASSOC);
    $loc = $row_ta_ucionica['fk_lokacija'];
    $lokac = $loc;


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




// if form was posted
if(isset($_POST['skola'])){
    $zauzeta_ucionica = 0;
    $zauzet_profesor = 0;
    $krk = 2;
    $termin->ucionica = $_POST['ucionica'] ?? 2000;
  
    $periodicni = $_POST['periodicni'];
    //  echo "periodicni:",$periodicni;
    if($periodicni == 2 || $periodicni == 3 ){  // Odavde su periodicni
        $datum_od = $_POST['datum_od'];
        $datum_do = $_POST['datum_do'];
       
        $datum_od = date("Y-m-d", strtotime($datum_od));
        $datum_do = date("Y-m-d", strtotime($datum_do));
        // var_dump($datum_od);
        // $datum_od = $_POST['datum_od'];
        // $datum_do = $_POST['datum_do'];
        $termin->grupa = $_POST['grupa'];
        $stmt_grupa = $grupa->read_one($termin->grupa);
        $row_grupa = $stmt_grupa->fetch(PDO::FETCH_ASSOC);
        $nacin_slusanja_nastave_grupa = $row_grupa['nacin'];
        // echo "<br>NAcin slusanja nastave pre",$nacin_slusanja_nastave_grupa,"<br>";
        $grupni_profesor = $row_grupa['fk_profesor'];
        $termin->fk_profesor_grupa = $grupni_profesor;
        $stmt_profesor = $profesor->read_one($row_grupa['fk_profesor']);
        $row_profesor = $stmt_profesor->fetch(PDO::FETCH_ASSOC);

        $termin->zoom_skype = $_POST['zoom_skype'] ?? 1;

        $datum_termina = $_POST['datum_termina'];

        
        
        $stmt_ucionica = $ucionica->read_one_ucionica($_POST['ucionica'],'ucionice');
        $row_ucionica = $stmt_ucionica->fetch(PDO::FETCH_ASSOC);
        $termin->fk_lokacija = $row_ucionica['fk_lokacija'] ?? 1;
        
        $boja = $row_profesor['color_prof'] ?? 0;
        $termin->color = $boja;
        $termin->komentar=$_POST['komentar'];
        $termin->status = 1;

       // echo "<br/>datum od: ", $datum_od, "  Datum do: ",$datum_do,"<br/>";
       
        $begin = new DateTime( $datum_od );
        $end   = new DateTime( $datum_do );
      
        $myDate =  $datum_od;
        $next_monday = date('Y-m-d', strtotime("next monday", strtotime($myDate)));
     //  echo "prvi ponedeljak: ", $next_monday;

        $next_monday_start_end = new DateTime(  $next_monday );
        $radni_dani = array('Mon','Tue','Wed','Thu','Fri','Sat');

                $isto_naizmenicno = $_POST['pre_posle_1'] ?? 1;
                // echo "Isto = 1, naizmenicno =2 : ",$isto_naizmenicno;
                $dan_za_cas = $_POST['dan_1'];
            //    echo "<br/> Dan kada ce biti cas: ", $dan_za_cas,"<br/>";
                $postojanje_rasporeda = $_POST['postojanje_rasporeda'];
            //      echo "Poztojanje rasporeda : ",$postojanje_rasporeda;

                if($postojanje_rasporeda != 4){ // Ako nije brisanje termina za period
                    if($isto_naizmenicno == 1){ // uvek isto
                        $sati_od = $_POST['sati_od_1'];
                        $minuti_od = $_POST['minuti_od_1'];
                        $sati_do = $_POST['sati_do_1'];
                        $minuti_do = $_POST['minuti_do_1'];
                            for($i = $begin; $i < $end; $i->modify('+1 day')){
                                // echo "<br/>",$i->format("Y-m-d")," i to je dan: ";
                                // echo "Sad samo provervamo <br>";
                                $timestamp = strtotime($i->format("Y-m-d"));
                                $day = date('D', $timestamp);
                                //  echo $day,"  /  ";
                                if($day == $dan_za_cas){
                                include 'zakazivanje_periodicno_prvi_test.php';
                                if($periodicni == 3){
                                    $i->modify('+8 day');
                                  //  var_dump($i->format("Y-m-d"));
                                   }
                                }
                                if($krk == 1){
                                    break;
                                    // echo "I ovde ga izbacujemo";
                                }
                                // echo "<br> ovo je krk: ",$krk,"<br>";
                            }
                         
                       // echo "<br> ovo je krk: ",$krk,"<br>";
                        if($krk == 2 ){
                             $begin = new DateTime( $datum_od );
                             $end   = new DateTime( $datum_do );
                            // $end   = new DateTime( $_POST['datum_do'] );
                            // echo "Begin: <br> ";
                            // var_dump($begin);
                            // "end: ", $end;
                            for($i = $begin; $i < $end; $i->modify('+1 day')){
                              //  echo "<br/>",$i->format("Y-m-d")," i to je dan: ";
                              // echo "Sad cemo da pisemo <br>";
                                $timestamp = strtotime($i->format("Y-m-d"));
                                $day = date('D', $timestamp);
                               // echo $day,"  /  ";
                                if($day == $dan_za_cas){
                                   include 'zakazivanje_periodicno_prvi.php';
                                   if($periodicni == 3){
                                    $i->modify('+8 day');
                                   // var_dump($i->format("Y-m-d"));
                                   }
                                }
                            }

                        }
                    }else{ // naizmenicni casovi
                       
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
                            // echo "<br/>",$i->format("Y-m-d")," i to je dan: ";
                            $timestamp = strtotime($i->format("Y-m-d"));
                            $day = date('D', $timestamp);
                            // echo $day,"  /  ";
                            if($day == $dan_za_cas  ){
                                if($prvo == 1){
                                    $termin->ucionica = $_POST['ucionica'] ?? 2000;
                                    // echo " Pisemo Od: ",$sati_od,":",$minuti_od," /DO: ",$sati_do,":",$minuti_do,"<br/>";
                                    include 'zakazivanje_periodicno_prvi_test.php';
                                    $prvo = 2;
                                    // }else{
                                    //     echo " Pisemo Od: ",$sati_od12,":",$minuti_od12," /DO: ",$sati_do12,":",$minuti_do12,"<br/>";
                                    $termin->ucionica = $_POST['ucionica3'] ?? 2000;
                                    include 'zakazivanje_periodicno_drugi_test.php';
                                    $prvo = 1;
                                }
                            }
                            if($krk == 1){
                                break;
                                // echo "I ovde ga izbacujemo";
                            }
                
                        }
                     //   echo "<br> ovo je krk: ",$krk,"<br>";
                        $prvo = 1;
                        if($krk == 2){
                            $begin = new DateTime( $datum_od );
                            $end   = new DateTime( $datum_do );
                            for($i = $begin; $i < $end; $i->modify('+1 day')){
                                // echo "<br/>",$i->format("Y-m-d")," i to je dan: ";
                                $timestamp = strtotime($i->format("Y-m-d"));
                                $day = date('D', $timestamp);
                                // echo $day,"  /  ";
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
                    }

                }else{
                  //  echo "Ke vidime sta ce radime";
                   
                    $stmt_brisi_termine_grupa_interval = $termin->brisi_termine_grupa_interval($datum_od, $datum_do , $termin->grupa); 
                  //  var_dump($stmt_brisi_termine_grupa_interval);            
                }
                          
                                
    }else{
      // jedan termin - zakazan cas  
        $tmp77 = explode (".", $_POST['datum_termina']);
        $_POST['datum_termina'] = $tmp77[2] . "-" . $tmp77[1] . "-" . $tmp77[0];
        $datum_termina = $_POST['datum_termina'];
      //  $termin->ucionica=$_POST['ucionica'] ?? null;
      //  echo "Ucionica je br: ",$termin->ucionica;
        $termin->grupa=$_POST['grupa'];
      //  $termingrua=$termin->grupa;
        $termin->zoom_skype = $_POST['zoom_skype'] ?? 1;
        $stts = $grupa->read_one_grupa($termin->grupa,"grupe");
        $row_category_analiza_grupe = $stts->fetch(PDO::FETCH_ASSOC);
        $grupni_profesor = $row_category_analiza_grupe['fk_profesor'];
        $termin->fk_profesor_grupa = $grupni_profesor;
        // echo '<script type="text/javascript">alert("'.$grupni_profesor.'");</script>';
        $smstat = $profesor->read_one_profesor($grupni_profesor,"users");
        $row_category_tarzenje_boje = $smstat->fetch(PDO::FETCH_ASSOC);
       // $boja = $row_category_tarzenje_boje['color_prof'];
         $stmt_ucionica = $ucionica->read_one_ucionica($_POST['ucionica'] ?? null,'ucionice');
         $row_ucionica = $stmt_ucionica->fetch(PDO::FETCH_ASSOC);
        $boja = $row_category_tarzenje_boje['color_prof'] ?? 0;
        $termin->color = $boja;
       // echo $boja;
        $termin->komentar=$_POST['komentar'];
        $termin->pocetak=$_POST['datum_termina'].' '.$_POST['sati_od'].":".$_POST['minuti_od'].":"."00";
        $termin->kraj=$_POST['datum_termina'].' '.$_POST['sati_do'].":".$_POST['minuti_do'].":"."00";
        $termin->fk_lokacija = $row_ucionica['fk_lokacija'] ?? 1;
      //  echo "Lokacija",  $termin->fk_lokacija ;
        $termin->status = 1;
        // echo "Ucionica je : ", $termin->ucionica;
        // var_dump($termin->ucionica);
        $termin->color =  $row_category_tarzenje_boje['color_prof'] ?? null;
          

            $begin_1 = new DateTime( $termin->pocetak );
            // Set end date
            $end_1 = new DateTime( $termin->kraj );
            // Set interval
            $interval = new DateInterval('PT9M');
            // Create daterange
            $daterange = new DatePeriod($begin_1, $interval ,$end_1);
            // Loop through range
          //  $krk=2;
            
            if(isset( $termin->ucionica) &&  $termin->ucionica != 2000 ){
                foreach($daterange as $date){
                    // Output date and time
                    $broj_istovremenih = $termin->provera_zauzetosti_ucionice_termin( $date->format("Y-m-d H:i:s"), $termin->ucionica );
                 //   echo $date->format("Y-m-d H:i:s")," broj termina za aparat " ,$row_category_aparat11['ime'],"&nbsp; : ",$broj_istovremenih, "<br>";
                    if($broj_istovremenih > 0){
                        $krk = 1;
                        $zauzeta_ucionica = 1;
                        $_SESSION['poruka_zauzeta_ucionica'] = "Učionica je zauzeta u terminu: " . date("d.m.Y", strtotime($datum_termina)) . " od: " .  $_POST['sati_od'] . ":" . $_POST['minuti_od'] . " do: " . $_POST['sati_do'] . ":" . $_POST['minuti_do'];
                      //   echo "SESIJA Ucionica: ",$_SESSION['poruka_zauzeta_ucionica'],"<br>"; 
                         break;              
                    }
                }
            }

            if(isset( $_POST['ucionica3']) &&  $_POST['ucionica3'] != 2000 ){
                foreach($daterange as $date){
                    // Output date and time
                    $broj_istovremenih = $termin->provera_zauzetosti_ucionice_termin( $date->format("Y-m-d H:i:s"), $_POST['ucionica3'] );
                 //   echo $date->format("Y-m-d H:i:s")," broj termina za aparat " ,$row_category_aparat11['ime'],"&nbsp; : ",$broj_istovremenih, "<br>";
                    if($broj_istovremenih > 0){
                        $krk = 1;
                        $zauzeta_ucionica3 = 1;
                        $_SESSION['poruka_zauzeta_ucionica'] = "Učionica je zauzeta u terminu: " . date("d.m.Y", strtotime($datum_termina)) . " od: " .  $_POST['sati_od'] . ":" . $_POST['minuti_od'] . " do: " . $_POST['sati_do'] . ":" . $_POST['minuti_do'];
                      //   echo "SESIJA Ucionica: ",$_SESSION['poruka_zauzeta_ucionica'],"<br>"; 
                         break;              
                    }
                }
            }
           // echo "grupni: ",$grupni_profesor;
           // $brojka_zauzetosti_profesora = 0;
            $brojka_zauzetosti_profesora = $termin->count_zauzetosti_profesor($termin->pocetak, $termin->kraj,  $grupni_profesor);
          //  var_dump($brojka_zauzetosti_profesora);
            if( $brojka_zauzetosti_profesora > 0){
                $zauzet_profesor = 1;
                $krk=1;
                $_SESSION['poruka_zauzet_profesor'] = "Profesor je zauzet u terminu: " . date("d.m.Y", strtotime($datum_termina)) . " od: " .  $_POST['sati_od'] . ":" . $_POST['minuti_od'] . " do: " . $_POST['sati_do'] . ":" . $_POST['minuti_do'];
                                      
            }
              //  var_dump($brojka_zauzetosti_profesora);
              //  echo "Drug profesor je zauzet na nivou:", $brojka_zauzetosti_profesora,"<br>";
              // $zauzet_profesor = 1;
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
  if($krk == 2){
    echo"
    <script>
    alert('Uspešno ste zakazali časove, pa poverite u kalendaru !');
    window.close();
    </script>";
  }else{
   //  unset($_POST['ucionica']);
   if(isset($_POST['ucionica'])){
   
   }
  }  
 
}
?>
<div class="com-md-12" id='glavni'>
<form  method='post' id='skola'  >
<?php 
$stmt_lokacije = $lokacija->read_all();
$sve_lokacije = $stmt_lokacije->fetchall(PDO::FETCH_ASSOC);
  
$stmt_online_grupa = $grupa->read_all_online();
$online_grupe = array();
while ($row_category_online_grupa = $stmt_online_grupa->fetch(PDO::FETCH_ASSOC)){
   $online_grupe[] = $row_category_online_grupa['id'];
}
$stmt_skola_grupa = $grupa->read_all_skola();
$skola_grupe_sve = array();
while ($row_category_skola_grupa = $stmt_skola_grupa->fetch(PDO::FETCH_ASSOC)){
   $skola_grupe_sve[] = $row_category_skola_grupa['id'];
}

$stmt_lokacije = $lokacija->read_all();
foreach($sve_lokacije as $row_category_lokacija ){
   // var_dump($row_category_lokacija);
   $stmt_skola_grupa = $grupa->read_all_skola_lokacija($row_category_lokacija['id']);
   $skola_grupe[$row_category_lokacija['id']] = array();
   while ($row_category_skola_grupa = $stmt_skola_grupa->fetch(PDO::FETCH_ASSOC)){
       $skola_grupe[$row_category_lokacija['id']][] = $row_category_skola_grupa['id'];
   }

}

foreach($sve_lokacije as $row_category_lokacija ){
    // var_dump($row_category_lokacija);
    $stmt_skola_ucionice = $ucionica->read_all_lokacija($row_category_lokacija['id']);
    $skola_ucionice[$row_category_lokacija['id']] = array();
    while ($row_category_skola_ucionica = $stmt_skola_ucionice->fetch(PDO::FETCH_ASSOC)){
        $skola_ucionice[$row_category_lokacija['id']][] = $row_category_skola_ucionica['id'];
    }
 
 }

    $broj_lokacija = $lokacija->count_all();
    
     $vidljivost_ucionice = "none";

    // Kodse selektuje online grupa i ide bez ucionice pa posle submita 
   
  $vidljivost_ucionice = "none";

      
 

// Kad je setovana grupa u skoli bez selektovane ucionice pa se vodi kao online posle submita kad se duplira termin za profesoraa 
    if(isset($termin->grupa)){
        $stmt_ta_grupa = $grupa->read_one_grupa($termin->grupa,"grupe");
        $row_category_ta_grupa = $stmt_ta_grupa->fetch(PDO::FETCH_ASSOC);
        $bas_ta_lokacija_grupe = $row_category_ta_grupa['fk_lokacija'];
    }
    
    if(isset($termin->ucionica)  && in_array($termin->grupa, $online_grupe)  && $termin->ucionica == 2000 ){
        $vidljivost_ucionice_oo = "block";
    }else if(isset($termin->ucionica)  && !in_array($termin->grupa,  $online_grupe)  && $termin->ucionica == 2000 ){
     $vidljivost_ucionice_oo = "none";
    }else if(isset($termin->ucionica)  && in_array($termin->grupa,  $online_grupe)  && $termin->ucionica != 2000 ){
        $vidljivost_ucionice_oo = "block"; ?>
        <script>
             var bas_ta_lokacija = <?=$bas_ta_lokacija_grupe ?>;
             document.getElementById('online_grupa').style.display = 'block'; 
                    document.getElementById('ucionica_all').style.display = 'block';
                    document.getElementById("ucionica_alll").disabled = false;
                    for (let i3 = 1; i3 <= broj_lokacija; i3++) {
                    document.getElementById('ucionica'+i3).style.display = 'none';
                    document.getElementById("ucionica"+i3+i3).disabled = true;
                    // docu
                    } 
        </script>
    <?php    
       }else{
     $vidljivost_ucionice_oo = "none";
    }

    if(isset($termin->ucionica)  && isset($termin->grupa) && in_array($termin->grupa, $skola_grupe_sve)  && $termin->ucionica == 2000 ){



        $vidljivost_ucionice = "block"; ?>
         <script>
             var bas_ta_lokacija = <?=$bas_ta_lokacija_grupe ?>;
           //  alert("Bas ta lokacija" + bas_ta_lokacija);
             document.getElementById('ucionica'+bas_ta_lokacija).style.display = 'block';
             document.getElementById("ucionica_alll").disabled = true;
             for (let i2 = 1; i2 <= broj_lokacija; i2++) {
                            if(i2 != bas_ta_lokacija ){
                                document.getElementById('ucionica'+i2).style.display = 'none';
                                document.getElementById("ucionica"+i2+i2).disabled = true;
                            }else{
                                document.getElementById('ucionica'+i2).style.display = 'block';
                                document.getElementById("ucionica"+i2+i2).disabled = false; 
                            }  
                    
            }
            //  alert("ta lokacija: "+bas_ta_lokacija+"  ");
         </script>
        <?php 

    }else if(isset($termin->ucionica)  && !in_array($termin->grupa, $skola_grupe)  && $termin->ucionica == 2000 ){
     $vidljivost_ucionice = "none";
    }else{
        $vidljivost_ucionice = "none";
    }

  ?>
    <div id = "ucionica_all" style="display : block;">
    <?php 
        $stmt33_ucionica = $ucionica->read_All(); 
        // $stmt_lokacija = $lokacija->read_one($ta_lokacija);
        // $row_lokacija = $stmt_lokacija->fetch(PDO::FETCH_ASSOC);
    ?>
    <table class='table table-responsive'>
        <tr >
            <td class='width-30-percent'><b>Učionica:</b></td>
            <td>
                <select class='form-control' id = "ucionica_alll" name='ucionica'  >
                <option value="2000" >Ako želiš online grupu u učionicu, odaberi ucionicu...</option>
                    <?php 
                    while ($row_ucionica = $stmt33_ucionica->fetch(PDO::FETCH_ASSOC)){ 
                        $stmt_lokacija = $lokacija->read_one($row_ucionica['fk_lokacija']);
                        $row_ta_lokacija =  $stmt_lokacija->fetch(PDO::FETCH_ASSOC);

                        ?>
                        
                        <option value= <?=$row_ucionica['id'] ?> <?php if( isset($_POST['ucionica'])){ if($_POST['ucionica'] == $row_ucionica['id'] ){ echo "selected";}} elseif(isset($ta_soba))
                                { if($ta_soba == $row_ucionica['id'] ){echo "selected";}}  ?>  > 
                                    <?php  echo  $row_ucionica['ime']," / ",$row_ta_lokacija['ime'] ?? "";  ?>
                                </option>
                        <?php 
                    } ?>    
                </select>
                <?php if(isset($zauzeta_ucionica) && $zauzeta_ucionica == 1 ){ ?>
                            <div   style="color:red;">
                                <?php 
                                    if(isset($_SESSION["poruka_zauzeta_ucionica"])){
                                        echo $_SESSION["poruka_zauzeta_ucionica"];
                                    }elseif(isset($_SESSION["poruka_zauzeta_ucionica"])){
                                        unset( $_SESSION["poruka_zauzeta_ucionica"]); 
                                    }
                                   
                                ?> 
                            </div>
                            <?php  
                            //  unset( $_SESSION['poruka_zauzeta_ucionica']); 
                        }  
                        ?>
            </td>
        </tr>
    </table>    
</div> 

    
    <table class='table table-responsive'>  
   
       
        <tr>
            <td class='width-30-percent'><b>Grupa:</b></td>
            <td>
                <?php
                // read the scool boards from the database
                if($_SESSION['access_level'] == 'Customer'){
                    if(isset($_GET['vreme_lokacija_resurs'])){
                        // $stmt = $grupa->read_allgrupa_lokacija_profesor_onlineplus($loc,  $_SESSION['user_id']);
                        $stmt = $grupa->read_allgrupa_profesor('grupe', $_SESSION['user_id']);
                        // echo "***";
                    }elseif(isset($_GET['vreme_online'])){
                        $stmt = $grupa->read_allgrupa_online_profesor($_SESSION['user_id']);
                    }elseif(isset($_GET['vreme_ucionica'])){
                        $stmt = $grupa->read_allgrupa_lokacija_profesor($loc,  $_SESSION['user_id']);
                    }else{
                        $stmt = $grupa->read_allgrupa_profesor('grupe', $_SESSION['user_id']);
                        // echo "xxxx";
                    }

                    
                }else{
                    if(isset($_GET['vreme_lokacija_resurs'])){
                        $stmt = $grupa->read_allgrupa_lokacija($loc);
                    }elseif(isset($_GET['vreme_online'])){
                        $stmt = $grupa->read_allgrupa_online('grupe');
                    }elseif(isset($_GET['vreme_ucionica'])){
                        $stmt = $grupa->read_allgrupa_lokacija($loc);
                    }else{
                        $stmt = $grupa->read_allgrupa_profesor('grupe', $profa);
                    }

                   
                }

              //  if(isset($termin->grupa)  ){ echo "radi se o grupi: ", $termin->grupa;}
                
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

                          $stmt_bas_ta_lokacija = $lokacija->read_one($fk_lokacija);
                          $row_bas_ta_lokacija = $stmt_bas_ta_lokacija->fetch(PDO::FETCH_ASSOC);
                          $ime_bas_te_lokacije =$row_bas_ta_lokacija['ime'];


                        ?>
                        <option value='<?=$id ?>' 
                        <?php 
                        if($nacin == 2){ ?>
                              style='background-image:url(images/online1.jpg); height: 25px !important; background-repeat: no-repeat'
                            <?php 
                        }else{ ?>
                             style='background-image:url(images/room3.jpg); height: 25px !important; background-repeat: no-repeat'
                        <?php
                       }
                        if(isset($termin->grupa) && $termin->grupa == $id ){ echo "selected";} ?> >
                         &nbsp; &nbsp;<?=$fk_jezik ?>&nbsp;<?=$nivo ?>&nbsp;<?=$row_profesor['firstname'] ?> &nbsp;-&nbsp;[ <?=$alias ?>] &nbsp <?php if($nacin == 1 && $broj_lokacija >1){echo  $row_bas_ta_lokacija['ime'];} ?> </option>          // vrednost z prenos je $id a prikazuje se $name, to jest ime
                        <?php 
                    }
                    echo "</select>";
                    if(isset($zauzet_profesor) && $zauzet_profesor == 1 ){ ?>
                        <div id='usluga1' style="color:red;"><?php echo  $_SESSION['poruka_zauzet_profesor']; unset( $_SESSION['poruka_zauzet_profesor']); ?> </div>
                        <?php  
                    }  
                    ?>
                    
            </td>  
            <!-- <script src="search/jquery.min.js"></script> -->
            <!-- <script src="search/bootstrap.min.js"></script> -->
            <!-- <script src="search/bootstrap-select.min.js"></script> -->
            </tr>
        </table>


        <script type="text/javascript">
            var jArray = <?php echo json_encode($online_grupe); ?>;
            // var skolaArray = <?php echo json_encode($skola_grupe); ?>;
            var broj_lokacija = <?php echo $broj_lokacija; ?>;
            // var skola1Array = <?php //* echo json_encode($skola_grupe['1']); */?>;
            // var skola2Array = <?php /* echo json_encode($skola_grupe['2']); */ ?>;
            var skolaArray = <?php echo json_encode($skola_grupe); ?>;
            var skolaArray_sve = <?php echo json_encode($skola_grupe_sve); ?>;
            var skolaArray_ucionice = <?php echo json_encode($skola_ucionice); ?>;

            console.log("Online: " + jArray);     
          
            console.log("Skole: " + skolaArray_sve);       

           




        </script>



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
                    <td class='width-30-percent'><b>Periodično/jednokratno <br> zakazivanje casa</b></td>
                    <td>      
                        <select class='form-control' name='periodicni' onchange="open_periodicni(this);" >
                            <option value="1" <?php if(isset($periodicni) && $periodicni == 1 ){echo "selected";} ?> > Jednokratni </option>
                            <option value=2 <?php if(isset($periodicni) && $periodicni == 2 ){echo "selected";} ?>  > Periodičan, svake nedelje </option>
                            <option value=3 <?php if(isset($periodicni) && $periodicni == 3 ){echo "selected";} ?>  > Periodičan, svake druge nedelje </option>
                      
                        </select>
                    </td>
            </tr>
    </table>
    
    <div id="vreme_jednokratno">
        <table>
            <tr>
                <td style="width: 25%;">Dana:</td>
                <td>
                    <input type="text"  name="datum_termina" <?php if(isset($datum)){echo"value='$datum'";} ?>  STYLE="background-color:white; padding: 6px; border-radius: 6px;" size="10" class="tcal" />
                </td>
                <?php
                $stmt_sati_od = $termin->read_all_sati();
                $sati_sat_jedan = $sati_sat;
                if(isset($_POST['sati_od']) && $_POST['sati_od'] < $sati_sat_jedan  ){ 
                   // echo $_POST['sati_od'];
                    $sati_sat_jedan = $_POST['sati_od'];
                }
                ?>
                <td align="right" style="width: 10%">OD:</td>
                <td style="width: 10%">
                    <select   class='form-control' name="sati_od">
                        <?php while($row_category_sati_od = $stmt_sati_od->fetch(PDO::FETCH_ASSOC)){ ?>
                                <option value = "<?=$row_category_sati_od['sat'] ?>" <?php if( isset($_POST['sati_od']) &&  $row_category_sati_od['sat'] == $_POST['sati_od']  ){echo "selected";}elseif( $row_category_sati_od['sat'] == $sati_sat_jedan ){echo "selected";} ?> >
                                <?=$row_category_sati_od['sat'] ?>
                            </option>
                            <?php 
                        } ?>
                    </select>
                </td>
                <td style="width: 10%">
                <?php 
                    $stmt_minuti = $termin->read_all_minuti();
                    $sati_min_jedan = $sati_min;
                    if(isset($_POST['minuti_od']) && $_POST['minuti_od'] < $sati_min_jedan ){
                        $sati_min_jedan = $_POST['minuti_od'];

                    }

                ?>    
                    <select  class='form-control' name="minuti_od">
                    <?php while($row_category_minuti = $stmt_minuti->fetch(PDO::FETCH_ASSOC)){ ?>
                        <option value = "<?=$row_category_minuti['minut'] ?>" <?php if(isset($_POST['minuti_od']) && $_POST['minuti_od'] == $row_category_minuti['minut']  ){echo "selected";}elseif( $sati_min_jedan == $row_category_minuti['minut']){echo "selected='selected'";} ?> >
                            <?=$row_category_minuti['minut'] ?>
                        </option>
                        <?php 
                    } ?>
                    </select>
                </td>
                <td align="right" style="width: 10%">DO:</td>
                <?php
                    $stmt_sati_do = $termin->read_all_sati();
                    $sati_sat_do_jedan = ($sati_sat ?? 0) + 1 ;
                    if(isset($_POST['sati_do']) && $_POST['sati_do'] < $sati_sat_do_jedan  ){ 
                        // echo $_POST['sati_od'];
                         $sati_sat_do_jedan = $_POST['sati_od'];
                     }
                     
                ?>
                <td align="center" style="width: 10%">
                    <select  class='form-control' name="sati_do">
                    <?php while($row_category_sati_do = $stmt_sati_do->fetch(PDO::FETCH_ASSOC)){ ?>
                        <option value = "<?=$row_category_sati_do['sat'] ?>" <?php if(isset($_POST['sati_do']) && $_POST['sati_do'] == $row_category_sati_do['sat']  ){echo "selected";}elseif( $row_category_sati_do['sat'] == $sati_sat_do_jedan){echo 'selected';} ?> >
                            <?=$row_category_sati_do['sat'] ?>
                        </option>
                        <?php 
                    } ?>
                    </select>
                </td>
                <?php 
                    $stmt_minuti_do = $termin->read_all_minuti();
                    $sati_min_do_jedan = $sati_min;
                    if(isset($_POST['minuti_od']) && $_POST['minuti_od'] < $sati_min_do_jedan ){
                        $sati_min_do_jedan = $_POST['minuti_od'];

                    }
                ?>    
                <td>
                    <select  class='form-control' name="minuti_do">
                    <?php while($row_category_minuti_do = $stmt_minuti_do->fetch(PDO::FETCH_ASSOC)){ ?>
                        <option value = "<?=$row_category_minuti_do['minut'] ?>" <?php if(isset($_POST['minuti_do']) && $_POST['minuti_do'] == $row_category_minuti_do['minut']  ){echo "selected";}elseif( $sati_min_do_jedan == $row_category_minuti_do['minut']){echo "selected";} ?> >
                            <?=$row_category_minuti_do['minut'] ?></option>
                        <?php 
                    } ?>    
                    </select>
                </td>
            </tr>
            <tr class="blank_row">
                <td  colspan="4">&nbsp;</td>
            </tr>
        </table>
    </div>
    <?php 
        if(isset($periodicni) && $periodicni == 1 ){
            $vidljiv_periodicni = "none";
            // echo "1",$vidljiv_periodicni;
        }elseif(isset($periodicni) && ($periodicni == 2 || $periodicni == 3 )){
            $vidljiv_periodicni = "block";
            // echo "2",$vidljiv_periodicni;
        }else{
            $vidljiv_periodicni = "none"; 
        }
        if(isset($datum_od) && isset($datum_do) ){
            $datum_od = date("d.m.Y", strtotime($datum_od));
            $datum_do = date("d.m.Y", strtotime($datum_do));
        }
       
    
    
    ?>
    <div id="vreme_periodicno" style="display : <?=$vidljiv_periodicni ?>; background-color: #F2F2F2;">
        <table class='table table-responsive'>
            <tr style="background-color : #E6E6E6;">
                <td class='width-30-percent'>Interval zakazaivanja</td>
                
                <td style="background-color : #E6E6E6;" >
                Od:   <input type="text"  name="datum_od" <?php if(isset($datum_od)){echo"value='$datum_od'";}elseif(isset($datum)){echo"value='$datum'";} ?>  STYLE="background-color:white; padding: 6px; margin-right: 20px; border: 1px solic gray; border-radius : 3px; " size="10" class="tcal" />
                Do:  <input type="text"  name="datum_do" <?php if(isset($datum_do)){echo"value='$datum_do'";}elseif(isset($datum)){echo"value='$datum'";}  ?>  STYLE="background-color:white;  padding: 6px; border: 1px solic gray; border-radius : 3px; " size="10" class="tcal" />
                </td>
            </tr>
            <tr>
                <td class='width-30-percent' style="background-color: #CECEF6;"><b>Ukoliko postoji raspored <br> za odredjeni dan</b</td>
                <td style="background-color: #CECEF6;">
                    <select class='form-control' name='postojanje_rasporeda'  >
                        <option value="1"> Ignorisati (zakazati novi i obrisati sve stare na taj dan) </option>
                        <option value="2"> Preskočiti taj dani </option>
                        <option value="3"> Dodati još jedan čas za taj dan </option>
                        <option value="4"> Obrisati sve zakazane termine u navedenom intervalu za grupu </option>
                    </select>
                </td>
            </tr>
        </table>
        <div id="jednom_nedeljno"  >
        <?php 
            
            $dan_za_biranje = date('D', strtotime($datum) );
            
            ?>
            <table class='table table-responsive'>
            <tr>
                <td class='width-30-percent'>Prvi dan kojim bi se cas odrzavao:</td>
                <td>
                <select class='form-control' name='dan_1'  >
                        <option value="Mon" <?php if(isset($_POST['dan_1']) && $_POST['dan_1'] === "Mon" ){ echo "selected";}elseif(isset($dan_za_biranje) && $dan_za_biranje === "Mon"){ echo "selected";} ?>> Ponedeljak </option>
                        <option value="Tue" <?php if(isset($_POST['dan_1']) && $_POST['dan_1'] === "Tue" ){ echo "selected";}elseif(isset($dan_za_biranje) && $dan_za_biranje === "Tue"){ echo "selected";} ?> > Utorak </option>
                        <option value="Wed" <?php if(isset($_POST['dan_1']) && $_POST['dan_1'] === "Wed" ){ echo "selected";}elseif(isset($dan_za_biranje) && $dan_za_biranje === "Wed"){ echo "selected";} ?> > Sreda </option>
                        <option value="Thu" <?php if(isset($_POST['dan_1']) && $_POST['dan_1'] === "Thu" ){ echo "selected";}elseif(isset($dan_za_biranje) && $dan_za_biranje === "Thu"){ echo "selected";} ?> > Cetvrtak </option>
                        <option value="Fri" <?php if(isset($_POST['dan_1']) && $_POST['dan_1'] === "Fri" ){ echo "selected";}elseif(isset($dan_za_biranje) && $dan_za_biranje === "Fri"){ echo "selected";} ?> > Petak </option>
                        <option value="Sat" <?php if(isset($_POST['dan_1']) && $_POST['dan_1'] === "Sat" ){ echo "selected";}elseif(isset($dan_za_biranje) && $dan_za_biranje === "Sat"){ echo "selected";} ?> > Subota </option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class='width-30-percent'>Način periodičnog zakazivanja</td>
                <td>
                    <select class='form-control' name='pre_posle_1' id='pre_posle_1' onchange="open_naizmenicno1(this);" >
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
                <button type="submit" name="skola" value="skola" class="btn btn-danger" onclick="validation()">
                    <span class="glyphicon glyphicon-plus"></span> snimi i izadji
                </button>
            </td>
        </tr>
    </table>
</form>
</div>

<?php 
 // Kod ucitavanja kad je zauzeta ucionica prikazuje nazmenicno
 if(isset($periodicni) &&  ($periodicni == 2 || $periodicni == 3)){
   
    ?>
    <script>
      //   document.getElementById('isto_1').style.display = 'none'; 
         document.getElementById('naizmenicno_1').style.display = 'block';
        //  document.getElementById('pre_posle_1').value = "2";

    </script>
     <?php 
     if($periodicni == 3){ ?>
        <script>
              document.getElementById("pre_posle_1").disabled = true;
              document.getElementById("pre_posle_1").value = 1;
        </script>
        <?php    
     }
    
     if(isset($isto_naizmenicno) && $isto_naizmenicno == 1){ ?>
        <script>
             document.getElementById('isto_1').style.display = 'block'; 
             document.getElementById('naizmenicno_1').style.display = 'none';

        </script>
       

      <?php   
    }elseif(isset($isto_naizmenicno) && $isto_naizmenicno == 2){ ?>
        <script>
            document.getElementById('pre_posle_1').value = "2";
             document.getElementById('isto_1').style.display = 'none'; 
             document.getElementById('naizmenicno_1').style.display = 'block';
            
        </script>
     
    <?php 
    }
    ?>
    <script>
          document.getElementById('vreme_jednokratno').style.display = 'none'; 
       
    </script>
    <?php  
}

?>




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
            // $utils = new Utils();

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
            $termin->fk_lokacija = 1;
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
       
            <form action='termin.php' method='post' id='slobodan1'>
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
                            <button type="submit" name="slobodan1"  value="slobodan1" class="btn btn-danger"  >
                                <span class="glyphicon glyphicon-record"></span> snimi i izadji
                            </button>
                        </td>
                    </tr>

                </table>
            </form>
</div>
<script>
    // var ovv = odabrana_vrednost;
     function validation(){
        //alert("bbb");
        if (!$('#grupa_ucenika').val()) {
                      //  $('#grupa_ucenika').prev().prev().css('border-bottom-color', 'rgb(220, 53, 69)');
                        alert('Morate odabrati grupu da bi ste zakazali čas !!!');
        }
       
            


    }

</script>



<?php



//echo "";



// include page footer HTML
//include_once "layout_foot.php";
?>
<script>

function ponistavanje() {
    console.log("11");
   
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
        document.getElementById("pre_posle_1").disabled = false;
     //   $("#vreme_periodicno").find("select, input, textarea, button, select").removeAttr("disabled");
        
    }
    if(sel.value == 3){
        document.getElementById('vreme_jednokratno').style.display = 'none';
        //   $('#vreme_jednokratno').find('input, textarea, button, select').attr('disabled','disabled');
        document.getElementById('vreme_periodicno').style.display = 'block';
        document.getElementById('pre_posle_1').value = 1;
        document.getElementById('pre_posle_1').disabled = true;
        document.getElementById('isto_1').style.display = 'block'; 
        document.getElementById('naizmenicno_1').style.display = 'none';
        
        
        alert (document.getElementById(pre_posle_1).value);
   
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