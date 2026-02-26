<?php
/*
** Programmer: Imam Prayudi
** Date Created: 2026-02-26
** Purpose: Update data Time Delivery Schedule dari text file
** convert to PDO version
*/


$env = parse_ini_file(__DIR__ . '/../config/.env');

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

try {

    // ======================
    // TRANSACTION
    // ======================
    $pdo->beginTransaction();

    // ======================
    // DELETE TEMP
    // ======================
    $pdo->exec("DELETE FROM tdstemp");

    // ======================
    // PREPARE INSERT
    // ======================
    $cols = [];
    for($i=1;$i<=32;$i++){
        $cols[] = "qty$i";
    }

    $colList = implode(',', $cols);

    $placeholders = implode(',', array_fill(0, 40, '?'));
    // 7 field + 32 qty = 39

    $sql = "
        INSERT INTO tdstemp(
            transdate,hd,tm,suppcode,partno,partname,balqty,
            $colList, scold
        ) VALUES ($placeholders)
    ";

    $stmt = $pdo->prepare($sql);

    // ======================
    // READ FILE
    // ======================
    $txtFile = dirname(__DIR__) . "/uploads/pbh01tds.txt";
    $fh = fopen($txtFile, 'r');

    while ($line = fgets($fh)) {

        set_time_limit(0);

        $transdate = substr($line,6,4).'-'.substr($line,0,2).'-'.substr($line,3,2);

        $values = [
            $transdate,
            substr($line,10,2),
            substr($line,12,2),
            trim(substr($line,14,8)),
            trim(substr($line,22,15)),
            trim(substr($line,37,15)),
            trim(substr($line,52,9))
        ];

        // qty1-qty32
        for($i=0;$i<32;$i++){
            $values[] = trim(substr($line,61+($i*9),9));
        }

        // scold
        $values[] = trim(substr($line,349,10));

        $stmt->execute($values);
    }

    fclose($fh);

    // ======================
    // CHECK TDS UPDATED
    // ======================
    $cek = $pdo->query("
        SELECT transdate, qty1
        FROM tdstemp
        WHERE hd='H' AND tm='H'
        LIMIT 1
    ")->fetch(PDO::FETCH_NUM);

    $transdate = substr($cek[0],0,10);
    $tdstglbln = $cek[1];

    echo 'transdate : ' . $transdate . '<br>';

    $tdsyear = substr($transdate,0,4);
    $tdstgl = substr($tdstglbln,0,2);
    $tdsbln = substr($tdstglbln,3,2);
    $tdsdate = $tdsyear.$tdsbln.$tdstgl;
    echo 'tdsdate : ' . $tdsdate . '<br>';

    // ======================
    // INSERT DATE
    // ======================
    $stmt = $pdo->prepare("INSERT INTO tdsdate(tdsdate,transdate) VALUES(?,?)");
    $stmt->execute([$tdsdate,$transdate]);

    // ======================
    // COPY TO FINAL TABLE
    // ======================
    $pdo->exec("INSERT INTO tds SELECT * FROM tdstemp");

    // ======================
    // COMMIT
    // ======================
    $pdo->commit();

    if (file_exists($txtFile)) {
      unlink($txtFile);
    }
    echo 'Insert tds from pbh01tds.txt success<br>';

} catch(Exception $e){

    $pdo->rollBack();
    echo "Error : " . $e->getMessage();
}
?>