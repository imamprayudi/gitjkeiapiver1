<!DOCTYPE html>
<?php
session_start();
if (isset($_SESSION['user'])) {
?>
  <html lang="en">
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <title>Forecast</title>
  </head>
  <body>
    <?php include 'menu.php'; ?>
    <form action="">Supplier : &nbsp;&nbsp;
      <select name="supp" id="idsupp">
      </select>&nbsp;&nbsp;
      <select name="tipe" id="idtipe">
        <option value="1">Weekly</option>
        <option value="2">Monthly</option>
      </select>
      &nbsp;&nbsp;
      <input type=BUTTON value="Display" name="mybtn" id="btn"></input>
    </form>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
      
    async function getSupplier(){
      const alamat = 'https://svr1.jkei.jvckenwood.com/api/test.php'; // ganti sesuai alamat server Anda
      //const alamat = 'http://136.198.117.118/api/supp.php'; // ganti sesuai alamat server Anda
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
        console.log(isidata);



      const selectsupp = document.getElementById('idsupp');
      console.log(selectsupp.value);
      //fetch('data.json')
      //.then(response => response.json())
      //.then(data => {
      // Loop data JSON
        isidata.forEach((item, index) => {
        const option = document.createElement('option');
        option.value = item.kode;       // nilai option
        option.textContent = item.nama + ' - ' + item.kode; // teks yang 
        if (index === 0) {
          option.selected = true;
        }
        selectsupp.appendChild(option);
        });
      
     // })
     // .catch(error => console.error('Gagal ambil data:', error));
    } catch (error) {
       // document.getElementById('hasil').innerHTML = 'Terjadi kesalahan koneksi.';
        console.error(error);
      }
    }
    const btn = document.getElementById('btn');

    getSupplier();
 
    btn.addEventListener('click', function() {
    alert('Javascript Ajax code here');
});
      
    </script>
  </body>
  </html>
<?php
} else {
  header("Location: index.php");
}
?>

