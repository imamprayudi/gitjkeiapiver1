<?php
/*
========================================================
soaendup.php  (FINAL FIXED + STABLE)
PDO + Header Transdate Only
========================================================
*/

echo "<br>Starting....Please wait....<br>";

date_default_timezone_set('Asia/Bangkok');
echo "Starting Process : ".date('Y-m-d H:i:s')."<br>";


// =====================================================
// LOAD ENV
// =====================================================
$env = parse_ini_file(__DIR__ . '/../config/.env');

$dsn = "mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$pdo = new PDO($dsn,$env['DB_USER'],$env['DB_PASSWORD'],$options);


// =====================================================
// FILE PATH
// =====================================================
$txtFile = dirname(__DIR__) . "/uploads/pdi01webend.txt";

if(!file_exists($txtFile)){
    die("File tidak ditemukan");
}


// =====================================================
// HELPER FUNCTIONS
// =====================================================
function fw($line,$start,$len){
    return trim(substr($line,$start,$len));
}

function parseInt($v){
    $v = trim($v);
    return $v==='' ? null : (int)$v;
}

function parseFloat($v){
    $v = trim($v);
    return $v==='' ? null : (float)$v;
}

/*
Support:
20260131
12/01/25
2026-01-31
*/
function parseDateFlex($str)
{
    $str = trim($str);

    if($str=='' || $str=='00000000') return null;

    // YYYY-MM-DD
    if(strpos($str,'-')!==false){
        $t = strtotime($str);
        return $t ? date('Y-m-d 00:00:00',$t) : null;
    }

    // DD/MM/YY
    if(strpos($str,'/')!==false){
        $t = strtotime(str_replace('/','-',$str));
        return $t ? date('Y-m-d 00:00:00',$t) : null;
    }

    // YYYYMMDD
    if(strlen($str)==8){
        $y=substr($str,0,4);
        $m=substr($str,4,2);
        $d=substr($str,6,2);

        if(checkdate((int)$m,(int)$d,(int)$y))
            return "$y-$m-$d 00:00:00";
    }

    return null;
}


// =====================================================
// AMBIL HEADER TRANSDATE (PENTING 🔥)
// =====================================================
$fh = fopen($txtFile,'r');
$firstLine = fgets($fh);

$transdate = parseDateFlex(substr($firstLine,0,10));

if(!$transdate){
    die("Transdate header tidak valid!");
}

echo "Header Transdate : $transdate <br>";

$stmtDate = $pdo->prepare("
    INSERT IGNORE INTO soaenddate(tanggal)
    VALUES(?)
");
$stmtDate->execute([$transdate]);

fclose($fh);


// =====================================================
// PREPARE INSERT DETAIL
// =====================================================
$columns = [
'transdate','hd','tm','blnthn','suppcode','ok','tgl','po','posq',
'invoice','partno','partname','qty','price','amount','dncnd','lastpay',
'purchase','dncns','netpur','vat','salesvat','payment','this','col027','col028',
'video','term15','term30','term45','term60','term75','term90','termtotal'
];

$placeholders = rtrim(str_repeat('?,',count($columns)),',');

$sql = "INSERT INTO soaend(".implode(',',$columns).") VALUES($placeholders)";
$stmt = $pdo->prepare($sql);


// =====================================================
// LOOP IMPORT
// =====================================================
$fh = fopen($txtFile,'r');
fgets($fh); // skip header

$pdo->beginTransaction();
$hitung = 0;

try{

while(($line=fgets($fh))){

    $hitung++;

    $data = [

        // 🔥 PAKAI HEADER DATE (BUKAN PARSE ULANG)
        $transdate,

        fw($line,10,2),
        fw($line,12,2),
        fw($line,14,7),
        fw($line,22,7),
        fw($line,30,1),

        parseDateFlex(substr($line,31,8)),

        fw($line,39,7),
        fw($line,46,2),
        fw($line,48,15),
        fw($line,63,15),
        fw($line,78,20),

        parseInt(substr($line,98,8)),

        parseFloat(substr($line,106,10)),
        parseFloat(substr($line,116,15)),
        parseFloat(substr($line,131,15)),
        parseFloat(substr($line,146,15)),
        parseFloat(substr($line,161,15)),
        parseFloat(substr($line,176,15)),
        parseFloat(substr($line,191,15)),
        parseFloat(substr($line,206,15)),
        parseFloat(substr($line,221,15)),
        parseFloat(substr($line,236,15)),

        parseFloat(substr($line,252,13)),
        fw($line,265,25),
        fw($line,290,12),

        fw($line,303,1),

        parseFloat(substr($line,304,15)),
        parseFloat(substr($line,319,15)),
        parseFloat(substr($line,334,15)),
        parseFloat(substr($line,349,15)),
        parseFloat(substr($line,364,15)),
        parseFloat(substr($line,379,15)),
        parseFloat(substr($line,394,15))
    ];

    $stmt->execute($data);
}

$pdo->commit();

}catch(Exception $e){
    $pdo->rollBack();
    die("Error saat insert : ".$e->getMessage());
}

fclose($fh);
unlink($txtFile);


// =====================================================
echo "Finish jumlah record : $hitung <br>";
echo "End Process : ".date('Y-m-d H:i:s');
?>