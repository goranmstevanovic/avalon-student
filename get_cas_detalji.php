<?php
include_once "config/database.php";
include_once "config/core.php";

$db = (new Database())->getConnection();

$id = (int)$_GET['id'];
$djak_id = $_SESSION['user_id'];

$query = "
SELECT 
    k.start,
    k.end,
    g.alias,
    e.prisutan,
    e.opravdao_otsustvo,
    e.komentar,
    k.komentar AS komentar_cas

FROM kalendar k

JOIN grupe g ON g.id = k.fk_grupa

LEFT JOIN evidencija e 
    ON e.termin_id = k.id 
    AND e.djak_id = :djak_id

WHERE k.id = :id
LIMIT 1
";

$stmt = $db->prepare($query);
$stmt->bindParam(":id", $id);
$stmt->bindParam(":djak_id", $djak_id);
$stmt->execute();

$d = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$d){
    echo "Nema podataka";
    exit;
}
?>

<div>
    <strong>Grupa:</strong> <?= htmlspecialchars($d['alias']) ?><br><br>

    <strong>Vreme:</strong><br>
    <?= date('d.m.Y H:i', strtotime($d['start'])) ?> - 
    <?= date('H:i', strtotime($d['end'])) ?><br><br>

    <strong>Prisustvo:</strong><br>

    <?php if($d['prisutan'] == 1){ ?>
        ✔ Prisutan
    <?php } elseif($d['prisutan'] == 0){ ?>
        <?= $d['opravdao_otsustvo'] ? "🟡 Opravdano" : "❌ Neopravdano" ?>
    <?php } else { ?>
        Nema evidencije
    <?php } ?>
    <br>
   

    <?php if(!empty($d['komentar'])){ ?>
        <strong>Komentar:</strong><br>
        <?= nl2br(htmlspecialchars($d['komentar'])) ?>
    <?php } ?>
       <?php if(!empty($d['komentar_cas'])){ ?>
        <br><strong>Komentar cas:</strong><br>
        <?= nl2br(htmlspecialchars($d['komentar_cas'])) ?>
    <?php } ?>
</div>