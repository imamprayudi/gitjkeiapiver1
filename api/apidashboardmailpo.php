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

$appkey = $env['APP_KEY'];

// ======================
// ONLY POST
// ======================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Halaman ini tidak bisa langsung diakses tanpa dari posting.');
}

$supplier = $_POST['supplier'] ?? '';
$tahun    = $_POST['tahun'] ?? '';
$bulan    = $_POST['bulan'] ?? '';

if (!$supplier || !$tahun || !$bulan) 
{
  echo json_encode([
      "status" => "error",
      "message" => "parameter tidak lengkap"
  ]);
  exit;
}

if ($bulan == 'ALL') {
    $filterBulan = "";
} else {
    $filterBulan = "AND MONTH(rdate) = ?";
}

$sql = "
SELECT

/* =======================
   MAILPO
======================= */
(
    SELECT COUNT(*)
    FROM mailpo
    WHERE supplier = ?
      AND YEAR(rdate) = ?
      $filterBulan
) AS po_total
,
(
    SELECT SUM(supconfstatus='UNREAD')
    FROM mailpo
    WHERE supplier = ?
      AND YEAR(rdate) = ?
      $filterBulan
) AS po_unread
,
(
    SELECT SUM(supconfstatus='READ')
    FROM mailpo
    WHERE supplier = ?
      AND YEAR(rdate) = ?
      $filterBulan
) AS po_read
,
(
    SELECT SUM(supconfstatus='REJECTED')
    FROM mailpo
    WHERE supplier = ?
      AND YEAR(rdate) = ?
      $filterBulan
) AS po_reject
,
/* =======================
   MAILPOC (REVISION)
======================= */
(
    SELECT COUNT(*)
    FROM mailpoc
    WHERE supplier = ?
      AND YEAR(rdate) = ?
      $filterBulan 
) AS poc_total
,
(
    SELECT SUM(supconfstatus='UNREAD')
    FROM mailpoc
    WHERE supplier = ?
      AND YEAR(rdate) = ?
      $filterBulan
) AS poc_unread
,
(
    SELECT SUM(supconfstatus='READ')
    FROM mailpoc
    WHERE supplier = ?
      AND YEAR(rdate) = ?
      $filterBulan
) AS poc_read
,
(
    SELECT SUM(supconfstatus='REJECTED')
    FROM mailpoc
    WHERE supplier = ?
      AND YEAR(rdate) = ?
      $filterBulan
) AS poc_reject
";



if ($bulan == 'ALL') {

    $params = [
        $supplier,$tahun,
        $supplier,$tahun,
        $supplier,$tahun,
        $supplier,$tahun,

        $supplier,$tahun,
        $supplier,$tahun,
        $supplier,$tahun,
        $supplier,$tahun
    ];

}else {

    $params = [
        $supplier,$tahun,$bulan,
        $supplier,$tahun,$bulan,
        $supplier,$tahun,$bulan,
        $supplier,$tahun,$bulan,

        $supplier,$tahun,$bulan,
        $supplier,$tahun,$bulan,
        $supplier,$tahun,$bulan,
        $supplier,$tahun,$bulan
    ];

}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$data = $stmt->fetch();

$data['po_total']   = (int) $data['po_total'];
$data['po_unread']  = (int) $data['po_unread'];
$data['po_read']    = (int) $data['po_read'];
$data['po_reject']  = (int) $data['po_reject'];

$data['poc_total']  = (int) $data['poc_total'];
$data['poc_unread'] = (int) $data['poc_unread'];
$data['poc_read']   = (int) $data['poc_read'];
$data['poc_reject'] = (int) $data['poc_reject'];

echo json_encode([
    "status" => "success",
    "data" => $data
]);