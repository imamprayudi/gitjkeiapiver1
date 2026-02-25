<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json; charset=utf-8");

//include('koneksi.php');

$env = parse_ini_file(__DIR__ . '/../config/.env');

// ===== koneksi PDO =====
$host = $env['DB_HOST'];
$db   = $env['DB_NAME'];     
$user = $env['DB_USER'];    
$pass = $env['DB_PASSWORD'];      
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
$db = new PDO($dsn, $user, $pass, $options);

try {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            "status" => "error",
            "message" => "POST only"
        ]);
        exit;
    }

    $nama = trim($_POST['nama'] ?? '');

    $sql = "
        SELECT 
            Supplier.SuppName,
            Supplier.SuppCode
        FROM UserSupp
        INNER JOIN Supplier 
            ON UserSupp.SuppCode = Supplier.SuppCode
        WHERE UserId = ?
        AND Supplier.status = 'active'
        ORDER BY SuppName
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute([$nama]);

    $rows = $stmt->fetchAll();

$data = [];

foreach ($rows as $row) {
    $data[] = [
        'nama' => trim($row['SuppName']),
        'kode' => trim($row['SuppCode'])
    ];
}

echo json_encode($data);
} catch (Exception $e) {

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}