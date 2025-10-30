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
        // print_r($data);
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
            $nomor = 1;
            foreach ($data['data'] as $index => $item):
              echo '<tr>';
              echo '<td>' . $nomor . '</td>'; 
              echo '<td>' . $item['partno'] . '<br>';
              echo $item['partname'] . '<br>' . $item['leadtime'] . '</td>';
              echo '<td>FIRM<br>FOREC<br>PLAN<br>TOTAL<br></td>';
              echo '<td align="right">' . $item['a1'] . '<br>' . $item['b1']; 
              echo '<br>' . $item['c1'] . '<br>' . $item['d1'] . '</td>';
              echo '<td align="right">' . $item['a2'] . '<br>' . $item['b2']; 
              echo '<br>' . $item['c2'] . '<br>' . $item['d2'] . '</td>';
              echo '<td align="right">' . $item['a3'] . '<br>' . $item['b3'];
              echo '<br>' . $item['c3'] . '<br>' . $item['d3'] . '</td>';
              echo '<td align="right">' . $item['a4'] . '<br>' . $item['b4'];
              echo '<br>' . $item['c4'] . '<br>' . $item['d4'] . '</td>';
              echo '<td align="right">' . $item['a5'] . '<br>' . $item['b5'] ;
              echo '<br>' . $item['c5'] . '<br>' . $item['d5'] . '</td>';
              echo '<td align="right">' . $item['a6'] . '<br>' . $item['b6'] ;
              echo '<br>' . $item['c6'] . '<br>' . $item['d6'] . '</td>';
              echo '<td align="right">' . $item['a7'] . '<br>' . $item['b7'] ;
              echo '<br>' . $item['c7'] . '<br>' . $item['d7'] . '</td>';
              echo '<td align="right">' . $item['a8'] . '<br>' . $item['b8'] ;
              echo '<br>' . $item['c8'] . '<br>' . $item['d8'] . '</td>';
              echo '<td align="right">' . $item['a9'] . '<br>' . $item['b9'] ;
              echo '<br>' . $item['c9'] . '<br>' . $item['d9'] . '</td>';
              echo '<td align="right">' . $item['a10'] . '<br>' . $item['b10'] ;
              echo '<br>' . $item['c10'] . '<br>' . $item['d10'] . '</td>';
              echo '<td align="right">' . $item['a11'] . '<br>' . $item['b11'] ;
              echo '<br>' . $item['c11'] . '<br>' . $item['d11'] . '</td>';
              echo '<td align="right">' . $item['a12'] . '<br>' . $item['b12'] ;
              echo '<br>' . $item['c12'] . '<br>' . $item['d12'] . '</td>';
              echo '<td align="right">' . $item['a13'] . '<br>' . $item['b13'] ;
              echo '<br>' . $item['c13'] . '<br>' . $item['d13'] . '</td>';
              echo '<td align="right">' . $item['a14'] . '<br>' . $item['b14'] ;
              echo '<br>' . $item['c14'] . '<br>' . $item['d14'] . '</td>';
              echo '<td align="right">' . $item['a15'] . '<br>' . $item['b15'] ;
              echo '<br>' . $item['c15'] . '<br>' . $item['d15'] . '</td>';
              echo '<td align="right">' . $item['a16'] . '<br>' . $item['b16'] ;
              echo '<br>' . $item['c16'] . '<br>' . $item['d16'] . '</td>';
              echo '<td align="right">' . $item['a17'] . '<br>' . $item['b17'] ;
              echo '<br>' . $item['c17'] . '<br>' . $item['d17'] . '</td>';
              echo '<td align="right">' . $item['a18'] . '<br>' . $item['b18'] ;
              echo '<br>' . $item['c18'] . '<br>' . $item['d18'] . '</td>';
              echo '<td align="right">' . $item['a19'] . '<br>' . $item['b19'] ;
              echo '<br>' . $item['c19'] . '<br>' . $item['d19'] . '</td>';
              echo '<td align="right">' . $item['a20'] . '<br>' . $item['b20'] ;
              echo '<br>' . $item['c20'] . '<br>' . $item['d20'] . '</td>';
              echo '<td align="right">' . $item['a21'] . '<br>' . $item['b21'] ;
              echo '<br>' . $item['c21'] . '<br>' . $item['d21'] . '</td>';
              echo '<td align="right">' . $item['a22'] . '<br>' . $item['b22'] ;
              echo '<br>' . $item['c22'] . '<br>' . $item['d22'] . '</td>';
              echo '<td align="right">' . $item['a23'] . '<br>' . $item['b23'] ;
              echo '<br>' . $item['c23'] . '<br>' . $item['d23'] . '</td>';
              echo '<td align="right">' . $item['a24'] . '<br>' . $item['b24'] ;
              echo '<br>' . $item['c24'] . '<br>' . $item['d24'] . '</td>';
              echo '<td align="right">' . $item['a25'] . '<br>' . $item['b25'] ;
              echo '<br>' . $item['c25'] . '<br>' . $item['d25'] . '</td>';
              echo '<td align="right">' . $item['a26'] . '<br>' . $item['b26'] ;
              echo '<br>' . $item['c26'] . '<br>' . $item['d26'] . '</td>';
              echo '<td align="right">' . $item['a27'] . '<br>' . $item['b27'] ;
              echo '<br>' . $item['c27'] . '<br>' . $item['d27'] . '</td>';
              echo '<td align="right">' . $item['a28'] . '<br>' . $item['b28'] ;
              echo '<br>' . $item['c28'] . '<br>' . $item['d28'] . '</td>';
              

              echo '</tr>';
              $nomor = $nomor + 1;
            endforeach;
            
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

