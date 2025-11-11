<?php
header("Access-Control-Allow-Origin: same-origin");
header("Content-Type: application/json");

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
header('Content-Type: application/json');
echo json_encode($response);