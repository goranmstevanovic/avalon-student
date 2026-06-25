<?php


include_once "../config/database.php";

$db = (new Database())->getConnection();

$root = dirname(__DIR__);

$stmt = $db->prepare("
    SELECT id, putanja 
    FROM dokumenti 
    WHERE datum_vazenja_do IS NOT NULL 
    AND datum_vazenja_do < NOW()
");
$stmt->execute();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

    $id = (int)$row['id'];

    // =====================
    // 1. OBRIŠI FAJL
    // =====================

    if (!empty($row['putanja'])) {

        $full_path = $root . "/" . $row['putanja'];

        if (file_exists($full_path)) {

            if (!unlink($full_path)) {
                error_log("Greška brisanja fajla: " . $full_path);
            }

        } else {
            error_log("Fajl ne postoji: " . $full_path);
        }
    }

    // =====================
    // 2. OBRIŠI PRISTUPE
    // =====================

    $db->prepare("DELETE FROM dokument_pristup WHERE fk_dokument=?")
       ->execute([$id]);

    // =====================
    // 3. OBRIŠI DOKUMENT
    // =====================

    $db->prepare("DELETE FROM dokumenti WHERE id=?")
       ->execute([$id]);
}