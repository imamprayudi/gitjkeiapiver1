<?php

/*
 *
** Programmer: Imam Prayudi
** Date Created: 6 Maret 2026
** Purpose: Secara berkala menghapus file JPG 
** yang sudah kadaluarsa dari folder tertentu
*/

date_default_timezone_set('Asia/Jakarta');
set_time_limit(0);

error_reporting(E_ALL);
ini_set('display_errors',1);

$folders = ['image','injc','prin','stmp','wire'];
$expire = 3600 * 24;
foreach ($folders as $folder) {

    $dir = __DIR__ . '/../printqr/' . $folder . '/';

    echo "Cek folder: $dir <br>";

    foreach (glob($dir . "*.jpg") as $file) {

        echo "File: $file <br>";
        echo "Modified: ".date("Y-m-d H:i:s", filemtime($file))."<br>";
        echo "Selisih detik: ".(time() - filemtime($file))."<br>";

        if (time() - filemtime($file) > $expire) {

            if (unlink($file)) {
                echo "Deleted <br>";
            } else {
                echo "Gagal delete <br>";
            }

        }

        echo "<br>";

    }

}

?>