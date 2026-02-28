<?php
// ======================
// HEADER
// ======================
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=utf-8");
header("X-Content-Type-Options: nosniff");
header("Cache-Control: no-cache, private");
header_remove("X-Powered-By");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


// ======================
// POST ONLY
// ======================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status"=>"failed","message"=>"POST only"]);
    exit();
}


// ======================
// LOAD ENV
// ======================
$env = parse_ini_file(__DIR__ . '/../config/.env');

$appkey  = $env['APP_KEY'];
$postkey = $_POST['postkey'] ?? '';

// if ($postkey !== $appkey) {
//     echo json_encode(["status"=>"failed"]);
//     exit();
// }


// ======================
// PDO CONNECTION
// ======================
// ===== koneksi PDO =====
$host = $env['DB_HOST'];
$dbname = $env['DB_NAME'];     
$user = $env['DB_USER'];    
$pass = $env['DB_PASSWORD'];      
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

// ======================
// QUERY
// ======================
$stmt = $pdo->prepare("
    SELECT period AS tanggal
    FROM sc01period
    ORDER BY period DESC
");

$stmt->execute();

$data = $stmt->fetchAll();


// ======================
// RESPONSE JSON
// ======================
echo json_encode($data);
exit();
?>