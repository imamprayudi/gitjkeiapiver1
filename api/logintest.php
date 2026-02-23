<?php
require 'koneksi.php';

//$userid = $_POST['userid'];
//$userpass = $_POST['userpass'];
$userid = 'asahi';
$userpass = 'hijer22';

$sql = "SELECT * FROM usertbl 
        WHERE userid = :userid 
        AND userpass = :userpass";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':userid' => $userid,
    ':userpass' => $userpass
]);

$user = $stmt->fetch();
echo '<br>';
if ($user) {
    echo "Login berhasil, selamat datang " . $user['userid'];
} else {
    echo "Login gagal";
}
