<?php
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
// PDO CONNECTION
// ======================
$env = parse_ini_file(__DIR__ . '/../config/.env');

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
// ONLY POST
// ======================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Halaman ini tidak bisa langsung diakses tanpa dari posting.');
}

$filterby = trim($_POST['filterby'] ?? 'month');
$tahun    = $_POST['tahun'] ?? '';
$bulan    = $_POST['bulan'] ?? '';
$tglawal  = trim($_POST['tglawal'] ?? '');
$tglakhir = trim($_POST['tglakhir'] ?? '');

if ($filterby === 'range') {
    if ($tglawal === '' || $tglakhir === '') {
        echo json_encode([
            "status" => "error",
            "message" => "parameter tidak lengkap"
        ]);
        exit;
    }
    $dateWhere = "rdate BETWEEN ? AND ?";
    $one = [$tglawal, $tglakhir];
} else {
    if (!$tahun || !$bulan) {
        echo json_encode([
            "status" => "error",
            "message" => "parameter tidak lengkap"
        ]);
        exit;
    }

    if ($bulan == 'ALL') {
        $dateWhere = "YEAR(rdate) = ?";
        $one = [$tahun];
    } else {
        $dateWhere = "YEAR(rdate) = ? AND MONTH(rdate) = ?";
        $one = [$tahun, $bulan];
    }
}

$sql = "
SELECT
    COUNT(*) AS poc_total,
    SUM(status = 'UP' AND potype = 'FIRM') AS poc_up,
    SUM(status = 'DOWN' AND potype = 'FIRM') AS poc_down,
    SUM(status = 'CANCELLATION' AND potype = 'FIRM') AS poc_cancellation,
    SUM(status = 'REDUCE QTY' AND potype = 'FIRM') AS poc_reduce_qty,
    SUM(status = 'INCREASE QTY' AND potype = 'FIRM') AS poc_increase_qty,
    SUM(status = 'UP & REDUCE QTY' AND potype = 'FIRM') AS poc_up_reduce_qty,
    SUM(status = 'UP & INCREASE QTY' AND potype = 'FIRM') AS poc_up_increase_qty,
    SUM(status = 'DOWN & REDUCE QTY' AND potype = 'FIRM') AS poc_down_reduce_qty,
    SUM(status = 'DOWN & INCREASE QTY' AND potype = 'FIRM') AS poc_down_increase_qty,
    SUM(status = 'UP' AND potype = 'FORECAST') AS poc_up_forecast,
    SUM(status = 'DOWN' AND potype = 'FORECAST') AS poc_down_forecast,
    SUM(status = 'CANCELLATION' AND potype = 'FORECAST') AS poc_cancellation_forecast,
    SUM(status = 'REDUCE QTY' AND potype = 'FORECAST') AS poc_reduce_qty_forecast,
    SUM(status = 'INCREASE QTY' AND potype = 'FORECAST') AS poc_increase_qty_forecast,
    SUM(status = 'UP & REDUCE QTY' AND potype = 'FORECAST') AS poc_up_reduce_qty_forecast,
    SUM(status = 'UP & INCREASE QTY' AND potype = 'FORECAST') AS poc_up_increase_qty_forecast,
    SUM(status = 'DOWN & REDUCE QTY' AND potype = 'FORECAST') AS poc_down_reduce_qty_forecast,
    SUM(status = 'DOWN & INCREASE QTY' AND potype = 'FORECAST') AS poc_down_increase_qty_forecast
FROM mailpoc
WHERE $dateWhere
";

$stmt = $pdo->prepare($sql);
$stmt->execute($one);

$data = $stmt->fetch();

$keys = [
    'poc_total',
    'poc_up',
    'poc_down',
    'poc_cancellation',
    'poc_reduce_qty',
    'poc_increase_qty',
    'poc_up_reduce_qty',
    'poc_up_increase_qty',
    'poc_down_reduce_qty',
    'poc_down_increase_qty',
    'poc_up_forecast',
    'poc_down_forecast',
    'poc_cancellation_forecast',
    'poc_reduce_qty_forecast',
    'poc_increase_qty_forecast',
    'poc_up_reduce_qty_forecast',
    'poc_up_increase_qty_forecast',
    'poc_down_reduce_qty_forecast',
    'poc_down_increase_qty_forecast'
];

foreach ($keys as $key) {
    $data[$key] = (int) ($data[$key] ?? 0);
}

echo json_encode([
    "status" => "success",
    "data" => $data
]);
