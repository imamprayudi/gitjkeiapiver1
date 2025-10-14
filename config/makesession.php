<?php
session_start();

// if (!defined('SECURE_ACCESS')) {
//     header('HTTP/1.0 403 Forbidden');
//     exit('Akses langsung dilarang!');
// }

//$env = parse_ini_file(__DIR__ . 'config/.env');
$env = parse_ini_file(__DIR__ . '/../config/.env');
$envappkey = trim($env['APP_KEY']);
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (empty($_GET['nama'])) {
    echo "Error";
    exit();
  }else{
    $nama = $_GET['nama'];
    $getkeyget = trim($_GET['getkey']);  
    $_SESSION['user'] = $nama;
    $_SESSION['level'] = $_GET['level'];
    $_SESSION['appkey'] = $envappkey;
    header("Location: ../public/dashboard.php");
  }
}

