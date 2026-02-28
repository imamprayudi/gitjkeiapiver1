<?php
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=utf-8");
header("X-Content-Type-Options: nosniff");
header("Cache-Control: no-cache, private");
header_remove("X-Powered-By");

// =====================
// PREFLIGHT
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status"=>"error","message"=>"Invalid request"]);
    exit();
}

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

    $suppid = trim($_POST['supp']);
    $tgl    = trim($_POST['tgl']);

    $tahunsoa = substr($tgl,0,4);
    $tablesoa = ($tahunsoa < 2020) ? "soa{$tahunsoa}" : "soa";

    // =====================
    // JUDUL (HEADER)
    // =====================
    $sqlJudul = "
        SELECT blnthn,suppcode,lastpay,purchase,
               dncns,netpur,vat,salesvat,payment,balance
        FROM {$tablesoa}
        WHERE hd='H'
          AND suppcode=?
          AND transdate=?
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sqlJudul);
    $stmt->execute([$suppid, $tgl]);
    $r = $stmt->fetch();

    $judul = [];

    if ($r) {
        $judul[] = [
            'blnthn'  => $r['blnthn'],
            'supp'    => $r['suppcode'],
            'lastpay' => $r['lastpay'],
            'purchase'=> $r['purchase'],
            'dncn'    => $r['dncns'],
            'netpur'  => $r['netpur'],
            'vat'     => $r['vat'],
            'salesvat'=> $r['salesvat'],
            'payment' => $r['payment'],
            'balance' => $r['balance']
        ];
        $blnthn = $r['blnthn'];
    } else {
        $blnthn = '';
    }

    // =====================
    // COMMENT (SOACOM)
    // =====================
    $sqlCom = "
        SELECT blnthn,suppcode,suppcom,jeincom
        FROM soacom
        WHERE blnthn=? AND suppcode=?
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sqlCom);
    $stmt->execute([$blnthn, $suppid]);
    $r = $stmt->fetch();

    if (!$r) {
        $datacom[] = [
            'blnthn'   => $blnthn,
            'suppcode' => $suppid,
            'suppcom'  => '',
            'jeincom'  => ''
        ];
    } else {
        $datacom[] = $r;
    }

    // =====================
    // DETAIL DATA
    // =====================
    $sqlData = "
        SELECT blnthn,suppcode,ok,tgl,po,posq,
               invoice,partno,partname,qty,price,amount,dncnd
        FROM {$tablesoa}
        WHERE hd='D'
          AND suppcode=?
          AND transdate=?
        ORDER BY invoice, ok, po, posq
    ";

    $stmt = $pdo->prepare($sqlData);
    $stmt->execute([$suppid, $tgl]);

    $data = [];

    while ($row = $stmt->fetch()) {
        $data[] = [
            'blnthn'   => $row['blnthn'],
            'supp'     => $row['suppcode'],
            'ok'       => $row['ok'],
            'tgl'      => substr($row['tgl'],0,10),
            'po'       => $row['po'],
            'sq'       => $row['posq'],
            'invoice'  => $row['invoice'],
            'partno'   => $row['partno'],
            'partname' => $row['partname'],
            'qty'      => $row['qty'],
            'price'    => $row['price'],
            'amount'   => $row['amount'],
            'dncn'     => $row['dncnd']
        ];
    }

    // =====================
    // FINAL JSON
    // =====================
    echo json_encode([
        "status"  => "success",
        "judul"   => $judul,
        "datacom" => $datacom,
        "data"    => $data
    ]);

} catch (Exception $e) {

    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}
?>