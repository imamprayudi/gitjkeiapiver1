<?php
error_reporting(E_ALL);

$baseDir = dirname(__DIR__) . '/uploads';

if (!file_exists($baseDir)) {
    mkdir($baseDir, 0777, true);
}

$dest = $baseDir . '/stdpacks.csv';

if (move_uploaded_file($_FILES["userfile"]["tmp_name"], $dest)) {
    echo "success";
} else {
    echo "failed";
    print_r(error_get_last());
}
?>
