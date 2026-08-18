<?php
header("Content-Type: application/json; charset=utf-8");

/* ================= CORS ================= */
// $allowed_origins = [
//     'https://jkeis.grahaindomedia.com',
//     'https://gitjkeiapiver1.grahaindomedia.net',
//     'https://gitjkei.grahaindomedia.net'
// ];

// if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
//     header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
//     header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//     header("Access-Control-Allow-Headers: Content-Type, Authorization");
//     header("Access-Control-Allow-Credentials: true");
// }

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

/* ================= PDO ================= */
$env = parse_ini_file(__DIR__ . '/../config/.env');

// ===== koneksi PDO =====
$host = $env['DB_HOST'];
$db   = $env['DB_NAME'];     
$user = $env['DB_USER'];    
$pass = $env['DB_PASSWORD'];      
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$db = new PDO($dsn, $env['DB_USER'], $env['DB_PASSWORD'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);


/* ================= POST ================= */
// if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//     echo json_encode(["status" => "error", "message" => "POST only"]);
//     exit();
// }

//$post_key = $_POST['postkey'] ?? '';
$searchby = trim($_POST['searchby'] ?? 'pono');
$rawKeyword = $_POST['keyword'] ?? [];

if ($searchby !== 'pono' && $searchby !== 'partno') {
    $searchby = 'pono';
}

if (!is_array($rawKeyword)) {
    $rawKeyword = [$rawKeyword];
}

$keywords = [];
foreach ($rawKeyword as $k) {
    $k = trim((string)$k);
    if ($k !== '') {
        $keywords[] = $k;
    }
}

if ($searchby === 'partno') {
    $keywords = array_slice($keywords, 0, 1);
}

if (count($keywords) === 0) {
    $message = ($searchby === 'partno') ? 'Input Part Number' : 'Input PO Number';
    echo json_encode(["status" => "error", "message" => $message]);
    exit();
}

$bind = [];
if ($searchby === 'partno') {
    $where = 'TRIM(partno) = :keyword';
    $bind[':keyword'] = $keywords[0];
} else {
    $placeholders = [];
    foreach ($keywords as $i => $k) {
        $ph = ':k' . $i;
        $placeholders[] = $ph;
        $bind[$ph] = $k;
    }
    $where = 'pono IN (' . implode(',', $placeholders) . ')';
}

/* ===== security key ===== */
// if ($post_key !== $env['POST_KEY']) {
//     echo json_encode(["status" => "failed"]);
//     exit();
// }


//===============================
// Original PO
//===============================

$sql="SELECT *
      FROM mailpo
      WHERE $where
      ORDER BY pono";

$stmt=$db->prepare($sql);

$stmt->execute($bind);

$mailpo=$stmt->fetchAll(PDO::FETCH_ASSOC);



//===============================
// Revision
//===============================

$sql="SELECT *
      FROM mailpoc
      WHERE $where
      ORDER BY pono,rdate,rtime";

$stmt=$db->prepare($sql);

$stmt->execute($bind);

$mailpoc=$stmt->fetchAll(PDO::FETCH_ASSOC);


//===============================

echo json_encode([

    "status"=>"success",

    "mailpo"=>$mailpo,

    "mailpoc"=>$mailpoc

]);