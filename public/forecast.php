<!DOCTYPE html>
<?php
session_start();
if (isset($_SESSION['user'])) {
  $appkey = $_SESSION['appkey'];
  $env = parse_ini_file(__DIR__ . '/../config/.env');
  $suppurl = $env['API_SUPP_URL'];
  $envappkey = $env['APP_KEY'];
  if ($appkey !== $envappkey) {
    header("Location: login.php");
    exit();
  }
?>
  <html lang="en">
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <title>Forecast</title>
  <link id="favicon" rel="icon" type="image/png" href="assets/gambar/g-green.png">
  </head>
  <body>
    <?php include 'menu.php'; ?>
    <br />
    <img src="assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION" 
    style="float:left;width:220px;height:35px;">
    PT JVCKENWOOD ELECTRONICS INDONESIA<br />
    PART PURCHASE LONG FORECAST <br /><br />

    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">Supplier : &nbsp;&nbsp;
      <select name="supp" id="idsupp">
      </select>&nbsp;&nbsp;
      <select name="tipe" id="idtipe">
        <option value="1">Weekly</option>
        <option value="2">Monthly</option>
      </select>
      &nbsp;&nbsp;
      <input type="submit" value="Display">
      <!-- <input type=BUTTON value="Display" name="mybtn" id="btn"></input> -->
    </form>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
      
    async function getSupplier(){
      const alamat = '<?=$suppurl?>';
      const nama = '<?php echo $_SESSION['user']; ?>';
      try {
        const response = await fetch(alamat, {
          method: 'POST',
          credentials: "include",
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: new URLSearchParams({
            nama: nama,
          })
        });

        const reply = await response.text(); // ambil balasan dari PHP
        const isidata = JSON.parse(reply);  

      const selectsupp = document.getElementById('idsupp');
      console.log(selectsupp.value);
        isidata.forEach((item, index) => {
        const option = document.createElement('option');
        option.value = item.kode;       // nilai option
        option.textContent = item.nama + ' - ' + item.kode; // teks yang 
        if (index === 0) {
          option.selected = true;
        }
        selectsupp.appendChild(option);
        });
      
    } catch (error) {
        console.error(error);
      }
    }
   // const btn = document.getElementById('btn');

    getSupplier();
 
    //btn.addEventListener('click', function() {
    //alert('Javascript Ajax code here');
    //const idsupp = document.getElementById('idsupp').value;
    //console.log(idsupp);
    //});
      
    </script>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
      $supp = $_POST['supp'] ?? '';
      $tipe = $_POST['tipe'] ?? '';
      $env = parse_ini_file(__DIR__ . '/../config/.env');
      $postkey = $env['POST_KEY'];
      $url = $env['API_FORECAST_URL'];
      // Data yang dikirim
      $data = [
      'supp' => $supp,
      'tipe' => $tipe,
      'postkey'  => $postkey
      ];

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
      if (curl_errno($ch)) 
      {
        echo "Error: " . curl_error($ch);
      } else 
      {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $data = json_decode($response, true);
        if (is_array($data)) 
        {
          $replystatus = $data['status'];
          if($replystatus === 'success')
          {
            foreach ($data['judul'] as $index => $item): 
              echo '<br>';
              echo '&nbsp;&nbsp;FORECAST FOR  ' . $item['subsuppname'];
              echo '&nbsp;(&nbsp;' . $item['suppcode'] . ' ) - ';
              echo $item['transdate'];
              echo ' - ';
              if($tipe === '1')
              {
                echo 'WEEKLY';
              }else
              {
                echo 'MONTHLY';
              }
              echo '<table class="table table-hover">';
              echo '<tr>';
              echo '<th>NO</th>';
              echo '<th>Part No</th>';
              echo '<th>DD/MM</th>';
              //foreach ($data['data'] as $item):
            
              echo '<th>' . $item['1'] . '</th>';
              echo '<th>' . $item['2'] . '</th>';
              echo '<th>' . $item['3'] . '</th>';
              echo '<th>' . $item['4'] . '</th>';
              echo '<th>' . $item['5'] . '</th>';
              echo '<th>' . $item['6'] . '</th>';
              echo '<th>' . $item['7'] . '</th>';
              echo '<th>' . $item['8'] . '</th>';
              echo '<th>' . $item['9'] . '</th>';
              echo '<th>' . $item['10'] . '</th>';
              echo '<th>' . $item['11'] . '</th>';
              echo '<th>' . $item['12'] . '</th>';
              echo '<th>' . $item['13'] . '</th>';
              echo '<th>' . $item['14'] . '</th>';
              echo '<th>' . $item['15'] . '</th>';
              echo '<th>' . $item['16'] . '</th>';
              echo '<th>' . $item['17'] . '</th>';
              echo '<th>' . $item['18'] . '</th>';
              echo '<th>' . $item['19'] . '</th>';
              echo '<th>' . $item['20'] . '</th>';
              echo '<th>' . $item['21'] . '</th>';
              echo '<th>' . $item['22'] . '</th>';
              echo '<th>' . $item['23'] . '</th>';
              echo '<th>' . $item['24'] . '</th>';
              echo '<th>' . $item['25'] . '</th>';
              if($tipe === '1')
              {
                echo '<th>' . $item['26'] . '</th>';
                echo '<th>' . $item['27'] . '</th>';
                echo '<th>' . $item['28'] . '</th>';
              }
              
            endforeach; 
            echo '</tr>';
            echo '</table>';
          } else 
          {
            echo "<br>Login gagal: " . $replystatus;
          }
        } else 
        {
          echo "Gagal decode JSON. Response: " . $response;
        }
      
  }
    // Tutup koneksi cURL
    curl_close($ch);
}


?>
  </body>
  </html>
<?php
} else {
  header("Location: index.php");
}
?>

