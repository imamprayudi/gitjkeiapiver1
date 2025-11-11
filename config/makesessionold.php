<?php
session_start();

// if (!defined('SECURE_ACCESS')) {
//     header('HTTP/1.0 403 Forbidden');
//     exit('Akses langsung dilarang!');
// }

$env = parse_ini_file(__DIR__ . '/../config/.env');
$envappkey = trim($env['APP_KEY']);
$envgetkey = trim($env['GET_KEY']);
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (empty($_GET['getkey'])) {
      echo "Error";
      exit();
    }

  if (empty($_GET['level'])) {
    echo "Error";
    exit();
  }

  if (empty($_GET['nama'])) {
    echo "Error";
    exit();
  }else{
    $nama = $_GET['nama'];
    $getkey = trim($_GET['getkey']);
    if(empty($getkey)) {
      echo "Error";
      exit();
    }
    if ($getkey !== $envgetkey) {
      echo "Error";
      exit();
    }  
    $_SESSION['user'] = $nama;
    $_SESSION['level'] = $_GET['level'];
    $_SESSION['appkey'] = $envappkey;
    header("Location: ../public/dashboard.php");
  }
}

