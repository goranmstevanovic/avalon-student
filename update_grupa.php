<html>
<head>
    <link rel="shortcut icon" href="images/kalen.png">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>    
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">
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
if(isset($_GET['id'])){$iid = $_GET['id']; // echo "Grupa je br: ", $iid;
}

if(isset($_GET['uvid'])){  echo "imamo uvid";
}

// core configuration
include_once "config/core.php";

// set page title
$page_title = "<h3>Izmeni generalije studentske grupe:</h3>";

// include login checker
include_once "login_checker.php";

// include classes
include_once 'config/database.php';
include_once 'objects/user.php';
include_once 'objects/grupa.php';
include_once 'objects/djak.php';
include_once "libs/php/utils.php";
include_once 'objects/povezivanje.php';
include_once 'objects/velicina.php';
include_once 'objects/uzrast.php';
include_once 'objects/udzbenik.php';
include_once 'objects/udzbenik_grupa.php';


// include page header HTML
include_once "layout_head2.php"; 



// get database connection
$database = new Database();
$db = $database->getConnection();
$profesor = new User($db);
$grupa = new grupa($db);
$povezivanje = new povezivanje($db);
$djak = new djak($db);
$velicina_grupe = new velicina($db);
$uzrast1 = new uzrast($db);
$udzbenik = new udzbenik($db);
$udzbenik_grupa = new udzbenik_grupa($db);
// initialize objects
//$user = new User($db);
$stmt = $grupa->read_one_grupa($iid,"grupe");
$row_category_grupa = $stmt->fetch(PDO::FETCH_ASSOC);
//echo "Lokacija",$row_category_grupa['fk_lokacija'];
extract($row_category_grupa);
//echo "Profesor je; ", $fk_profesor;
//$stmt7 = $profesor->read_profesor_svi("users");
$stmt7 = $profesor->read_profesor("users");
$stmt = $profesor->read_one_profesor($fk_profesor,"users");
$row_category_profesor = $stmt->fetch(PDO::FETCH_ASSOC); 
$nacin_zaduzivanja =  new nacin_zaduzivanja($db);
?>

<div class='col-md-12' style="width:100% ; margin-left:0%" id='glavni' >

<h4 style='padding: 20px;' >Izmeni generalije studentske grupe:</h4>

<?php
//$stmt_uzrast = $uzrast1->read_all();
//var_dump($stmt_uzrast);
if($_POST){

    $grupa->fk_jezik=$_POST['jezik'];
    $grupa->nivo=$_POST['nivo'];
    $grupa->fk_profesor=$_POST['profesor'];
    $grupa->alias=$_POST['alias'];
    $grupa->komentar=$_POST['komentar'];
    $grupa->velicina=$_POST['velicina'];
    $grupa->nacin=$_POST['nacin'];
    $grupa->uzrast=$_POST['uzrast'];
    $grupa->fk_lokacija = $row_category_grupa['fk_lokacija'];
    $grupa->vrtic = isset($_POST['vrtic']) ? 1 : 0;
    $grupa->fk_nacin_zaduzivanja = $_POST['fk_nacin_zaduzivanja'] ?? 1;
   // $grupa->procenat_za_platu = null;
    // $procenat_za_platu - ono sto stoji u tabeli grupa = $row_category_grupa koja se ekstrakuje
    // row_category_profesor - ono sto stoji u tabeli user

    
        $grupa->procenat_za_platu = 50;

    






   // if( isset($_POST['status']) && $_POST['status'] == "aktivan"  ) {$grupa->status = TRUE;} else {$grupa->status = FALSE;}

    $grupa->status = isset($_POST['status']) ? 1 : 0;
  //  echo '<script type="text/javascript">alert("'.$grupa->status.'");</script>';

// create the user
    if($grupa->update_profesor($iid)){

        $udz_grupa = $_POST['udzbenik_grupa'];
        //    $_SESSION["poruka_kat_user"] ="";

            $udzbenik_grupa->delete_all_uzbenike_grupa($iid);
           
                foreach($udz_grupa as $lla){
                    echo $lla,"<br/>";
                    $udzbenik_grupa->fk_udzbenik = $lla;
                    $udzbenik_grupa->fk_grupa = $iid;
                    $udzbenik_grupa->active = 1;
                    if($udzbenik_grupa->create()){
                      //  $_SESSION["poruka_udznenik_grupa"] = $_SESSION["poruka_kat_user"]."<br/>"."Uspešno ste saradniku: ".$user->firstname."&nbsp".$user->lastname."&nbsp; dodali kategoriju usluga";
                    } 
                }
            

        echo "<div class='alert alert-info'>";
     //   echo "row_category_grupa[procenat_za_platu] ",$row_category_grupa["procenat_za_platu"],"--<br/> POST[procenat_za_platu]: ",$_POST['procenat_za_platu'],"<br/> row_category_profesor[procenat_za_platu]",$row_category_profesor["procentat_za_platu"],"<br/>";
        echo "Uspesno ste promenili generalije grupe.";
        echo "</div>";
        $location = $home_url."read_grupa";
      //  $location = $_SERVER['HTTP_REFERER'];
        $_SESSION["poruka_grupa"] =  "Uspešno ste izmenili generalije grupe: ".$_POST['alias'];
        
        $_POST=array();
        header("Location: $location?message=success");
       // header('Location: ' . $_SERVER['HTTP_REFERER']);
       ?>
       <script>
           console.log(history.go(-1));
       </script>
       <?php  
      //  header("location:javascript://history.go(-1)");
             

       
    
      //  echo'<script>window.parent.opener.location.reload();</script>';

       // echo"<script>window.close();</script>";

    }else{
        echo "<div class='alert alert-danger' role='alert'>Neuspešna promena podataka. Molimo Vas da pokušate ponovo.</div>";
    }

}
?>

    <form  method='post' >

        <table class='table table-responsive'>

            <tr>
                <td class='width-30-percent'>Jezik:</td>
                <td>
                <?php 
                 include_once 'objects/nivo_znanja.php';
                 include_once 'objects/jezik.php';
                 $nivo_znanja = new nivo_znanja($db);
                 $jezik = new jezik($db);
                 
                 $stm_jezik = $jezik->read_one_jezik($fk_jezik,'jezik');
                 $stm_jezik = $jezik->read_all();
                
                ?>
                    <select class='form-control' name='jezik'  required>
                        <?php while ($row_category_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC)){?>

                            <option value = "<?php echo $row_category_jezik['id']; ?>" <?php if($fk_jezik==$row_category_jezik['id']){echo "selected"; } ?> > 
                            <?php echo $row_category_jezik['ime']; ?> </option>

                        <?php } ?>
                        
                      
                    </select>
                </td>
            </tr>

            <tr>
                <td>Edukacijski nivo:</td>
                <td>
                    <?php
                    $stm_nivo = $nivo_znanja->read_all();
                    ?>
                    <select class='form-control' name='nivo'  required>
                    <?php while ($row_category_nivo = $stm_nivo->fetch(PDO::FETCH_ASSOC)){?>
                        <option value = "<?php echo $row_category_nivo['id']; ?>" <?php if($nivo == $row_category_nivo['id']){echo "selected"; } ?> > 
                        <?php echo $row_category_nivo['ime']; ?> </option>
                    <?php } ?>    
                    </select>
                </td>
            </tr>
            <tr>
                <td>Profesor:</td>
                <td>
                    <?php
                    $stmt_taj_profesor = $profesor->read_jedan_profesor($fk_profesor,"users");
                    $row_category_taj_profesor = $stmt_taj_profesor->fetch(PDO::FETCH_ASSOC);
                    echo $row_category_taj_profesor['firstname'],"&nbsp;",$row_category_taj_profesor['lastname']; ?>
                    <input type='number' name='profesor' value= "<?php echo $fk_profesor; ?>" style='display:none;' />
                
                </td>
            </tr>
            <tr>
                <td>Djaci u grupi:</td>
                <td style="color: #555;">
                    <?php
                    //echo $iid;
                    $sts = $povezivanje->read_all_students($iid);
                    while ($row_category_djaci = $sts->fetch(PDO::FETCH_ASSOC))
                    {
                        $indeks = $row_category_djaci['fk_djak'];
                        $ssts = $djak->read_one_djak("djaci", $indeks );
                        //echo $row_category_djaci['fk_djak'],  "<br/>";
                        $row_category_detalj_djaci = $ssts->fetch(PDO::FETCH_ASSOC);
                        if($row_category_detalj_djaci['status'] == 1) {
                            echo "<input type='checkbox' name='status' value='aktivan'";
                            if ($row_category_detalj_djaci['status'] == 1) {
                                echo "checked";
                            }
                            echo "> &nbsp;";
                            echo $row_category_detalj_djaci['firstname'], "&nbsp;", $row_category_detalj_djaci['lastname'], "<br/>";
                            // echo "ja";
                        }
                    }
                    ?>

                </td>
            </tr>
            <tr>
                <td>Vrsta grupe po veličini:</td>
                <td>
                <select class='form-control' name='velicina'>
                        <option >Odaberi vrstu grupe...</option>
                            <?php 
                            $stmt_velicina = $velicina_grupe->read_all();
                            while ($row_category_velicina =  $stmt_velicina->fetch(PDO::FETCH_ASSOC)){ ?>
                                <option value = "<?php echo $row_category_velicina['id']; ?>" <?php if($velicina == $row_category_velicina['id'] ){ echo "selected";} ?>  > <?php echo $row_category_velicina['ime'];  ?> </option>

                            <?php  } ?>
                </select>
                </td>
            </tr>
            <tr>
                <td>Vrsta grupe po načinu slušanja nastave:</td>
                <td>
                    <select class='form-control' name='nacin'>
                        <option >Odaberi vrstu grupe...</option>
                        <option value=1 <?php if($nacin == 1){echo 'selected';} ?> >U školi</option>
                        <option value=2 <?php if($nacin == 2){echo 'selected';} ?>  >Online</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Grupa pripada vrtiću</td>
                <td> <input type="checkbox" class='form-control' <?php if(isset($vrtic) AND $vrtic == 1){echo 'checked';} ?> id="vrtic" name="vrtic" value="1"></td>
            </tr>
            <tr>
            <tr>
                    <td class='width-30-percent' >Način zaduživanja djaka:</td>
                    <td> 
                    <?php
                     
                        $stmt_nacin_zaduzivanja = $nacin_zaduzivanja->read_all(); ?>
                        <select class='form-control' name='fk_nacin_zaduzivanja' disabled required >
                        <option value="">Odaberi način zaduživanja...</option>
                                <?php while ($row_nacin_zaduzivanja =  $stmt_nacin_zaduzivanja->fetch(PDO::FETCH_ASSOC)){ ?>
                                    <option value="<?=$row_nacin_zaduzivanja['id'] ?>" <?php if (isset($fk_nacin_zaduzivanja)  AND $fk_nacin_zaduzivanja == $row_nacin_zaduzivanja['id']) { echo "selected";} ?> ><?=$row_nacin_zaduzivanja['ime']; ?> </option>


                            <?php    
                            }
                            ?>
                    </td>
                </tr>        
            <td>Uzrast:</td>
                <td>
                    <select class='form-control' name='uzrast' required>
                        <option value="">Odaberi starosnu grupu...</option>
                        <?php
                         $stmt_uzrast = $uzrast1->read_all();
                       //  var_dump($stmt_uzrast);
                        while ($row_category_uzrast = $stmt_uzrast->fetch(PDO::FETCH_ASSOC)){ 
                            //var_dump($row_category_uzrast);
                            ?>
                            <option value="<?=$row_category_uzrast['id']; ?>" <?php if($uzrast == $row_category_uzrast['id']){echo 'selected';} ?> ><?=$row_category_uzrast['ime']; ?></option>
                        
                       <?php } ?>
                      
                    </select>
                </td>
            <tr>
            <td>Knjige u grupi</td>
            <td>
            <?php 
            $stmt_udzbenici = $udzbenik_grupa->read_all_udzbenik_grupa($id);
            while ($row_udbenici = $stmt_udzbenici->fetch(PDO::FETCH_ASSOC)){
              //  var_dump($row_udbenici['fk_udzbenik']);
                $stmt_ime_udzbenik = $udzbenik->read_one1($row_udbenici['fk_udzbenik'],'udzbenici');
                $row_ime_udbenici = $stmt_ime_udzbenik->fetch(PDO::FETCH_ASSOC);
              //  var_dump($stmt_ime_udzbenik);
              //  echo $row_ime_udbenici['ime'],"<br/>";
                echo"<div ><span class='label label-success label-inline ' style='line-height: 200%; margin-right: 5px; font-size :10px' >";
                echo  /*$row_category_polozen_ispit['datum'],"&nbsp;", */$row_ime_udbenici['ime'];
                echo" </span></div>";  

            }
        
        ?>
            
            </td>
            </tr>    

            <tr>
                <td class='width-30-percent' >
                    Knjige pridruzene grupi
                </td>
                <td>
                <?php 
                 $sdmjk = $udzbenik_grupa->read_all_udzbenik_grupa($iid);
                $svi_udzbenici = array();
                while( $row_kategorij_udz = $sdmjk->fetch(PDO::FETCH_ASSOC)){
                    if($row_kategorij_udz != null){
                        $svi_udzbenici[] = $row_kategorij_udz['fk_udzbenik']; 

                    }
                   

                }
               // var_dump( $svi_udzbenici);
                 //var_dump($row_kategorij_usluga);
                
                ?>
                               
			    <select id="languages" name="udzbenik_grupa[]" class='form-control'  multiple  >						    
                <?php $stm_udzbenik = $udzbenik->read_all();
                        while ($row_category_udz = $stm_udzbenik->fetch(PDO::FETCH_ASSOC)){?>
                                <option value = "<?php echo $row_category_udz['id']; ?>" <?php if (in_array($row_category_udz['id'], $svi_udzbenici)){echo "selected";} ?>  > 
                                <?php echo $row_category_udz['ime']; ?> </option>
                        <?php } ?>       
                    
                   
			    </select>	
			
                </td>
            </tr>  
            <script>
                $(document).ready(function() {       
                    $('#languages').multiselect({		
                        nonSelectedText: 'Odaberi udbenike u grupi',
                        enableFiltering :true,
                        enableCaseInsensitiveFiltering: true
                        	
                    });
                });
            </script>              



            <tr>
                <td>Alias:</td>
                <td><input type='text'  name='alias' class='form-control' required value="<?php echo $alias; ?>"  /></td>
            </tr>

            <tr>
                <td>Komentar:</td>
                <td><textarea name='komentar' class='form-control' required> <?php echo $komentar;  ?></textarea></td>
            </tr>
            <tr>
                <td style="display:none;">Aktivan</td>
                <td style="display:none;"><input type="hidden" name="status" value="aktivan" <?php if($status==1){echo"checked";}  ?> > </td>
            </tr>

            <tr>
                <td></td>
                <td>
                    <button type="submit" class="btn btn-danger">
                        <span class="glyphicon glyphicon-record"></span> Snimi izmene
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
</body>
</html>
