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
$suppid   = trim($_POST['supp'] ?? '');
$tglawal  = trim($_POST['tglawal'] ?? '');
$tglakhir = trim($_POST['tglakhir'] ?? '');
$status = trim($_POST['status'] ?? '');

// ======================
// QUERY (MySQL version)
// ======================
$sql = "
select distinct rdate 
from mailpo 
where supplier = ?
  AND rdate BETWEEN ? AND ?
";
$params = [$suppid, $tglawal, $tglakhir];

if($status !== ''){
    $sql .= " AND supconfstatus = ? ";
    $params[] = $status;
}

$sql .= " order by rdate desc ";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$data = $stmt->fetchAll();


// ======================
// RESPONSE JSON
// ======================
echo json_encode([
    "status" => "success",
    "data"   => $data
]);

exit();
?>