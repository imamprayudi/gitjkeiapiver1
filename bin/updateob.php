<?php

$host = "localhost";
$db   = "edi";
$user = "root";
$pass = "";
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}


/* =============================
   PATH CSV (folder uploads sejajar bin)
============================= */
$csvFile = dirname(__DIR__) . "/uploads/OrdBal.csv";

if (!file_exists($csvFile)) {
    die("File tidak ditemukan: " . $csvFile);
}


/* =============================
   PREPARE INSERT
============================= */
$sql = "INSERT INTO edi.ordbal (
            transdate, suppcode, partnumber, partname, orderqty,
            reqdate, ponumber, posq, orderbalance, supprest,
            model, issuedate, potype, statuspart, remark, statusread
        ) VALUES (
            ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
        )";

$stmt = $pdo->prepare($sql);


try {

    /* =============================
       START TRANSACTION
    ============================= */
    $pdo->beginTransaction();


    /* =============================
       DELETE DATA LAMA
    ============================= */
    $deleted = $pdo->exec("DELETE FROM edi.ordbal");
    echo "Deleted rows: $deleted <br>";


    /* =============================
       BACA CSV + INSERT
    ============================= */
    $handle = fopen($csvFile, "r");

    // skip header
    fgetcsv($handle);

    $rows = 0;

    while (($data = fgetcsv($handle, 1000, ",")) !== false) {

        $stmt->execute([
            $data[0],
            trim($data[1]),
            trim($data[2]),
            $data[3],
            (int)$data[4],
            $data[5] ?: null,
            $data[6],
            $data[7],
            (int)$data[8],
            (int)$data[9],
            $data[10],
            $data[11] ?: null,
            $data[12],
            $data[13],
            $data[14],
            $data[15]
        ]);

        $rows++;
    }

    fclose($handle);


    /* =============================
       COMMIT
    ============================= */
    $pdo->commit();

    if (file_exists($csvFile)) {
      unlink($csvFile);
    }

    echo "Import selesai & file CSV sudah dihapus. Total insert: $rows";


} catch (Exception $e) {

    $pdo->rollBack();
    echo "ERROR: " . $e->getMessage();
}
?>