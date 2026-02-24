<?php
/*
** Programmer: Imam Prayudi
** Date Created: 2026-02-23
** Purpose: Upload file Big Part dari text file
** 
*/

if (!isset($_FILES['file'])) {
    die("File tidak diterima");
}

if ($_FILES['file']['error'] !== 0) {
    die("Upload error code: " . $_FILES['file']['error']);
}

$tmp  = $_FILES['file']['tmp_name'];
$name = basename($_FILES['file']['name']);

$folder = __DIR__ . "/../uploads/"; // <<< FIX DI SINI

if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

$target = $folder . $name;

if (move_uploaded_file($tmp, $target)) {
    echo "UPLOAD_OK : " . realpath($target);
} else {
    echo "UPLOAD_FAILED";
}