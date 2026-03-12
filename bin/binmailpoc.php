<?php

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

// ============================
// 1. DELETE MAILPOCTODAY
// ============================
$pdo->exec("DELETE FROM mailpoctoday");

// ============================
// 2. IMPORT TXT KE MAILPOCTODAY
// ============================
$file = dirname(__DIR__) . "/uploads/mailpoctoday.txt";

if (($handle = fopen($file, "r")) !== FALSE) {

    $sql = "INSERT INTO mailpoctoday
    (idno,rdate,rtime,supplier,suppliername,actioncode,pono,partno,partname,newqty,newdate,oldqty,olddate,price,model,potype,altno)
    VALUES
    (:idno,:rdate,:rtime,:supplier,:suppliername,:actioncode,:pono,:partno,:partname,:newqty,:newdate,:oldqty,:olddate,:price,:model,:potype,:altno)";

    $stmt = $pdo->prepare($sql);

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

        $stmt->execute([
            ':idno' => $data[0],
            ':rdate' => $data[1],
            ':rtime' => $data[2],
            ':supplier' => $data[3],
            ':suppliername' => $data[4],
            ':actioncode' => $data[5],
            ':pono' => $data[6],
            ':partno' => $data[7],
            ':partname' => $data[8],
            ':newqty' => $data[9],
            ':newdate' => $data[10],
            ':oldqty' => $data[11],
            ':olddate' => $data[12],
            ':price' => $data[13],
            ':model' => $data[14],
            ':potype' => $data[15],
            ':altno' => $data[16]
        ]);
    }

    fclose($handle);
}

// ============================ 
// 3. UPDATE STATUS 
// ============================ 

$pdo->exec(" UPDATE mailpoctoday 
SET status = CASE 
WHEN newqty = 0 THEN 'CANCELED' 
WHEN newqty < oldqty THEN 'PO DOWN' 
WHEN newqty > oldqty THEN 'PO UP'
WHEN newqty = oldqty AND newdate < olddate THEN 'ADVANCED' 
WHEN newqty = oldqty AND newdate > olddate THEN 'DELAYED' 
ELSE 'NO CHANGE' END ");

// ============================
// 3. INSERT MAILPOCTODAY → MAILPOC
// ============================
$sqlInsert = "
INSERT IGNORE INTO mailpoc
(idno,rdate,rtime,supplier,suppliername,actioncode,pono,partno,partname,newqty,newdate,oldqty,olddate,price,model,potype,altno,status)
SELECT
idno,rdate,rtime,supplier,suppliername,actioncode,pono,partno,partname,newqty,newdate,oldqty,olddate,price,model,potype,altno,status
FROM mailpoctoday
";

$pdo->exec($sqlInsert);

// ============================
// 4. HAPUS FILE TXT
// ============================
if (file_exists($file)) {
    unlink($file);
}

echo "Import mailpoctoday selesai";

?>

