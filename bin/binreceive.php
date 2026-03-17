<?php
header("Content-Type: application/json; charset=utf-8");

try {

    $env = parse_ini_file(__DIR__ . '/../config/.env');

    $dsn = "mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']};charset=utf8mb4";

    $pdo = new PDO($dsn, $env['DB_USER'], $env['DB_PASSWORD'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $pdo->beginTransaction();

    // kosongkan temp
    $pdo->exec("DELETE FROM receivetemp");

    $stmt = $pdo->prepare("
        INSERT INTO receivetemp
        (period,suppcode,partno,partname,tanggal,invoice,po,sq,qty,price,webcode)
        VALUES (?,?,?,?,?,?,?,?,?,?,?)
    ");

    $file = dirname(__DIR__) . "/uploads/pck31web.txt";

    if (!file_exists($file)) {
        throw new Exception("File tidak ditemukan");
    }

    $fh = fopen($file, 'r');

    $rows = 0;

    while (($data = fgetcsv($fh, 1000, ",")) !== FALSE) {

    // skip baris kosong
    if (!$data || count($data) < 10) {
        continue;
    }

    // skip EOF
    if (trim($data[0]) === 'EOF') {
        break;
    }

    $period   = trim($data[0] ?? '');
    $suppcode = trim($data[1] ?? '');
    $partno   = trim($data[2] ?? '');
    $partname = trim($data[3] ?? '');
    $tanggal  = trim($data[4] ?? '');
    $invoice  = trim($data[5] ?? '');
    $po       = trim($data[6] ?? '');
    $sq       = trim($data[7] ?? '');
    $qty      = trim($data[8] ?? 0);
    $price    = trim($data[9] ?? 0);
    $webcode  = trim($data[11] ?? '');

    $tanggal = date("Y-m-d", strtotime($tanggal));

    $stmt->execute([
        $period,
        $suppcode,
        $partno,
        $partname,
        $tanggal,
        $invoice,
        $po,
        $sq,
        $qty,
        $price,
        $webcode
    ]);

    $rows++;
}

    fclose($fh);

    // proses period
    $pdo->exec("
        DELETE FROM receiveperiod
        WHERE period = (SELECT period FROM receivetemp LIMIT 1)
    ");

    $pdo->exec("
        INSERT INTO receiveperiod(period)
        SELECT DISTINCT period FROM receivetemp
    ");

    $pdo->exec("
        DELETE FROM receive
        WHERE period = (SELECT period FROM receivetemp LIMIT 1)
    ");

    $pdo->exec("
        INSERT INTO receive
        (period,suppcode,partno,partname,tanggal,invoice,po,sq,qty,price,webcode)
        SELECT
        period,suppcode,partno,partname,tanggal,invoice,po,sq,qty,price,webcode
        FROM receivetemp
    ");

    $pdo->commit();

    unlink($file);

    echo json_encode([
        "status"=>"success",
        "rows"=>$rows
    ]);

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        "status"=>"error",
        "message"=>$e->getMessage()
    ]);
}
?>