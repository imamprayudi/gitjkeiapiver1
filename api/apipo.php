<?php
header("Content-Type: application/json; charset=utf-8");

/*
=====================================
PDO CONNECT (sama seperti file lain)
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
$suppcode   = trim(str_replace(["'","\""],'', $_POST['suppcode'] ?? ''));
$partnumber = trim($_POST['partnumber'] ?? '');

if(!$suppcode || !$partnumber){
    echo json_encode([]);
    exit;
}


/*
=====================================
QUERY
=====================================
*/
$sql = "
    SELECT DISTINCT ponumber
    FROM ordbal
    WHERE suppcode = :supp
      AND partnumber = :part
    ORDER BY ponumber ASC
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    'supp' => $suppcode,
    'part' => $partnumber
]);


/*
=====================================
FORMAT JSON (array biasa)
=====================================
*/
$data = [];

foreach($stmt as $r){
    $data[] = [
        'ponumber' => $r['ponumber']
    ];
}

echo json_encode($data);