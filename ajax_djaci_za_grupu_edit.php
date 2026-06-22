<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "config/database.php";
$database = new Database();
$db = $database->getConnection();

$fk_grupa = isset($_POST['fk_grupa']) ? (int)$_POST['fk_grupa'] : 0;
//echo "grupa: ". $fk_grupa;
if ($fk_grupa <= 0) {
    echo "<div class='alert alert-danger'>Neispravna grupa.</div>";
    exit;
}

$query = "SELECT d.id, d.firstname, d.lastname
          FROM povezivanje p
          INNER JOIN djaci d ON d.id = p.fk_djak
          WHERE p.fk_grupa = :fk_grupa
          AND p.status = 1
          AND d.status = 1
          ORDER BY d.lastname, d.firstname";

$stmt = $db->prepare($query);
$stmt->bindParam(':fk_grupa', $fk_grupa);
$stmt->execute();
//var_dump($stmt);
if ($stmt->rowCount() == 0) {
    echo "<div class='alert alert-info'>Nema aktivnih đaka u ovoj grupi.</div>";
    exit;
}

echo "<div class='row'>";
//  echo "<div style='margin-bottom:8px;'>";
//     echo "<label><input type='checkbox' class='cekiraj-sve-djake' data-grupa='{$fk_grupa}'> Čekiraj sve đake</label>";
//     echo "</div>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $firstname = trim($row['lastname'] . " " . $row['firstname']);
   
    echo "<div class='col-md-12' style='margin-bottom:5px;'>";
 
    echo "<label style='font-weight:normal;'>";
    echo "<input type='checkbox' name='djaci[]' value='" . (int)$row['id'] . "' class='djak-check djak-grupa-" . $fk_grupa . "'> ";
    echo htmlspecialchars($firstname);
    echo "</label>";
    echo "</div>";
}

echo "</div>";