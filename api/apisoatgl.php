<?php
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=utf-8");
header("X-Content-Type-Options: nosniff");
header("Cache-Control: no-cache, private");
header_remove("X-Powered-By");

/* ======================
   PREFLIGHT
====================== */
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

/* ======================
   LOAD ENV + APP KEY
====================== */

$env = parse_ini_file(__DIR__ . '/../config/.env');
$appkey = $env['APP_KEY'] ?? '';
$dbappkey = $_POST['appkey'] ?? '';

// if ($dbappkey !== $appkey) {
//     http_response_code(403);
//     echo json_encode(["status"=>"error","message"=>"Invalid key"]);
//     exit();
// }

/* ======================
   ONLY POST
====================== */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status"=>"error","message"=>"Invalid request"]);
    exit();
}



// ===== koneksi PDO =====
$host = $env['DB_HOST'];
$db   = $env['DB_NAME'];     
$user = $env['DB_USER'];    
$pass = $env['DB_PASSWORD'];      
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {

    /* ================= PDO ================= */


$pdo = new PDO($dsn, $env['DB_USER'], $env['DB_PASSWORD'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

    /* ======================
       QUERY
    ====================== */
    $sql = "
        SELECT DATE(tanggal) AS tanggal
        FROM soadate
        WHERE tanggal IS NOT NULL
        ORDER BY tanggal DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $data = $stmt->fetchAll();

    echo json_encode([
        "status" => "success",
        "data"   => $data
    ]);

} catch (Exception $e) {

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}