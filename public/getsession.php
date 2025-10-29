<?php
// code ini berfungsi untuk mencegah panggil getsession.php langsung
// tanpa dari fetch javascript, namun masih gagal, fetch javascript juga terblokir
// if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
//       strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
//     http_response_code(403);
//     exit('403 Forbidden');
// }
// Fungsi untuk mendapatkan data session
// dan mengembalikannya dalam format JSON
// Programmer: Imam Prayudi
// Date Created : 2025-10-20
// Date Updated : 2025-10-20
//-----------------------------------------------
session_start();
if (isset($_SESSION['user'])) {
  $user = trim($_SESSION['user']);
  $level = $_SESSION['level'];
  $appkey = $_SESSION['appkey'];
  $env = parse_ini_file(__DIR__ . '/../config/.env');
  $suppurl = $env['API_SUPP_URL'];
  // Buat array respons JSON
  $response = array(
    'user' => $user,
    'level' => $level,
    'appkey' => $appkey,
    'urlsupp' => $suppurl
  );
  // Header JSON
  header('Content-Type: application/json');
  // Kirim respons JSON
  echo json_encode($response);
} else {
    $user = "";
    echo "Session not found";
}

?>
