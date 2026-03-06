<?php

/*
 *
** Programmer: Imam Prayudi
** Date Created: 6 Maret 2026
** Purpose: Secara berkala menghapus file JPG 
** yang sudah kadaluarsa dari folder tertentu
*/

$folders = ['image','injc','prin','stmp','wire'];
$expire = 3600 * 24; // 1 hari

foreach ($folders as $folder) {

    $dir = __DIR__ . '/../printqr/' . $folder . '/';

    if (!is_dir($dir)) {
        echo "Folder tidak ditemukan: $dir <br>";
        continue;
    }

    foreach (glob($dir . "*.jpg") as $file) {

        if (time() - filemtime($file) > $expire) {

            if (unlink($file)) {
              //  echo "Deleted: $file <br>";
            } else {
                echo "Gagal delete: $file <br>";
            }

        }

    }

}

echo "Cleanup selesai";

?>