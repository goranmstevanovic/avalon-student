<?php
// check_email.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $id_djak = $_POST['id_djak'] ?? '';

    // Validacija da email nije prazan
    if (empty($email)) {
        echo json_encode(['exists' => false, 'error' => 'Email is empty']);
        exit;
    }

    // Povezivanje na bazu (koristi PDO za sigurnost)
    try {
        // $pdo = new PDO('mysql:host=localhost;dbname=tvoja_baza', 'korisnik', 'lozinka');
        // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        include_once 'config/database.php';
        $database = new Database();
        $db = $database->getConnection();

        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        // Proverite da li je validna email adresa
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['error' => 'Invalid email address']);
            exit; // Prekida skriptu ako email nije validan
        }


        // Provera da li email postoji
        $upit = "SELECT COUNT(*) FROM djaci WHERE email = '$email' AND status = 1 AND id != $id_djak";
        $stmt = $db->prepare($upit);
        $stmt->execute();
        $exists = $stmt->fetchColumn() > 0;

        echo json_encode(['exists' => $exists,
                            'email' => $upit
                            ]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
