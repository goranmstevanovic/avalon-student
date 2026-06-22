<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<head>
<meta charset="UTF-8">
<title>Dnevnik rada grupe</title>
    <link rel="shortcut icon" href="images/kalen.png">
    <!-- make sure the src path points to your copied ckeditor folder -->
    <style>
        input[type=checkbox]
        {
        /* Double-sized Checkboxes */
        -ms-transform: scale(2); /* IE */
        -moz-transform: scale(2); /* FF */
        -webkit-transform: scale(2); /* Safari and Chrome */
        -o-transform: scale(2); /* Opera */
        transform: scale(2);
        padding-left: 10px;
        margin-right: 0;
        }
    </style>

</head>
<?php
include_once "config/core.php";
 include_once "login_checker.php";
include_once 'config/database.php';
include_once 'config/autoload.php';
//include_once "layout_head.php";

$database = new Database();
$db = $database->getConnection();

$termin = new kalendar($db);
$evidencija = new evidencija($db);
$djak = new djak($db);
$povezivanje = new povezivanje($db);
$grupa = new grupa($db);
$profesor = new user($db);
$program_rada_nastavna_jedinica = new program_rada_nastavna_jedinica($db);

$idd = $_GET['grupa'] ?? die("Nedostaje ID grupe");

// 1. Učitaj podatke o grupi
$stmt = $grupa->read_one($idd);
$row_grupa = $stmt->fetch(PDO::FETCH_ASSOC);

//$row_category_grupa = $stmt->fetch(PDO::FETCH_ASSOC);



$fk_jezik = $row_grupa['ime_jezika'];
$nivo = $row_grupa['ime_nivoa'];

$broj_casova = $termin->count_broj_casova_grupa_vreme1($idd);
// var_dump($row_category_grupa);

?>

<table class='table table-hover table-responsive table-bordered'>
        <tr><th style= "color:red;">Dnevnik rada studentske grupe:</th><th>Jezik: <?=$fk_jezik ?></th><th>Nivo: <?= $nivo ?></th><th>Grupa: <?=$row_grupa['alias'] ?></th>
        <th>Održanih časova : <?=$broj_casova ?></th>
        <th>
        <button style="display : inline-block" class="btn btn-danger  pull-right" ><a style="color:white;" href = "kartica_grupa?id=<?php echo $idd; ?> " target='_blank'>Finasijska kartica grupe</a></button>
        </th>
        </tr>
</table>
<?php

if($row_grupa['fk_program_rada'] == null ){
    echo "<b style='color:red;'>Grupa nema pridružen program rada</b>";
}else{
    echo "<b>Grupa ima pridružen program rada : ".$row_grupa['ime_programa'] ."</b>" ;
   // $program_rada_nastavna_jedinica = new program_rada($db);
    $stmr_sve_jedinice = $program_rada_nastavna_jedinica->read_all_for_one($row_grupa['fk_program_rada']);

   $sve_jedinice_programa_rada = $stmr_sve_jedinice->fetchall(PDO::FETCH_ASSOC);
      
   $casovi = [];
   //$brojac_casova = 0;
    foreach($sve_jedinice_programa_rada as $jedan_cas){
         $casovi[$jedan_cas['redni_br']] = $jedan_cas['nastavna_jedinica'];
    }
    // $bbb = 0;
    // foreach($casovi as $cas){
    //     $bbb++;
    //    // echo $casovi[$bbb]['nastavna_jedinica']."<br>";
    // }
    // echo "<pre>";
    // var_dump($casovi);
    //  echo "</pre>";


   //echo "<pre>",var_dump($sve_jedinice_programa_rada),"</pre>";

}



// 2. Učitaj sve termine
$termini = [];
$termin_ids = [];

$tstmt = $termin->read_all_grupa_vremenski_interval($idd, date('Y-m-d H:i:s'));
foreach ($tstmt->fetchAll(PDO::FETCH_ASSOC) as $t) {
    $termini[$t['id']] = $t;
    $termin_ids[] = $t['id'];
}

// 3. Učitaj sve učenike u grupi
$djaci = [];
$djaci_ids = [];

$stmt_djaci = $povezivanje->read_all_students($idd);
foreach ($stmt_djaci->fetchAll(PDO::FETCH_ASSOC) as $d) {
    $d_id = $d['fk_djak'];
    $djaci_ids[] = $d_id;

    $d_stmt = $djak->read_one_djak('djaci', $d_id);
    $djaci[$d_id] = $d_stmt->fetch(PDO::FETCH_ASSOC);
}

// 4. Učitaj sva prisustva za termine
$prisustva = [];

if (!empty($termin_ids)) {
    $pstmt = $evidencija->read_prisustva_po_terminima($termin_ids);
    foreach ($pstmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $prisustva[$row['termin_id']][$row['djak_id']] = $row;
    }
}
?>
<!-- // 5. Prikaz -->

<table class='table table-bordered' style='width: 100%;'>
<tr><th>Datum i vreme</th><th>Status</th><th>Profesor</th><th>Prisustvo</th><th style='width: 30%;'>Opis časa</th><?php if($row_grupa['fk_program_rada'] !== null){ ?> <th style='min-width: 10em; '>Program</th> <?php } ?></tr>
<?php
$brojac_casova = 0;
foreach ($termini as $termin_id => $t) {
    $datum = date('d.m.Y', strtotime($t['start']))." <br> ".date('H:i', strtotime($t['start']))." - ". date('H:i', strtotime($t['end'])) ;
    $status = ($t['status'] == 2) ? "Održan" : (($t['status'] == 1) ? "U kalendaru" : "Otkazan");
    if($t['status'] == 2){
        $brojac_casova++;
    }
    if($status == "Otkazan" ){
        $backcolor = '#f5f1ef';
    }else{
        $backcolor = '#ffffff';
    }
    // Učitaj profesora
    $fk_prof = $t['fk_profesor'] ?? $row_grupa['fk_profesor'];
    $prof = $profesor->read_one($fk_prof)->fetch(PDO::FETCH_ASSOC);

    if($t['fk_profesor'] !== null AND $t['fk_profesor'] !== 0 AND $t['fk_profesor'] !== $row_grupa['fk_profesor'] ){
        $zamena = "zamena";
    }else{
        $zamena = "";
    }
    $profesor_prezime = $prof['lastname'] ?? "" ;
    //var_dump($prof);
   // $prof_ime = $prof['firstname'] ?? '' . " " . $profesor_prezime . ' <br> <tab style="color:red;">' . $zamena . '</tab>';
    $prof_ime = ($prof['firstname'] ?? '') . " " . $profesor_prezime . ' <br> <tab style="color:red;">' . $zamena . '</tab>';

    // Ako je komentar prazan, ubaci &nbsp; da ćelija ima sadržaj
   
    echo "<tr style='background-color: {$backcolor}'><td>";
    ?>
     <a onclick="open_in_new_tab_and_reload('dogadjaj.php?id=<?=$t['id'] ?>')" href="#">
    <?php
    echo $datum;
      if($t['status'] == 2){
        echo "<br><b> Redni broj: ".$brojac_casova."<b>";
    }
    ?>
     </a>
    <script>
                function open_in_new_tab_and_reload(url)
                {
                //Open in new tab
                window.open(url, '_blank');
                //focus to thet window
                window.focus();
                //reload current page
                location.reload();
                }
                </script>
    <?php
    echo "</td>";
    echo "<td>{$status}</td>";
    echo "<td>{$prof_ime}</td>";

    // Prikaz prisustva đaka
    echo "<td style='width: 30%;'><table class='table table-bordered'>";
    foreach ($djaci as $djak_id => $d) {
        $ime_djaka = $d['firstname'] . ' ' . $d['lastname'];
        $prisutno = $prisustva[$termin_id][$djak_id]['prisutan'] ?? null;
        $komentar = $prisustva[$termin_id][$djak_id]['komentar'] ?? '';
         if ($komentar === '') {
            $komentar = '&nbsp; &nbsp; &nbsp;';
        }
        echo "<tr>";
        echo "<td style='min-width: 10%; '><input type='checkbox' onclick='return false;' " . ($prisutno ? "checked" : "") . "></td>";
        echo "<td style='min-width: 45%; ' >{$ime_djaka}</td>";
        echo "<td style='width: 45%;  '>{$komentar}</td>";
        echo "</tr>";
    }
    echo "</table></td>";

    echo "<td>" . ($t['komentar'] ?? "") . "</td>";
   if ($row_grupa['fk_program_rada'] !== null) {
        
        echo "<td style='background-color: #F1F1F0 '>";
         if($t['status'] == 2){
            echo $casovi[$brojac_casova];
         }
        echo "</td>";
    }
    echo "</tr>";
}

echo "</table>";
include_once "layout_foot.php";
?>
