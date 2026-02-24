<?php
/*
 *
** Programmer: Imam Prayudi
** Date Created: 2026-02-24
** Purpose: Update data Big Part dari text file
** PDO version
*/

set_time_limit(0);

/* ======================
   KONEKSI PDO
====================== */

$env = parse_ini_file(__DIR__ . '/../config/.env');

// ===== koneksi PDO =====
$host = $env['DB_HOST'];
$db   = $env['DB_NAME'];     
$user = $env['DB_USER'];    
$pass = $env['DB_PASSWORD'];      
$charset = "utf8mb4";

$dsn = "mysql:host=$host;db=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$pdo = new PDO($dsn, $user, $pass, $options);

try {

    /* ======================
       TRANSACTION START
    ====================== */
    $pdo->beginTransaction();

    /* ======================
       DELETE TEMP
    ====================== */
    $pdo->exec("DELETE FROM bpstemp");

    /* ======================
       PREPARE INSERT TEMP
    ====================== */
    $columns = 40;
    $placeholders = implode(',', array_fill(0, $columns, '?'));

    $sql = "INSERT INTO bpstemp(
      transdate,hd,tm,suppcode,partno,partname,balqty,
      qty1,qty2,qty3,qty4,qty5,qty6,qty7,qty8,qty9,qty10,
      qty11,qty12,qty13,qty14,qty15,qty16,qty17,qty18,qty19,qty20,
      qty21,qty22,qty23,qty24,qty25,qty26,qty27,qty28,qty29,qty30,
      qty31,scold,scnew
    ) VALUES ($placeholders)";

    $stmt = $pdo->prepare($sql);

    /* ======================
       READ FILE
    ====================== */
    $txtFile = dirname(__DIR__) . "/uploads/pbh01web.txt";
    $fh = fopen($txtFile, 'r');

    while ($line = fgets($fh)) {

        $data = [
            substr($line,6,4).'-'.substr($line,0,2).'-'.substr($line,3,2),
            substr($line,10,2),
            substr($line,12,2),
            substr($line,14,8),
            substr($line,22,15),
            substr($line,37,15),
            substr($line,52,9),

            substr($line,61,9), substr($line,70,9), substr($line,79,9), substr($line,88,9),
            substr($line,97,9), substr($line,106,9), substr($line,115,9), substr($line,124,9),
            substr($line,133,9), substr($line,142,9),

            substr($line,151,9), substr($line,160,9), substr($line,169,9), substr($line,178,9),
            substr($line,187,9), substr($line,196,9), substr($line,205,9), substr($line,214,9),
            substr($line,223,9), substr($line,232,9),

            substr($line,241,9), substr($line,250,9), substr($line,259,9), substr($line,268,9),
            substr($line,277,9), substr($line,286,9), substr($line,295,9), substr($line,304,9),
            substr($line,313,9), substr($line,322,9),

            substr($line,331,9),
            substr($line,340,9),
            substr($line,349,12)
        ];

        $stmt->execute($data);
    }

    fclose($fh);

    /* ======================
       CEK DATA
    ====================== */
    $cek = $pdo->query("SELECT transdate, qty1 FROM bpstemp WHERE hd='H' AND tm='H' LIMIT 1")->fetch();

    $transdate = substr($cek['transdate'],0,10);
    $bpstglbln = $cek['qty1'];

    $bpstgl = substr($bpstglbln,0,2);
    $bpsbln = substr($bpstglbln,3,2);
    $bpsdate = $bpsbln.$bpstgl;

    echo "transdate : $transdate<br>";
    echo "bpsdate : $bpsdate<br>";

    /* ======================
       INSERT BPSDATE
    ====================== */
    $stmt = $pdo->prepare("INSERT INTO bpsdate(bpsdate,transdate) VALUES(?,?)");
    $stmt->execute([$bpsdate, $transdate]);

    /* ======================
       INSERT FINAL
    ====================== */
    $pdo->exec("
        INSERT INTO bps
        SELECT * FROM bpstemp
    ");

    /* ======================
       COMMIT
    ====================== */
    $pdo->commit();

    echo "connection close";

    if (file_exists($txtFile)) {
      unlink($txtFile);
    }

$pdo = null;
echo "Insert bps from pbh01web.txt success";

    

} catch (Exception $e) {

    $pdo->rollBack();
    die($e->getMessage());
}

?>