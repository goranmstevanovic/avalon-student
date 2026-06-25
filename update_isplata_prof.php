<head>
<link rel="shortcut icon" href="images/kalen.png">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
</head>
<?php
if(isset($_GET['id'])){
  $id = $_GET['id']; 
  $id_profesor = $_GET['id']; $fk_prof = $id;
 // $pomocni = explode('?')
} 

ob_start();
// core configuration
include_once "config/core.php";
$id = $_SESSION['user_id']; 
  $id_profesor =$id; 
  $fk_prof = $id;

// check if logged in as admin
include_once "login_checker.php";

// include classes
include_once 'config/database.php';
include_once 'config/autoload.php';




// get database connection
$database = new Database();
$db = $database->getConnection();

// initialize objects
$user = new user($db);
$profesor = new user($db);
$isplata = new isplata($db);
$velicina1 = new velicina($db);
$nivo_znanja = new nivo_znanja($db);
$duzina_casa = new duzina_casa($db);
$platni_razred = new platni_razred($db);
$isplate_profesoru = new isplate_profesoru($db);
$stmt = $profesor->read_one_profesor($id,"users");
$row_category_profesor = $stmt->fetch(PDO::FETCH_ASSOC);

// set page title
$page_title = "Tabela isplata profesora:";

// include page header HTML
include_once "layout_head2.php";
if (isset($_SESSION["poruka_isplate_profesoru"]))
{
    echo "<div class='alert alert-info'>";
    echo $_SESSION["poruka_isplate_profesoru"];
    unset($_SESSION["poruka_isplate_profesoru"]);
  //  unset($_SESSION["dodati_jezik"]);
    echo "</div>";
}
if($_POST){
  //  echo "<pre>"; 
  //       var_dump($_POST['vrednost']);
  //   echo "</pre>";
    foreach($_POST['vrednost'] as $key1 => $pojedini_platni_razred){

        $fk_platni_razred =  $key1;
        foreach($pojedini_platni_razred as $key2 => $pojedina_velicina_grupe){
            $fk_velicina_grupe = $key2;
            foreach($pojedina_velicina_grupe AS $key3 => $pojedina_duzina_casa){
              $fk_duzina_casa = $key3;
           // echo $pojedina_duzina_casa,"<br>";
              $provera_da_li_postoji_zapis = $isplate_profesoru->provera_postojanja($id_profesor,$fk_velicina_grupe, $fk_platni_razred ,$fk_duzina_casa );
            //  var_dump($provera_da_li_postoji_zapis);
             // echo "Provera dal postoji zapis: ",$provera_da_li_postoji_zapis;
              
              $isplate_profesoru->fk_platni_razred = $fk_platni_razred;
              $isplate_profesoru->fk_velicina_grupe = $fk_velicina_grupe;
              $isplate_profesoru->fk_duzina_casa = $fk_duzina_casa;
              $isplate_profesoru->active = 1;
              $isplate_profesoru->iznos = $pojedina_duzina_casa;
              $isplate_profesoru->fk_profesor = $id_profesor;
              
              $row_provere_prof_tabele =  $provera_da_li_postoji_zapis->fetch(PDO::FETCH_ASSOC);
              
              if($row_provere_prof_tabele == false){
                 // ako nema dosadupisanih promen za prpfesora

    
                $stmt_provera_opsta = $isplata->provera_postojanja_vrednost($fk_velicina_grupe, $fk_platni_razred ,$fk_duzina_casa );
                $row_opste_tabele =  $stmt_provera_opsta->fetch(PDO::FETCH_ASSOC);
                $iznos_opste_tabele = $row_opste_tabele['iznos'] ?? 0;
                if($iznos_opste_tabele != $isplate_profesoru->iznos ){
                  if($isplate_profesoru->create()){
                    echo "<div class='alert alert-info'>";
                    echo "Uspesno ste promenili vrednost ", $iznos_opste_tabele , " u: ", $isplate_profesoru->iznos  ;
                    echo "</div>";
                  //  echo "Uspesno kreiran novi zapis";
                    //var_dump($isplata);
                   // echo "<hr>";
                  }else{
                    echo "JBG greska"; 
                  }

                }
                
              }
              else{
                if($row_provere_prof_tabele['iznos'] != $isplate_profesoru->iznos ){

                  if($isplate_profesoru->create()){
                    echo "<div class='alert alert-info'>";
                    echo "Uspesno ste promenili vrednost ",$row_provere_prof_tabele['iznos'] , " na: ", $isplate_profesoru->iznos  ;
                    echo "</div>";
                  //  echo "Uspesno kreiran novi zapis";
                    //var_dump($isplata);
                   // echo "<hr>";
                  }else{
                    echo "JBG greska"; 
                  }


                }

             

              }
              
            }
        }
    
     
      // echo"<pre>";
      //   var_dump($pojedini_platni_razred);
      // echo"</pre>";


    }
    $location = $home_url."admin/isplate";
        $_SESSION["poruka_tabela"] =  "Uspešno ste upisali izmene u tabeli isplata profesora: ";
       // $_SESSION["dodati_jezik"] = $jezik->ime;
        $_POST=array();
     //   header("Location: $location");


}


echo "<div class='col-md-12' style = 'width:100%; margin-left: 0%; margin-right: 0%;' id='glavni' >";

// read all users from the database
//$stmt = $user->read_All_profesor();
echo "<div class='col-md-12' style='margin-bottom:10px;'  id='glavni1'  >";
echo"<h3><u>Tabela isplata za profesora: ",$row_category_profesor['firstname'],"&nbsp;",
$row_category_profesor['lastname']," </u></h3>";
echo "</div>";
// count retrieved users
$num = $stmt->rowCount();
?>


<?php 
$stm_velicina = $velicina1->read_all();
//  $row_velicina = $stm_velicina->fetch(PDO::FETCH_ASSOC);
?>
<table class='table table-hover table-responsive table-bordered' >
<tr><th rowspan='2' ></th>
<?php
$stmt_duzina_casa = $duzina_casa->read_all();
$broj_duzina =  $stmt_duzina_casa->rowcount($stmt_duzina_casa) ?? 1;
//  $stm_velicina1 = $velicina1->read_all();
//  $broj_velicina =  $stm_velicina1->rowcount($stm_velicina1) ?? 1;
while ($row_velicina = $stm_velicina->fetch(PDO::FETCH_ASSOC)){ ?>
    <th colspan='<?=$broj_duzina ?>' style='text-align:center'>
    <?php echo $row_velicina['ime']," - ",$row_velicina['opis'];?>
    </th>
  <?php
}
?>
</tr>
<tr>
<?php
$stm_velicina1 = $velicina1->read_all();
// $broj_velicina =  $stm_velicina1->rowcount($stmt_duzina_casa);
while ($row_velicina1 = $stm_velicina1->fetch(PDO::FETCH_ASSOC)){
$stmt_duzina_casa = $duzina_casa->read_all();
$broj_duzina =  $stmt_duzina_casa->rowcount($stm_velicina1) ?? 1;
//echo $sirina_jedne_kolone = intdiv(100, $broj_duzina*$broj_velicina);
while($row_duzina_casa = $stmt_duzina_casa->fetch(PDO::FETCH_ASSOC)){
echo "<th style='width:90px'>{$row_duzina_casa['duzina']} &nbsp; min. </th> ";
}      
}
echo"</tr>";


//$stmt_platni_razred = $nivo_znanja->read_one_platni_razred($fk_platni_razred,'platni_razredi');
$stmt_platni_razred = $platni_razred->read_all();
while ($row_platni_razred = $stmt_platni_razred->fetch(PDO::FETCH_ASSOC)){ ?>
    
    <tr>
      <td>
        <?= $row_platni_razred['ime'] ?> 
      </td>
      <?php  
        $stm_velicina1 = $velicina1->read_all();
        while ($row_velicina1 = $stm_velicina1->fetch(PDO::FETCH_ASSOC)){ 
          $stmt_duzina_casa1 = $duzina_casa->read_all();
          while($row_duzina_casa1 = $stmt_duzina_casa1->fetch(PDO::FETCH_ASSOC)){
              ?>
            <td style='text-align:center'>
                <?php 
                $stmt_postoji = $isplate_profesoru->read_one_last($fk_prof, $row_velicina1['id'], $row_platni_razred['id'],$row_duzina_casa1['id']  );
                //var_dump($stmt_postoji);
                $row_postoji = $stmt_postoji->fetch(PDO::FETCH_ASSOC); 

               // var_dump( $row_postoji);
                //echo "<hr>",$row_duzina_casa1['id'];
                if($row_postoji == false){
                  $stmt_jedna = $isplata->read_po_velicini_platni_razred_velicina_trajanje($row_velicina1['id'], $row_platni_razred['id'],$row_duzina_casa1['id']);
                  // var_dump($stmt_jedna);
                  // echo "<hr>";
                  $platni_razred = $row_platni_razred['id'];
                  $velicina2 = $row_velicina1['id'];
                  $duzina_casa1 = $row_duzina_casa1['id'];
                  $row_isplata_jedan = $stmt_jedna->fetch(PDO::FETCH_ASSOC);
                  $iznos_za_prikaz = $row_isplata_jedan['iznos'] ?? 0;
                  //var_dump( $row_isplata_jedan);
                }else{
                  $platni_razred = $row_postoji['fk_platni_razred'];
                  $velicina2 = $row_postoji['fk_velicina_grupe'];
                  $duzina_casa1 = $row_postoji['fk_duzina_casa'];
                  $iznos_za_prikaz = $row_postoji['iznos']; 
                }
                echo $iznos_za_prikaz; 
                }
              ?>   
            </td>
        
        <?php
      }
        ?>
     </tr>
    <?php
}
?>

<tr>
<td colspan='10' style="border:none">

                    <!-- <button type="submit" class="btn btn-danger pull-right">
                        <span class="glyphicon glyphicon-plus"></span> Snimi vrednosti iz tabele
                    </button> -->


<a class="btn btn-primary" style='display:none;' href='isplate'><span class="glyphicon glyphicon-step-backward"></span> &nbsp;vrati se nazad</a>       
</td>
</tr>


</table>

</div>
<div id='glavni2' class='col-md-12' style="margin-top: 20px; width: 90%; margin-left:5%; display:none; " >
<?php
$stmt_isplata_za_profeseora = $isplate_profesoru->read_all_izmene_jedan_profesor($id_profesor);
$broj_vrsta = $stmt_isplata_za_profeseora->rowCount();
if($broj_vrsta > 0){
?>
<br/>
<h3 style='margin-left:5%; margin-top:10px;'>Sve izmene kriterijuma isplate do sada:</h3>
<table class='table  table-bordered text-center' style='width: 90%; margin-left:5%;'>
  <tr class="text-center" ><th>platni razred</th><th>Velicina grupe</th><th> Duzina casa</th><th>Iznos din. </th><th>Datum izmene</th><th>Akcija</th><th>Obriši</th>
</tr>
  <?php while ($row_isplata_za_profesora = $stmt_isplata_za_profeseora->fetch(PDO::FETCH_ASSOC)){  ?>
  <tr class="text-center">
    <td>
      <?=$row_isplata_za_profesora['ime_platnog_razreda'] ?>
     
      
    </td>
    <td>
      <?=$row_isplata_za_profesora['ime_velicine'] ?>
     
    </td>
    <td>
    <?=$row_isplata_za_profesora['duz_casa'] ?>
      
    </td>
    <td>
    <?=$row_isplata_za_profesora['iznos'] ?>
    </td>
    
    <td>
      <?php 
        $created = $row_isplata_za_profesora['created'];
        echo date("d.m.Y H:m", strtotime($created));
      ?>
    </td>
    <td>
    <a class='btn btn-primary btn-sm' style='color:white;' href="update_tabela_isplata?id=<?=$row_isplata_za_profesora['id'] ?> "  > 
    <span class="glyphicon glyphicon-edit"></span> Izmeni</a>
    </td>
    
    <td >
        <a class="remove" role="button" data-rowid="<?php echo $row_isplata_za_profesora['id']; ?>"> <span class="glyphicon glyphicon-trash"></span></a>
        
        
    </td>
    
    
	
		
    

  </tr>
  <?php } ?>      
</table>
<?php } ?>

</div>
<script type="text/javascript">
    $(".remove").click(function(){
        var idd = $(this).data("rowid");
        var row = $(this).closest("tr");
        /*   alert("Zdravo id" + id + "! Kako si danas?"); */
         //  alert("Zdravo idd: " + idd + "! Kako si danas?"); 
        if (typeof idd !== 'undefined')
        {
            if (confirm('Jeste li sigurni da želite da obrišete ovaj kriterijum isplate ?')) {
                $.ajax({
                    type: "POST",
                    url: "delete_izplata_profesor.php",
                    data: {
                        operation: "remove",
                        idd: idd
                    },
                    error: function () {
                        alert('Nešto nije u redu sa brisanjem, kontaktiraj Goran-a goranmstevanovic@gmail.com');
                    },
                    success: function (data) {
                        row.remove();
                        /* $("#" + ddid).remove(); */
                        console.log(data);
                        alert("Uspešno deaktivirana izmena u tabeli isplata");
                        $("#glavni").load(location.href + " #glavni");
						           // location.reload();

                    }
                });
            }
        }
    });
 </script>   
<?php
// include page footer HTML
include_once "layout_foot.php";
?>

