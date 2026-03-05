<?php
header("Content-Type: application/json; charset=utf-8");

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

/* =========================
POST ONLY
========================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status"=>"error","message"=>"POST only"]);
    exit;
}

$suppcode = trim($_POST['suppcode'] ?? '');
if(!$suppcode){
    echo json_encode([]);
    exit;
}

/* =========================
QUERY
========================= */
$stmt = $pdo->prepare("
    SELECT DISTINCT partnumber as partno
    FROM ordbal
    WHERE suppcode = :supp
    ORDER BY partnumber
");

$stmt->execute(['supp'=>$suppcode]);

$data = [];

foreach($stmt as $r){
    $data[] = [
        'partno' => $r['partno']
    ];
}

echo json_encode($data);