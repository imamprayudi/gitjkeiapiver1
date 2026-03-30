<?php
header("Content-Type: application/json");

// Ambil POST body biasa
if (!isset($_POST['userid'])) {
    echo json_encode([
        "status" => "error",
        "message" => "User ID missing"
    ]);
    exit();
}

$userid = $_POST['userid'];
// Load ENV
$env = parse_ini_file(__DIR__ . '/../config/.env');
$host = $env['DB_HOST'];
$dbname = $env['DB_NAME'];
$user = $env['DB_USER'];
$pass = $env['DB_PASSWORD'];

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $stmt = $pdo->prepare("SELECT username, useremail FROM usertbl WHERE userid = ?");
    $stmt->execute([$userid]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo json_encode([
            "status" => "error",
            "message" => "User not found"
        ]);
        exit();
    }

    echo json_encode([
        "status" => "success",
        "data" => $data
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "DB Error"
    ]);
}