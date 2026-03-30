<?php
header("Content-Type: application/json");

// Ambil JSON body
$input = json_decode(file_get_contents("php://input"), true);

$userid    = trim($input['userid'] ?? "");
$username  = trim($input['username'] ?? "");
$email     = trim($input['email'] ?? "");
$oldpass   = trim($input['oldpassword'] ?? "");
$newpass   = trim($input['newpassword'] ?? "");

// Validasi
if ($userid === "") {
    echo json_encode(["status" => "error", "message" => "UserID required"]);
    exit();
}

if ($username === "" || $email === "") {
    echo json_encode(["status" => "error", "message" => "Username and email required"]);
    exit();
}

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

// Ambil password lama
$stmt = $pdo->prepare("SELECT userpass FROM usertbl WHERE userid = ?");
$stmt->execute([$userid]);
$row = $stmt->fetch();

if (!$row) {
    echo json_encode(["status" => "error", "message" => "User not found"]);
    exit();
}

$dbpass = $row['userpass'];

// =============================
//         UPDATE LOGIC
// =============================

// Tidak ganti password
if ($newpass === "") {

    $stmt = $pdo->prepare("UPDATE usertbl SET username=?, useremail=? WHERE userid=?");
    $stmt->execute([$username, $email, $userid]);

} else {

    // Kalau mau ganti password → oldpass wajib benar
    if ($oldpass === "") {
        echo json_encode(["status" => "error", "message" => "Old password required"]);
        exit();
    }

    if ($oldpass !== $dbpass) {
        echo json_encode(["status" => "error", "message" => "Old password incorrect"]);
        exit();
    }

    // Update semua
    $stmt = $pdo->prepare("UPDATE usertbl SET username=?, useremail=?, userpass=? WHERE userid=?");
    $stmt->execute([$username, $email, $newpass, $userid]);
}

echo json_encode(["status" => "success", "message" => "Profile updated ok"]);
exit();