<?php


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
    $nama = 'asahi';
    $sql = "
        SELECT 
            supplier.suppname,
            supplier.suppcode
        FROM usersupp
        INNER JOIN supplier 
            ON usersupp.suppcode = supplier.suppcode
        WHERE userid = :nama
        AND supplier.status = 'active'
        ORDER BY suppname
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute(['nama' => $nama]);
    $rows = $stmt->fetchAll();

$data = [];

foreach ($rows as $row) {
    $data[] = [
        'nama' => trim($row['suppname']),
        'kode' => trim($row['suppcode'])
    ];
}

echo json_encode($data);
exit;
} catch (Exception $e) {

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}