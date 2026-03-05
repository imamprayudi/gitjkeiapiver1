<?php
header("Content-Type: application/json; charset=utf-8");

/*
=====================================
PDO CONNECT
=====================================
*/
$env = parse_ini_file(__DIR__ . '/../config/.env');

$dsn = "mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']};charset=utf8mb4";

$pdo = new PDO(
    $dsn,
    $env['DB_USER'],
    $env['DB_PASSWORD'],
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);


/*
=====================================
POST ONLY
=====================================
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status"=>"error","message"=>"POST only"]);
    exit;
}


/*
=====================================
PARAM
=====================================
*/
$suppcode   = trim($_POST['suppcode'] ?? '');
$partnumber = trim($_POST['partnumber'] ?? '');
$ponumber   = trim($_POST['ponumber'] ?? '');

if(!$suppcode || !$partnumber || !$ponumber){
    echo json_encode([]);
    exit;
}


/*
=====================================
QUERY (SUM lebih aman)
=====================================
*/
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(orderqty),0) AS qty
    FROM ordbal
    WHERE suppcode = :supp
      AND partnumber = :part
      AND ponumber = :po
");

$stmt->execute([
    'supp' => $suppcode,
    'part' => $partnumber,
    'po'   => $ponumber
]);

$row = $stmt->fetch();


/*
=====================================
RETURN ARRAY BIASA
=====================================
*/
echo json_encode([
    [
        'qty' => (int)$row['qty']
    ]
]);