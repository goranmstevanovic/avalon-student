<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once "config/core.php";
include_once "config/database.php";
$moj_id = $_SESSION['user_id'];
$db = (new Database())->getConnection();

// =======================
// INPUT (GRUPE ZA IZBACIVANJE)
// =======================

$exclude = $_POST['exclude_grupe'] ?? [];

if (!is_array($exclude)) {
    $exclude = [];
}

// sigurnost → sve u int
$exclude = array_map('intval', $exclude);

// =======================
// QUERY
// =======================

$query = "
SELECT 
    g.id, 
    g.alias, 
    j.ime AS jezik_ime, 
    nz.ime AS nivo_ime, 
    u.ime AS uzrast_ime,
    us.color_prof AS color_prof
FROM grupe g
INNER JOIN jezik j ON j.id = g.fk_jezik
INNER JOIN nivo_znanja nz ON nz.id = g.nivo
INNER JOIN uzrasti u ON u.id = g.uzrast
JOIN users us ON us.id = g.fk_profesor
WHERE g.status = 1 AND g.fk_profesor = $moj_id
";

// =======================
// EXCLUDE LOGIKA (ISPRAVNO)
// =======================

if (!empty($exclude)) {

    // ?, ?, ?, ...
    $placeholders = implode(',', array_fill(0, count($exclude), '?'));

    $query .= " AND g.id NOT IN ($placeholders)";
}

// sortiranje
$query .= " ORDER BY j.ime, nz.ime, u.ime, g.alias";

// =======================
// PREPARE + BIND
// =======================

$stmt = $db->prepare($query);

// bind exclude parametara
$i = 1;
foreach ($exclude as $ex) {
    $stmt->bindValue($i++, $ex, PDO::PARAM_INT);
}

$stmt->execute();

// =======================
// PRAZAN REZULTAT
// =======================

if ($stmt->rowCount() === 0) {
    echo "<div class='alert alert-info'>Nema dostupnih grupa za dodavanje.</div>";
    exit;
}

// =======================
// HEADER (UX)
// =======================

echo "
<div style='margin-bottom:10px; font-weight:bold;'>
    Dostupne grupe za dodavanje:
</div>
";

// =======================
// SELECT ALL
// =======================

echo "
<div style='margin-bottom:10px;'>
    <label>
        <input type='checkbox' id='cekirajSveGrupe'> 
        Selektuj sve grupe
    </label>
</div>
";

// =======================
// LISTA GRUPA
// =======================

echo "<div class='row'>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $gid = (int)$row['id'];

    $naziv = $row['jezik_ime'] . " / " . $row['nivo_ime'] . " / " . $row['uzrast_ime'];
    $color_prof = $row['color_prof'];

    if (!empty($row['alias'])) {
        $naziv .= " / " . $row['alias'];
    }

    $boja = $color_prof ?? '#cccccc';

    echo "
    <div class='col-md-3' style='margin-bottom:15px;'>

        <div class='panel' style='
            border:1px solid #ddd;
            border-radius:8px;
            overflow:hidden;
            box-shadow:0 2px 6px rgba(0,0,0,0.05);
        '>

            <div class='panel-heading' style='
                background: {$boja}20;
                border-left:5px solid {$boja};
                display:flex; 
                justify-content:space-between; 
                align-items:center;
                border-top-left-radius:8px;
                border-top-right-radius:8px;
            '>

                <label style='font-weight:normal; margin:0;'>
                    <input type='checkbox' 
                        value='{$gid}' 
                        class='grupa-check'
                        data-grupa='{$gid}'
                    >
                    " . htmlspecialchars($naziv) . "
                </label>

                <button 
                    type='button' 
                    class='btn btn-xs btn-info toggle-djaci'
                    data-id='{$gid}'
                >
                    👥
                </button>

            </div>

            <div 
                class='panel-body djaci-wrapper' 
                id='djaci_{$gid}' 
                style='display:none; background:#fafafa;'
            ></div>

        </div>

    </div>
    ";
}

echo "</div>";
?>

<script>
// =======================
// SELECT ALL (delegacija!)
// =======================

$(document).on('change', '#cekirajSveGrupe', function(){

    let checked = $(this).is(':checked');

    $('.grupa-check').each(function(){

        if ($(this).is(':disabled')) return;

        $(this).prop('checked', checked).trigger('change');
    });

});
</script>