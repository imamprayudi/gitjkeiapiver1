<?php
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=utf-8");
header("X-Content-Type-Options: nosniff");
header("Cache-Control: no-cache, private");
header_remove("X-Powered-By");

// preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status"=>"error","message"=>"Invalid request"]);
    exit();
}

try {

    // ======================
    // LOAD ENV
    // ======================
    $env = parse_ini_file(__DIR__ . '/../config/.env');

  // ===== koneksi PDO =====
  $host = $env['DB_HOST'];
  $dbname = $env['DB_NAME'];     
  $user = $env['DB_USER'];    
  $pass = $env['DB_PASSWORD'];      
  $charset = "utf8mb4";

  $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
    // ======================
    // PARAMETER
    // ======================
    $suppid   = trim($_POST['supp'] ?? '');
    $tglawal  = trim($_POST['tglawal'] ?? '');
    $tglakhir = trim($_POST['tglakhir'] ?? '');

    // ======================
    // QUERY PDO (AMAN)
    // ======================
    $sql = "
        SELECT 
            partno,
            tanggal AS tgl,
            qty,
            tipe,
            partname
        FROM vi07
        WHERE suppcode = ?
          AND tanggal BETWEEN ? AND ?
          AND tipe IN ('VT','IC','B')
        ORDER BY tanggal, partno
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$suppid, $tglawal, $tglakhir]);

    $data = $stmt->fetchAll();

    echo json_encode([
        "status" => "success",
        "data"   => $data
    ]);

} catch (Exception $e) {

    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}