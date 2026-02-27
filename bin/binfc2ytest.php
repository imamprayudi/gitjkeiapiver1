<?php
/*
**** convert to PDO version
**** Imam Prayudi
*/
set_time_limit(0);
// ======================
// KONEKSI PDO
// ======================
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

// ======================
// PATH FILE
// ======================
$txtFile = dirname(__DIR__) . "/uploads/pnb01web.txt";

if (!file_exists($txtFile)) {
    exit("File tidak ditemukan");
}

try {

    // =====================================================
    // START TRANSACTION (SATU KALI SAJA)
    // =====================================================
    $pdo->beginTransaction();


    // ======================
    // CLEAR TEMP
    // ======================
    $pdo->exec("DELETE FROM fc2ytemp");


    // ======================
    // PREPARE INSERT TEMP
    // ======================
    $columns = [
        'transdate','rt','suppcode','subsuppcode','subsuppname',
        'partno','partname','leadtime'
    ];

    for($i=1;$i<=53;$i++) $columns[]="dt1qt$i";
    for($i=1;$i<=53;$i++) $columns[]="dt2qt$i";
    for($i=1;$i<=53;$i++) $columns[]="dt3qt$i";
    for($i=1;$i<=53;$i++) $columns[]="dt4qt$i";

    $columns[]='webcode';

    $placeholders = array_fill(0, count($columns), '?');

    $sql = "INSERT INTO fc2ytemp (".implode(',', $columns).")
            VALUES (".implode(',', $placeholders).")";

    $stmt = $pdo->prepare($sql);


    // ======================
    // BACA FILE + INSERT
    // ======================
    $fh = fopen($txtFile, 'r');

    while ($line = fgets($fh)) {

        $data = [];

        $data[] = substr($line,6,4).'-'.substr($line,0,2).'-'.substr($line,3,2);
        $data[] = substr($line,10,2);
        $data[] = substr($line,12,5);
        $data[] = substr($line,17,11);
        $data[] = trim(substr($line,28,40));
        $data[] = trim(substr($line,68,15));
        $data[] = trim(substr($line,83,15));
        $data[] = trim(substr($line,98,8));

        $start = 106;

        for($i=0;$i<53*4;$i++){
            $data[] = trim(substr($line, $start + ($i*9), 9));
        }

        $data[] = trim(substr($line,2014,20));

        $stmt->execute($data);
    }

    fclose($fh);


    // ======================
    // CLEANING
    // ======================
    $pdo->exec("DELETE FROM fc2ytemp WHERE transdate LIKE '%EO%'");


    // ======================
    // VALIDASI TRANS DATE
    // ======================
    $insertDate = $pdo->exec("
        INSERT IGNORE INTO fc2ydate (transdate)
        SELECT DISTINCT transdate FROM fc2ytemp
    ");

    $totalTemp = $pdo->query("
        SELECT COUNT(DISTINCT transdate) FROM fc2ytemp
    ")->fetchColumn();


    if ($insertDate != $totalTemp) {

        // DUPLIKAT → BATAL
        $pdo->rollBack();

        unlink($txtFile);

        exit("Transdate sudah pernah diproses. Import dibatalkan.");
    }


    // ======================
    // COPY FINAL
    // ======================
    $pdo->exec("DELETE FROM fc2y");
    $pdo->exec("INSERT INTO fc2y SELECT * FROM fc2ytemp");
    $pdo->exec("INSERT INTO fc2yarc SELECT * FROM fc2ytemp");

    // ======================
    // COMMIT (HANYA SEKALI)
    // ======================
    $pdo->commit();


    unlink($txtFile);

    echo "Insert fc2y SUCCESS<br>DONE";
}
catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo "ERROR : " . $e->getMessage();
}
?>