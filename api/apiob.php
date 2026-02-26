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
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "POST only"]);
    exit();
}

$supp     = trim($_POST['supp'] ?? '');
$urutan   = $_POST['urutan'] ?? '1';
$post_key = $_POST['postkey'] ?? '';

/* ===== security key ===== */
if ($post_key !== $env['POST_KEY']) {
    echo json_encode(["status" => "failed"]);
    exit();
}

/* ===== mapping order (hindari SQL injection) ===== */
$orderMap = [
    "1" => "partnumber",
    "2" => "reqdate",
    "3" => "ponumber",
    "4" => "model",
    "5" => "issuedate"
];

$orderBy = $orderMap[$urutan] ?? "partnumber";

/* ===== query PDO ===== */
$sql = "
    SELECT
        partnumber   AS partno,
        partname,
        orderqty     AS qty,
        reqdate,
        ponumber     AS po,
        posq,
        OrderBalance AS ob,
        supprest,
        model,
        IssueDate    AS issuedate,
        potype
    FROM ordbal
    WHERE suppcode = :supp
    ORDER BY $orderBy ASC
";

$stmt = $db->prepare($sql);
$stmt->execute(['supp' => $supp]);

$data = $stmt->fetchAll();

if (!$data) {
    echo json_encode(["status" => "failed"]);
    exit();
}

/* ===== response ===== */
echo json_encode([
    "status" => "success",
    "data"   => $data
]);
exit();