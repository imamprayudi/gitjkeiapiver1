<?php
// ======================
// HEADER
// ======================
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
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

$appkey  = $env['APP_KEY'];
$postkey = $env['POST_KEY'];


// ======================
// ONLY POST
// ======================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status"=>"failed","message"=>"POST only"]);
    exit();
}


// ======================
// INPUT
// ======================
$supp     = trim($_POST['supp'] ?? '');
$tipeReq  = $_POST['tipe'] ?? '';
$post_key = $_POST['postkey'] ?? '';
$tgl      = $_POST['tgl'] ?? '';

if (!$tgl) {
    echo json_encode(["status"=>"failed","message"=>"Tanggal kosong"]);
    exit();
}

if ($post_key !== $postkey) {
    echo json_encode(["status"=>"failed"]);
    exit();
}


// ======================
// QUERY SELECTOR
// ======================
switch ($tipeReq) {

    case "1":
        $tipe = 'WEEKLY';
        $range = "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28";
        break;

    case "2":
        $tipe = 'MONTHLY';
        $range = "29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53";
        break;

    default:
        echo json_encode(["status"=>"failed"]);
        exit();
}


// ======================
// BUILD COLUMN DINAMIS (BIAR TIDAK 500 BARIS 😄)
// ======================
function buildCols($prefix, $rangeArr){
    $out=[];
    foreach($rangeArr as $n){
        $out[] = $prefix.$n;
    }
    return implode(',', $out);
}

$r = explode(',', $range);

$dt1 = buildCols('dt1qt', $r);
$dt2 = buildCols('dt2qt', $r);
$dt3 = buildCols('dt3qt', $r);
$dt4 = buildCols('dt4qt', $r);


// ======================
// HEADER QUERY
// ======================
$sqlHeader = "
SELECT transdate, rt, suppcode, subsuppcode, subsuppname, partno, partname, leadtime, $dt1
FROM fc2y
WHERE rt='H' AND suppcode = ? AND DATE(transdate) = ?
";

$stmt = $pdo->prepare($sqlHeader);
$stmt->execute([$supp, $tgl]);
$judul = $stmt->fetchAll();

if(!$judul){
    echo json_encode(["status"=>"failed"]);
    exit();
}


// ======================
// DATA QUERY
// ======================
$sqlData = "
SELECT partno, partname, leadtime, $dt1, $dt2, $dt3, $dt4
FROM fc2y
WHERE rt='D' AND suppcode = ? AND DATE(transdate) = ?
";

$stmt = $pdo->prepare($sqlData);
$stmt->execute([$supp, $tgl]);

$data = $stmt->fetchAll();

if(!$data){
    echo json_encode(["status"=>"failed"]);
    exit();
}


// ======================
// RESPONSE
// ======================
echo json_encode([
    "status" => "success",
    "tipe"   => $tipe,
    "judul"  => $judul,
    "data"   => $data
]);