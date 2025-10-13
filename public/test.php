<?php
//echo "Testing make API by PHP Native\n";



// ✅ Header CORS
// header("Access-Control-Allow-Origin: https://jkeis.grahaindomedia.com"); // * artinya semua domain boleh akses
header("Access-Control-Allow-Origin: *"); // * artinya semua domain boleh akses
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// ✅ Handle preflight request dari browser
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ✅ Logika API
// if ($_SERVER['REQUEST_METHOD'] === 'GET') {
//     header('Content-Type: application/json');
//     //echo 'berhasil get dari cross domain';
//     echo json_encode(['status' => 'Pak IP semakin sukses !!!']);

// } 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    //echo 'berhasil get dari cross domain';
//    $nama = trim($_POST['nama'] ?? '');
//    $password = $_POST['password'] ?? '';
    echo json_encode(['status' => 'POST : Pak IP semakin sukses !!!']);
    exit();  
} 

echo 'Halaman ini tidak bisa langsung diakses tanpa dari posting.';
// $host = "136.198.117.80\JeinSql2017S";        // atau IP server MSSQL
// $port = 1433;               // port default SQL Server
// $dbname = "edi";
// $username = "sa";
// $password = "password";

// try {
//     // Format DSN untuk MSSQL
//     $dsn = "sqlsrv:Server=$host,$port;Database=$dbname";

//     // Buat koneksi PDO
//     $pdo = new PDO($dsn, $username, $password);

//     // Set mode error ke Exception agar mudah debug
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//     echo "Koneksi ke MSSQL berhasil!";
// } catch (PDOException $e) {
//     echo "Koneksi gagal: " . $e->getMessage();
// }


