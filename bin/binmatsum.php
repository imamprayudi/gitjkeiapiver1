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
    // LOAD ENV
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
    $pdo->exec("DELETE FROM sc01temp");


    // ======================
    // PREPARE INSERT (sekali saja)
    // ======================
    $stmt = $pdo->prepare("
        INSERT INTO sc01temp
        (period, whcode, loccode, partno, partname,
         prevblncqty, recqty, shipqty, thisblncqty)
        VALUES (?,?,?,?,?,?,?,?,?)
    ");


    // ======================
    // READ FILE
    // ======================
    $file = dirname(__DIR__) . "/uploads/sc01.txt";

    if (!file_exists($file)) {
        throw new Exception("File tidak ditemukan");
    }

    $fh = fopen($file, 'r');

    $rows = 0;

    while ($line = fgets($fh)) {

        $data = [
            substr($line,0,8),
            substr($line,16,3),
            substr($line,22,10),
            trim(substr($line,44,20)),
            trim(substr($line,82,20)),
            trim(substr($line,152,13)),
            trim(substr($line,166,13)),
            trim(substr($line,180,13)),
            trim(substr($line,194,13))
        ];

        $stmt->execute($data);
        $rows++;
    }

    fclose($fh);


    // ======================
    // PERIOD PROCESS
    // ======================
    $pdo->exec("
        DELETE FROM sc01period
        WHERE period = (SELECT DISTINCT period FROM sc01temp)
    ");

    $pdo->exec("
        INSERT INTO sc01period
        SELECT DISTINCT period FROM sc01temp
    ");

    $pdo->exec("
        DELETE FROM sc01
        WHERE period = (SELECT DISTINCT period FROM sc01temp)
    ");

    $pdo->exec("
        INSERT INTO sc01
        (period, loccode, partno, partname, prevblncqty, recqty, shipqty, thisblncqty)
        SELECT period, loccode, partno, partname, prevblncqty, recqty, shipqty, thisblncqty
        FROM sc01temp
        WHERE whcode = 'MC1'
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
        "message" => "Import selesai"
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