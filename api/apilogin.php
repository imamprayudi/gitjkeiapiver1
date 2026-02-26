<?php
//header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=utf-8");
header("X-Content-Type-Options: nosniff");
header("Cache-Control: no-cache, no-store, must-revalidate");
header_remove("X-Powered-By");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'koneksi.php';

$env = parse_ini_file(__DIR__ . '/../config/.env');
$postkeyenv = $env['POST_KEY'];
$appkey = $env['APP_KEY'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $postkey  = trim($_POST['postkey'] ?? '');
    $userid   = trim($_POST['userid'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // if ($postkey !== $postkeyenv) {
    //     echo json_encode(['status' => 'invalid post key']);
    //     exit();
    // }

    $query = "SELECT userid, usersecure, :appkey AS kunci 
              FROM usertbl 
              WHERE userid = :userid 
              AND userpass = :password";

   $stmt = $pdo->prepare($query);
$stmt->execute([
    ':userid'  => $userid,
    ':password'=> $password,
    ':appkey'  => $appkey
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['status' => 'Wrong ID or Password']);
    exit();
}

echo json_encode([
    "status" => "success",
    "data" => [
        trim($user['userid']),
        trim($user['usersecure']),
        trim($user['kunci'])
    ]
]);
exit();


} 

else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed"]);
}
