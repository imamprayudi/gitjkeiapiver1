<?php
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {

    // ======================
    // LOAD ENV + PDO
    // ======================
    $env = parse_ini_file(__DIR__ . '/../config/.env');

    $dsn = "mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']};charset=utf8mb4";

    $pdo = new PDO($dsn, $env['DB_USER'], $env['DB_PASSWORD'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);


    // ======================
    // START TRANSACTION
    // ======================
    $pdo->beginTransaction();


    // ======================
    // DELETE TEMP
    // ======================
    $pdo->exec("DELETE FROM vi07temp");


    // ======================
    // PREPARE INSERT (sekali saja)
    // ======================
    $stmt = $pdo->prepare("
        INSERT INTO vi07temp
        (partno, tipe, tanggal, qty, po, suppcode, partname)
        VALUES (?,?,?,?,?,?,?)
    ");


    // ======================
    // READ FILE
    // ======================
    $file = dirname(__DIR__) . "/uploads/vi07.txt";

    if (!file_exists($file)) {
        throw new Exception("File vi07.txt tidak ditemukan");
    }

    $fh = fopen($file, 'r');

    $rows = 0;
    $tglthn = '';
    $tglbln = '';

    while ($line = fgets($fh)) {

        set_time_limit(0);

        $partno   = trim(substr($line,0,15));
        $tipe     = trim(substr($line,49,2));
        $tgl      = trim(substr($line,55,10));
        $qtystr   = trim(substr($line,66,13));
        $po       = trim(substr($line,95,10));
        $suppcode = trim(substr($line,146,10));
        $partname = trim(substr($line,264,20));

        // format tanggal
        $tglthn = substr($tgl,0,4);
        $tglbln = substr($tgl,4,2);
        $tgltgl = substr($tgl,6,2);

        $tanggal = "$tglthn-$tglbln-$tgltgl";

        $qty = (int)$qtystr;

        $stmt->execute([
            $partno,
            $tipe,
            $tanggal,
            $qty,
            $po,
            $suppcode,
            $partname
        ]);

        $rows++;
    }

    fclose($fh);


    // ======================
    // DELETE PERIOD
    // ======================
    $period = "$tglthn-$tglbln";

    $stmtDel = $pdo->prepare("
        DELETE FROM vi07
        WHERE tanggal LIKE ?
    ");

    $stmtDel->execute(["$period%"]);


    // ======================
    // COPY FINAL
    // ======================
    $pdo->exec("
        INSERT INTO vi07(partno,tipe,tanggal,qty,po,suppcode,partname)
        SELECT partno,tipe,tanggal,qty,po,suppcode,partname
        FROM vi07temp
    ");


    // ======================
    // COMMIT
    // ======================
    $pdo->commit();


    // ======================
    // DELETE FILE (optional)
    // ======================
    unlink($file);


    // ======================
    // RESPONSE JSON
    // ======================
    echo json_encode([
        "status" => "success",
        "rows_inserted" => $rows,
        "period_deleted" => $period,
        "message" => "Import VI07 selesai"
    ]);

} catch (Exception $e) {

    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        "status" => "failed",
        "message" => $e->getMessage()
    ]);
}
?>