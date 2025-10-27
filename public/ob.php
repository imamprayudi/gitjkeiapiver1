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

    <form action="">Supplier : &nbsp;&nbsp;
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
      <input type=BUTTON value="Display" name="mybtn" id="btn"></input>
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
 
    btn.addEventListener('click', function() {
    alert('Javascript Ajax code here');
    const idsupp = document.getElementById('idsupp').value;
    console.log(idsupp);
});
      
    </script>
  </body>
  </html>
<?php
} else {
  header("Location: index.php");
}
?>

