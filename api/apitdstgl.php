<?php
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=utf-8");
header("X-Content-Type-Options: nosniff");
header("Cache-Control: no-cache, private");
header_remove("X-Powered-By");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


// ======================
// PDO CONNECTION
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

$appkey = $env['APP_KEY'];


// ======================
// ONLY POST
// ======================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Halaman ini tidak bisa langsung diakses tanpa dari posting.');
}


// ======================
// VALIDASI KEY
// ======================
// if (($dbappkey ?? '') !== $appkey) {
//     exit();
// }


// ======================
// QUERY
// ======================
try {

    $stmt = $pdo->prepare("
        SELECT transdate
        FROM tdsdate
        ORDER BY transdate DESC
    ");

    $stmt->execute();

    $rows = $stmt->fetchAll();

    $data = [];

    foreach ($rows as $row) {
        $data[] = [
            'tanggal' => substr($row['transdate'], 0, 10)
        ];
    }

    // 🔥 FORMAT JSON SAMA PERSIS SEPERTI LAMA
    echo json_encode($data);
    exit();
 
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
