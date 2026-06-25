<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Europe/Belgrade');
//require_once "../config/database.php";
include_once(__DIR__ . '/../config/database.php');

$database = new Database();
$db = $database->getConnection();

/*
    1) Učitaj sve aktivne profesore
*/
$sqlProfesori = "
    SELECT id
    FROM users
    WHERE status = 1
      AND access_level = 'Customer'
";
$stmtP = $db->prepare($sqlProfesori);
$stmtP->execute();
$profesori = $stmtP->fetchAll(PDO::FETCH_COLUMN);

if (!$profesori) {
    exit("Nema profesora.");
}

/*
    2) Poslednjih 36 meseci
*/
$today = new DateTime();
$today->modify('first day of this month');

/*
    3) Priprema SQL-a
*/
$sqlKalendar = "
SELECT 
    COUNT(CASE WHEN status IN (1,2) THEN 1 END) AS zakazani,
    COUNT(CASE WHEN status = 2 THEN 1 END) AS odrzani
FROM kalendar
WHERE start >= :firstDay
AND start < :nextMonth
AND (
        fk_profesor = :profesor_id
     OR (fk_profesor IS NULL AND fk_profesor_grupa = :profesor_id)
)
";

$stmtK = $db->prepare($sqlKalendar);

$sqlInsert = "
INSERT INTO profesor_casovi_mesec
    (profesor_id, godina, mesec, broj_zakazanih, broj_odrzanih)
VALUES
    (:profesor_id, :godina, :mesec, :zakazani, :odrzani)
ON DUPLICATE KEY UPDATE
    broj_zakazanih = VALUES(broj_zakazanih),
    broj_odrzanih  = VALUES(broj_odrzanih)
";

$stmtInsert = $db->prepare($sqlInsert);

/*
    4) Petlja meseci × profesori
*/
for ($i = 0; $i < 36; $i++) {

    $currentMonth = clone $today;
    $currentMonth->modify("-$i month");

    $godina = (int)$currentMonth->format('Y');
    $mesec  = (int)$currentMonth->format('m');

    $firstDay = $currentMonth->format('Y-m-01');
    $lastDay  = $currentMonth->format('Y-m-t');
    $firstDay = $currentMonth->format('Y-m-01');
$nextMonth = (clone $currentMonth)->modify('+1 month')->format('Y-m-01');

    foreach ($profesori as $profesorId) {
        $stmtK->execute([
            ':profesor_id' => (int)$profesorId,
            ':firstDay'    => $firstDay,
            ':nextMonth'   => $nextMonth
        ]);

        $rez = $stmtK->fetch(PDO::FETCH_ASSOC);

        $zakazani = (int)($rez['zakazani'] ?? 0);
        $odrzani  = (int)($rez['odrzani'] ?? 0);

        $stmtInsert->execute([
            ':profesor_id' => (int)$profesorId,
            ':godina'      => $godina,
            ':mesec'       => $mesec,
            ':zakazani'    => $zakazani,
            ':odrzani'     => $odrzani
        ]);
    }
}

echo "CRON profesor statistika uspešno ažurirana.";