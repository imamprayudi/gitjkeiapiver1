<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $userid = $_POST['userid'] ?? '';
  $password = $_POST['password'] ?? '';
  $env = parse_ini_file(__DIR__ . '/../config/.env');
  $postkey = $env['POST_KEY'];
  $url = $env['API_LOGIN_URL'];
  // Data yang dikirim
  $data = [
    'userid' => $userid,
    'password' => $password,
    'postkey'  => $postkey
  ];
  // Inisialisasi cURL
  $ch = curl_init($url);
  // Set opsi cURL
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // jika HTTPS dan belum ada sertifikat
  $headers = [
    'Content-Type: application/x-www-form-urlencoded' //,
   // 'Content-Length: ' . strlen($postFields) // opsional, cURL biasanya menetapkan ini sendiri
  ];
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  // Eksekusi
  $response = curl_exec($ch);
  // Cek error
  if (curl_errno($ch)) {
    echo "Error: " . curl_error($ch);
  } else {
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "HTTP Status: $httpCode<br>";
    //echo "Response: $response";
    $data = json_decode($response, true);
    if (is_array($data)) {
      echo "<pre>";
      print_r($data);
      $replystatus = $data['status'];
      if($replystatus === 'success'){
        $replyuserid = $data['data'][0];
        $replylevel = $data['data'][1];
        $replyappkey = $data['data'][2];  
        echo "<br>Login berhasil untuk userid: " . $replyuserid . " dengan level: " . $replylevel;
        $_SESSION['user'] = $replyuserid;
        $_SESSION['level'] = $replylevel;
        $_SESSION['appkey'] = $replyappkey;
        header("Location: ../public/dashboard.php");
      } else {
        echo "<br>Login gagal: " . $replystatus;
        header("Location: ../public/login.php?pesan=Login Failed");

        }
    } else {
      echo "Gagal decode JSON. Response: " . $response;
    }
      //  echo $data[0]; 

       // echo "<br>Decoded JSON: " . print_r($data, true);
    //     if ($response === 'success') {
    //         echo "<br>Login berhasil! cek dari logintestpost.php";
    //         // Proses login berhasil, 
    //         // ambil userid dan level ( level dari api ) --> response pakai json
    //         // set app_key dari .env 
    //         // buat session userid, level, app_key
    //         // redirect ke dashboard
    //     } else {
    //       echo "<br>login gagal! cek dari logintestpost.php";
    //         // Login gagal, tangani kesalahan
    //     }
     }

    // Tutup koneksi cURL
    curl_close($ch);
}
?>
