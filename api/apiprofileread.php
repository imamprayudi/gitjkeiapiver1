<?php
// require_once "security.php";
ini_set('session.cookie_path', '/');
session_start();

header("Content-Type: application/json");

if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$userid = $_SESSION['user'];

$env = parse_ini_file(__DIR__ . '/../config/.env');
$host    = $env['DB_HOST'];
$dbname  = $env['DB_NAME'];
$user    = $env['DB_USER'];
$pass    = $env['DB_PASSWORD'];

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$stmt = $pdo->prepare("SELECT username, useremail FROM usertbl WHERE userid = ?");
$stmt->execute([$userid]);
$data = $stmt->fetch();

echo json_encode([
    "status" => "success",
    "data"   => $data
]);