<html>
<head>
    <title>Zadženje djak</title>
    <link rel="shortcut icon" href="images/kalen.png">
    <link rel="stylesheet" type="text/css" href="tigrakal/tcal.css" />
    <script type="text/javascript" src="tigrakal/tcal.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
    <!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script> -->
    <!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
  <!--  <script src="ajax/ajax321jquery.min.js"></script> -->

</head>
<body>
<?php
/**
 * Created by PhpStorm.
 * User: goran
 * Date: 6.11.2019
 * Time: 13:13
 */
if(isset($_GET['id'])){$idd = $_GET['id'];// echo "Radi se o djaku  br: ", $idd;
}
//echo 'Current PHP version: ' . phpversion();
// core configuration
include_once "config/core.php";

// set page title
$page_title = "<h3 style='color:red' >Zaduži djaka:</h3>";

// include login checker
include_once "login_checker.php";

// include classes
include_once 'config/database.php';
include_once 'config/autoload.php';
/*
include_once 'objects/user.php';
include_once "libs/php/utils.php";
include_once 'objects/grupa.php';
include_once 'objects/djak.php';
include_once 'objects/zaduzenje.php';
include_once 'objects/povezivanje.php';
include_once 'objects/nivo_znanja.php';
include_once 'objects/jezik.php';
*/
include_once "layout_head2.php";
$database = new Database();
$db = $database->getConnection();
$grupa = new grupa($db);
$djak = new djak($db);
$profesor = new user($db);
$zaduzenje = new zaduzenje($db);
$povezivanje = new povezivanje($db);
$nivo_znanja = new nivo_znanja($db);
$jezik = new jezik($db);

$stmt = $djak->read_one_djak("djaci", $idd);
$row_category_djak = $stmt->fetch(PDO::FETCH_ASSOC);
extract($row_category_djak);



// registration form HTML
// code when form was submitted
// if form was posted
if($_POST){

    // set values to object properties
    $zaduzenje->fk_grupa=$_POST['grupa'];

    $tmp77 = explode (".", $_POST['od_datuma']);
    $_POST['od_datuma'] = $tmp77[2] . "-" . $tmp77[1] . "-" . $tmp77[0];
    $tmp777 = explode (".", $_POST['do_datuma']);
    $_POST['do_datuma'] = $tmp777[2] . "-" . $tmp777[1] . "-" . $tmp777[0];

    $zaduzenje->zaduzio=$_SESSION['user_id'];
    $zaduzenje->komentar=$_POST["komentar"];
    $zaduzenje->fk_djak=$idd;
    $zaduzenje->aktivan = 1;


    $zaduzenje->od_datuma=$_POST["od_datuma"];
    $zaduzenje->do_datuma=$_POST["do_datuma"];
    // Ako se pogresi datum
    if($_POST["od_datuma"] > $_POST["do_datuma"]){
        $zaduzenje->od_datuma=$_POST["do_datuma"];
        $zaduzenje->do_datuma=$_POST["od_datuma"];
    }

    $zaduzenje->iznos=$_POST['iznos'];
    $ukupan_iznos = $zaduzenje->iznos; 
    $zaduzenje->valuta= $zaduzenje->od_datuma;
    $zaduzenje_ukupno=$_POST['iznos'];
    $komentar = $_POST['komentar'] ?? "";

    
    $date1 = $zaduzenje->od_datuma;
    $date2 = $zaduzenje->do_datuma;

    $rate_ili_odjedared = $_POST['rate_ili_odjedared'];

    if($rate_ili_odjedared == 1){
        if($zaduzenje->create()){

            echo "<div class='alert alert-info'>";
            echo "Uspesno ste generisali zaduzenje za djaka.";
            echo "</div>";
            // empty posted values
            $_POST=array();
            //  echo'<script>window.parent.opener.location.reload();</script>';
            //echo"<script>window.location=window.location;</script>";
            header("Location: kartica_djak.php?id=$idd", true, 303);
            //echo"<script>window.close();</script>";

        }else{
            echo "<div class='alert alert-danger' role='alert'>";


            echo "Nastala je greska prilikom upisa zaduzenja";

            echo"</div>";
        }

    }else{
        $broj_rata = $_POST['broj_rata1'];
        echo "Broj rata: ",$broj_rata,"<br/>";
        $prva_rata = $_POST['prva_rata'];
        echo "prva rata: ",$prva_rata,"<br/>";

        $zaduzenje->iznos = $zaduzenje->iznos/$broj_rata;


        $godina_od = $tmp77[2];
        $mesec_od = $tmp77[1];
        $godina_do = $tmp777[2];
        $mesec_do = $tmp777[1];
        $brojac_iteracija = 0;    
        for ( $x = 1; $x <= $broj_rata; $x++ ) {
            $brojac_iteracija++;
            $zaduzenje->komentar = "rata br: ".$brojac_iteracija." / ".$ukupan_iznos ;
            if($komentar !== ""){
                $zaduzenje->komentar = $zaduzenje->komentar."5555".$komentar;
            }
            $dateString = strval($godina_od)."-".strval($mesec_od)."-15";
             $zadnji_dan_u_mesecu = date("Y-m-t", strtotime($dateString));
            if($x == 1){
              //  $time_prva_rata = strtotime($prva_rata);
               // $datum_valute = new DateTime($prva_rata);
                $datum_valute =  date('Y-m-d', strtotime($prva_rata ));

                
            }
            
            //    echo "prva rata je:",$prva_rata,"<br/>";
            echo  $datum_valute ;
            if($datum_valute > $zadnji_dan_u_mesecu){
                $zaduzenje->valuta = $zadnji_dan_u_mesecu;
            }else{
                $zaduzenje->valuta = $datum_valute;
            }
            
          
          $date = strtotime($datum_valute);
          $datum_valute = date("Y-m-d", strtotime("+1 month", $date));
            

            echo "Valuta: ",$x," -> ",$zaduzenje->valuta,"<br/>";
                echo "Godina: ", $godina_od," Mesec: ",$mesec_od," Iznos: ",$zaduzenje->iznos,"<br/>";
                if($zaduzenje->create()){

                    echo "<div class='alert alert-info'>";
                    echo "Uspesno ste generisali zaduzenje za djaka.";
                    echo "</div>";
                    // empty posted values
                    $_POST=array();
                    //  echo'<script>window.parent.opener.location.reload();</script>';
                    //echo"<script>window.location=window.location;</script>";



                     header("Location: kartica_djak.php?id=$idd", true, 303);
                    
                    
                    
                    //echo"<script>window.close();</script>";
        
                }else{
                    echo "<div class='alert alert-danger' role='alert'>";
        
        
                    echo "Nastala je greska prilikom upisa zaduzenja";
        
                    echo"</div>";
                }


            $mesec_od++;
            if($mesec_od > 12){
                $mesec_od = 1;
                $godina_od++;
                
            }
        } 

    }
             $pokazatelj = 1;
             if($pokazatelj == 0){
                if($zaduzenje->create()){

                    echo "<div class='alert alert-info'>";
                    echo "Uspesno ste generisali zaduzenje za djaka.";
                    echo "</div>";
                    // empty posted values
                    $_POST=array();
                    //  echo'<script>window.parent.opener.location.reload();</script>';
                    //echo"<script>window.location=window.location;</script>";
                    header("Location: kartica_djak.php?id=$idd", true, 303);
                    //echo"<script>window.close();</script>";
    
                }else{
                    echo "<div class='alert alert-danger' role='alert'>";
    
    
                    echo "Nastala je greska prilikom upisa zaduzenja";
    
                    echo"</div>";
                }

             }
          

        
        
      //  header("Location: kartica_djak.php?id=$idd", true, 303);


    





// create the user


}
?>

<div class="row" style='width:90%; margin-left:5%; border: solid 1px white;' id='glavni' >
<h3>Zaduži djaka</h3><hr>
<form action='zaduzenje_djak.php?id=<?php echo $id; ?>' method='post' >
    <div class=' col-md-4' id='glavni2' >
        <table class='table table-responsive'>
            <tr>
                <td class='width-30-percent'>Ime:</td>
                <td> <?php echo $firstname; ?></td>
            </tr>
            <tr>
                <td>Prezime:</td>
                <td><?php echo $lastname; ?></td>
            </tr>
            <tr>
                <td>Broj telefona:</td>
                <td><?php echo $contact_number;  ?></td>
            </tr>
            <tr>
                <td>Adresa:</td>
                <td><?php echo $address;  ?></td>
            </tr>
            <tr>
                <td>Email:</td>
                <td><?php echo $email;  ?></td>
            </tr>

            <tr>
                <td>Komentar</td>
                <td>
                    <textarea class='form-control' readonly ><?php echo $komentar;  ?></textarea>
                </td>
            </tr>
            <tr>
                <td>Komentar finasije</td>
                <td >
                <textarea class='form-control' readonly > <?=$komenatr_finansije ?></textarea>
                </td>
            </tr>
        </table>
    </div>
    <div class='col-md-4' style="border-style: solid; border-color: #F2F2F2; padding : 10px; " >
        <?php
        $broj_grupa_u_kojima_je = $povezivanje->count_grupa_po_kursu($idd);
        // echo "Broj grupa:", $broj_grupa_u_kojima_je;
        // echo $broj_grupa_u_kojima_je,"<br/>";
        $osim = array();
        if($broj_grupa_u_kojima_je > 0)
        {
            echo "Djak je u grupi:<br/><hr>";

            $ttss = $povezivanje->read_all_group_students_po_kursu($idd);
            while ($row_category_grupe = $ttss->fetch(PDO::FETCH_ASSOC)) {

                array_push($osim, $row_category_grupe['fk_grupa']);
                $sst = $grupa->read_one_grupa1($row_category_grupe['fk_grupa']);
                $row_citanje_grupe = $sst->fetch(PDO::FETCH_ASSOC);
                extract($row_citanje_grupe);
                // echo "nivo=", $nivo;
                $stm_jezik = $jezik->read_one_jezik($fk_jezik,'jezik');
                $row_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC);
                $fk_jezik = $row_jezik['ime'];


                   
                $stm_nivo = $nivo_znanja->read_one_nivo($nivo,'nivo_znanja');
                $row_nivo_znanja = $stm_nivo->fetch(PDO::FETCH_ASSOC);
                $nivo = $row_nivo_znanja['ime'];

                echo $fk_jezik, "&nbsp;", $nivo, "&nbsp;<b> [ ", $alias," ]</b><br/>";

                echo "U grupi je od: ",$row_category_grupe['created'],"<br/>";

                $broj_zaduzenja_dosad = $zaduzenje->count_zaduzenja($idd,$row_category_grupe['fk_grupa']);
                if($broj_zaduzenja_dosad > 0)
                {
                    echo "<b> Dosadasnja zauzenja: </b><br/>";
                    echo"<table class='table table-bordered p-5' >"; ?>
                    <tr ><th>Od:</th><th>Do:</th><th>Iznos</th></tr>
                    <?php  $nsbp = $zaduzenje->read_all_group_students($idd,$row_category_grupe['fk_grupa']);
                    while ($row_category_zaduzenje = $nsbp->fetch(PDO::FETCH_ASSOC))
                    {
                        $tmp77 = explode ("-", $row_category_zaduzenje['od_datuma']);
                        $row_category_zaduzenje['od_datuma'] = $tmp77[2] . "." . $tmp77[1] . "." . $tmp77[0];
                        $id=$row_category_zaduzenje['id'];
                        $broj_za_prikaz = number_format((float)$row_category_zaduzenje['iznos'], 2, '.', ' ');
                        $tmp177 = explode ("-", $row_category_zaduzenje['do_datuma']);
                        $row_category_zaduzenje['do_datuma'] = $tmp177[2] . "." . $tmp177[1] . "." . $tmp177[0]; ?>
                        <tr id="<?php echo $id; ?>"><?php echo" <td>{$row_category_zaduzenje['od_datuma']}</td>
                        <td>{$row_category_zaduzenje['do_datuma']}</td><td>{$broj_za_prikaz}</td>"; ?>
                            <!-- <td style="border:none;" ><button class="btn btn-danger btn-sm remove" ><img style="border:0; outline: none;" src="kanta32.png" width=16 height=16>  </button></td> -->
                        </tr>
                        <?php
                        //  echo "<b>Od:</b> ",$row_category_zaduzenje['od_datuma'],"&nbsp;"," <b> Do:</b> ", $row_category_zaduzenje['do_datuma'],"&nbsp;[ ",$row_category_zaduzenje['iznos']," din. ]<br/>";
                    }
                    echo"</table>";



                }else{echo"Jos nema zaduzenja";}
                echo"<hr>";

            }
        } else {echo "Nema grupu koja se zaduzuje po kursu";  };

        ?>
    </div>

   


   


    <div class='col-md-4' id='glavni3' >
        <table class='table table-responsive'>
            <tr>
                <td colspan="2">
                    <b>  Zaduženje vezano za grupu: </b>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <?php
                    if($broj_grupa_u_kojima_je > 1){

                        // read the scool boards from the database
                        $stmt = $grupa->read_allgrupa_samo("grupe", $osim);

                        // put them in a select drop-down
                        echo "<select class='form-control' name='grupa' required> ";
                        echo "<option value=''>Odaberi grupu...</option>";

                        while ($row_category = $stmt->fetch(PDO::FETCH_ASSOC)) {     // Citanje jedne po jedne vrste  iz $smtp i mecanje u $rowcategory

                            extract($row_category);                       //ekstrakovanje na pojedniacne zapise unutar vrste
                            $stmt1 = $profesor->read_one_profesor($fk_profesor, "users");
                            $row_profesor = $stmt1->fetch(PDO::FETCH_ASSOC);
                            $stm_jezik = $jezik->read_one_jezik($fk_jezik,'jezik');
                            $row_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC);
                            $fk_jezik = $row_jezik['ime'];


                   
                            $stm_nivo = $nivo_znanja->read_one_nivo($nivo,'nivo_znanja');
                            $row_nivo_znanja = $stm_nivo->fetch(PDO::FETCH_ASSOC);
                            $nivo = $row_nivo_znanja['ime'];

                            echo "<option value='{$id}'>{$fk_jezik}&nbsp;{$nivo}&nbsp;{$row_profesor['firstname']}&nbsp;-[{$alias}]</option>";           // vrednost z prenos je $id a prikazuje se $name, to jest ime
                        }

                        echo "</select>";
                    } elseif($broj_grupa_u_kojima_je == 1) {
                        $stmt = $grupa->read_allgrupa_samo("grupe", $osim);
                        $row_category = $stmt->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <input type="hidden" name="grupa" value = " <?php echo $row_category['id']; ?> " />
                        <?php   echo "{$fk_jezik}&nbsp;{$nivo}&nbsp;&nbsp;-[{$alias}]";
                    }else{
                        echo "<div class='alert alert-info'>";
                        echo "Djak nema grupa, koje se zaduzuju po kursu";
                        echo "</div>"; ?>
                        <script>
                            $(document).ready(function () {
                               
                                    $("#register2").attr("disabled", true);
                                    return true;
                                
                            });
                            </script>
                    <?php        
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td style="width:45%" >
                    <b>  Iznos zaduzenja: </b>
                </td>
                <td>
                    <input type="number" class='form-control' placeholder="2700.00" style="width: 180px;" required name="iznos" min="1" value="3000" step="0.01" title="Currency" required >
                </td>
            </tr>
            <tr>
                <td>
                    <b>  Nacin placanja: </b>
                </td>
                <td>
                <select class='form-control' name='rate_ili_odjedared' onchange="getval(this);" required>
                            <option value= 1 > Sve od jednom</option>
                            <option value= 2 > Na rate</option>
                </td>
            </tr>
        </table>
        <table  class='table table-responsive'  id='broj_rata' style="display:none; width:100%; border: solid 1px #E6E6E6; background-color: #E6E6E6; border-radius : 5px; " >
            <tr>
                    <td style="width:50%"> <b> Broj rata: </b> </td>
                    <td >
                        <select class='form-control' id='broj_rata1' name='broj_rata1' disabled required>
                            <option value= "" >Odaberi broj rata...</option>
                            <?php
                            for ($br = 2; $br <= 12; $br++) {?>
                                <option value=<?=$br ?> ><?=$br ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </td>
            </tr>
            <tr>
                    <td style="width:40%"> <b> Prva rata ide:</b> </td>
                    <td  style="width:60%" >
                      <input type="text" id='prva_rata'  name="prva_rata" disabled  required STYLE="background-color:white; border-color: #D8D8D8 ;  border-radius: 5px; padding: 4px; " size="10" class="tcal" />
                    </td>
            </tr>
         </table>   
            <script>
                    function getval(sel)
                    {
                        console.log(sel.value);
                        if(sel.value == '1'){document.getElementById('broj_rata').style.display = 'none'; 
                          //  $('#broj_rata1').find("select, input, textarea, button, select").attr('disabled','disabled');
                          //  $('#prva_rata').find("select, input, textarea, button, select").attr('disabled','disabled');
                            document.getElementById("broj_rata1").disabled = true;
                            document.getElementById("prva_rata").disabled = true;
                           
                        }
                        if(sel.value == '2'){
                            document.getElementById('broj_rata').style.display = 'block'; 
                            $("[name='broj_rata1']").prop("disabled", false);
                            $("[name='prva_rata']").prop("disabled", false);
                         //   $("#broj_rata1").find("select, input, textarea, button, select").removeAttr("disabled");
                          //  $("#prva_rata").find("select, input, textarea, button, select").removeAttr("disabled");
                           
                        }

                       
                    }
            </script>
            <table  class='table table-responsive' >
            <tr>
                <td colspan="2" style='border-bottom : none;' >
                    <b>  Interval zaduzenja: </b>
                </td>
            </tr>
            <tr>
                <td style='border-top : none;' >
                    od:  <input type="text"  name="od_datuma" <?php if(isset($datum_od)){echo"value='$datum_od'";} ?> required STYLE="background-color:white; border-color: #D8D8D8 ;  border-radius: 5px; padding: 4px; " size="10" class="tcal" />
                </td>
                <td style='border-top : none;' >
                    do:  <input type="text"  name="do_datuma" <?php if(isset($datum_do)){echo"value='$datum_do'";} ?> required STYLE="background-color:white; border-color: #D8D8D8 ;  border-radius: 5px; padding: 4px;" size="10" class="tcal" />
                </td>
            </tr>
            <tr>
                <td colspan="2" >
                    <b>  Komentar: </b>
                </td>
            </tr>
            <tr>

                <td colspan="2" ><textarea name='komentar' class='form-control' ></textarea></td>

            </tr>


        </table>
    </div>
    
    <div class='col-md-12' >

        <table>
            <tr>
                <td></td>
                <td>
                    <button type="submit" id='register2' class="btn btn-danger">
                        <span class="glyphicon glyphicon-record"></span> Snimi izmene
                    </button>
                </td>
            </tr>
        </table>
    </div>
    
</form>
</div>
<?php



// include page footer HTML
include_once "layout_foot.php";
?>



</body>
</html>
