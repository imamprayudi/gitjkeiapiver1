<?php
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
  // Buat array respons JSON
  $response = array(
    'user' => $user,
    'level' => $level,
    'appkey' => $appkey
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
