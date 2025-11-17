<!DOCTYPE html>
<?php
session_start();
if (isset($_SESSION['user'])) {
  $appkey = $_SESSION['appkey'];
  $env = parse_ini_file(__DIR__ . '/../config/.env');
  $suppurl = $env['API_SUPP_URL'];
  $matsumtglurl = $env['API_MATSUM_TGL_URL'];
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
  <title>Material Issued Detail</title>
  <link id="favicon" rel="icon" type="image/png" href="assets/gambar/g-green.png">
  </head>
  <body>
    <?php include 'menu.php'; ?>
    <br />
    <img src="assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION" 
    style="float:left;width:220px;height:35px;">
    PT JVCKENWOOD ELECTRONICS INDONESIA<br />
    MATERIAL DETAIL RECEIVED STATUS REPORT<br /><br />

    <form action="">
      Supplier : &nbsp;&nbsp;
      <select name="supp" id="idsupp">
      </select>&nbsp;&nbsp;
      <label for="idtglawal">DATE BETWEEN : </label>
      <input type="date" id="idtglawal" name="tglawal">&nbsp;&nbsp;
      <label for="idtglakhir">AND&nbsp;&nbsp;</label>
      <input type="date" id="idtglakhir" name="tglakhir">&nbsp;&nbsp;
      <input type="submit" value="Display">
    </form>
    <table id="dataTable" border="1" cellpadding="5" class="table table-hover">
      <thead></thead>
      <tbody></tbody>
    </table>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script>
let user = '';
let level = '';
let appkey = '';   
let urlsupp = '';
let urlmatrec = '';
    
fetch('getsession.php', 
{
  method: 'GET',
  headers: 
  {
  'X-Requested-With': 'XMLHttpRequest'
  }
})
.then(response => response.json())
.then(data => 
{
  user = data.user;
  level = data.level;
  appkey = data.appkey;
  urlsupp = data.urlsupp;  
  urlmatrec = data.urlmatrec;
  getSupplier(user);
}) 
.catch(err => console.error(err));

      
async function getSupplier(user)
{
  try 
  {
    const response = await fetch(urlsupp, {
      method: 'POST',
      credentials: "include",
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        nama: user,
      })
    });

    const reply = await response.text(); // ambil balasan dari PHP
    const isidata = JSON.parse(reply);  
    const selectsupp = document.getElementById('idsupp');
    isidata.forEach((item, index) => 
    {
      const option = document.createElement('option');
      option.value = item.kode;       // nilai option
      option.textContent = item.nama + ' - ' + item.kode; // teks yang 
      if (index === 0) 
      {
        option.selected = true;
      }
      selectsupp.appendChild(option);
    });
      
  } catch (error) 
  {
    console.error(error);
  }
}
// ------------------------------------------------------------------------
async function getMatrec(supp,tglawal,tglakhir)
{
  let supplier = supp;
  let awal = tglawal;
  let akhir = tglakhir;
  try 
  {
    const response = await fetch(urlmatrec, 
    {
      method: 'POST',
      credentials: "include",
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        supp: supplier,
        tglawal: awal,
        tglakhir: akhir
      })
    });
  
    const reply = await response.text(); // ambil balasan dari PHP
    const isidata = JSON.parse(reply);  
    const data = isidata.data;
    const table = document.getElementById("dataTable");
    const thead = table.querySelector("thead");
    const tbody = table.querySelector("tbody");
    thead.innerHTML = "";
    tbody.innerHTML = "";
    let judul = `
        <tr>
            <th>NO</th>
            <th>DATE</th>
            <th>PARTNO</th>
            <th>PO</th>
            <th>QTY</th>
            <th>TRANSCODE</th>
            <th>PM</th>  
        </tr>`;
    thead.innerHTML += judul;
    let datarow = ``;
    data.forEach((item, index) => 
    {
      datarow += `<tr>
      <td align="right">${index + 1}</td>
      <td>${item.tgl.substring(0, 10)}</td>
      <td><pre>${item.partno}</pre></td>
      <td>${item.po}</td>
      <td align="right">${item.qty}</td>
      <td>${item.transcode}</td>
      <td>${item.pm}</td>
      </tr>`;
    });
    tbody.innerHTML += datarow;
  } catch (error) 
  {        
    console.error(error);
  }

}
// ------------------------------------------------------------------------
document.addEventListener('submit', function(e)
{
  e.preventDefault();
  const suppid = document.getElementById('idsupp').value;
  const tglawal = document.getElementById('idtglawal').value;
  const tglakhir = document.getElementById('idtglakhir').value;
  if (!tglawal || !tglakhir) 
  {
    alert("Start date and end date must be filled in!");
    return;
  }
  let awal = new Date(tglawal);
  let akhir   = new Date(tglakhir);
  if (awal > akhir) 
  {
    alert("The start date cannot be greater than the end date!");
    return;
  } 
  getMatrec(suppid,tglawal,tglakhir);

});
      
</script>
  </body>
</html>
<?php
} else {
  header("Location: index.php");
}
?>
