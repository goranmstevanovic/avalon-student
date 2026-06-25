<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
//echo phpversion();
date_default_timezone_set('Europe/Belgrade');
//phpinfo();
//php_ini_loaded_file()
//require_once "../config/database.php";
//include_once(__DIR__ . '/../config/core.php');
include_once(__DIR__ . '/../config/database.php');
$globalStart = microtime(true);

$database = new Database();
$db = $database->getConnection();

/*
    ===== 1. STATISTIKA 36 MESECI =====
*/

$statStart = microtime(true);

$today = new DateTime();
$today->modify('first day of this month');

for ($i = 0; $i < 36; $i++) {

    $currentMonth = clone $today;
    $currentMonth->modify("-$i month");

    $godina = $currentMonth->format('Y');
    $mesec  = $currentMonth->format('m');

    $firstDay = $currentMonth->format('Y-m-01');
    $lastDay  = $currentMonth->format('Y-m-t');

   // $firstDay = $currentMonth->format('Y-m-01');
$nextMonth = (clone $currentMonth)->modify('+1 month')->format('Y-m-01');

    // 1. Uplate
    $sqlUplate = "
        SELECT COALESCE(SUM(iznos),0)
        FROM uplate
        WHERE aktivan = 1
        AND datum_generisanja BETWEEN :firstDay AND :lastDay
    ";

    $stmtU = $db->prepare($sqlUplate);
    $stmtU->execute([
        ':firstDay' => $firstDay,
        ':lastDay'  => $lastDay
    ]);

    $ukupno = $stmtU->fetchColumn();

    // 2. Termini
    $sqlKalendar = "
        SELECT 
            COUNT(CASE WHEN status IN (1,2,3) THEN 1 END) AS zakazani,
            COUNT(CASE WHEN status = 2 THEN 1 END) AS odrzani
        FROM kalendar
        WHERE start >= :firstDay
        AND start < :nextMonth
    ";

    $stmtK = $db->prepare($sqlKalendar);
    $stmtK->execute([
        ':firstDay'  => $firstDay,
        ':nextMonth' => $nextMonth
    ]);
    $kalendar = $stmtK->fetch(PDO::FETCH_ASSOC);

    $zakazani = $kalendar['zakazani'] ?? 0;
    $odrzani  = $kalendar['odrzani'] ?? 0;

    // 3. Insert / Update
    $sqlInsert = "
        INSERT INTO uplate_casovi_mesec 
        (godina, mesec, ukupno_uplate, broj_zakazanih, broj_odrzanih)
        VALUES (:godina, :mesec, :ukupno, :zakazani, :odrzani)
        ON DUPLICATE KEY UPDATE
            ukupno_uplate = VALUES(ukupno_uplate),
            broj_zakazanih = VALUES(broj_zakazanih),
            broj_odrzanih = VALUES(broj_odrzanih)
    ";

    $stmtInsert = $db->prepare($sqlInsert);
    $stmtInsert->execute([
        ':godina'    => $godina,
        ':mesec'     => $mesec,
        ':ukupno'    => $ukupno,
        ':zakazani'  => $zakazani,
        ':odrzani'   => $odrzani
    ]);
}

$statEnd = microtime(true);
$statTime = number_format($statEnd - $statStart, 4);


/*
    ===== 2. SALDO PROCEDURA =====
*/

$saldoStart = microtime(true);

try {

    $today = date('Y-m-d');

    $stmtSaldo = $db->prepare("CALL azuriraj_saldo_svi(:datum)");
    $stmtSaldo->bindParam(':datum', $today);
    $stmtSaldo->execute();

    // Ako procedura vraća ROW_COUNT()
    $result = $stmtSaldo->fetch(PDO::FETCH_ASSOC);
    $updatedRows = $result['updated_rows'] ?? 0;

    do {
        $stmtSaldo->fetch();
    } while ($stmtSaldo->nextRowset());

    $stmtSaldo->closeCursor();

    $saldoEnd = microtime(true);
    $saldoTime = number_format($saldoEnd - $saldoStart, 4);

    echo "Saldo ažuriran (izmenjeno: {$updatedRows}) | vreme: {$saldoTime}s\n";

} catch (PDOException $e) {
    echo "Greška pri ažuriranju salda: " . $e->getMessage();
}

$globalEnd = microtime(true);
$totalTime = number_format($globalEnd - $globalStart, 4);

echo "Statistika vreme: {$statTime}s | Ukupno vreme CRON: {$totalTime}s\n";