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

    <form>
      <label for="idsupp">Supplier : </label>&nbsp;&nbsp;
      <select name="supp" id="idsupp">
      </select>&nbsp;&nbsp;
      <label for="idurutan">Order By :</label>&nbsp;&nbsp;
      <select name="urutan" id="idurutan">
        <option value="1">Part Number</option>
        <option value="2">Required Date</option>
        <option value="3">PO Number</option>
        <option value="4">Model</option>
        <option value="5">Issue Date</option>
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
let urlob = '';

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
  urlob = data.urlob;
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

async function getOb(supp,urutan,post)
{
  let supplier = supp;
  let urutannya = urutan;
  let postkey = post;
  try 
  {
    const response = await fetch(urlob, 
    {
      method: 'POST',
      credentials: "include",
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        supp: supplier,
        urutan: urutannya,
        postkey: postkey
      })
    });
    const reply = await response.text(); // ambil balasan dari PHP
    const isidata = JSON.parse(reply);
    const data = isidata.data;
    const dataTable = document.getElementById('dataTable');
    const thead = dataTable.querySelector('thead');
    const tbody = dataTable.querySelector('tbody');
    // Clear existing table data
    thead.innerHTML = '';
    tbody.innerHTML = '';
    let judul = `<tr><th>NO</th><th>PARTNO</th><th>PART NAME</th>
    <th>QUANTITY</th><th>ReqDate</th><th>PO</th><th>SQ</th> 
    <th>BAL</th><th>SuppRest</th><th>MODEL</th>
    <th>ISSUE DATE</th><th>PO Type</th></tr>`;
    thead.innerHTML += judul;
    let datarow = ``;
    data.forEach((item, index) => 
    {
      datarow += `<tr>
      <td align="right">${index + 1}</td>
      <td><pre>${item.partno}</pre></td>
      <td>${item.partname}</td>
      <td align="right">${item.qty}</td>
      <td>${item.reqdate.substring(0, 10)}</td>
      <td>${item.po}</td>
      <td>${item.posq}</td>
      <td align="right">${item.ob}</td>
      <td align="right">${item.supprest}</td>
      <td>${item.model}</td>
      <td>${item.issuedate.substring(0, 10)}</td>
      <td>${item.potype}</td>
      </tr>`;
    });
    tbody.innerHTML += datarow;
  } catch (error)
  {
    console.error(error);
  }
}

document.addEventListener('submit', function(e)
{
  e.preventDefault();
  const suppid = document.getElementById('idsupp').value;
  const urutan = document.getElementById('idurutan').value;
  //alert(suppid + ' - ' + urutan);
  getOb(suppid,urutan,postkey);
});
</script>
    </body>
  </html>
<?php
} else {
  header("Location: index.php");
}
?>