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

// ===============================
// 1. DELETE DATA MAILPOTODAY
// ===============================
$pdo->exec("DELETE FROM mailpotoday");

// ===============================
// 2. IMPORT TXT KE MAILPOTODAY
// ===============================
$file = dirname(__DIR__) . "/uploads/mailpotoday.txt";

if (($handle = fopen($file, "r")) !== FALSE) {

    $sql = "INSERT INTO mailpotoday
    (idno,rdate,rtime,supplier,suppliername,actioncode,pono,partno,partname,newqty,newdate,price,model,potype)
    VALUES
    (:idno,:rdate,:rtime,:supplier,:suppliername,:actioncode,:pono,:partno,:partname,:newqty,:newdate,:price,:model,:potype)";

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
            ':price' => $data[11],
            ':model' => $data[12],
            ':potype' => $data[13]
        ]);
    }

    fclose($handle);
}

// ===============================
// 3. INSERT MAILPOTODAY -> MAILPO
// ===============================
$sqlInsert = "
INSERT IGNORE INTO mailpo
(idno,rdate,rtime,supplier,suppliername,actioncode,pono,partno,partname,newqty,newdate,price,model,potype)
SELECT
idno,rdate,rtime,supplier,suppliername,actioncode,pono,partno,partname,newqty,newdate,price,model,potype
FROM mailpotoday
";

$pdo->exec($sqlInsert);

// ===============================
// 4. HAPUS FILE TXT
// ===============================
if (file_exists($file)) {
    unlink($file);
}

echo "Import mailpotoday selesai";

?>
