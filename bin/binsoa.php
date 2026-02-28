<?php
echo '<br />Starting....Please wait....';

date_default_timezone_set('Asia/Bangkok');
$tgljam = date('Y-m-d H:i:s');

echo '<br />Starting Process : ' . $tgljam . '<br />';

$env = parse_ini_file(__DIR__ . '/../config/.env');

// ===== koneksi PDO =====
$host = $env['DB_HOST'];
$db   = $env['DB_NAME'];     
$user = $env['DB_USER'];    
$pass = $env['DB_PASSWORD'];      
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$pdo = new PDO($dsn, $user, $pass, $options);

$lanjut = true;

/* ======================
   INSERT SOADATE (baris pertama)
====================== */
$txtFile = dirname(__DIR__) . "/uploads/pdh01web.txt";

if (!file_exists($txtFile)) {
    exit("File tidak ditemukan");
}

$fh = fopen($txtFile,'r');
$line = fgets($fh);

$transdate = substr($line,0,4).'-'.substr($line,5,2).'-'.substr($line,8,2);

echo '<br />' . $transdate . '<br />';

try {

    $stmtDate = $pdo->prepare("INSERT INTO soadate(tanggal) VALUES (?)");
    $stmtDate->execute([$transdate]);

} catch (Exception $e) {

    $lanjut = false;
    echo ' ada error / data sudah ada...<br />';
}

fclose($fh);

$fh = fopen($txtFile, 'r');
$line = fgets($fh);

$transdate = substr($line,0,4).'-'.substr($line,5,2).'-'.substr($line,8,2);

echo '<br />Tanggal : ' . $transdate . '<br />';

try {
    $stmtDate = $pdo->prepare("INSERT INTO soadate(tanggal) VALUES (?)");
    $stmtDate->execute([$transdate]);
} catch (Exception $e) {
    echo 'Tanggal sudah ada / skip<br />';
}

fclose($fh);


/* =========================
   PREPARE INSERT (1x saja)
========================= */
$sql = "
INSERT INTO soa(
transdate,hd,tm,blnthn,suppcode,ok,tgl,po,posq,
invoice,partno,partname,qty,price,amount,dncnd,lastpay,
purchase,dncns,netpur,vat,salesvat,payment,balance,
col025,col026,col027
) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
";

$stmt = $pdo->prepare($sql);


/* =========================
   LOOP INSERT
========================= */
$pdo->beginTransaction(); // 🔥 super cepat

$hitung = 0;

$fh = fopen($txtFile, 'r');

while (($line = fgets($fh)) !== false) {

    $hitung++;

    $num = fn($v) => (float)(trim($v) ?: 0); // helper numeric

    $data = [
        substr($line,0,4).'-'.substr($line,5,2).'-'.substr($line,8,2),
        trim(substr($line,10,2)),
        trim(substr($line,12,2)),
        trim(substr($line,14,7)),
        trim(substr($line,22,7)),
        trim(substr($line,30,1)),
        trim(substr($line,31,8)),
        trim(substr($line,39,7)),
        trim(substr($line,46,2)),
        trim(substr($line,48,15)),
        trim(substr($line,63,15)),
        trim(substr($line,78,20)),

        (int)$num(substr($line,98,7)),   // qty
        $num(substr($line,106,10)),      // price
        $num(substr($line,116,15)),
        $num(substr($line,131,15)),
        $num(substr($line,146,15)),
        $num(substr($line,161,15)),
        $num(substr($line,176,15)),
        $num(substr($line,191,15)),
        $num(substr($line,206,15)),
        $num(substr($line,221,15)),
        $num(substr($line,236,15)),
        $num(substr($line,251,15)),

        trim(substr($line,266,15)),
        trim(substr($line,291,12)),
        trim(substr($line,303,9))
    ];

    $stmt->execute($data);
}

$pdo->commit(); // 🔥 commit sekali saja

unlink($txtFile);

/* =========================
   FINISH
========================= */
$end = date('Y-m-d H:i:s');

echo '<br />Finish jumlah record : ' . $hitung;
echo '<br />End Process : ' . $end;
?>