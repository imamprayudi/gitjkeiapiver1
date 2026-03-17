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
  $bpstglurl = $env['API_BPS_TGL_URL'];
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
  <title>Big Parts</title>
  <link id="favicon" rel="icon" type="image/png" href="assets/gambar/g-green.png">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

<style>

body{
    font-family: 'Inter', sans-serif;
    background:#f4f6f9;
}

.container-box{
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
}

/* HEADER */
.title-area{
    display:flex;
    align-items:center;
    gap:15px;
    margin-bottom:20px;
}

.title-text{
    font-weight:600;
    font-size:18px;
    line-height:1.4;
}

/* FORM */
.filter-box{
    background:#f8fafc;
    padding:15px;
    border-radius:8px;
    margin-bottom:20px;
}

.filter-box label{
    font-weight:500;
}

select{
    border-radius:6px;
    padding:5px 8px;
}

button{
    background:#0d6efd;
    border:none;
    padding:6px 16px;
    color:white;
    border-radius:6px;
}

button:hover{
    background:#0b5ed7;
}

/* TABLE */

.table{
    font-size:13px;
}

thead th{
    position:sticky;
    top:0;
    background:#1f2937 !important;
    color:white;
    text-align:center;
    vertical-align:middle;
}

tbody tr:hover{
    background:#f1f5f9;
}

td{
    white-space:nowrap;
}

td:first-child{
    text-align:center;
}

.table-container{
    max-height:600px;
    overflow:auto;
    border:1px solid #ddd;
}

/* HEADER FREEZE */
#dataTable thead th{
    position: sticky;
    top: 0;
    background:#1f2937;
    color:white;
    z-index:3;
    text-align:center;
    white-space:nowrap;
}

/* FREEZE COLUMN NO */
#dataTable th:nth-child(1),
#dataTable td:nth-child(1){
    position: sticky;
    left: 0;
    background:white;
    z-index:2;
    text-align:center;
    min-width:60px;
}

/* FREEZE COLUMN PARTNO */
#dataTable th:nth-child(2),
#dataTable td:nth-child(2){
    position: sticky;
    left:60px;
    background:white;
    z-index:2;
    min-width:220px;
}

/* HEADER UNTUK KEDUA KOLOM */
#dataTable thead th:nth-child(1),
#dataTable thead th:nth-child(2){
    z-index:4;
    background:#1f2937;
}

#dataTable td{
    white-space:nowrap;
}

#dataTable td:nth-child(2){
    font-weight:500;
}

</style>
  </head>
  <body>
    <?php include 'menu.php'; ?>
   <div class="container mt-4">

<div class="container-box">

<div class="title-area">

<img src="assets/gambar/jvc.gif" 
style="width:220px;height:35px;">

<div class="title-text">
PT JVCKENWOOD ELECTRONICS INDONESIA<br>
BIG PARTS SCHEDULE
</div>

</div>

<div class="filter-box">

<form class="row g-3 align-items-center">

<div class="col-auto">
<label>Supplier</label>
<select name="supp" id="idsupp" class="form-select"></select>
</div>

<div class="col-auto">
<label>Transmission Date</label>
<select name="tanggal" id="idtanggal" class="form-select"></select>
</div>

<div class="col-auto">
<label>&nbsp;</label><br>
<button type="button" id="btn">Display</button>
</div>

</form>
<button id="btnDownload" class="btn btn-success mb-2">
  Download CSV
</button>
</div>

<div style="overflow-x:auto">

<div class="table-container">
<table id="dataTable" class="table table-bordered table-hover">
  <thead></thead>
  <tbody></tbody>
</table>
</div>

</div>

</div>

</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<script>
let user = '';
let level = '';
let appkey = '';   
let urlsupp = '';
let urlbpstgl = '';
let urlbps = '';



function downloadCSV_TDS() {
  const table = document.getElementById("dataTable");
  let csv = [];

  // ===== HEADER =====
  const headers = table.querySelectorAll("thead th");
  let headerRow = [];
  headers.forEach((th, index) => {
  let text = th.innerText.replace(/\n/g, " ").trim();
text = text.replace(/"/g, '""');

// paksa jadi text (anti auto format Excel)
if (/^\d{1,2}\/\d{1,2}$/.test(text)) {
  text = "\t" + text;
}

  if (index === 1) {
    headerRow.push(`"PARTNO"`);
    headerRow.push(`"PARTNAME"`);
  } else {
    headerRow.push(`"${text}"`);
  }
});
  csv.push(headerRow.join(","));

  // ===== BODY =====
  const rows = table.querySelectorAll("tbody tr");
  rows.forEach(row => {
  let cols = row.querySelectorAll("td");
  let rowData = [];

  cols.forEach((td, index) => {

    // ===== KHUSUS PARTNO + PARTNAME =====
    if (index === 1) {
      const pre = td.querySelector("pre");

      let partno = "";
      let partname = "";

      if (pre) {
        partno = pre.innerText.trim();

        let fullText = td.innerText.trim();
        partname = fullText.replace(partno, "").trim();
      }

      rowData.push(`"${partno}"`);
      rowData.push(`"${partname}"`);
    } 
    // ===== KOLOM LAIN =====
    else {
      let text = td.innerText.trim();
      text = text.replace(/\n/g, " ");
      text = text.replace(/"/g, '""');

      rowData.push(`"${text}"`);
    }

  });

  csv.push(rowData.join(","));
});

  // ===== DOWNLOAD =====
  const csvString = csv.join("\n");
  const blob = new Blob([csvString], { type: "text/csv;charset=utf-8;" });
  const url = URL.createObjectURL(blob);

  const a = document.createElement("a");
  a.href = url;

  // nama file otomatis
  const supp = document.getElementById('idsupp').value;
  const tgl = document.getElementById('idtanggal').value;

  a.download = `BPS_${supp}_${tgl}.csv`;
  a.click();

  URL.revokeObjectURL(url);
}



function formatNumber(num)
{
  if(num === null || num === '') return '';
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
  urlsupp = data.urlsupp;  
  urlbpstgl = data.urlbpstgl;
  urlbps = data.urlbps;
  getSupplier(user);
  getTanggal(user);
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
// ------------------------------------------------------------------------
async function getTanggal(user)
{
  try 
  {
    const response = await fetch(urlbpstgl, 
    {
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
    const selecttgl = document.getElementById('idtanggal');
    isidata.forEach((item, index) => {
    const option = document.createElement('option');
    option.value = item.tanggal;       // nilai option
    option.textContent = item.tanggal; // teks yang 
    if (index === 0) {
      option.selected = true;
    }
    selecttgl.appendChild(option);
    });    
  } catch (error) 
  {        
    console.error(error);
  }
}
// ------------------------------------------------------------------------


async function getBps(supp,tgl)
{
  let supplier = supp;
  let tanggal = tgl;
  try 
  {
    const response = await fetch(urlbps, 
    {
      method: 'POST',
      credentials: "include",
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        supp: supplier,
        tgl: tanggal
      })
    });

    const reply = await response.text(); // ambil balasan dari PHP
    const isidata = JSON.parse(reply);  
    const judul = isidata.header;
    const data = isidata.data;
    const table = document.getElementById("dataTable");
    const thead = table.querySelector("thead");
    const tbody = table.querySelector("tbody");
    thead.innerHTML = "";
    tbody.innerHTML = "";

    judul.forEach(item => {
    thead.innerHTML = `
        <tr>
            <th>NO</th>
            <th>PARTNO</th>
            <th>BAL</th>  
            <th>${item.qty1}</th>
            <th>${item.qty2}</th>
            <th>${item.qty3}</th>
            <th>${item.qty4}</th>
            <th>${item.qty5}</th>
            <th>${item.qty6}</th>
            <th>${item.qty7}</th>
            <th>${item.qty8}</th>
            <th>${item.qty9}</th>
            <th>${item.qty10}</th>  
            <th>${item.qty11}</th>
            <th>${item.qty12}</th>
            <th>${item.qty13}</th>
            <th>${item.qty14}</th>
            <th>${item.qty15}</th>
            <th>${item.qty16}</th>
            <th>${item.qty17}</th>
            <th>${item.qty18}</th>
            <th>${item.qty19}</th>
            <th>${item.qty20}</th>
          </tr>`;
    });

    let number = 1;
    data.forEach(item => {  
      const row = document.createElement("tr");
      const tdNumber = document.createElement("td");
      tdNumber.textContent = number++;
      row.appendChild(tdNumber);
      const td = document.createElement("td");
      td.innerHTML = `<pre>${item.partno?.trim() || ''}</pre><br>${item.partname?.trim() || ''}`;
      row.appendChild(td);
      Object.keys(item).forEach(key => 
      {
        if (key !== "partno" && key !== "partname") 
        {
          const td = document.createElement("td");
          td.textContent = formatNumber(item[key]);
          td.style.textAlign = "right";
          row.appendChild(td);
        }
      });
      tbody.appendChild(row);
    });
  } catch (error) 
  {        
    console.error(error);
  }

}
// ------------------------------------------------------------------------
const btn = document.getElementById('btn');
btn.addEventListener('click', function() 
{
  getBps(document.getElementById('idsupp').value,document.getElementById('idtanggal').value);
});

document.getElementById("btnDownload")
  .addEventListener("click", downloadCSV_TDS);
    </script>
  </body>
  </html>


