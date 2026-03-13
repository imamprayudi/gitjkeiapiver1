<?php

session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['user'])) {
    echo json_encode(["success"=>false]);
    exit();
}

$env = parse_ini_file(__DIR__ . '/../config/.env');

$host = $env['DB_HOST'];
$dbname = $env['DB_NAME'];
$dbuser = $env['DB_USER'];
$dbpass = $env['DB_PASSWORD'];

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

$pdo = new PDO($dsn,$dbuser,$dbpass,[
PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
]);

$data = json_decode(file_get_contents("php://input"),true);

$rdate = $data['rdate'] ?? '';
$supp  = $data['supp'] ?? '';

$sql = "
UPDATE mailpo
SET supconfstatus='READ'
WHERE supplier=:supp
AND rdate=:rdate
AND supconfstatus='UNREAD'
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
":supp"=>$supp,
":rdate"=>$rdate
]);

echo json_encode(["success"=>true]);