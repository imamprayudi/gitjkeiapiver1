<?php
header("Access-Control-Allow-Origin: same-origin");
header("Content-Type: application/json");
// code ini berfungsi untuk mencegah panggil getsession.php langsung
// dengan 2 cara, yaitu dengan API key atau dengan header X-Requested-With
// $api_token = "ACCESS_KEY=BLNPTYndE1m7tJVTDqLHd7ZrTCRhRFfk";
// if (!isset($_SERVER['HTTP_X_ACCESS_KEY']) || $_SERVER['HTTP_X_ACCESS_KEY'] !== $api_token) {
//     http_response_code(403);
//     die("Forbidden");
//     exit();
// }
// Cara kedua dengan header X-Requested-With
if (
    !isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
) {
    http_response_code(403);
    die("Forbidden Access");
    exit();
}

// Fungsi untuk mendapatkan data session
// dan mengembalikannya dalam format JSON
// Programmer: Imam Prayudi
// Date Created : 2025-10-20
// Date Updated : 2025-11-07
//-----------------------------------------------
session_start();
if (isset($_SESSION['user'])) {
  $user = trim($_SESSION['user']);
  $level = $_SESSION['level'];
  $appkey = $_SESSION['appkey'];
  $env = parse_ini_file(__DIR__ . '/../config/.env');
  $postkey = $env['POST_KEY'];
  $suppurl = $env['API_SUPP_URL'];
  $tdstgl = $env['API_TDS_TGL_URL'];
  $urltds = $env['API_TDS_URL'];
  $urlbsp = $env['API_BPS_URL'];
  $urlbpstgl = $env['API_BPS_TGL_URL']; 
  $urlforecast = $env['API_FORECAST_URL'];
  $urlmatsum = $env['API_MATSUM_URL'];
  $urlmatsumtgl = $env['API_MATSUM_TGL_URL'];
  // Buat array respons JSON
  $response = array(
    'user' => $user,
    'level' => $level,
    'appkey' => $appkey,
    'postkey' => $postkey,
    'urlsupp' => $suppurl,
    'urltdstgl' => $tdstgl,
    'urltds' => $urltds,
    'urlbps' => $urlbsp,
    'urlbpstgl' => $urlbpstgl,
    'urlforecast' => $urlforecast,
    'urlmatsum' => $urlmatsum,
    'urlmatsumtgl' => $urlmatsumtgl
  );
  // Header JSON
  header('Content-Type: application/json');
  // Kirim respons JSON
  echo json_encode($response);
} else {
    $user = "";
    echo "Session not found";
}

?>
