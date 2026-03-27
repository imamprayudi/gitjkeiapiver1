<?php
session_start();

header("Content-Type: application/json");

// Cek session
if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

// Ambil JSON dari fetch()
$input = json_decode(file_get_contents("php://input"), true);

$username  = trim($input['username'] ?? "");
$email     = trim($input['email'] ?? "");
$oldpass   = trim($input['oldpassword'] ?? "");
$newpass   = trim($input['newpassword'] ?? "");

if ($username === "" || $email === "") {
    echo json_encode(["status" => "error", "message" => "Username and email required"]);
    exit();
}

// Validasi email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Email tidak valid"]);
    exit();
}

// Koneksi DB
$env = parse_ini_file(__DIR__ . '/../config/.env');
$pdo = new PDO(
    "mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']};charset=utf8mb4",
    $env['DB_USER'],
    $env['DB_PASSWORD'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$userid = $_SESSION['user'];

// Ambil password lama dari DB
$stmt = $pdo->prepare("SELECT userpass FROM usertbl WHERE userid = ?");
$stmt->execute([$userid]);
$row = $stmt->fetch();

$dbpass = $row['userpass'];

// =============================
//         UPDATE LOGIC
// =============================

// ➤ User tidak ingin ganti password
if ($newpass === "") {

    $stmt = $pdo->prepare("UPDATE usertbl SET username=?, useremail=? WHERE userid=?");
    $stmt->execute([$username, $email, $userid]);

} else {

    // ➤ User ingin ganti password → wajib isi oldpass
    if ($oldpass === "") {
        echo json_encode(["status" => "error", "message" => "Old password required"]);
        exit();
    }

    if ($oldpass !== $dbpass) {
        echo json_encode(["status" => "error", "message" => "Old password incorrect"]);
        exit();
    }

    // Update all
    $stmt = $pdo->prepare("UPDATE usertbl SET username=?, useremail=?, userpass=? WHERE userid=?");
    $stmt->execute([$username, $email, $newpass, $userid]);
}

echo json_encode(["status" => "success", "message" => "Profile updated"]);
exit();