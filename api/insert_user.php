<?php
require 'koneksi.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
  http_response_code(400);
  echo json_encode(["status"=>"error","message"=>"Invalid JSON"]);
  exit();
}

$stmt = $pdo->prepare("
  INSERT INTO testusertbl (userid, username, usersecure)
  VALUES (:userid, :username, :usersecure)
  ON DUPLICATE KEY UPDATE
    username = VALUES(username),
    usersecure = VALUES(usersecure)
");

foreach ($data as $row) {
  $stmt->execute([
    ':userid' => $row['userid'],
    ':username'   => $row['username'],
    ':usersecure'  => $row['usersecure']
  ]);
}

echo json_encode(["status"=>"success"]);


// kalau datanya banyak, bisa gunakan transaction
// $pdo->beginTransaction();
// foreach ($data as $row) {
//   $stmt->execute([...]);
// }
// $pdo->commit();

