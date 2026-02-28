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

/* =========================
   CONNECT PDO
========================= */
$env = parse_ini_file(__DIR__ . '/../config/.env');

// ===== koneksi PDO =====
$host = $env['DB_HOST'];
$db   = $env['DB_NAME'];     
$user = $env['DB_USER'];    
$pass = $env['DB_PASSWORD'];      
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$pdo = new PDO($dsn, $env['DB_USER'], $env['DB_PASSWORD'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

/* =========================
   ONLY POST
========================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error"]);
    exit();
}

$suppid = trim($_POST['supp'] ?? '');
$tgl    = trim($_POST['tgl'] ?? '');


/* =========================
   QUERY
========================= */
$qryjudul = "
SELECT blnthn,suppcode,lastpay,purchase,
dncns,netpur,vat,salesvat,payment,this,
term15,term30,term45,term60,term75,term90,termtotal
FROM soamid
WHERE hd='H' AND suppcode=? AND transdate=?
";

$qrydata = "
SELECT blnthn,suppcode,ok,tgl,po,posq,
invoice,partno,partname,qty,price,amount,dncnd
FROM soamid
WHERE hd='D' AND suppcode=? AND transdate=?
ORDER BY invoice, ok, tgl, po, posq
";

$qrycom = "
SELECT blnthn,suppcode,suppcom,jeincom
FROM soacommid
WHERE blnthn=? AND suppcode=?
";

function nf($v, $d = 2) {
    return number_format((float)($v ?? 0), $d, '.', '');
}
/* =========================
   HEADER DATA (judul)
========================= */
$stmt = $pdo->prepare($qryjudul);
$stmt->execute([$suppid, $tgl]);

$row = $stmt->fetch();

$judul = [];

if ($row) {

    $judul[] = [
        'blnthn'   => $row['blnthn'],
        'supp'     => $row['suppcode'],
        'lastpay' => nf($row['lastpay']),
        'purchase' => nf($row['purchase']),
        'dncn' => nf($row['dncns']),
        'netpur' => nf($row['netpur']),
        'vat' => nf($row['vat']),
        'salesvat' => nf($row['salesvat']),
        'payment' => nf($row['payment']),
        'this' => nf($row['this']),
        'term15' => number_format($rsjudul->fields[10] ?? 0, 2, '.', ''),
        'term30' => number_format($rsjudul->fields[11] ?? 0, 2, '.', ''),
        'term45' => number_format($rsjudul->fields[12] ?? 0, 2, '.', ''),
        'term60' => number_format($rsjudul->fields[13] ?? 0, 2, '.', ''),
        'term75' => number_format($rsjudul->fields[14] ?? 0, 2, '.', ''),
        'term90' => number_format($rsjudul->fields[15] ?? 0, 2, '.', ''),
        'termtotal' => number_format($rsjudul->fields[16] ?? 0, 2, '.', ''),
            ];

    $blnthn = $row['blnthn'];
} else {
    $blnthn = '';
}


/* =========================
   COMMENT DATA
========================= */
$stmt = $pdo->prepare($qrycom);
$stmt->execute([$blnthn, $suppid]);

$com = $stmt->fetch();

$datacom = [];

if (!$com) {
    $datacom[] = [
        'blnthn' => $blnthn,
        'suppcode' => $suppid,
        'suppcom' => '',
        'jeincom' => ''
    ];
} else {
    $datacom[] = $com;
}


/* =========================
   DETAIL DATA
========================= */
$stmt = $pdo->prepare($qrydata);
$stmt->execute([$suppid, $tgl]);

$data = [];

while ($r = $stmt->fetch()) {

    $data[] = [
        'blnthn'   => $r['blnthn'],
        'supp'     => $r['suppcode'],
        'ok'       => $r['ok'],
        'tgl'      => $r['tgl'],
        'po'       => $r['po'],
        'sq'       => $r['posq'],
        'invoice'  => $r['invoice'],
        'partno'   => $r['partno'],
        'partname' => $r['partname'],
        'qty'      => $r['qty'],
        'price'    => number_format($r['price'], 4, '.', ''),
        'amount'   => number_format($r['amount'], 2, '.', ''),
        'dncn'     => number_format($r['dncnd'], 2, '.', '')
    ];
}


/* =========================
   RESPONSE JSON (SAMA PERSIS)
========================= */
echo json_encode([
    "status"  => "success",
    "judul"   => $judul,
    "datacom" => $datacom,
    "data"    => $data
]);