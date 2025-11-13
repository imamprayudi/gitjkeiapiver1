<?php
header("Access-Control-Allow-Origin: same-origin");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: text/html; charset=utf-8");
header("X-Content-Type-Options: nosniff");

if (
    !isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
) {
    http_response_code(403);
    die("Forbidden Access");
    exit();
}

$env = parse_ini_file(__DIR__ . '/../config/.env');
$postkey = $env['POST_KEY'];
$loginurl = $env['API_LOGIN_URL'];
$response = array(
    'postkey' => $postkey,
    'loginurl' => $loginurl
  );
echo json_encode($response);