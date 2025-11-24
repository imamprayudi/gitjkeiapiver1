<?php
header("Access-Control-Allow-Origin: same-origin");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: text/html; charset=utf-8");
header("X-Content-Type-Options: nosniff");
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
  $urlob = $env['API_OB_URL'];
  $urlmatsum = $env['API_MATSUM_URL'];
  $urlmatsumtgl = $env['API_MATSUM_TGL_URL'];
  $urlmatiss = $env['API_MATISS_URL'];
  $urlmatrec = $env['API_MATREC_URL'];
  $urlsoa = $env['API_SOA_URL'];
  $urlsoatgl = $env['API_SOA_TGL_URL'];
  $urlsoamid = $env['API_SOAMID_URL'];
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
    'urlob' => $urlob,
    'urlmatsum' => $urlmatsum,
    'urlmatsumtgl' => $urlmatsumtgl,
    'urlmatiss' => $urlmatiss,
    'urlmatrec' => $urlmatrec,
    'urlsoa' => $urlsoa,
    'urlsoatgl' => $urlsoatgl,
    'urlsoamid' => $urlsoamid
  );
  echo json_encode($response);
} else {
    $user = "";
    echo "Session not found";
}

?>
