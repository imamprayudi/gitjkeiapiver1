<?php
header("Access-Control-Allow-Origin: same-origin");
header("Content-Type: application/json");
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
  $replyuserid = $_POST['userid'];
  $replylevel = $_POST['level'];  
  $replyappkey = $_POST['appkey'];
  $_SESSION['user'] = $replyuserid;
  $_SESSION['level'] = $replylevel;
  $_SESSION['appkey'] = $replyappkey;
  echo json_encode(['status' => 'success']);
  exit();
}

http_response_code(403);
die("Forbidden Access");  