<html>
    <head>
        <!-- <title>edit materijala</title>
        <link rel="icon" href="images/kalen.png" type="image/png"/> -->
        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    </head>
<body>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "config/core.php";
include_once "login_checker.php";
include_once 'config/database.php';
include_once "layout_head77.php";

$db = (new Database())->getConnection();

$dokument_id = (int)($_GET['id'] ?? 0);
if ($dokument_id <= 0) die("Neispravan ID");

// ADMIN ONLY
if ($_SESSION['access_level'] !== 'Customer') {
    die("Nemate pravo pristupa.");
}

// dokument
$stmt = $db->prepare("SELECT * FROM dokumenti WHERE id=?");
$stmt->execute([$dokument_id]);
$dokument = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$dokument) die("Ne postoji");

// trenutno dodeljene grupe i djaci
$stmt = $db->prepare("
SELECT dp.*, 
       g.alias,
       d.firstname AS firstname , d.lastname AS lastname,
       j.ime AS jezik, nz.ime AS nivo, us.color_prof AS color_prof
FROM dokument_pristup dp
LEFT JOIN grupe g ON (dp.tip_pristupa='grupa' AND g.id=dp.fk_entitet)
LEFT JOIN djaci d ON (dp.tip_pristupa='djak' AND d.id=dp.fk_entitet)
LEFT JOIN jezik j ON j.id = g.fk_jezik
LEFT JOIN nivo_znanja nz ON nz.id = g.nivo
LEFT JOIN uzrasti u ON u.id = g.uzrast
LEFT JOIN users us ON us.id = g.fk_profesor
WHERE dp.fk_dokument = ?
");
$stmt->execute([$dokument_id]);

$selected_grupe = [];
$selected_djaci = [];
$selected = [];

while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {

    if ($r['tip_pristupa'] === 'grupa') {

        $gid = (int)$r['fk_entitet'];

        $selected[$gid] = [
            'naziv' => $r['jezik'] . ' / ' . $r['nivo'] . ' / ' . $r['alias'],
            'boja' => $r['color_prof'] ?? '#999999',
            'djaci' => [],
            'svi_djaci' => []
        ];
    }

    if ($r['tip_pristupa'] === 'djak') {

        // ❗ MORAMO NAĆI GRUPU ĐAKA
        $stmt2 = $db->prepare("SELECT fk_grupa FROM povezivanje WHERE fk_djak=? AND status=1 LIMIT 1");
        $stmt2->execute([$r['fk_entitet']]);
        $gid = $stmt2->fetchColumn();

        if(!$gid) continue;

        if (!isset($selected[$gid])) {
            $selected[$gid] = [
                'naziv' => 'Nepoznata grupa',
                'djaci' => [],
                'svi_djaci' => []
            ];
        }

        $selected[$gid]['djaci'][] = [
            'id' => (int)$r['fk_entitet'],
            'ime' => $r['firstname'],
            'prezime' => $r['lastname']
        ];
    }
}


foreach ($selected as $gid => &$grupa) {

    $stmt3 = $db->prepare("
        SELECT d.id, d.firstname, d.lastname
        FROM povezivanje p
        JOIN djaci d ON d.id = p.fk_djak
        WHERE p.fk_grupa = ? AND p.status = 1
    ");
    $stmt3->execute([$gid]);

    $grupa['svi_djaci'] = $stmt3->fetchAll(PDO::FETCH_ASSOC);
}
foreach ($selected as $gid => &$grupa) {

    $selectedIds = array_map(function($d){
        return $d['id'];
    }, $grupa['djaci']);

    foreach ($grupa['svi_djaci'] as &$d) {
        $d['checked'] = in_array($d['id'], $selectedIds);
    }
}
?>

<div class="container" style="width:100% !important; margin:auto; margin-top:19px; ">

<h3>Edit dokumenta</h3>

<!-- ===================== -->
<!-- TRENUTNO DODELJENO   -->
<!-- ===================== -->

<h4>Trenutni pristup</h4>
<div class="row" id="trenutneGrupe"></div>
<div id="trenutniDjaci"></div>

<hr>

<form id="editForma">

<input type="hidden" name="id" value="<?= $dokument_id ?>">

<div class="form-group">
<label>Naziv</label>
<input type="text" name="naziv" class="form-control"
value="<?= htmlspecialchars($dokument['naziv']) ?>">
</div>

<div class="form-group">
<label>Opis</label>
<textarea name="opis" class="form-control"><?= htmlspecialchars($dokument['opis']) ?></textarea>
</div>

<div class="form-group">
    <label>Važi do:</label>
    <input type="datetime-local" 
           name="datum_vazenja_do" 
           id="datum_vazenja_do"
           class="form-control"
           value="<?= !empty($dokument['datum_vazenja_do']) 
                ? date('Y-m-d\TH:i', strtotime($dokument['datum_vazenja_do'])) 
                : '' ?>">
    <small>Maksimalno 2 meseca unapred</small>
</div>

<!-- ===================== -->
<!-- DUGME ZA UCITAVANJE  -->
<!-- ===================== -->

<button type="button" id="ucitajGrupe" class="btn btn-info">
    Prikaži grupe za dodavanje
</button>

<div id="grupeWrapper" style="display:none; margin-top:15px;"></div>

<br>

<label>
<input type="checkbox" name="vidljiv_profesorima" value="1"
<?= ($dokument['vidljiv_profesorima'] ? 'checked' : '') ?>>
Profesori vide
</label>

<br><br>

<button type="submit" class="btn btn-primary">Sačuvaj</button>

</form>

<div id="poruka"></div>

</div>

<script>
// =======================
// GLOBAL STATE
// =======================

// već dodeljeno (ne diramo)
let existingGrupe = <?= json_encode($selected_grupe) ?>;
let existingDjaci = <?= json_encode($selected_djaci) ?>;
let existing = <?= json_encode($selected) ?>;

// NOVO što korisnik bira
let selectedGrupe = [];
let selectedDjaci = [];

// =======================
// RENDER TRENUTNOG STANJA
// =======================

function renderSelected(){

    let html = '';

    Object.keys(existing).forEach(gid => {

        let g = existing[gid];

        html += `
        <div class="col-md-3" style="margin-bottom:15px;">

            <div class="panel" style="
                border:1px solid #ddd;
                border-radius:8px;
                overflow:hidden;
                box-shadow:0 2px 6px rgba(0,0,0,0.05);
            ">

                
                <div class="panel-heading" style="
                        background: rgba(0,0,0,0.03);
                        border-left:5px solid ${g.boja};
                        display:flex;
                        justify-content:space-between;
                        align-items:center;
                    ">
                    <span>${g.naziv}</span>
                    <button class="btn btn-xs btn-danger remove-grupa" data-id="${gid}">✖</button>
                </div>

                <div class="panel-body">

                    <button class="btn btn-xs btn-info toggle-djaci1" data-id="${gid}">
                        Prikaži đake
                    </button>

                    <div id="djaci_${gid}" style="display:none; margin-top:10px;">
        `;

     

        g.svi_djaci.forEach(d => {

            html += `
            <div style="padding:5px 0;">
                <label>
                    <input type="checkbox" class="djak-existing" 
                        data-grupa="${gid}" 
                        value="${d.id}" 
                        ${d.checked ? 'checked' : ''}>
                    ${d.firstname} ${d.lastname}
                </label>
            </div>
            `;
        });

        html += `
            </div>
        `;

        html += `
                    </div>
                </div>
            </div>

        </div>
        `;
    });

    $('#trenutneGrupe').html(html);
}


$(document).on('click', '.toggle-djaci1', function(){

    let gid = parseInt($(this).data('id')); // 👈 OVO DODAJ
    let box = $('#djaci_' + gid);

    console.log("klik", gid, box.length); // DEBUG

    if(box.length === 0){
        console.log("NE POSTOJI ELEMENT");
        return;
    }

    if (box.is(':visible')) {
        box.stop(true,true).slideUp();
    } else {
        box.stop(true,true).slideDown();
    }

});

$(document).on('click', '.remove-grupa', function(){

    let gid = $(this).data('id');

    delete existing[gid];

    renderSelected();
});

$(document).on('change', '.djak-existing', function(){

    let gid = $(this).data('grupa');
    let id = parseInt($(this).val());

    if($(this).is(':checked')){

        if(!existing[gid].djaci.some(d => d.id === id)){
            existing[gid].djaci.push({id:id});
        }

    } else {

        existing[gid].djaci = existing[gid].djaci.filter(d => d.id !== id);
    }
});

// =======================
// UCITAJ GRUPE (EDIT VERZIJA)
// =======================

$('#ucitajGrupe').click(function(){

    let wrapper = $('#grupeWrapper');

    if(wrapper.is(':visible')){
        wrapper.slideUp();
        return;
    }

    wrapper.html('Učitavam...').slideDown();

    // BITNO: koristi EDIT verziju
    let existingIds = Object.keys(existing);

    $.post('ajax_grupe_za_dokument_edit.php', {exclude_grupe: existingIds}, function(html){

        wrapper.html(html);

        // onemogući već postojeće
        $('.grupa-check').each(function(){

            let gid = parseInt($(this).val());

            let existingIds = Object.keys(existing).map(id => parseInt(id));

            if(existingIds.includes(gid)){

                $(this).prop('disabled', true);

                $(this).closest('label').css({
                    opacity: 0.5,
                    textDecoration: 'line-through'
                });
            }
        });
    });
});

// =======================
// OPEN / CLOSE DJACI
// =======================

$(document).on('click', '.toggle-djaci', function(){

    let gid = $(this).data('id');
    let container = $('#djaci_' + gid);

    // toggle zatvaranje
    if(container.is(':visible')){
        container.slideUp();
        return;
    }

    // ako već učitano → samo otvori
    if(container.data('loaded')){
        container.slideDown();
        return;
    }

    // prvi load
    container.html('Učitavam...').slideDown();

    $.post('ajax_djaci_za_grupu_edit.php', { fk_grupa: gid }, function(html){

        container.html(html);
        container.data('loaded', true);

    });

});
// =======================
// CHECK GRUPA
// =======================

$(document).on('change', '.grupa-check', function(){

    let gid = parseInt($(this).val());

    if($(this).is(':checked')){

        if(!selectedGrupe.includes(gid)){
            selectedGrupe.push(gid);
        }

        let container = $('#djaci_' + gid);

        // ako već učitano → samo čekiraj
        if(container.data('loaded')){

            container.find('.djak-check').each(function(){

                let id = parseInt($(this).val());

                $(this).prop('checked', true);

                if(!selectedDjaci.includes(id)){
                    selectedDjaci.push(id);
                }
            });

            container.slideDown();
            return;
        }

        // prvi load
        container.html('Učitavam...').slideDown();

        $.post('ajax_djaci_za_grupu_edit.php', { fk_grupa: gid }, function(html){

            container.html(html);
            container.data('loaded', true);

            // čekiraj sve đake
            container.find('.djak-check').each(function(){

                let id = parseInt($(this).val());

                $(this).prop('checked', true);

                if(!selectedDjaci.includes(id)){
                    selectedDjaci.push(id);
                }
            });

        });

    } else {

        selectedGrupe = selectedGrupe.filter(x=>x!==gid);

        let container = $('#djaci_' + gid);

        container.find('.djak-check').each(function(){

            let id = parseInt($(this).val());

            // ❗ 1. uncheck UI
            $(this).prop('checked', false);

            // ❗ 2. ukloni iz state-a
            selectedDjaci = selectedDjaci.filter(x=>x!==id);
        });

        // BONUS: zatvori panel
        container.slideUp();
    }

});

// =======================
// CHECK DJAK
// =======================

$(document).on('change', '.djak-check', function(){

    let id = parseInt($(this).val());

    if($(this).is(':checked')){
        if(!selectedDjaci.includes(id)){
            selectedDjaci.push(id);
        }
    }else{
        selectedDjaci = selectedDjaci.filter(x=>x!==id);
    }

    renderSelected();
});

// =======================
// SUBMIT
// =======================

$('#editForma').submit(function(e){

    e.preventDefault();

    let formData = new FormData(this);

    // =========================
    // SETOVI (da nema duplikata)
    // =========================

    let grupeSet = new Set();
    let djaciSet = new Set();

    // =========================
    // 1. EXISTING
    // =========================

    Object.keys(existing).forEach(gid => {

        gid = parseInt(gid);
        grupeSet.add(gid);

        existing[gid].djaci.forEach(d => {
            djaciSet.add(d.id);
        });
    });

    // =========================
    // 2. NOVO
    // =========================

    selectedGrupe.forEach(g=>{
        grupeSet.add(parseInt(g));
    });

    selectedDjaci.forEach(d=>{
        djaciSet.add(parseInt(d));
    });

    // =========================
    // APPEND (BEZ DUPLIKATA)
    // =========================

    grupeSet.forEach(g=>{
        formData.append('grupe[]', g);
    });

    djaciSet.forEach(d=>{
        formData.append('djaci[]', d);
    });

    // =========================
    // AJAX
    // =========================

    $.ajax({
        url:'admin/update_dokument.php',
        type:'POST',
        data:formData,
        processData:false,
        contentType:false,
        success:function(res){
            $('#poruka').html(res);
        }
    });
});

// INIT
$(document).ready(function(){
    renderSelected();
});
</script>
</body>
</html>