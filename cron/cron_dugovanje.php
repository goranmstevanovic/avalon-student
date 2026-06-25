<?php

// =============================
// CLI ONLY
// =============================
// if (php_sapi_name() !== 'cli') {
//     exit("Ova skripta može da se pokrene samo iz CLI.\n");
// }

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {

    // =============================
    // LOAD FILES
    // =============================
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../config/autoload.php';

    if (!class_exists('Database')) {
        throw new Exception("Database klasa nije učitana.");
    }

    // =============================
    // DB CONNECTION
    // =============================
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        throw new Exception("DB konekcija nije uspela.");
    }

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // =============================
    // UKUPAN DUG
    // =============================
    $sumSql = "SELECT SUM(dj.saldo_danas)
               FROM djaci dj
               WHERE dj.status = 1 AND dj.saldo_danas < 0";

    $sumStmt = $db->prepare($sumSql);

    if (!$sumStmt->execute()) {
        throw new Exception("SUM query execute nije uspeo.");
    }

    $ukupan_saldo_svih = (float)$sumStmt->fetchColumn();

    // =============================
    // OBJEKTI
    // =============================
    $djak = new djak($db);
    $profesor = new user($db);
    $kalendar = new kalendar($db);

    $broj_djaka = $djak->count_all();
    $broj_profesora = $profesor->count_profesor();
    $broj_casova_danas = $kalendar->count_casovi_danas();

    // VALIDACIJA (da ne prođe tiha greška)
    if (!is_numeric($broj_djaka) || !is_numeric($broj_profesora) || !is_numeric($broj_casova_danas)) {
        throw new Exception("Jedna od count metoda nije vratila broj.");
    }

    $broj_djaka = (int)$broj_djaka;
    $broj_profesora = (int)$broj_profesora;
    $broj_casova_danas = (int)$broj_casova_danas;

    


    $datum = date('Y-m-d');

    // =============================
    // UPSERT
    // =============================
    $query = "INSERT INTO danas_casova_profesora_djaka_dugovanja
                (datum, broj_djaka, broj_casova, broj_profesora, duguju_danas)
              VALUES
                (:datum, :broj_djaka, :broj_casova, :broj_profesora, :duguju_danas)
              ON DUPLICATE KEY UPDATE
                broj_djaka = VALUES(broj_djaka),
                broj_casova = VALUES(broj_casova),
                broj_profesora = VALUES(broj_profesora),
                duguju_danas = VALUES(duguju_danas),
                updated_at = NOW()";

    $stmt = $db->prepare($query);

    $result = $stmt->execute([
        ':datum' => $datum,
        ':broj_djaka' => $broj_djaka,
        ':broj_casova' => $broj_casova_danas,
        ':broj_profesora' => $broj_profesora,
        ':duguju_danas' => $ukupan_saldo_svih
    ]);

    if (!$result) {
        throw new Exception("UPSERT execute nije uspeo.");
    }

    // Ako je sve OK — ništa ne štampamo
    exit(0);

} catch (Throwable $e) {

    echo "========== CRON ERROR ==========\n";
    echo "Datum: " . date('Y-m-d H:i:s') . "\n";
    echo "Poruka: " . $e->getMessage() . "\n";
    echo "Fajl: " . $e->getFile() . "\n";
    echo "Linija: " . $e->getLine() . "\n";
    echo "================================\n";

    exit(1);
}
echo "Cron uspešno izvršen za datum {$datum}\n";