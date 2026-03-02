<?php
/*
=====================================================
soaend_api.php
ADODB ➜ PDO VERSION
Output JSON sama persis
=====================================================
*/

header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-cache");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit(json_encode(["status"=>"error","msg"=>"POST only"]));
}

date_default_timezone_set('Asia/Bangkok');


// =====================================================
// PDO CONNECTION
// =====================================================
$env = parse_ini_file(__DIR__ . '/../config/.env');

$dsn = "mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']};charset=utf8mb4";

$pdo = new PDO($dsn,$env['DB_USER'],$env['DB_PASSWORD'],[
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);


// =====================================================
// INPUT
// =====================================================
$suppid = trim($_POST['supp'] ?? '');
$tgl    = trim($_POST['tgl'] ?? '');

if(!$suppid || !$tgl){
    exit(json_encode(["status"=>"error","msg"=>"Parameter kosong"]));
}

$tahunsoa = substr($tgl,0,4);
$tablesoa = ($tahunsoa < 2020) ? "soaend$tahunsoa" : "soaend";

function nf($v, $dec = 2){
    return number_format((float)($v ?? 0), $dec, '.', '');
}
// =====================================================
// QUERY JUDUL
// =====================================================
$qryjudul = "
SELECT blnthn,suppcode,lastpay,purchase,
dncns,netpur,vat,salesvat,payment,this,term15,term30,
term45,term60,term75,term90,termtotal
FROM $tablesoa
WHERE hd='H' AND suppcode=? AND transdate=?
";

$stmt = $pdo->prepare($qryjudul);
$stmt->execute([$suppid,$tgl]);
$r = $stmt->fetch();

$judul = [];

if($r){
    $judul[] = [
        'blnthn'=>$r['blnthn'],
        'supp'=>$r['suppcode'],
        'lastpay'=>nf($r['lastpay'],2,'.',''),
        'purchase'=>nf($r['purchase'],2,'.',''),
        'dncn'=>nf($r['dncns'],2,'.',''),
        'netpur'=>nf($r['netpur'],2,'.',''),
        'vat'=>nf($r['vat'],2,'.',''),
        'salesvat'=>nf($r['salesvat'],2,'.',''),
        'payment'=>nf($r['payment'],2,'.',''),
        'this'=>nf($r['this'],2,'.',''),
        'term15'=>nf($r['term15'],2,'.',''),
        'term30'=>nf($r['term30'],2,'.',''),
        'term45'=>nf($r['term45'],2,'.',''),
        'term60'=>nf($r['term60'],2,'.',''),
        'term75'=>nf($r['term75'],2,'.',''),
        'term90'=>nf($r['term90'],2,'.',''),
        'termtotal'=>nf($r['termtotal'],2,'.','')
    ];
}

$blnthn = $r['blnthn'] ?? '';


// =====================================================
// QUERY COMMENT
// =====================================================
$qrycom = "
SELECT blnthn,suppcode,suppcom,jeincom
FROM soacomend
WHERE blnthn=? AND suppcode=?
";

$stmt = $pdo->prepare($qrycom);
$stmt->execute([$blnthn,$suppid]);

$r = $stmt->fetch();

$datacom = [];

if(!$r){
    $datacom[]=[
        'blnthn'=>$blnthn,
        'suppcode'=>$suppid,
        'suppcom'=>'',
        'jeincom'=>''
    ];
}else{
    $datacom[]=$r;
}


// =====================================================
// QUERY DETAIL
// =====================================================
$qrydata = "
SELECT blnthn,suppcode,ok,tgl,po,posq,
invoice,partno,partname,qty,price,amount,dncnd
FROM $tablesoa
WHERE hd='D' AND suppcode=? AND transdate=?
ORDER BY invoice,ok,tgl,po,posq
";

$stmt = $pdo->prepare($qrydata);
$stmt->execute([$suppid,$tgl]);

$data = [];

while($r=$stmt->fetch()){
    $data[]=[
        'blnthn'=>$r['blnthn'],
        'supp'=>$r['suppcode'],
        'ok'=>$r['ok'],
        'tgl'=>$r['tgl'],
        'po'=>$r['po'],
        'sq'=>$r['posq'],
        'invoice'=>$r['invoice'],
        'partno'=>$r['partno'],
        'partname'=>$r['partname'],
        'qty'=>$r['qty'],
        'price'=>nf($r['price'],4,'.',''),
        'amount'=>nf($r['amount'],2,'.',''),
        'dncn'=>nf($r['dncnd'],2,'.','')
    ];
}


// =====================================================
// OUTPUT JSON (SAMA PERSIS FORMAT LAMA)
// =====================================================
echo json_encode([
    "status"=>"success",
    "judul"=>$judul,
    "datacom"=>$datacom,
    "data"=>$data
]);