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
  <title>Order Balance</title>
  <link id="favicon" rel="icon" type="image/png" href="assets/gambar/g-green.png">
  </head>
  <body>
    <?php include 'menu.php'; ?>
    <br />
    <img src="assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION" 
    style="float:left;width:220px;height:35px;">
    PT JVCKENWOOD ELECTRONICS INDONESIA<br />
    ORDER BALANCE <br /><br />

    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">Supplier : &nbsp;&nbsp;
      <select name="supp" id="idsupp">
      </select>&nbsp;&nbsp;Order By : &nbsp;&nbsp;
      <select name="urutan" id="idurutan">
        <option value="1">Part Number</option>
        <option value="2">Required Date</option>
        <option value="3">PO Number</option>
        <option value="4">Model</option>
        <option value="5">Issue Date</option>
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
    const btn = document.getElementById('btn');

    getSupplier();
      
    </script>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
  $supp = $_POST['supp'] ?? '';
  $urutan = $_POST['urutan'] ?? '';
  $env = parse_ini_file(__DIR__ . '/../config/.env');
  $postkey = $env['POST_KEY'];
  $url = $env['API_OB_URL'];

  // Data yang dikirim
  $data = [
    'supp' => $supp,
    'urutan' => $urutan,
    'postkey'  => $postkey
  ];
  // Inisialisasi cURL
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
        echo '<table class="table table-hover">';
        echo '<tr>';
        echo '<th>NO</th>';
        echo '<th>Part No</th>';
        echo '<th>Part Name</th>';
        echo '<th>Quantity</th>';
        echo '<th>ReqDate</th>';
        echo '<th>PO</th>';
        echo '<th>SQ</th>';
        echo '<th>BAL</th>';
        echo '<th>SuppRest</th>';
        echo '<th>Model</th>';
        echo '<th>Issue Date</th>';
        echo '<th>PO Type</th>';
        echo '</tr>';
        $no = 1;
        foreach ($data['data'] as $item):
          echo '<tr>';
          echo '<td align="right">' . $no . '</td>';
          echo '<td>' . htmlspecialchars($item['partno']) . '</td>';
          echo '<td>' . htmlspecialchars($item['partname']) . '</td>';
          echo '<td align="right">' . htmlspecialchars($item['qty']) . '</td>';
          echo '<td>' . htmlspecialchars(substr($item['reqdate'],0,10)) . '</td>';
          echo '<td>' . htmlspecialchars($item['po']) . '</td>';
          echo '<td>' . htmlspecialchars($item['posq']) . '</td>';
          echo '<td align="right">' . htmlspecialchars($item['ob']) . '</td>';
          echo '<td align="right">' . htmlspecialchars($item['supprest']) . '</td>';
          echo '<td>' . htmlspecialchars($item['model']) . '</td>';
          echo '<td>' . htmlspecialchars(substr($item['issuedate'],0,10)) . '</td>';
          echo '<td>' . htmlspecialchars($item['potype']) . '</td>';
          $no = $no + 1;
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

