<?php

session_start();

header("Content-Type: application/json");


// ======================
// VALIDASI SESSION
// ======================

if (!isset($_SESSION['user'])) {
    echo json_encode([
        "success" => false,
        "message" => "Session expired"
    ]);
    exit();
}

$user  = $_SESSION['user'];
$level = $_SESSION['level'] ?? 0;


// ======================
// PDO CONNECTION (.env style)
// ======================

$env = parse_ini_file(__DIR__ . '/../config/.env');

$host = $env['DB_HOST'];
$dbname = $env['DB_NAME'];
$dbuser = $env['DB_USER'];
$dbpass = $env['DB_PASSWORD'];
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

try {

    $pdo = new PDO($dsn, $dbuser, $dbpass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "DB connection failed"
    ]);
    exit();
}


// ======================
// AMBIL DATA JSON
// ======================

$data = json_decode(file_get_contents("php://input"), true);

$ids    = $data['ids'] ?? [];
$status = $data['status'] ?? '';
$reason = $data['reason'] ?? '';

if ($status == 'REJECTED' && trim($reason) == '') {

    echo json_encode([
        "success" => false,
        "message" => "Reason is required for rejected status"
    ]);
    exit();
}

if (empty($ids) || $status == '') {

    echo json_encode([
        "success" => false,
        "message" => "Invalid data"
    ]);
    exit();
}


// ======================
// TENTUKAN FIELD BERDASARKAN LEVEL
// ======================

if ($level == 3) {

    $statusField = "supconfstatus";
    $reasonField = "supconfreason";
    $byField     = "supconfby";
    $atField     = "supconfat";

} elseif ($level == 4) {

    $statusField = "purconfstatus";
    $reasonField = "purconfreason";
    $byField     = "purconfby";
    $atField     = "purconfat";

} elseif ($level == 6) {

    $statusField = "mcconfstatus";
    $reasonField = "mcconfreason";
    $byField     = "mcconfby";
    $atField     = "mcconfat";

} else {

    echo json_encode([
        "success" => false,
        "message" => "Unauthorized level"
    ]);
    exit();
}


// ======================
// UPDATE DATA
// ======================

try {

    $pdo->beginTransaction();

    $sql = "
    UPDATE mailpo
    SET $statusField = :status,
        $reasonField = :reason,
        $byField = :user,
        $atField = NOW()
    WHERE idno = :id
    ";

    $stmt = $pdo->prepare($sql);

    foreach ($ids as $id) {

        $stmt->execute([
            ":status" => $status,
            ":reason" => $reason,
            ":user" => $user,
            ":id" => $id
        ]);
    }

    $pdo->commit();

    echo json_encode([
        "success" => true,
        "message" => "Status updated successfully"
    ]);

} catch (Exception $e) {

    $pdo->rollBack();

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}