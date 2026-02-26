<!DOCTYPE html>
<?php
session_start();
if (isset($_SESSION['user'])) 
{
  $appkey = $_SESSION['appkey'];
  $env = parse_ini_file(__DIR__ . '/../config/.env');
  $suppurl = $env['API_SUPP_URL'];
  $forecasturl = $env['API_FORECAST_URL'];
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

    <form>
    <label for="idsupp">Supplier:</label>  
      <select name="supp" id="idsupp">
      </select>&nbsp;&nbsp;
      <label for="idtipe">By :</label>
      <select name="tipe" id="idtipe">
        <option value="1">Weekly</option>
        <option value="2">Monthly</option>
      </select>
      &nbsp;&nbsp;
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
let postkey = '';
let urlsupp = '';
let urlforecast = '';

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
  postkey = data.postkey;
  urlsupp = data.urlsupp;  
  urlforecast = data.urlforecast;
  getSupplier(user);
}) 
.catch(err => console.error(err));

      

async function getSupplier(user)
{
  try 
  {
    const response = await fetch(urlsupp, 
    {
      method: 'POST',
      credentials: "include",
      headers: 
      {
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
  
async function getForecast(supp,tipe,post)
{
  let supplier = supp;
  let jenis = tipe;
  let postkey = post;
  try 
  {
    const response = await fetch(urlforecast, 
    {
      method: 'POST',
      credentials: "include",
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        supp: supplier,
        tipe: jenis,
        postkey: postkey
      })
    });
    const reply = await response.text(); // ambil balasan dari PHP
    const isidata = JSON.parse(reply);
    const judul = isidata.judul;
    const tipe = isidata.tipe;
    const data = isidata.data;
    const dataTable = document.getElementById('dataTable');
    const thead = dataTable.querySelector('thead');
    const tbody = dataTable.querySelector('tbody');
    // Clear existing table data
    thead.innerHTML = '';
    tbody.innerHTML = '';
    let start = (tipe === 'WEEKLY') ? 1 : 29;
    let end   = (tipe === 'WEEKLY') ? 28 : 53;
    // Create table header
    let judulrow = `
    <tr>
    <th>NO</th>
    <th>PARTNO</th>
    <th>DD/MM</th>
    `;

    judul.forEach(item => {
  for (let i = start; i <= end; i++) {
    judulrow += `<th>${item[`dt1qt${i}`] ?? ''}</th>`;
  }
});

    judulrow += `</tr>`;
    thead.innerHTML += judulrow;

    // Create table body
    let datarow = ``;
    data.forEach((item, index) => 
    {
      datarow += `<tr>`;
      datarow += `<td align="right">${index + 1}</td>`;
      datarow += `<td><pre>${item.partno}</pre><br>`;
      datarow += `${item.partname}<br>`;
      datarow += `${item.leadtime}</td>`;
      datarow += `<td>FIRM<br>FOREC<br>PLAN<br>TOTAL</td>`;
      
      for (let i = start; i <= end; i++) {
  datarow += `<td align="right">
    ${item[`dt1qt${i}`] ?? ''}<br>
    ${item[`dt2qt${i}`] ?? ''}<br>
    ${item[`dt3qt${i}`] ?? ''}<br>
    ${item[`dt4qt${i}`] ?? ''}
  </td>`;
}
        
    });
    datarow += `</tr>`;
    tbody.innerHTML += datarow;

  }catch (error) 
  {
    console.error(error);
  }
}

document.addEventListener('submit', function(e)
{
  e.preventDefault();
  const suppid = document.getElementById('idsupp').value;
  const tipe = document.getElementById('idtipe').value;
  getForecast(suppid,tipe,postkey);
});
</script>
  </body>
  </html>
<?php
}else 
{
  header("Location: index.php");
}
?>