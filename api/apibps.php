<?php
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


// ======================
// PDO CONNECTION (.env style kamu)
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
// ONLY POST
// ======================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Halaman ini tidak bisa langsung diakses tanpa dari posting.');
}


$suppid  = trim($_POST['supp'] ?? '');
$tanggal = trim($_POST['tgl'] ?? '');

try {

    // ======================
    // HEADER QUERY
    // ======================
    $sqlHeader = "
        SELECT
        qty1,qty2,qty3,qty4,qty5,qty6,qty7,qty8,qty9,qty10,
        qty11,qty12,qty13,qty14,qty15,qty16,qty17,qty18,qty19,qty20
        FROM bps
        WHERE hd='H' AND suppcode=? AND transdate=?
    ";

    $stmt = $pdo->prepare($sqlHeader);
    $stmt->execute([$suppid, $tanggal]);

    $header = [];

    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $header[] = [
            'qty1'=>$row[0],'qty2'=>$row[1],'qty3'=>$row[2],'qty4'=>$row[3],'qty5'=>$row[4],
            'qty6'=>$row[5],'qty7'=>$row[6],'qty8'=>$row[7],'qty9'=>$row[8],'qty10'=>$row[9],
            'qty11'=>$row[10],'qty12'=>$row[11],'qty13'=>$row[12],'qty14'=>$row[13],'qty15'=>$row[14],
            'qty16'=>$row[15],'qty17'=>$row[16],'qty18'=>$row[17],'qty19'=>$row[18],'qty20'=>$row[19]
        ];
    }


    // ======================
    // DATA QUERY
    // ======================
    $sqlData = "
        SELECT
        partno,partname,balqty,
        qty1,qty2,qty3,qty4,qty5,qty6,qty7,qty8,qty9,qty10,
        qty11,qty12,qty13,qty14,qty15,qty16,qty17,qty18,qty19,qty20
        FROM bps
        WHERE hd='D' AND suppcode=? AND transdate=?
        ORDER BY partno
    ";

    $stmt = $pdo->prepare($sqlData);
    $stmt->execute([$suppid, $tanggal]);

    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $data[] = [
            'partno'=>$row[0],
            'partname'=>$row[1],
            'balqty'=>$row[2],
            'qty1'=>$row[3],'qty2'=>$row[4],'qty3'=>$row[5],'qty4'=>$row[6],'qty5'=>$row[7],
            'qty6'=>$row[8],'qty7'=>$row[9],'qty8'=>$row[10],'qty9'=>$row[11],'qty10'=>$row[12],
            'qty11'=>$row[13],'qty12'=>$row[14],'qty13'=>$row[15],'qty14'=>$row[16],'qty15'=>$row[17],
            'qty16'=>$row[18],'qty17'=>$row[19],'qty18'=>$row[20],'qty19'=>$row[21],'qty20'=>$row[22]
        ];
    }


    // ======================
    // RESPONSE (SAMA PERSIS)
    // ======================
    echo json_encode([
        "status" => "success",
        "header" => $header,
        "data"   => $data
    ]);

} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        "status" => "failed",
        "error"  => $e->getMessage()
    ]);
}