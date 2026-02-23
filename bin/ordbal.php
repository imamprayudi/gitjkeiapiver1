<?php
/*
** Programmer: Imam Prayudi
** Date Created: 2026-02-23
** Purpose: Update data ordbal dari text file
** convert to PDO version
*/

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

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    echo "delete data<br>";

    // ===== delete data =====
    $pdo->exec("DELETE FROM ordbal");

    // ===== prepare insert (HANYA SEKALI) =====
    $stmt = $pdo->prepare("
        INSERT INTO ordbal (
            transdate, suppcode, partnumber, partname,
            orderqty, reqdate, ponumber, posq,
            orderbalance, supprest, model, issuedate,
            potype, statuspart, remark
        )
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    ");

    // ===== baca file =====
    $txtFile = dirname(__DIR__) . "/uploads/PCK11WEB.txt";
    $fh = fopen($txtFile, 'r');

    while (($line = fgets($fh)) !== false) {

        set_time_limit(0);
        if (!preg_match('/^\d{2}\/\d{2}\/\d{4}/', $line)) {
        continue;
        }
        $transdate   = substr($line,6,4).'-'.substr($line,0,2).'-'.substr($line,3,2);
        $suppcode    = trim(substr($line,10,8));
        $partnumber  = substr($line,18,15);
        $partname    = substr($line,33,15);
        $orderqty    = substr($line,53,4);
        $reqdate     = substr($line,63,4).'-'.substr($line,57,2).'-'.substr($line,60,2);
        $ponumber    = trim(substr($line,67,7));
        $posq        = substr($line,74,2);
        $orderbalance= substr($line,76,9);
        $supprest    = substr($line,85,9);
        $model       = substr($line,94,15);
        $issuedate   = substr($line,115,4).'-'.substr($line,109,2).'-'.substr($line,112,2);
        $potype      = trim(substr($line,119,30));
        $statuspart  = substr($line,149,3);
        $remark      = trim(substr($line,152,12));

        // execute insert
        $stmt->execute([
            $transdate,
            $suppcode,
            $partnumber,
            $partname,
            $orderqty,
            $reqdate,
            $ponumber,
            $posq,
            $orderbalance,
            $supprest,
            $model,
            $issuedate,
            $potype,
            $statuspart,
            $remark
        ]);
    }

    fclose($fh);

    echo "connection close";
    if (file_exists($txtFile)) {
      unlink($txtFile);
    }
    $pdo = null;

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>