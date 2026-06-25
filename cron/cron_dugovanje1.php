<?php

// CLI only zaštita
// if (php_sapi_name() !== 'cli') {
//     exit("Ova skripta može da se pokrene samo iz CLI.\n");
// }

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 0);


//require_once __DIR__ . '/../config/core.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/autoload.php';

$database = new Database();
$db = $database->getConnection();

//var_dump($database);

// --- SUMA DUGOVANJA ---
$sumSql = "SELECT SUM(dj.saldo_danas) AS ukupno
           FROM djaci dj
           WHERE dj.status = 1 AND dj.saldo_danas < 0";

$sumStmt = $db->prepare($sumSql);
$sumStmt->execute();
$ukupan_saldo_svih = (float)$sumStmt->fetchColumn();

// --- OBJEKTI ---
$djak = new djak($db);
$profesor = new user($db);
$kalendar = new kalendar($db);


// --- BROJEVI ---
$broj_djaka = $djak->count_all();
$broj_profesora = $profesor->count_profesor();
$broj_casova_danas = $kalendar->count_casovi_danas();
//$datum = date('Y-m-d');

echo $broj_djaka,"/ ", $broj_profesora," / ", $broj_casova_danas,"/", $ukupan_saldo_svih;
// --- UPSERT ---
$datum = date('Y-m-d');

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

$stmt->bindParam(':datum', $datum);
$stmt->bindParam(':broj_djaka', $broj_djaka);
$stmt->bindParam(':broj_casova', $broj_casova_danas);
$stmt->bindParam(':broj_profesora', $broj_profesora);
$stmt->bindParam(':duguju_danas', $ukupan_saldo_svih );

$stmt->execute();


// $query = "INSERT INTO danas_casova_profesora_djaka_dugovanja
//           (datum, broj_djaka, broj_casova, broj_profesora, duguju_danas)
//           VALUES (NOW(), 999, 999, 999, -999)";

// $db->exec($query);

// echo "Manual insert done";
exit;

echo "Cron dugovanja uspešno izvršen za datum {$datum}\n";