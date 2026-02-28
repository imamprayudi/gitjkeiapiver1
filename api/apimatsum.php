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
    echo json_encode(["status"=>"failed"]);
    exit();
}


// ======================
// PDO CONNECTION
// ======================
$env = parse_ini_file(__DIR__ . '/../config/.env');

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
// INPUT
// ======================
$suppid = 'C' . trim($_POST['supp'] ?? '');
$tanggal = trim($_POST['tgl'] ?? '');


// ======================
// QUERY (PDO)
// ======================
$sql = "
SELECT 
    partno,
    partname,
    CAST(prevblncqty AS DECIMAL(18,2))  AS qty1,
    CAST(recqty      AS DECIMAL(18,2))  AS qty2,
    CAST(shipqty     AS DECIMAL(18,2))  AS qty3,
    CAST(thisblncqty AS DECIMAL(18,2))  AS qty4
FROM sc01
WHERE loccode = ?
  AND period  = ?
ORDER BY partno
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$suppid, $tanggal]);

$data = $stmt->fetchAll();


// ======================
// RESPONSE
// ======================
echo json_encode([
    "status" => "success",
    "data"   => $data
]);

exit();
?>