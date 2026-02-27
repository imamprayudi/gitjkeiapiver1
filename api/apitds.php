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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status"=>"failed"]);
    exit();
}


// ======================
// PDO CONNECTION (.env style kamu)
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


$suppid  = trim($_POST['supp']);
$tanggal = trim($_POST['tgl']);


// ======================
// FUNCTION BUILD QTY
// ======================
function buildQtyCols($n = 32){
    $cols = [];
    for($i=1;$i<=$n;$i++){
        $cols[] = "qty$i";
    }
    return implode(",", $cols);
}

$qtyCols = buildQtyCols(32);


// ======================
// HEADER QUERY
// ======================
$sqlHeader = "
SELECT $qtyCols
FROM tds
WHERE hd='H' AND suppcode=? AND transdate=?
";

$stmt = $pdo->prepare($sqlHeader);
$stmt->execute([$suppid, $tanggal]);

$header = $stmt->fetchAll();


// ======================
// DATA QUERY
// ======================
$sqlData = "
SELECT partno, partname, balqty, $qtyCols
FROM tds
WHERE hd='D' AND suppcode=? AND transdate=?
ORDER BY partno
";

$stmt = $pdo->prepare($sqlData);
$stmt->execute([$suppid, $tanggal]);

$data = $stmt->fetchAll();


// ======================
// RESPONSE JSON
// ======================
echo json_encode([
    "status" => "success",
    "header" => $header,
    "data"   => $data
]);