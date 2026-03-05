<?php
/*
    Created by Imam Prayudi
    Convert ADOdb -> PDO
    Import CSV stdpack
*/

error_reporting(E_ALL);
ini_set('display_errors', 1);


/* =========================
   PDO CONNECTION (.env)
========================= */
$env = parse_ini_file(__DIR__ . '/../config/.env');

$dsn = "mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']};charset=utf8mb4";

$pdo = new PDO($dsn, $env['DB_USER'], $env['DB_PASSWORD'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);


/* =========================
   DELETE OLD DATA
========================= */
$pdo->exec("DELETE FROM stdpack");


/* =========================
   READ CSV
========================= */
$txtFile = dirname(__DIR__) . "/uploads/stdpacks.csv";

$fh = fopen($txtFile, 'r');

if ($fh === false) {
    die('File csv tidak bisa dibuka...');
}


/* =========================
   PREPARE INSERT (FAST)
========================= */
$sql = "
INSERT INTO stdpack
(
    suppcode,
    partnumber,
    partname,
    stdpack,
    kategori,
    lokasi,
    stdpack_supp,
    input_user,
    input_date,
    imincl
)
VALUES (?,?,?,?,?,?,?,?,?,?)
";

$stmt = $pdo->prepare($sql);


/* =========================
   TRANSACTION (WAJIB supaya cepat)
========================= */
$pdo->beginTransaction();


// skip header
fgetcsv($fh);


while (($row = fgetcsv($fh)) !== false) {

    // skip header
    if ($row[0] === 'suppcode') {
        continue;
    }

    // pastikan jumlah kolom cukup
    if (count($row) < 10) {
        continue;
    }

    $suppcode   = trim($row[0]);
    $partnumber = trim($row[1]);

    // skip kalau kolom wajib kosong
    if ($suppcode === '' || $partnumber === '') {
        continue;
    }

    $stmt->execute([
        $suppcode,
        $partnumber,
        trim($row[2]) ?: null,
        trim($row[3]) ?: null,
        trim($row[4]) ?: null,
        trim($row[5]) ?: null,
        trim($row[6]) ?: null,
        trim($row[7]) ?: null,
        trim($row[8]) ?: null,
        trim($row[9]) ?: null
    ]);
}


$pdo->commit();

if (file_exists($txtFile)) {
      unlink($txtFile);
    }

echo "success";
?>