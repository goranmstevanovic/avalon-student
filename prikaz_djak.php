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
/**
 * Created by PhpStorm.
 * User: goran
 * Date: 25.10.2019
 * Time: 12:36
 */
if(isset($_GET['id'])){$iid = $_GET['id']; // echo "Djak je br: ", $iid;
}

// core configuration
include_once "config/core.php";

// set page title
$page_title = "Generalije djaka:";

// include login checker
include_once "login_checker.php";

// include classes
include_once 'config/database.php';
include_once 'config/autoload.php';


// include page header HTML
include_once "layout_head2.php";
// get database connection
$database = new Database();
$db = $database->getConnection();
$profesor = new User($db);
$nivo_znanja = new nivo_znanja($db);
$jezik = new jezik($db);
$velicina_grupe = new velicina($db);
$grupa = new grupa($db);
$povezivanje = new povezivanje($db);
$polozeni_ispit = new polozeni_ispit($db);
$djak_ispit = new djak_ispit($db);
$djak = new djak($db);
$lokacija = new lokacija($db);
$porodica = new porodica($db);
// initialize objects
//$user = new User($db);
$stmt = $djak->read_one_djak("djaci", $iid);
$row_category_djak = $stmt->fetch(PDO::FETCH_ASSOC);
extract($row_category_djak);
if(isset($_SESSION["poruka_izmena_djak"])){
    echo "<div class='alert alert-info'>";
    echo $_SESSION["poruka_izmena_djak"];
    unset($_SESSION["poruka_izmena_djak"]);
    echo "</div>";
}

echo "<div class='col-md-12' id='glavni'>";
echo"<h3>Izmeni generalije djaka</h3><br/>";

// registration form HTML
// code when form was submitted
// if form was posted
if($_POST){


    // $utils = new Utils();

    // set user email to detect if it already exists
    //  $user->email=$_POST['email'];

    // check if email already exists
	$djak->email="";
    // create user
    // set values to object properties
    $djak->firstname=$_POST['firstname'];
    $djak->lastname=$_POST['lastname'];
    $djak->contact_number=$_POST['contact_number'];
    $djak->address=$_POST['address'];
    if(isset($_POST['datum_rodjenja']) && $_POST['datum_rodjenja'] != '0000-00-00' && $_POST['datum_rodjenja'] != null  ){
        $tmp777 = explode (".", $_POST['datum_rodjenja']);
        $_POST['datum_rodjenja'] = $tmp777[2] . "-" . $tmp777[1] . "-" . $tmp777[0];
   }
   $djak->dat_rodjenja=$_POST['datum_rodjenja'] ?? null;
    $djak->email=$_POST['email'];
	$djak->komentar=$_POST['komentar'];
    $djak->online=$_POST['online'];
    $djak->komenatr_finansije=$_POST['komenatr_finansije']; 
    $djak->fk_lokacija=$_POST['fk_lokacija']; 
    $djak->fk_porodica=$_POST['fk_porodica'] ?? 0; 

    $djak->status = isset($_POST['status']) ? 1 : 0;
 //   echo '<script type="text/javascript">alert("'.$djak->status.'");</script>';

// create the user
    if($djak->update_djak($id)){
        if(isset($_POST['fk_ispit']) && $_POST['fk_ispit'] > 0 ){
            $stmt_provera = $djak_ispit->provera_dupliranja($id,$_POST['fk_ispit']);
            $broj_ispita_istih = $stmt_provera;
          //  echo $broj_ispita_istih;
           // exit();
            if($broj_ispita_istih == 0){
              //  var_dump($_POST['datum']);
                $_POST['datum'] = $_POST['datum'] ?? "0000.00.00";
             //   var_dump($_POST['datum']);
                $tmp77 = explode (".", $_POST['datum']);
                $_POST['datum'] = $tmp77[2] . "-" . $tmp77[1] . "-" . $tmp77[0];
             //   var_dump($_POST['datum']);
                $djak_ispit->datum = $_POST['datum'];
                $djak_ispit->fk_djak = $id;
                $djak_ispit->fk_ispit = $_POST['fk_ispit'];
                $djak_ispit->active = 1;
              
                $djak_ispit->komentar = $_POST['komentar_ispit'];
              //  $djak_ispit->fk_nivo_znanja = $_POST['fk_nivo_znanja'];
                if($djak_ispit->create()){
                    $_SESSION['poruka_ispit']="uspesno ste pridruzili ispit djaku";
                 //   echo "uspesnooooooo";
                }


            }
           
            
        }




        echo "<div class='alert alert-info'>";
        echo "Uspesno ste izmenili generalije djaka: ",$djak->firstname,"&nbsp;" ,$djak->lastname;
        echo "</div>";

        // empty posted values
        $_POST=array();
        //echo'<script>window.parent.opener.location.reload();</script>';
        //    echo'<script>window.location=window.parent.opener.location;</script>';
      //  echo"<script>window.close();</script>";
      $location = $home_url."admin/read_djak";
        $_SESSION["poruka_dodavanje"] =  "Uspesno ste promenili generalije djaka";
     //   $_SESSION["dodat_grupa"] = $_POST['alias'];
        $_POST=array();
       // header("Location: $location?message=success");
       echo "<script>window.history.go(-2)</script>";

    }else{
        echo "<div class='alert alert-danger' role='alert'>Neuspešna promena podataka. Molimo Vas da pokušate ponovo.</div>";
    }

}
?>
    

        <table class='table table-responsive'>

            <tr>
                <td class='width-30-percent'>Ime:</td>
                <td><input readonly type='text' name='firstname' value= "<?php echo $firstname; ?>" class='form-control' required /></td>
            </tr>

            <tr>
                <td>Prezime:</td>
                <td><input readonly type='text' name='lastname' class='form-control' required value="<?php echo $lastname; ?>" /></td>
            </tr>

            <tr>
                <td>Broj telefona:</td>
                <td><input readonly type='text' name='contact_number' class='form-control' required value="<?php echo $contact_number;  ?>" /></td>
            </tr>
            <tr>
            <?php if(isset($dat_rodjenja) && $dat_rodjenja != '0000-00-00' && $dat_rodjenja != null  ){
                 $tmp777 = explode ("-", $dat_rodjenja);
                 $dat_rodjenja = $tmp777[2] . "." . $tmp777[1] . "." . $tmp777[0];
            }else{
                $dat_rodjenja = null;
            } ?>
            <td class='width-30-percent'>Datum rodjenja:</td>
                <td>
                    <input readonly type="text"  name="datum_rodjenja" <?php if(isset($dat_rodjenja)){echo"value='$dat_rodjenja'";} ?>  STYLE="background-color:white; border: solid 0.01em gray; border-radius : 3px; padding: 6px;" size="10" class="tcal" />
               </td>
        </tr>
        <tr>
                <td class='width-30-percent' >Lokacija:</td>
                <td> 
                <?php 
                     $stmt_lokacija = $lokacija->read_All(); ?>
                     <select disabled class='form-control' name='fk_lokacija'>
                            <?php while ($row_lokcija =  $stmt_lokacija->fetch(PDO::FETCH_ASSOC)){ ?>
                                <option value="<?=$row_lokcija['id'] ?>" <?php if ($fk_lokacija == $row_lokcija['id']) { echo "selected";} ?> ><?=$row_lokcija['ime']; ?> </option>


                        <?php    
                        }
                        ?>
                </td>
           
            </tr>
            <tr>
                <td>Pripada porodici:</td>
                <td>
                    <?php $stmt_porodica = $porodica->read_all(); ?>
                    <select disabled class='form-control' name='fk_porodica'>
                            <option value = 0 >Odaberi porodicu...</option>
                            <?php while ($row_porodica =  $stmt_porodica->fetch(PDO::FETCH_ASSOC)){ ?>
                                <option value=<?=$row_porodica['id'] ?> <?php if (isset($fk_porodica) && $fk_porodica == $row_porodica['id']) { echo "selected";} ?> ><?=$row_porodica['ime']; ?> </option>
                               <?php    
                            }
                        ?>
                </td>
            </tr>

            <?php
             $stmt_polozen_ispit = $djak_ispit->read_all_ispit_djak($iid);
             $brojac = 0;
             while($row_category_polozen_ispit = $stmt_polozen_ispit->fetch(PDO::FETCH_ASSOC)){
                $brojac++;
             }

           //  var_dump( $brojac);
             if($brojac > 0){
                    echo" <tr>               
                    <td>Polozen ispit:</td>
                    <td>";
                    $stmt_polozen_ispit = $djak_ispit->read_all_ispit_djak($iid);
                    while($row_category_polozen_ispit = $stmt_polozen_ispit->fetch(PDO::FETCH_ASSOC)){
                        $stmt_ime_ispita = $polozeni_ispit->read_one($row_category_polozen_ispit['fk_ispit']);
                        $row_category_ime_polozen_ispit = $stmt_ime_ispita->fetch(PDO::FETCH_ASSOC);

                        $tmp77 = explode ("-", $row_category_polozen_ispit['datum']);
                        $row_category_polozen_ispit['datum'] = $tmp77[2] . "." . $tmp77[1] . "." . $tmp77[0];

                        $id_tog_termina = $row_category_polozen_ispit['id'];
                        echo"<a href='{$home_url}admin/edit_taj_ispit?id={$id_tog_termina}' title='vidi detalje'><span class='label label-primary' style='line-height: 200%; margin-right: 20px; font-size :13px' >";
                        echo $row_category_polozen_ispit['datum'],"&nbsp;",$row_category_ime_polozen_ispit['ime'];
                        echo" </span></a>";  
                    }
                    echo"</td>
                    </tr>  ";
                }
     

             
             ?> 
             
            <tr>
             
           
            <td>Dodaj polozen ispit:</td>
           
            <td>
            <div class="row" style="margin-bottom:20px;"  >
            <div class="col-md-12">
                  <select disabled class="selectpicker form-control down" data-show-subtext="true" data-live-search="true" name='fk_ispit' onchange="vreme(this);"  >
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
            <div id="vreme" class="col-md-6 " style="display:none;">
            Odaberi datum polaganja ispita:
            <input type="text"  name="datum" <?php if(isset($datum)){echo"value='$datum'";} ?>  STYLE="background-color:white; border: 1px solid gray; border-radius: 3px; padding: 6px;" size="10" class="tcal" />
                       
            </div>
          
               
               

                <div class="col-md-6" style="margin-top:0px;">
                <textarea name='komentar_ispit' placeholder="Unesite komentar za ispit..." class='form-control' ></textarea>
                                
                </div>
            </div>                
            
            </div>
        </td>
       
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

            <tr>
                <td>Adresa:</td>
                <td><textarea readonly name='address' class='form-control' ><?php echo $address;  ?></textarea></td>
            </tr>

            <tr>
                <td>Email:</td>
                <td><input readonly type='email' name='email'   class='form-control'  value="<?php echo $email;  ?>" /></td>
            </tr>
			
			<tr>
                <td>Komentar:</td>
                <td><textarea readonly name='komentar' class='form-control' ><?php echo $komentar;  ?></textarea></td>
            </tr>
            <tr>
            <td>Komentar za posebne finsijske uslove:</td>
            <td><input readonly type='text' name='komenatr_finansije' class='form-control' value="<?php echo $komenatr_finansije; ?>" / ></td>
            </tr>

            <tr style='display:none;'>
                <td>Vrsta obrazovanja:</td>
                <td><select readonly style="max-width: 250px;" class='form-control' name='online' required>
                        <option value='0'>Pohadja časove</option>
                        <option value='1' <?php if($online == TRUE){echo "selected";} ?> >Online</option>
                    </select> </td>
            </tr>
            <tr>
            <tr>
                <td>Aktivan</td>
                <td><input readonly type="checkbox" name="status" value="aktivan" <?php if($status==1){echo"checked";}  ?> > </td>
            </tr>

            <tr>
                <td></td>
                <td>
                <button class="btn btn-primary" onclick="window.history.back();">
                    <span class="glyphicon glyphicon-arrow-left"></span> Vrati se
                </button>
                </td>
            </tr>

        </table>

<?php

echo "</div>";

// include page footer HTML
include_once "layout_foot.php";
?>
</body>
</html>
