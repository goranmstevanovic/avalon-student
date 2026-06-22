
<?php
function formatSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' B';
    }
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "config/core.php";
include_once "login_checker.php";
include_once 'config/database.php';

include_once 'objects/jezik.php';
include_once 'objects/nivo_znanja.php';
include_once 'objects/uzrast.php';
// include_once "layout_head77.php";
$database = new Database();
$db = $database->getConnection();

$jezik = new jezik($db);
$nivo_znanja = new nivo_znanja($db);
$uzrast = new uzrast($db);

// FILTERI
$fk_jezik = isset($_GET['fk_jezik']) ? (int)$_GET['fk_jezik'] : 0;
$fk_nivo = isset($_GET['fk_nivo_znanja']) ? (int)$_GET['fk_nivo_znanja'] : 0;
$fk_uzrast = isset($_GET['fk_uzrast']) ? (int)$_GET['fk_uzrast'] : 0;
$moj_id = $_SESSION['user_id'];
// QUERY
$query = "
SELECT 
    d.id,
    d.naziv,
    d.created_at,
    d.datum_vazenja_do,
    d.velicina,
    d.uploadovao_id,
    COUNT(DISTINCT CASE 
        WHEN dp.tip_pristupa = 'grupa' THEN dp.fk_entitet 
    END) AS broj_grupa,

    COUNT(DISTINCT CASE 
        WHEN dp.tip_pristupa = 'djak' THEN dp.fk_entitet 
    END) AS broj_djaka

FROM dokumenti d

LEFT JOIN dokument_pristup dp 
    ON dp.fk_dokument = d.id

LEFT JOIN grupe g 
    ON (dp.tip_pristupa = 'grupa' AND g.id = dp.fk_entitet)

WHERE d.aktivan = 1 AND (d.vidljiv_profesorima = 1 OR d.uploadovao_id = $moj_id ) 
";

$params = [];

if ($fk_jezik > 0) {
    $query .= " AND g.fk_jezik = :fk_jezik";
    $params[':fk_jezik'] = $fk_jezik;
}

if ($fk_nivo > 0) {
    $query .= " AND g.nivo = :fk_nivo";
    $params[':fk_nivo'] = $fk_nivo;
}

if ($fk_uzrast > 0) {
    $query .= " AND g.uzrast = :fk_uzrast";
    $params[':fk_uzrast'] = $fk_uzrast;
}

$query .= "
GROUP BY d.id
ORDER BY d.created_at DESC
";

$stmt = $db->prepare($query);

foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}

$stmt->execute();

// GRUPISANJE PO MESECIMA
$dokumenti_po_mesecima = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $mesec = date('Y-m', strtotime($row['created_at']));
    $dokumenti_po_mesecima[$mesec][] = $row;
}
?>

<div class="container" style="margin-top:20px;">

<h3>Dokumenti</h3>

<!-- FILTERI -->
<!-- <form method="get" class="form-inline" style="margin-bottom:20px;"> -->
<form method="get" class="row g-2 align-items-center mb-3">
<div class="col-auto">    
<select name="fk_jezik" class="form-select">
<option value="0">Svi jezici</option>
<?php 
$stmt_j = $jezik->read_all();
while ($r = $stmt_j->fetch(PDO::FETCH_ASSOC)) { ?>
<option value="<?= $r['id'] ?>" <?= ($fk_jezik==$r['id']?'selected':'') ?>>
<?= htmlspecialchars($r['ime']) ?>
</option>
<?php } ?>
</select>
</div>
<div class="col-auto">
<select name="fk_nivo_znanja" class="form-select">
<option value="0">Svi nivoi</option>
<?php 
$stmt_n = $nivo_znanja->read_all();
while ($r = $stmt_n->fetch(PDO::FETCH_ASSOC)) { ?>
<option value="<?= $r['id'] ?>" <?= ($fk_nivo==$r['id']?'selected':'') ?>>
<?= htmlspecialchars($r['ime']) ?>
</option>
<?php } ?>
</select>
</div>
<div class="col-auto">
<select name="fk_uzrast" class="form-select">
<option value="0">Svi uzrasti</option>
<?php 
$stmt_u = $uzrast->read_all();
while ($r = $stmt_u->fetch(PDO::FETCH_ASSOC)) { ?>
<option value="<?= $r['id'] ?>" <?= ($fk_uzrast==$r['id']?'selected':'') ?>>
<?= htmlspecialchars($r['ime']) ?>
</option>
<?php } ?>
</select>
</div>
<div class="col-auto">
<button type="submit" class="btn btn-primary">Filtriraj</button>
</div>
</form>

<!-- LISTA -->
<?php if (empty($dokumenti_po_mesecima)) { ?>
<div class="alert alert-info">Nema dokumenata.</div>
<?php } ?>

<?php foreach ($dokumenti_po_mesecima as $mesec => $dokumenti) { ?>

<?php
$timestamp = strtotime($mesec . "-01");
$mesec_naziv = strftime('%B %Y', $timestamp);
?>

<div class="panel panel-default" >
    <div class="panel-heading">
        <strong><?= ucfirst($mesec_naziv) ?></strong>
    </div>

    <div class="panel-body">

        <table class="table table-bordered table-striped">

        <tr>
        <th>Naziv</th>
        <th>Datum</th>
        <th>Vazi do</th>
        <th>Velicina</th>
        <th>Grupe</th>
        <th>Đaci</th>
        <th>Akcija</th>
        </tr>

        <?php foreach ($dokumenti as $d) { ?>

        <tr>
        <td><?= htmlspecialchars($d['naziv']) ?></td>
        <td><?= date('d.m.Y', strtotime($d['created_at'])) ?></td>
        <td><?= date('d.m.Y', strtotime($d['datum_vazenja_do'])) ?></td>
        <td><?= formatSize((int)$d['velicina']) ?></td>
        <td><?= (int)$d['broj_grupa'] ?></td>
        <td><?= (int)$d['broj_djaka'] ?></td>

        <td>

        <a href="<?php echo $home_url; ?>admin/download_dokument.php?id=<?= $d['id'] ?>" class="btn btn-outline-success btn-xs"> <span class="glyphicon glyphicon-download"></span> Download</a>
        <?php if($d['uploadovao_id'] == $moj_id){ ?>
        <a href="<?php echo $home_url; ?>edit_dokument.php?id=<?= $d['id'] ?>" class="btn btn-outline-primary btn-xs" ><span class="glyphicon glyphicon-edit"></span> Edit</a>

        <button 
            class="btn btn-outline-danger btn-xs delete-dokument" 
            data-id="<?= $d['id'] ?>">
            Delete
        </button>
        <?php } ?>
        </td>

        </tr>

        <?php } ?>

        </table>

    </div>
</div>

<?php } ?>

</div>
</div>
