<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once "config/core.php";
include_once "config/database.php";
$database = new Database();
$db = $database->getConnection();

$moj_id = $_SESSION['user_id'];
$fk_jezik = isset($_POST['fk_jezik']) ? (int)$_POST['fk_jezik'] : 0;
$fk_nivo_znanja = isset($_POST['fk_nivo_znanja']) ? (int)$_POST['fk_nivo_znanja'] : 0;
$fk_uzrast = isset($_POST['fk_uzrast']) ? (int)$_POST['fk_uzrast'] : 0;

$query = "SELECT g.id, g.alias, j.ime AS jezik_ime, nz.ime AS nivo_ime, u.ime AS uzrast_ime
          FROM grupe g
          INNER JOIN jezik j ON j.id = g.fk_jezik
          INNER JOIN nivo_znanja nz ON nz.id = g.nivo
          INNER JOIN uzrasti u ON u.id = g.uzrast
          WHERE g.status = 1 AND g.fk_profesor = $moj_id ";

$params = array();

if ($fk_jezik > 0) {
    $query .= " AND g.fk_jezik = :fk_jezik";
    $params[':fk_jezik'] = $fk_jezik;
}

if ($fk_nivo_znanja > 0) {
    $query .= " AND g.nivo = :fk_nivo_znanja";
    $params[':fk_nivo_znanja'] = $fk_nivo_znanja;
}

if ($fk_uzrast > 0) {
    $query .= " AND g.uzrast = :fk_uzrast";
    $params[':fk_uzrast'] = $fk_uzrast;
}

$query .= " ORDER BY j.ime, nz.ime, u.ime, g.alias";

$stmt = $db->prepare($query);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->execute();

if ($stmt->rowCount() == 0) {
    echo "<div class='alert alert-info'>Nema grupa za odabrane filtere.</div>";
    exit;
}
echo"<input type='checkbox' id='cekirajSveGrupe'>". " Selektuj sve grupe"  ;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $naziv_grupe = $row['jezik_ime'] . " / " . $row['nivo_ime'] . " / " . $row['uzrast_ime'];
    if (!empty($row['alias'])) {
        $naziv_grupe .= " / " . $row['alias'];
    }

    echo "<div class='panel panel-default grupa-box' style='margin-bottom:10px;'>";
    echo "<div class='panel-heading'>";
    echo "<label style='font-weight:normal;'>";

    echo "<input type='checkbox' name='grupe[]' value='" . (int)$row['id'] . "' class='grupa-check' data-grupa='" . (int)$row['id'] . "'> ";
    echo htmlspecialchars($naziv_grupe);
    echo "</label> ";
    echo "<div style='text-align:right;'>
            <button type='button' class='btn btn-xs btn-outline-primary prikazi-djake'
                data-grupa='" . (int)$row['id'] . "'>
                Prikaži đake
            </button>
        </div>";
    echo "</div>";
    echo "<div class='panel-body djaci-wrapper' id='djaci_" . (int)$row['id'] . "' style='display:none;'></div>";
    echo "</div>";
}