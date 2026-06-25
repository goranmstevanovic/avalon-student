<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "config/database.php";

$db = (new Database())->getConnection();

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    echo "Neispravan ID";
    exit;
}

try {

    $db->beginTransaction();

    // =====================
    // 1. UZMI PUTANJU
    // =====================

    $stmt = $db->prepare("SELECT putanja FROM dokumenti WHERE id=?");
    $stmt->execute([$id]);
    $putanja = $stmt->fetchColumn();

    // =====================
    // 2. OBRIŠI FAJL
    // =====================

    if ($putanja) {

        $root = dirname(__DIR__);
        $full_path = $root . "/" . $putanja;

        if (file_exists($full_path)) {
            unlink($full_path);
        }
    }

    // =====================
    // 3. OBRIŠI PRISTUPE
    // =====================

    $db->prepare("DELETE FROM dokument_pristup WHERE fk_dokument=?")
       ->execute([$id]);

    // =====================
    // 4. OBRIŠI DOKUMENT
    // =====================

    $db->prepare("DELETE FROM dokumenti WHERE id=?")
       ->execute([$id]);

    $db->commit();

    echo "OK";

} catch(Exception $e) {

    if ($db->inTransaction()) {
        $db->rollBack();
    }

    echo "Greška: " . $e->getMessage();
}