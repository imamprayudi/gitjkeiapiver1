<?php
/* =========================
   SECURITY HEADER
========================= */
$allowed_origin = "http://localhost";

if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] === $allowed_origin) {
    header("Access-Control-Allow-Origin: $allowed_origin");
}

header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("X-Content-Type-Options: nosniff");
header("Cache-Control: no-cache, private");
header_remove("X-Powered-By");

/* =========================
   HANDLE PREFLIGHT
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

/* =========================
   ONLY POST ALLOWED
========================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Halaman ini tidak bisa langsung diakses tanpa dari posting.');
}

/* =========================
   JSON RESPONSE
========================= */
header("Content-Type: application/json; charset=utf-8");

// =====================
// ENV + PDO CONNECT
// =====================
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


try {

    $blnthn     = trim($_POST['bulantahun'] ?? '');
    $suppid     = trim($_POST['suppid'] ?? '');
    $suppkomen  = trim($_POST['suppcom'] ?? '');
    $jeinkomen  = trim($_POST['jeincom'] ?? '');

    /* =========================
       CEK DATA EXIST
    ========================= */
    $sql = "SELECT 1 FROM soacommid
            WHERE blnthn = ? AND suppcode = ? 
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$blnthn, $suppid]);

    if ($stmt->fetch()) {

        /* =========================
           UPDATE
        ========================= */
        $update = "UPDATE soacommid 
                   SET suppcom = ?, jeincom = ?
                   WHERE blnthn = ? AND suppcode = ?";

        $stmt = $pdo->prepare($update);
        $stmt->execute([$suppkomen, $jeinkomen, $blnthn, $suppid]);

        $status = "Data has been updated.";

    } else {

        /* =========================
           INSERT
        ========================= */
        $insert = "INSERT INTO soacommid (blnthn, suppcode, suppcom, jeincom)
                   VALUES (?, ?, ?, ?)";

        $stmt = $pdo->prepare($insert);
        $stmt->execute([$blnthn, $suppid, $suppkomen, $jeinkomen]);

        $status = "Data has been inserted";
    }

    echo json_encode([
        "status" => $status
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}