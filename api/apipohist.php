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
$pono=trim($_POST['pono'] ?? '');
//$pono=2122060;

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
      WHERE pono=:pono";

$stmt=$db->prepare($sql);

$stmt->execute([
    ":pono"=>$pono
]);

$mailpo=$stmt->fetch(PDO::FETCH_ASSOC);



//===============================
// Revision
//===============================

$sql="SELECT *
      FROM mailpoc
      WHERE pono=:pono
      ORDER BY rdate,rtime";

$stmt=$db->prepare($sql);

$stmt->execute([
    ":pono"=>$pono
]);

$mailpoc=$stmt->fetchAll(PDO::FETCH_ASSOC);


//===============================

echo json_encode([

    "status"=>"success",

    "mailpo"=>$mailpo,

    "mailpoc"=>$mailpoc

]);