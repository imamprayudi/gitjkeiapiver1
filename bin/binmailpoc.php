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


// ===== file TXT =====
$file = dirname(__DIR__) . "/uploads/mailpoctoday.txt";

if (($handle = fopen($file, "r")) !== FALSE) {

    $sql = "INSERT IGNORE INTO mailpoc
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

     if (file_exists($file)) {
      unlink($file);
    }
}

echo "Import mailpoctoday selesai";