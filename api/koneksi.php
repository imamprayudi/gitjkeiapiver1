<?php
$env = parse_ini_file(__DIR__ . '/../config/.env');
$host = $env['DB_HOST'];
$db = $env['DB_NAME'];
$user = $env['DB_USER'];
$pass = $env['DB_PASSWORD'];
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
