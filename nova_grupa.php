<head>
    <link rel="shortcut icon" href="images/kalen.png">
</head>
<?php
// core configuration
include_once "config/core.php";

// check if logged in as admin
include_once "login_checker.php";

// include classes
include_once 'config/database.php';
include_once 'objects/user.php';
include_once 'objects/grupa.php';
include_once 'objects/uzrast.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// initialize objects
//$djak = new djak($db);
$profesor = new user($db);
$uzrast1 = new uzrast($db);
// set page title
$page_title = "Dodaj novu studentsku grupu:";

// include page header HTML
include_once "layout_head1.php";

echo "<div class='col-md-12'>";

if($_POST){

    // get database connection


    // initialize objects

    $grupa = new grupa($db);

    // create termin
    // set values to object properties

    $grupa->fk_jezik=$_POST['jezik'];
   //echo "jezik", $grupa->fk_jezik;
    $grupa->nivo=$_POST['nivo'];
    $grupa->fk_profesor=$_SESSION['user_id'];
    $grupa->alias=$_POST['alias'];
    $grupa->komentar=$_POST['komentar'];
    $grupa->velicina=$_POST['velicina'];
    $grupa->nacin=$_POST['nacin'];
    $grupa->uzrast=$_POST['uzrast'];


    $grupa->status = 1;


    /* $vreme_od=$sati_od.":".$minuti_od.":"."00";
                $odd= $datum_termina.' '.$vreme_od;  */

// create the user
    if($grupa->create() ){

        echo "<div class='alert alert-info'>";
        echo "Uspesno ste definisali novu grupu u aplikaciju. ";
        echo "</div>";
        // empty posted values
        $location = $home_url."read_grupa";
        $_SESSION["poruka_grupa_dodavanje"] =  "Uspešno ste dodali grupu: ".$_POST['alias'];
       // $_SESSION["dodat_grupa"] = $_POST['alias'];
        $_POST=array();
       header("Location: $location?message=success");
    //    echo'<script>window.parent.opener.location.reload();</script>';
    //    echo"<script>window.close();</script>";
    }else{
        echo "<div class='alert alert-danger' role='alert'>Doslo je do greške. Please try again.</div>";
    }

}

include_once 'objects/nivo_znanja.php';
include_once 'objects/jezik.php';
include_once 'objects/velicina.php';
$nivo_znanja = new nivo_znanja($db);
$velicina_grupe = new velicina($db);
$jezik = new jezik($db);


?>
<form action='nova_grupa.php' method='post' id='register'>

    <table class='table table-responsive'>



        <tr>
            <td class='width-30-percent'>Jezik:</td>
            <td>
                    <select class='form-control' name='jezik'  required>
                                <option value="" > Odaberi jezik... </option>
                        <?php $stm_jezik = $jezik->read_all();
                        while ($row_category_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC)){?>
                                <option value = "<?php echo $row_category_jezik['id']; ?>"  > 
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
                    <option value="" > Odaberi edukacijski nivo... </option>
                    <?php while ($row_category_nivo = $stm_nivo->fetch(PDO::FETCH_ASSOC)){?>
                        <option value = "<?php echo $row_category_nivo['id']; ?>"  > 
                        <?php echo $row_category_nivo['ime']; ?> </option>
                    <?php } ?>    
                    </select>
                

            </td>
        </tr>

        <tr>
            <td>Profesor:</td>
            <td>
                <?php
                // read the scool boards from the database
                $stmt_profesor = $profesor->read_one_profesor($_SESSION['user_id'],"users");
                $row_category_profesor =  $stmt_profesor->fetch(PDO::FETCH_ASSOC);
                echo "&nbsp;",$row_category_profesor['firstname'],"&nbsp;",$row_category_profesor['lastname'];

               
                ?>
            </td>
        </tr>
        <tr>
                <td>Vrsta grupe po veličini:</td>
                <td>
                    <select class='form-control' name='velicina' required>
                    <option value ="" >Odaberi vrstu grupe...</option>
                    <?php
                         $stmt_velicina = $velicina_grupe->read_all();
                         while ($row_category_velicina =  $stmt_velicina->fetch(PDO::FETCH_ASSOC)){ ?>
                            <option value = "<?php echo $row_category_velicina['id']; ?>" > <?php echo $row_category_velicina['ime'];  ?> </option>

                        <?php  } ?>
                        
                       
                    </select>
                </td>
            </tr>
            <tr>
                <td>Vrsta grupe po načinu slušanja nastave:</td>
                <td>
                    <select class='form-control' name='nacin' required>
                        <option value="">Odaberi vrstu grupe...</option>
                        <option value=1>U školi</option>
                        <option value=2>Online</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Uzrast:</td>
                <?php $stmt_uzrast = $uzrast1->read_all(); ?>
                <td>
                    <select class='form-control' name='uzrast' required>
                        <option value="">Odaberi starosnu grupu...</option>
                        <?php 
                        while ($row_category_uzrast =  $stmt_uzrast->fetch(PDO::FETCH_ASSOC)){ ?>
                            <option value = "<?php echo $row_category_uzrast['id']; ?>" > <?php echo $row_category_uzrast['ime'];  ?> </option>

                        <?php  } ?>
                    </select>
                </td>
            </tr>
        <tr>
            <td>Alias:</td>
            <td><input type='text' name='alias' class='form-control' maxlength="12" required /></td>
        </tr>
        <tr>
            <td>Komentar:</td>
            <td><textarea name='komentar' class='form-control' ></textarea></td>
        </tr>


        <tr class="blank_row">
            <td  colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td></td>
            <td>
                <button type="submit" class="btn btn-primary">
                    <span class="glyphicon glyphicon-plus"></span> Dodaj grupu
                </button>
            </td>
        </tr>

    </table>
</form>


<?php
echo "</div>";
// include page footer HTML
include_once "layout_foot.php";
?>