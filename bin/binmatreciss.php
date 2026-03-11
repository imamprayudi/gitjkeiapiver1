<?php

// ======================
// LOAD ENV + PDO
// ======================
$env = parse_ini_file(__DIR__ . '/../config/.env');

$dsn = "mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']};charset=utf8mb4";

$pdo = new PDO($dsn, $env['DB_USER'], $env['DB_PASSWORD'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$file = dirname(__DIR__) . "/uploads/vi07.txt";

try {

    $pdo->beginTransaction();

    // ======================
    // TRUNCATE TEMP TABLE
    // ======================
    $pdo->exec("TRUNCATE TABLE vi07temp");

    // ======================
    // PREPARE INSERT TEMP
    // ======================
    $sql = "INSERT INTO vi07temp
    (partno, tipe, tanggal, qty, po, suppcode, partname)
    VALUES
    (:partno,:tipe,:tanggal,:qty,:po,:suppcode,:partname)";

    $stmt = $pdo->prepare($sql);

    $handle = fopen($file,"r");

    $total = 0;

    while(($line = fgets($handle)) !== false)
    {
        $cols = explode(",", trim($line));

        if(count($cols) < 23) continue;

        $stmt->execute([
            ':partno' => trim($cols[0]),
            ':tipe' => trim($cols[2]),
            ':tanggal' => trim($cols[4]),
            ':qty' => trim($cols[5]),
            ':po' => trim($cols[8]),
            ':suppcode' => trim($cols[7]),
            ':partname' => trim($cols[22])
        ]);

        $total++;
    }

    fclose($handle);

    echo "Insert temp selesai: $total data\n";

    // ======================
    // DELETE DATA BULAN YANG SAMA DI TABLE FINAL
    // ======================
    $deleteSql = "
    DELETE FROM vi07
    WHERE DATE_FORMAT(tanggal,'%Y%m') IN (
        SELECT DISTINCT DATE_FORMAT(STR_TO_DATE(tanggal,'%Y%m%d'),'%Y%m')
        FROM vi07temp
    )
    ";

    $pdo->exec($deleteSql);

    echo "Delete bulan yang sama di vi07 selesai\n";

    // ======================
    // INSERT KE TABLE FINAL
    // ======================
    $sqlInsert = "
    INSERT INTO vi07
    (partno, tipe, tanggal, qty, po, suppcode, partname)
    SELECT
        TRIM(partno),
        TRIM(tipe),
        STR_TO_DATE(tanggal,'%Y%m%d'),
        CAST(REPLACE(qty,'.000','') AS SIGNED),
        TRIM(po),
        SUBSTRING(TRIM(suppcode),2),
        TRIM(partname)
    FROM vi07temp
    ";

    $pdo->exec($sqlInsert);

    echo "Insert ke vi07 selesai\n";

    if ($pdo->inTransaction()) {
        $pdo->commit();
    }

     if (file_exists($file)) {
      unlink($file);
    }

}
catch(Exception $e)
{
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo "ERROR: " . $e->getMessage();
}