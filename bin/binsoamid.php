<?php
/*
****  Application Name : soamidup.php (PDO FINAL FIX)
****  convert ADOdb -> PDO + fix datetime kosong
*/

echo 'Starting....Please wait....<br>';

date_default_timezone_set('Asia/Bangkok');
$startTime = date('Y-m-d H:i:s');
echo 'Starting Process : ' . $startTime . '<br>';


/* =========================
   HELPER FUNCTION
========================= */
// function parseDate($str)
// {
//     $str = trim($str);

//     if ($str === '' || strlen($str) < 8) {
//         return null; // <<< penting untuk MySQL
//     }

//     return substr($str,0,4).'-'.substr($str,4,2).'-'.substr($str,6,2);
// }

function toInt($v)
{
    $v = trim($v);
    return $v === '' ? 0 : (int)$v;
}

function toFloat($v)
{
    $v = trim($v);
    return $v === '' ? 0 : (float)$v;
}


/* =========================
   LOAD ENV + PDO CONNECT
========================= */
$env = parse_ini_file(__DIR__ . '/../config/.env');

$dsn = "mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']};charset=utf8mb4";

$pdo = new PDO(
    $dsn,
    $env['DB_USER'],
    $env['DB_PASSWORD'],
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);


/* =========================
   FILE CHECK
========================= */
$txtFile = dirname(__DIR__) . "/uploads/pdi01webmid.txt";

if (!file_exists($txtFile)) {
    exit("File tidak ditemukan");
}


/* =========================
   READ FIRST LINE (DATE)
========================= */
$fh = fopen($txtFile,'r');

$line = fgets($fh);

$transdate = parseDate(substr($line,0,10));

function parseDate($str)
{
    $str = trim($str);

    if ($str === '') return null;

    $time = strtotime($str);

    if ($time === false) return null;

    return date('Y-m-d', $time);
}

$transdate = parseDate(substr($line,0,10));

echo 'Transdate : ' . $transdate . '<br>';

try {
    $stmtDate = $pdo->prepare("INSERT INTO soamiddate(tanggal) VALUES (?)");
    $stmtDate->execute([$transdate]);
} catch (Exception $e) {
    echo 'Data tanggal sudah ada / skip...<br>';
}

fclose($fh);


/* =========================
   PREPARE INSERT
========================= */
$sql = "
INSERT INTO soamid (
transdate,hd,tm,blnthn,suppcode,ok,tgl,po,posq,
invoice,partno,partname,qty,price,amount,dncnd,lastpay,
purchase,dncns,netpur,vat,salesvat,payment,this,col027,col028,
video,term15,term30,term45,term60
)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
";

$stmt = $pdo->prepare($sql);


/* =========================
   LOOP FILE + TRANSACTION
========================= */
$pdo->beginTransaction();

$fh = fopen($txtFile, 'r');

$hitung = 0;

while (($line = fgets($fh)) !== false) {

    if (trim($line) === '') continue;

    set_time_limit(0);

    $data = [
    parseDate(substr($line,0,10)),
    trim(substr($line,10,2)),
    trim(substr($line,12,2)),
    trim(substr($line,14,7)),
    trim(substr($line,22,7)),
    trim(substr($line,30,1)),
    parseDate(substr($line,31,8)),
    trim(substr($line,39,7)),
    trim(substr($line,46,2)),
    trim(substr($line,48,15)),
    trim(substr($line,63,15)),
    trim(substr($line,78,20)),

    toInt(substr($line,98,8)),        // ✅ qty
    toFloat(substr($line,106,10)),    // ✅ price
    toFloat(substr($line,116,15)),    // ✅ amount
    toFloat(substr($line,131,15)),    // dncnd
    toFloat(substr($line,146,15)),    // lastpay
    toFloat(substr($line,161,15)),    // purchase
    toFloat(substr($line,176,15)),    // dncns
    toFloat(substr($line,191,15)),    // netpur
    toFloat(substr($line,206,15)),    // vat
    toFloat(substr($line,221,15)),    // salesvat
    toFloat(substr($line,236,15)),    // payment
    toFloat(substr($line,252,13)),

    trim(substr($line,265,25)),
    trim(substr($line,290,13)),
    trim(substr($line,303,1)),
    toFloat(substr($line,304,15)),
    toFloat(substr($line,319,15)),
    toFloat(substr($line,334,15)),
    toFloat(substr($line,349,15))
];

    $stmt->execute($data);
    $hitung++;
}

fclose($fh);

$pdo->commit();


/* =========================
   DELETE FILE
========================= */
unlink($txtFile);


/* =========================
   FINISH
========================= */
echo 'Finish jumlah record : ' . $hitung . '<br>';
echo 'End Process : ' . date('Y-m-d H:i:s');
?>