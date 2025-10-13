<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (empty($_GET['nama'])) {
    echo "Parameter 'user' kosong atau tidak ada.";
    exit();
}else{
  $nama = $_GET['nama'];
  $_SESSION['user'] = $nama;
  $_SESSION['level'] = $_GET['level'];
  header("Location: dashboard.php");
  exit();
  }
}

