<html>
<head>
    <link rel="shortcut icon" href="images/kalen.png">
    <link rel="stylesheet" type="text/css" href="tigrakal/tcal.css" />
    <script type="text/javascript" src="tigrakal/tcal.js"></script>
    <link href="search/bootstrap.min.css" rel="stylesheet"> 
     <link rel="stylesheet" href="search/css/bootstrap-select.min.css" />
    
</head>
<body>
<?php




// core configuration
// include_once "config/core.php";

// // set page title
// $page_title = "";


  include 'layout_head2.php';
  //  include_once "navigation.php";



// include login checker
include_once "login_checker.php";

// include classes
include_once 'config/database.php';
include_once 'config/autoload.php';


// include_once 'objects/user.php';
// include_once 'objects/djak.php';
// include_once "libs/php/utils.php";
// include_once 'objects/grupa.php';
// include_once 'objects/nivo_znanja.php';
// include_once 'objects/jezik.php';
// include_once 'objects/velicina.php';
// include_once 'objects/povezivanje.php';
// include_once 'objects/polozeni_ispit.php';
// include_once 'objects/djak_ispit.php';
// include_once 'objects/lokacija.php';
// include_once 'objects/uzrast.php';



// include page header HTML
//include_once "layout_head1.php";

echo "<div class='col-md-12' style=' border: 1px solid silver '  >";
echo"<h3 style='background-color: #cddcfa; padding: 15px;'> Dodaj novog đaka: </h3>";
$database = new Database();
$db = $database->getConnection();
$profesor = new user($db);
$nivo_znanja = new nivo_znanja($db);
$jezik = new jezik($db);
$velicina_grupe = new velicina($db);
$grupa = new grupa($db);
$povezivanje = new povezivanje($db);
$polozeni_ispit = new polozeni_ispit($db);
$djak_ispit = new djak_ispit($db);
$lokacija = new lokacija($db);
$uzrast_djaka = new uzrast($db);
$porodica = new porodica($db);
$broj_lokacija = $lokacija->count_all();
$nacin_zaduzivanja = new nacin_zaduzivanja($db);
$program_rada = new program_rada($db);

// registration form HTML
// code when form was submitted
// if form was posted
if($_POST){

    // get database connection


    // initialize objects
    $djak = new djak($db);
  //  $utils = new Utils();

     // create termin
    // set values to object properties

    $djak->firstname=$_POST['firstname'];
    $djak->lastname=$_POST['lastname'];
    $djak->email=$_POST['email'] ?? null;
    $djak->contact_number=$_POST['contact_number'];
    $djak->address=$_POST['address'];
    $djak->fk_lokacija=$_POST['fk_lokacija'];
    if(isset($_POST['datum_rodjenja']) && $_POST['datum_rodjenja'] != '0000-00-00' && $_POST['datum_rodjenja'] != null  ){
        $tmp777 = explode (".", $_POST['datum_rodjenja']);
        $_POST['datum_rodjenja'] = $tmp777[2] . "-" . $tmp777[1] . "-" . $tmp777[0];
   }
    $djak->dat_rodjenja=$_POST['datum_rodjenja'] ?? null;
    $djak->komentar=$_POST['komentar']; 
    $djak->komenatr_finansije=$_POST['komenatr_finansije']; 
    $djak->online=$_POST['individual'];
    echo 'individual:',$djak->online,"<br/>";
    $individual = $_POST['individual'];
    $djak->fk_porodica=$_POST['fk_porodica'] ?? 0; 
    $djak->status = 1;

    if ($individual == 1 ) {
        $grupa->fk_jezik=$_POST['jezik1'];
        echo "jezik", $grupa->fk_jezik,"<br/>";
        $grupa->nivo=$_POST['nivo1'];
        echo 'nivo',$grupa->nivo,"<br/>";
        $grupa->fk_profesor=$_POST['profesor1'];
        echo 'profa',$grupa->fk_profesor,"<br/>";
        $grupa->alias=$_POST['alias1'];
        echo 'alias1: ',$grupa->alias,"<br/>";
        $grupa->komentar=$_POST['komentar1'];
        echo 'komentar',$grupa->komentar,"<br/>";
        $grupa->velicina=1;
        $grupa->fk_lokacija = $_POST['fk_lokacija'] ?? 1;
        $grupa->vrtic = isset($_POST['vrtic']) ? 1 : 0;
        $grupa->nacin=$_POST['nacin1'];
        echo 'nacin', $grupa->nacin,"<br/>";
        $grupa->uzrast=$_POST['uzrast1'];
        $grupa->isplata_profesoru=$_POST['isplata_profesoru1'] ?? null;
        echo 'uzrast',$grupa->uzrast,"<br/>";
        $grupa->status = 1;
        $grupa->fk_nacin_zaduzivanja = $_POST['fk_nacin_zaduzivanja'] ?? 1;
         $grupa->fk_program_rada = $_POST['fk_program_rada'] ?? null;
    }

    if ($individual == 2 ) {
        $grupa->fk_jezik=$_POST['fk_jezik'];
        echo "jezik", $grupa->fk_jezik,"<br/>";
        $grupa->nivo=$_POST['nivo'];
        echo 'nivo',$grupa->nivo,"<br/>";
        $grupa->fk_profesor=$_SESSION['user_id'];
        echo 'profa',$grupa->fk_profesor,"<br/>";
        $grupa->velicina=$_POST['velicina'];
        echo 'velicina',$grupa->velicina,"<br/>";
        $grupa->alias=$_POST['alias'];
        echo 'alias',$grupa->alias,"<br/>";
        $grupa->komentar=$_POST['komentar'];
        echo 'komentar',$grupa->komentar,"<br/>";
        //$grupa->velicina=1;
        $grupa->fk_lokacija = $_POST['fk_lokacija'] ?? 1;
        $grupa->vrtic = isset($_POST['vrtic']) ? 1 : 0;
        $grupa->nacin=$_POST['nacin'];
        $grupa->isplata_profesoru=$_POST['isplata_profesoru'] ?? null;
        echo 'nacin', $grupa->nacin,"<br/>";
        $grupa->uzrast=$_POST['uzrast'];
        echo 'uzrast',$grupa->uzrast,"<br/>";
        $grupa->status = 1;
        $grupa->fk_nacin_zaduzivanja = $_POST['fk_nacin_zaduzivanja'] ?? 1;
         $grupa->fk_program_rada = $_POST['fk_program_rada'] ?? null;
    }

    if ($individual == 3 ) {
        $povezivanje->fk_grupa=$_POST['grupa_pridruzivanje'];
    }

    

    /* $vreme_od=$sati_od.":".$minuti_od.":"."00";
                $odd= $datum_termina.' '.$vreme_od;  */

    // create the user
    if($id_djak = $djak->create_djak() ){
        if(isset($_POST['fk_ispit']) && $_POST['fk_ispit'] > 0 ){
            var_dump($_POST['datum']);
            $_POST['datum'] = $_POST['datum'] ?? "0000.00.00";
            var_dump($_POST['datum']);
            $tmp77 = explode (".", $_POST['datum']);
            $_POST['datum'] = $tmp77[2] . "-" . $tmp77[1] . "-" . $tmp77[0];
            var_dump($_POST['datum']);
            $djak_ispit->datum = $_POST['datum'];
            $djak_ispit->fk_djak = $id_djak;
            $djak_ispit->fk_ispit = $_POST['fk_ispit'];
            $djak_ispit->active = 1;
          
            $djak_ispit->komentar = $_POST['komentar_ispit'];
          //  $djak_ispit->fk_nivo_znanja = $_POST['fk_nivo_znanja'];
            if($djak_ispit->create()){
                $_SESSION['poruka_ispit']="uspesno ste pridruzili ispit djaku";
                echo "uspesnooooooo";
            }
            
        }
        if ($individual == 1 or $individual == 2 ){
            if($id_grupa = $grupa->create()){
                echo "<div class='alert alert-info'>";
                $poruka2 = "Uspesno ste uneli novu grupe u aplikaciju: <b> ".$grupa->alias."</b>";
               // echo "Uspesno ste uneli novu grupe u aplikaciju. i ima id: ",$id_grupa;
                echo "</div>";
                $povezivanje->fk_grupa=$id_grupa;
                $povezivanje->fk_djak=$id_djak;
                //$user->access_level='Customer';
                $povezivanje->status = 1;
            // create the user
                if($povezivanje->create()){
                    echo "<div class='alert alert-info'>";
                    $poruka3 = "Uspesno ste povezali klijenta: <b> ".$_POST['firstname']."&nbsp;".$_POST['lastname']." </b> sa grupom <b> ".$grupa->alias."</b>";
                   // echo "Uspesno ste povezali klijenta: ".$_POST['firstname'],"&nbsp;",$_POST['lastname']," sa grupom ",$grupa->alias;
                    echo "</div>";
                }
              //  $_POST=array();
            }
        }
        if ($individual == 3 ){
           // $povezivanje->fk_grupa=$id_grupa;
                $povezivanje->fk_djak=$id_djak;
                //$user->access_level='Customer';
                $povezivanje->status = 1;
                if($povezivanje->create()){
                    echo "<div class='alert alert-info'>";
                    $poruka3 = "Uspesno ste povezali djaka: <b>".$_POST['firstname']."&nbsp;".$_POST['lastname']." </b> sa grupom ".$grupa->alias;
                   // echo "Uspesno ste povezali klijenta: ".$_POST['firstname'],"&nbsp;",$_POST['lastname']," sa grupom ",$grupa->alias;
                    echo "</div>";
                }
            //    $_POST=array();

        }




        echo "<div class='alert alert-info'>";
        $poruka1 = "Uspesno ste uneli novog djaka u aplikaciju: <b> ".$_POST['firstname']."&nbsp;".$_POST['lastname']."</b>";
    //  echo "Uspesno ste uneli novog djaka u aplikaciju. i ima id: ",$id_djak;
        echo "</div>";

        // empty posted values
        $_POST=array();
        $location = $home_url."read_djak";
        $_SESSION["poruka_dodavanje"] =  $poruka1."<br/>".$poruka2."<br/>".$poruka3;
     //   $_SESSION["dodat_grupa"] = $_POST['alias'];
        $_POST=array();
        header("Location: $location?message=success");

       // echo'<script>window.parent.opener.location.reload();</script>';

       // echo"<script>window.close();</script>";

    }else{
        echo "<div class='alert alert-danger' role='alert'>Doslo je do greške. Please try again.</div>";
    }

}
?>
<form action='novi_djak.php' method='post' id='register'>

    <table class='table table-responsive padding: 20px;'>
        <tr>
            <td class='width-30-percent'>Ime:</td>
            <td><input type='text' name='firstname' class='form-control' required value="<?php echo isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname'], ENT_QUOTES) : "";  ?>" /></td>
        </tr>
        <tr>
            <td>Prezime:</td>
            <td><input type='text' name='lastname' class='form-control' required value="<?php echo isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname'], ENT_QUOTES) : "";  ?>" /></td>
        </tr>
        <tr>
            <td>Broj telefona:</td>
            <td><input type='text' name='contact_number' class='form-control' required value="<?php echo isset($_POST['contact_number']) ? htmlspecialchars($_POST['contact_number'], ENT_QUOTES) : "";  ?>" /></td>
        </tr>
        <tr>
            <td class='width-30-percent'>Datum rodjenja:</td>
                <td>
                    <input type="text"  name="datum_rodjenja" <?php if(isset($datum)){echo"value='$datum'";} ?>  STYLE="background-color:white; border: solid 0.01em gray; border-radius : 3px; padding: 6px;" size="10" class="tcal" />
               </td>
        </tr>
        <?php if($broj_lokacija > 1){ ?>
        <tr>
                <td class='width-30-percent' >Lokacija:</td>
                <td> 
                    <?php 
                     $stmt_lokacija = $lokacija->read_All(); ?>
                     <select class='form-control' name='fk_lokacija' id='fk_lokacija' onchange="locc(this);" required >
                     <option value='' >Odaberi lokaciju...</option>
                            <?php while ($row_lokcija =  $stmt_lokacija->fetch(PDO::FETCH_ASSOC)){ ?>
                                <option value="<?=$row_lokcija['id'] ?>"  ><?=$row_lokcija['ime']; ?> </option>
                        <?php    
                        }
                        ?>
                </td>
        </tr> 
        <?php }else{ 
             $stmt_lokacija = $lokacija->read_All(); 
             $row_jedna_lokacija = $stmt_lokacija->fetch(PDO::FETCH_ASSOC);
             $locc = $row_jedna_lokacija['id'];
            ?>
            <input style=' visibility: hidden;' type='number' value = '<?=$locc ?>' name = "fk_lokacija" />
        <?php 
        } ?>
        <script>
        function locc(sel)
        {
            if(sel.value == ""){var loc = 'none'; }
            if(sel.value == 1){var loc = 1; }
            if(sel.value == 2){var loc = 2; }
            <?php $loc = "<script>document.write(loc)</script>"?>   
            document.cookie = loc;
           console.log(loc);
            /*  alert(sel.value); */
        }
        </script>
          <tr>
                <td>Pripada porodici:</td>
                <td>
                    <?php $stmt_porodica = $porodica->read_all(); ?>
                    <select class='form-control' name='fk_porodica'>
                            <option value = 0 >Odaberi porodicu...</option>
                            <?php while ($row_porodica =  $stmt_porodica->fetch(PDO::FETCH_ASSOC)){ ?>
                                <option value=<?=$row_porodica['id'] ?> <?php if (isset($fk_porodica) && $fk_porodica == $row_porodica['id']) { echo "selected";} ?> ><?=$row_porodica['ime']; ?> </option>
                               <?php    
                            }
                        ?>
                </td>
            </tr>

        <tr>
            <td>Polozen ispit:</td>
           
            <td>
            <div class="row" style="margin-bottom:20px;"  >
            <div class="col-md-12">
                  <select class="selectpicker form-control down" data-show-subtext="true" data-live-search="true" name='fk_ispit' onchange="vreme(this);"  >
                    <option value ="0" >Odaberi ispit...</option>
                    <?php
                         $stmt_ispit = $polozeni_ispit->read_all();
                         while ($row_category_ispit =  $stmt_ispit->fetch(PDO::FETCH_ASSOC)){ 
                            //var_dump($row_category_ispit);
                            $nbsp_taj_jezik = $jezik->read_one($row_category_ispit['fk_jezik'],"jezik");
                            $row_category_taj_jezik =  $nbsp_taj_jezik->fetch(PDO::FETCH_ASSOC);
                           // var_dump($row_category_taj_jezik );
                           $nbsp_taj_nivo_znanja = $nivo_znanja-> read_one_nivo($row_category_ispit['fk_nivo_znanja'],"nivo_znanja");
                            $row_category_taj_taj_nivo_znanja =   $nbsp_taj_nivo_znanja->fetch(PDO::FETCH_ASSOC);

                             ?>
                            <option value = "<?php echo $row_category_ispit['id']; ?>" ><?php echo $row_category_ispit['ime']; ?>&nbsp; / &nbsp;<?php echo $row_category_taj_jezik['ime']; ?> &nbsp; / &nbsp; <?php echo $row_category_taj_taj_nivo_znanja['ime'];  ?>  </option>

                        <?php  } ?>
                        
                       
                    </select>
            </div>
             </div>
            <div class="row" id="jezik" style="display:none; margin-top:20px;">
            <div id="vreme" class="col-md-7 " style="display:none;">
            Odaberi datum polaganja ispita:
            <input type="text"  name="datum" <?php if(isset($datum)){echo"value='$datum'";} ?>  STYLE="background-color:white; border: 1px solid gray; border-radius: 3px; padding: 6px;" size="10" class="tcal" />
                       
            </div>
          
               
               

                <div class="col-md-5" style="margin-top:0px;">
                <textarea name='komentar_ispit' placeholder="Unesite komentar za ispit..." class='form-control' ></textarea>
                                
                </div>
            </div>                
            
            </div>
        </td>
        </tr>
             <script>
                function vreme(sel)
                    {
                            if(sel.value != "0"){ document.getElementById('vreme').style.display = 'block'; document.getElementById('jezik').style.display = 'block';   }
                            if(sel.value == "0"){ document.getElementById('vreme').style.display = 'none';  document.getElementById('jezik').style.display = 'none';  }
                            event.preventDefault();
                            return false;
                                        /*  alert(sel.value); */
                    }
             </script>

     
        <tr>
            <td>Adresa:</td>
            <td><textarea name='address' class='form-control' ></textarea></td>
        </tr>
        <tr>
            <td>Email:</td>
            <td><input type='email' name='email' id="email" class='form-control' value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES) : "";  ?>" /></td>
        </tr>
        <tr>
            <td>Komentar:</td>
            <td><textarea name='komentar' class='form-control' ></textarea></td>
        </tr>
        <tr>
            <td>Komentar za posebne finsijske uslove:</td>
            <td><input type='text' name='komenatr_finansije' class='form-control' / ></td>
        </tr>
        
        <script>
        function getval(sel)
        {
            if(sel.value == ""){document.getElementById('individual').style.display = 'none'; document.getElementById('grupa').style.display = 'none';   document.getElementById('pridruzivanje').style.display = 'none'; }
            if(sel.value == 1){document.getElementById('individual').style.display = 'block'; document.getElementById('grupa').style.display = 'none';   document.getElementById('pridruzivanje').style.display = 'none'; }
            if(sel.value == 2){document.getElementById('individual').style.display = 'none'; document.getElementById('grupa').style.display = 'block';  document.getElementById('pridruzivanje').style.display = 'none'; }
            if(sel.value == 3){document.getElementById('individual').style.display = 'none'; document.getElementById('grupa').style.display = 'none';  document.getElementById('pridruzivanje').style.display = 'block'; }
         
            /*  alert(sel.value); */
        }
        </script>
        <tr>
            <td>Vrsta:</td>
            <td><select   class='form-control' name='individual' onchange="getval(this);" required>
                    <option value=''>Odaberi način pohadjanja nastave</option>
                    <!-- <option value='4'>Bez grupe za sad</option> -->
                    <option value='2'>grupna nastava sa novom grupom</option>
                    <option value='3'>grupna nastava sa prodruzivanjem postojecoj grupi</option>
                </select> </td>
        </tr>
    </table>
    
        <div id='individual' class='col-md-12' style = 'display: none; border: 1px solid #A4A4A4; border-radius:5px;  background-color:#F2F2F2'>
            <h4 style="background-color:#D8D8D8; padding:5px">Odaberi generalije individualne grupe za novog djaka </h4>
            <table class='table table-responsive'>
                                 
            <tr>
            <td class='width-30-percent'>Jezik:</td>
            <td>
                    <select class='form-control' name='jezik1' id='jezik1' novalidate  >
                        
                        <?php $stm_jezik = $jezik->read_all();
                        while ($row_category_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC)){?>
                                <option value = "<?php echo $row_category_jezik['id']; ?>"  > 
                                <?php echo $row_category_jezik['ime']; ?> </option>
                        <?php } ?>
                    </select>
            </td>
            <script type='text/javascript'>
				 document.getElementById('jezik1').value = "<?php echo $_POST['jezik1'];?>";
			</script>
        </tr>
            <tr>
            <td>Edukacijski nivo:</td>
            <td>
                <?php
                $stm_nivo = $nivo_znanja->read_all();
                ?>
                <select class='form-control' name='nivo1' id='nivo1' value='0'  >
                 
                    <?php while ($row_category_nivo = $stm_nivo->fetch(PDO::FETCH_ASSOC)){?>
                       <option value = "<?php echo $row_category_nivo['id']; ?>"  > 
                       <?php echo $row_category_nivo['ime']; ?> </option>
                    <?php } ?>    
                </select>
            </td>
            <script type='text/javascript'>
				 document.getElementById('nivo1').value = "<?php echo $_POST['nivo1'];?>";
			</script>
        </tr>
        <tr>
        <td>Profesor:</td>
        <td>
            <?php
            
            ?>
        </td>
        <script type='text/javascript'>
				 document.getElementById('profesor1').value = "<?php echo $_POST['profesor1'];?>";
			</script>
        </tr>

        <tr>
            <td>Spec isplata prof. za grupu:</td>
            <td style='text-align:center; width:5em'>
            <input type="number" name="isplata_profesoru1"   class='form-control' / >
            </td>
            </tr>        


        <tr>

         <tr>
            <td>Vrsta grupe po načinu slušanja nastavee:</td>
            <td>
                <select class='form-control' name='nacin1' id='nacin1' >
                    
                    <option value=1>U školi</option>
                    <option value=2>Online</option>
                </select>
            </td>
            <script type='text/javascript'>
				 document.getElementById('nacin1').value = "<?php echo $_POST['nacin1'];?>";
			</script>
        </tr>
        <tr>
            <td>Uzrast:</td>
            <td>
                    <select class='form-control' name='uzrast1' >
                        
                        <?php
                         $stmt_uzrast = $uzrast_djaka->read_all();
                       //  var_dump($stmt_uzrast);
                        while ($row_category_uzrast = $stmt_uzrast->fetch(PDO::FETCH_ASSOC)){ 
                            //var_dump($row_category_uzrast);
                            ?>
                            <option value="<?=$row_category_uzrast['id']; ?>"  ><?=$row_category_uzrast['ime']; ?></option>
                        
                       <?php } ?>
                      
                    </select>
                </td>
            <script type='text/javascript'>
				 document.getElementById('uzrast1').value = "<?php echo $_POST['uzrast1'];?>";
			</script>
        </tr>
        <tr>
            <td>Alias:</td>
            <td><input type='text' name='alias1' id='alias1' class='form-control' maxlength="12" value='alias'   /></td>
            <script type='text/javascript'>
				 document.getElementById('alias1').value = "<?php echo $_POST['alias1'];?>";
			</script>
        </tr>
        <tr>
            <td>Komentar:</td>
            <td><textarea name='komentar1' id='komentar1' class='form-control' ></textarea></td>
            <script type='text/javascript'>
				 document.getElementById('komentar1').value = "<?php echo $_POST['komentar1'];?>";
			</script>
        </tr>
    </table>
    </table>
    </div>
             
    <div id='grupa' class='col-md-12' style = 'display: none; border: 1px solid #A4A4A4; border-radius:5px;  background-color:#E0F8F7'>
            <h4 style="background-color:#D8D8D8; padding:5px">Odaberi generalije nove grupe za novog djaka </h4>
            <table class='table table-responsive'>
            <tr>
            <td class='width-30-percent'>Jezik:</td>
            <td>
                    <select class='form-control' name='fk_jezik' id='jezik' novalidate  >
                        
                        <?php $stm_jezik = $jezik->read_all();
                        while ($row_category_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC)){?>
                                <option value = "<?php echo $row_category_jezik['id']; ?>"  > 
                                <?php echo $row_category_jezik['ime']; ?> </option>
                        <?php } ?>
                    </select>
            </td>
            <script type='text/javascript'>
				 document.getElementById('jezik').value = "<?php echo $_POST['jezik'];?>";
			</script>
        </tr>
            <tr>
            <td>Edukacijski nivo:</td>
            <td>
                <?php
                $stm_nivo = $nivo_znanja->read_all();
                ?>
                <select class='form-control' name='nivo' id='nivo' value='0'  >
                 
                    <?php while ($row_category_nivo = $stm_nivo->fetch(PDO::FETCH_ASSOC)){?>
                       <option value = "<?php echo $row_category_nivo['id']; ?>"  > 
                       <?php echo $row_category_nivo['ime']; ?> </option>
                    <?php } ?>    
                </select>
            </td>
            <script type='text/javascript'>
				 document.getElementById('nivo').value = "<?php echo $_POST['nivo'];?>";
			</script>
        </tr>
        <tr>
            <td>Profesor:</td>
            <td>
                <?php
                $stmt_profesor = $profesor->read_one($_SESSION['user_id']);
                $row_category_profesor =  $stmt_profesor->fetch(PDO::FETCH_ASSOC);
                echo $row_category_profesor['firstname'],"&nbsp;",$row_category_profesor['lastname'];
                ?>
            </td>

        </tr>
       
        <tr>
                <td>Vrsta grupe po veličini:</td>
                <td>
                    <select class='form-control' name='velicina' id='velicina' >
                    
                    <?php
                         $stmt_velicina = $velicina_grupe->read_all();
                         while ($row_category_velicina =  $stmt_velicina->fetch(PDO::FETCH_ASSOC)){ ?>
                            <option value = "<?php echo $row_category_velicina['id']; ?>" > <?php echo $row_category_velicina['ime'];  ?> </option>

                        <?php  } ?>
                        
                       
                    </select>
                </td>
          
        </tr>
                  <tr>
                <td class='width-30-percent'>Plan rada grupe:</td>
                <td>
                  <?php 
                    $stm_program_rada = $program_rada->read_all();
                      //  $stm_jezik = $jezik->read_all();
                    ?>
                    <select class='form-control' name='fk_program_rada'  >
                        <option  >Odaberi plan rada grupe...</option>
                        <?php while ($row_category_program_rada = $stm_program_rada->fetch(PDO::FETCH_ASSOC)){
                            ?>
                            <option value = "<?php echo $row_category_program_rada['id']; ?>"  > 
                            <?php echo $row_category_program_rada['ime'] . ' / ' . $row_category_program_rada['ime_jezika']; ?> </option>
                            <?php 
                        } ?>
                    </select>
                </td>
            </tr>
        <tr>
        <tr>
                <td>Grupa pripada vrtiću</td>
                <td> <input type="checkbox"   id="vrtic" name="vrtic" value="1"></td>
            </tr>               

        <tr>
            <td>Vrsta grupe po načinu slušanja nastave:</td>
            <td>
                <select class='form-control' name='nacin' id='nacin' >
                    
                    <option value=1>U školi</option>
                    <option value=2>Online</option>
                </select>
            </td>
         
        </tr>
        <tr>
            <td>Uzrast:</td>
            <td>
                    <select class='form-control' name='uzrast' >
                        
                        <?php
                         $stmt_uzrast = $uzrast_djaka->read_all();
                       //  var_dump($stmt_uzrast);
                        while ($row_category_uzrast = $stmt_uzrast->fetch(PDO::FETCH_ASSOC)){ 
                            //var_dump($row_category_uzrast);
                            ?>
                            <option value="<?=$row_category_uzrast['id']; ?>"  ><?=$row_category_uzrast['ime']; ?></option>
                        
                       <?php } ?>
                      
                    </select>
                </td>
            <script type='text/javascript'>
				 document.getElementById('uzrast').value = "<?php echo $_POST['uzrast'];?>";
			</script>
        </tr>
        <tr>
                <td class='width-30-percent' >Način zaduživanja djaka:</td>
                <td> 
                    <?php
                     
                        $stmt_nacin_zaduzivanja = $nacin_zaduzivanja->read_all(); ?>
                        <select class='form-control' name='fk_nacin_zaduzivanja' required>
                        
                                <?php while ($row_nacin_zaduzivanja =  $stmt_nacin_zaduzivanja->fetch(PDO::FETCH_ASSOC)){ ?>
                                    <option value="<?=$row_nacin_zaduzivanja['id'] ?>" <?php if (isset($fk_nacin_zaduzivanja)  AND $fk_nacin_zaduzivanja == $row_nacin_zaduzivanja['id']) { echo "selected";} ?> ><?=$row_nacin_zaduzivanja['ime']; ?> </option>
                            <?php    
                            }
                            ?>
                </td>
            </tr>    
        <tr>
        <tr>
            <td>Alias:</td>
            <td><input type='text' name='alias' id='alias' class='form-control' maxlength="12" value='alias'   /></td>
            <script type='text/javascript'>
				 document.getElementById('alias').value = "<?php echo $_POST['alias'];?>";
			</script>
        </tr>
        <tr>
            <td>Komentar:</td>
            <td><textarea name='komentar' id='komentar' class='form-control' ></textarea></td>
            <script type='text/javascript'>
				 document.getElementById('komentar').value = "<?php echo $_POST['komentar'];?>";
			</script>
        </tr>
    </table>
    </div> 
    <div id='pridruzivanje' class='col-md-12' style = 'display: none; border: 1px solid #A4A4A4; border-radius:5px;  background-color:#F2F5A9'>
    <h4>Odaberi grupu kojoj bi pridruzio novog djaka:</h4>
    <div class='col-md-8' >
            <table class='table table-responsive'>
                <tr>
                    <td>
                        <b>  Dodaj u grupu: </b>
                    </td>
                
                    <td>

                    
                    <?php
                   
                   // var_dump($osim);
                 
                  // echo  $_COOKIE["loc"];
                  $loc = "<script>document.getElementByID('fk_lokacija').value</script>";
                 // if(isset($loc)){echo " Loc je: ",'php_'.$loc;}
                    $stmt_dodavanje = $grupa->read_all_moji($_SESSION['user_id']);
                 //   var_dump($stmt_dodavanje->fetch(PDO::FETCH_ASSOC));
                  //  $stmt = $grupa->read_allgrupa_osim("grupe", $osim );
                  //  var_dump($stmt->fetch(PDO::FETCH_ASSOC));
    
                   // $stmt = $grupa->read_allgrupa("grupe" );

                    // put them in a select drop-down
                    ?>
                  <select   name='grupa_pridruzivanje' class="selectpicker form-control down" data-show-subtext="true" data-live-search="true"  >
                  
                    <?php
                    while ($row_category_dodavanja = $stmt_dodavanje->fetch(PDO::FETCH_ASSOC)){     // Citanje jedne po jedne vrste  iz $smtp i mecanje u $rowcategory
                      //  extract($row_category_dodavanja); 
                        $stmt1 = $profesor->read_one_profesor($row_category_dodavanja['fk_profesor'],"users");
                        $row_profesor = $stmt1->fetch(PDO::FETCH_ASSOC);
                       // var_dump($row_profesor);
                      //  echo"qq<br/>";
                        $stmt1_lokacija = $lokacija->read_one($row_category_dodavanja['fk_lokacija']); 
                        $row1_lokacija = $stmt1_lokacija->fetch(PDO::FETCH_ASSOC);

                        $stm_jezik = $jezik->read_one_jezik($row_category_dodavanja['fk_jezik'],'jezik');
                        $row_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC);
                        $stm_nivo = $nivo_znanja->read_one_nivo($row_category_dodavanja['nivo'],'nivo_znanja');
                        $row_nivo_znanja = $stm_nivo->fetch(PDO::FETCH_ASSOC); ?>
                        <option value=<?php echo $row_category_dodavanja['id']; ?> > 
                        <?php 
                        echo $row_jezik['ime'],"&nbsp;",$row_nivo_znanja['ime'],"&nbsp;",$row_profesor['firstname'],"&nbsp;-[",$row_category_dodavanja['alias'],"] - "
                        ,$row1_lokacija['ime']; 
                        ?>
                        
                       </option>   
                    <?php
                    }
                    ?>
                    </select>
                    
                    </td>
                </tr>
            <script src="search/jquery.min.js"></script>
            <script src="search/bootstrap.min.js"></script>
            <!-- <script src="search/bootstrap-select.min.js"></script> -->
            </table>                   
    </div>            


    </div>
          




    <table class='table table-responsive'>
    <tr>
        <td></td>
        <td>
            <button type="submit" id='register2' class="btn btn-danger">
            <span class="glyphicon glyphicon-plus"></span> Dodaj đaka
            </button>
        </td>
    </tr>
    </table>
</form>
<script>
$(document).ready(function () {
    $("#register").submit(function () {
        $("#register2").attr("disabled", true);
        return true;
    });
});

$(document).ready(function() {
            $("#register").on("submit", function(e) {
                e.preventDefault(); // Spreči podnošenje forme

                // Preuzmi email vrednost
                const email = $("#email").val();
                // alert(email);
                // AJAX zahtev za proveru email-a
                $.ajax({
                    url: "check_email.php", // Putanja do PHP skripte
                    type: "POST",
                    data: { email: email },
                    dataType: "json",
                    success: function(response) {
                       // alert(JSON.stringify(response));

                        if (response.exists) {
                            // Ako email postoji, označi polje i prikaži grešku
                            $("#email").addClass("error");
                            alert("Ova email adresa već postoji u bazi, pokušajte neku drugu !");
                        } else {
                            // Ako email ne postoji, ukloni grešku i podnesi formu
                            $("#email").removeClass("error");
                            //alert("Email je slobodan!"); // Samo za test
                            // Ako želite da podnesete formu, možete koristiti:
                             $("#register")[0].submit();
                        }
                        $("#register2").attr("disabled", false);
                    },
                    error: function() {
                        alert("Došlo je do greške prilikom provere email-a.");
                    }
                });
            });

            // Ukloni crveni okvir kada korisnik unese nešto novo
            $("#email").on("input", function() {
                $(this).removeClass("error");
            });
        });

</script>
<?php

echo "</div>";

// include page footer HTML
//include_once "layout_foot.php";
?>
</body>
</html>
