<?php
require_once "security.php";
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

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
<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <title>Forecast</title>
  <link id="favicon" rel="icon" type="image/png" href="assets/gambar/g-green.png">
  <style>

.table-container{
  max-height:500px;
  overflow:auto;
}

/* tabel */
#dataTable{
  border-collapse:collapse;
}

#dataTable th,
#dataTable td{
  border:1px solid #d0d0d0;
  padding:4px;
  white-space:nowrap;
}

/* HEADER */
#dataTable thead th{
  position:sticky;
  top:0;
  background:#f5f5f5;
  z-index:5;
}

/* NO */
#dataTable th:nth-child(1),
#dataTable td:nth-child(1){
  position:sticky;
  left:0;
  background:#fff;
  z-index:4;
  width:50px;
}

/* PARTNO */
#dataTable th:nth-child(2),
#dataTable td:nth-child(2){
  position:sticky;
  left:50px;
  background:#fff;
  z-index:4;
  width:260px;
}

/* DD/MM */
#dataTable th:nth-child(3),
#dataTable td:nth-child(3){
  position:sticky;
  left:310px;
  background:#fff;
  z-index:4;
  width:90px;
}

/* header untuk kolom sticky */
#dataTable thead th:nth-child(1),
#dataTable thead th:nth-child(2),
#dataTable thead th:nth-child(3){
  z-index:6;
  background:#f5f5f5;
}

#dataTable{
  border-collapse: collapse;
  width: max-content;
}

/* kolom tanggal forecast */
#dataTable th:nth-child(n+4),
#dataTable td:nth-child(n+4){
  min-width:60px;
  text-align:right;
}

#dataTable thead th{
  background: linear-gradient(#f8f9fa,#e9ecef);
  color:#333;
  font-weight:600;
  border-bottom:2px solid #dcdcdc;
}

#dataTable tbody tr:nth-child(even){
  background:#fafafa;
}

#dataTable tbody tr:hover{
  background:#eef6ff;
}

.firm{
  color:#0d6efd;
  font-weight:600;
}

.forec{
  color:#198754;
}

.plan{
  color:#fd7e14;
}

.total{
  font-weight:700;
  color:#212529;
}

</style>

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
    <br>
<button id="btnDownload" class="btn btn-success">Download CSV</button>
<br><br>
    <div class="table-container">
<table id="dataTable" class="table table-hover">
<thead></thead>
<tbody></tbody>
</table>
</div>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script>
let user = '';
let level = '';
let appkey = '';   
let postkey = '';
let urlsupp = '';
let urlforecast = '';

function downloadCSV_Forecast() {
  const table = document.getElementById("dataTable");
  let csv = [];

  // ===== HEADER =====
  const headers = table.querySelectorAll("thead th");
  let headerRow = [];

  headers.forEach((th, index) => {
    let text = th.innerText.trim().replace(/"/g, '""');

    // kolom PARTNO kita pecah jadi 3
    if(index === 1){
      headerRow.push('"PARTNO"','"PARTNAME"','"LEADTIME"');
    }
    else if(index === 2){
      // DD/MM jadi 4 baris
      headerRow.push('"TYPE"'); 
    }
    else{
      headerRow.push(`="` + text + `"`); 
    }
  });

  csv.push(headerRow.join(","));

  // ===== BODY =====
  const rows = table.querySelectorAll("tbody tr");

  rows.forEach(row => {
    const cols = row.querySelectorAll("td");

    // kita buat 4 baris (FIRM, FOREC, PLAN, TOTAL)
    let rowFirm = [];
    let rowForec = [];
    let rowPlan = [];
    let rowTotal = [];

    cols.forEach((td, index) => {

      // ===== KOLOM PART (gabungan) =====
      if(index === 1){
        const html = td.innerHTML.split("<br>");

        const partno = (td.querySelector("pre")?.innerText || '').trim();
        const partname = (html[1] || '').trim();
        const leadtime = (html[2] || '').trim();

        [rowFirm,rowForec,rowPlan,rowTotal].forEach(r=>{
          r.push(`"${partno}"`);
          r.push(`"${partname}"`);
          r.push(`"${leadtime}"`);
        });
      }

      // ===== KOLOM TYPE =====
      else if(index === 2){
        rowFirm.push('"FIRM"');
        rowForec.push('"FOREC"');
        rowPlan.push('"PLAN"');
        rowTotal.push('"TOTAL"');
      }

      // ===== KOLOM QTY =====
      else if(index >= 3){
        let lines = td.innerText.split("\n");

        rowFirm.push(`"${(lines[0]||'').trim()}"`);
        rowForec.push(`"${(lines[1]||'').trim()}"`);
        rowPlan.push(`"${(lines[2]||'').trim()}"`);
        rowTotal.push(`"${(lines[3]||'').trim()}"`);
      }

      // ===== KOLOM NO =====
      else {
        let text = td.innerText.trim().replace(/"/g,'""');
        [rowFirm,rowForec,rowPlan,rowTotal].forEach(r=>{
          r.push(`"${text}"`);
        });
      }

    });

    csv.push(rowFirm.join(","));
    csv.push(rowForec.join(","));
    csv.push(rowPlan.join(","));
    csv.push(rowTotal.join(","));
  });

  // ===== DOWNLOAD =====
  const csvString = csv.join("\n");
  const blob = new Blob([csvString], { type: "text/csv;charset=utf-8;" });
  const url = URL.createObjectURL(blob);

  const a = document.createElement("a");
  a.href = url;

  const supp = document.getElementById('idsupp').value;
  const tipe = document.getElementById('idtipe').value;

  a.download = `FORECAST_${supp}_${tipe}.csv`;
  a.click();

  URL.revokeObjectURL(url);
}

function formatNumber(num){
  if(num === null || num === '' || isNaN(num)) return num;
  return Number(num).toLocaleString('en-US');
}

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
      datarow += `<td>
      <span class="firm">FIRM</span><br>
      <span class="forec">FOREC</span><br>
      <span class="plan">PLAN</span><br>
      <span class="total">TOTAL</span>
      </td>`;
      
      for (let i = start; i <= end; i++) {
  datarow += `<td align="right">
    ${formatNumber(item[`dt1qt${i}`])}<br>
    ${formatNumber(item[`dt2qt${i}`])}<br>
    ${formatNumber(item[`dt3qt${i}`])}<br>
    ${formatNumber(item[`dt4qt${i}`])}
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

document.getElementById("btnDownload")
  .addEventListener("click", downloadCSV_Forecast);

</script>
  </body>
  </html>
