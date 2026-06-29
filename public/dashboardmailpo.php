
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
  $mailpotglurl = $env['API_MAILPO_TGL_URL'];
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
  <title>Purchase Order</title>
  <style type="text/css">
.card{
    border-radius:15px;
    transition:.25s;
}

.card:hover{
    transform:translateY(-5px);
    box-shadow:0 .8rem 2rem rgba(0,0,0,.15)!important;
}

.display-5{
    font-weight:bold;
}
  </style>
  <link id="favicon" rel="icon" type="image/png" href="assets/gambar/g-green.png">
  </head>
  <body>
    <?php include 'menu.php'; ?>
    <br />
    <img src="assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION" 
    style="float:left;width:220px;height:35px;">
    PT JVCKENWOOD ELECTRONICS INDONESIA<br />
    PURCHASE ORDER DASHBOARD 

    <form action="">
      Supplier : &nbsp;&nbsp;
      <select name="supp" id="idsupp">
      </select>&nbsp;&nbsp;
      <label for="idtahun">TAHUN :</label>
<input type="number" id="idtahun" name="tahun" style="width:90px;">

&nbsp;&nbsp;

<label for="idbulan">BULAN :</label>
<select id="idbulan" name="bulan">
    <option value="ALL">ALL</option>
    <option value="1">Januari</option>
    <option value="2">Februari</option>
    <option value="3">Maret</option>
    <option value="4">April</option>
    <option value="5">Mei</option>
    <option value="6">Juni</option>
    <option value="7">Juli</option>
    <option value="8">Agustus</option>
    <option value="9">September</option>
    <option value="10">Oktober</option>
    <option value="11">November</option>
    <option value="12">Desember</option>
</select>
      <input type="submit" value="Display">
    </form>
 
<!-- CARD DISPLAY FOR PO -->    
<div class="container-fluid mt-3">
  <div class="row g-3">

    <div class="col-md-3">
<div class="card shadow border-0">
<div class="card-body text-center">
<h1 id="po_total" class="display-5 fw-bold text-primary">0</h1>
<div>Total PO</div>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card shadow border-0">
<div class="card-body text-center">
<h1 id="po_unread" class="display-5 fw-bold text-warning">0</h1>
<div>Unread PO</div>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card shadow border-0">
<div class="card-body text-center">
<h1 id="po_read" class="display-5 fw-bold text-success">0</h1>
<div>Read PO</div>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card shadow border-0">
<div class="card-body text-center">
<h1 id="po_reject" class="display-5 fw-bold text-danger">0</h1>
<div>Reject PO</div>
</div>
</div>
</div>

</div>

</div>
<!-- END OF CARD DISPLAY FOR PO -->

<!-- CARD DISPLAY FOR POC -->
<div class="container-fluid mt-3">
  <div class="row g-3">

    <div class="col-md-3">
<div class="card shadow border-0">
<div class="card-body text-center">
<h1 id="poc_total" class="display-5 fw-bold text-primary">0</h1>
<div>Total POC</div>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card shadow border-0">
<div class="card-body text-center">
<h1 id="poc_unread" class="display-5 fw-bold text-warning">0</h1>
<div>Unread POC</div>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card shadow border-0">
<div class="card-body text-center">
<h1 id="poc_read" class="display-5 fw-bold text-success">0</h1>
<div>Read POC</div>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card shadow border-0">
<div class="card-body text-center">
<h1 id="poc_reject" class="display-5 fw-bold text-danger">0</h1>
<div>Reject POC</div>
</div>
</div>
</div>

</div>

</div>
<!-- END OF CARD DISPLAY FOR POC -->

 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
<script>

let user = '';
let level = '';
let appkey = '';   
let urlsupp = '';
let urldashboardmailpo = '';

fetch('getsession.php',
{
  method:'GET',
  headers:{'X-Requested-With':'XMLHttpRequest'}
})
.then(response => response.json())
.then(data =>
{
  user = data.user;
  level = data.level;
  appkey = data.appkey;
  urlsupp = data.urlsupp;
  urlmailpotgl = data.urlmailpotgl;
  urldashboardmailpo = data.urldashboardmailpo;

  getSupplier(user);
})
.catch(err => console.error(err));


function encryptParam(data)
{
  return encodeURIComponent(btoa(data));
}


async function getSupplier(user)
{
  try
  {
    const response = await fetch(urlsupp,
    {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:new URLSearchParams({nama:user})
    });

    const reply = await response.text();
    const isidata = JSON.parse(reply);

    const selectsupp = document.getElementById('idsupp');

    isidata.forEach((item,index)=>
    {
      const option = document.createElement('option');
      option.value = item.kode;
      option.textContent = item.nama + ' - ' + item.kode;

      if(index===0) option.selected=true;

      selectsupp.appendChild(option);
    });

    displayData();


  }
  catch(error)
  {
    console.error(error);
  }
}


function displayData()
{
    const supp = document.getElementById('idsupp').value;
    const tahun = document.getElementById('idtahun').value;
    const bulan = document.getElementById('idbulan').value;

    getMailpo(supp, tahun, bulan);
}

async function getMailpo(supp, tahun, bulan)
{
  try
  {
    const response = await fetch(urldashboardmailpo,
    {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:new URLSearchParams({
        supplier:supp,
        tahun:tahun,
        bulan:bulan
      })
    });

    const result = await response.json();
    const d = result.data;
    document.getElementById("po_total").innerHTML   = d.po_total;
    document.getElementById("po_unread").innerHTML  = d.po_unread;
    document.getElementById("po_read").innerHTML    = d.po_read;
    document.getElementById("po_reject").innerHTML  = d.po_reject;
    document.getElementById("poc_total").innerHTML  = d.poc_total;
    document.getElementById("poc_unread").innerHTML = d.poc_unread;
    document.getElementById("poc_read").innerHTML   = d.poc_read;
    document.getElementById("poc_reject").innerHTML = d.poc_reject;

  }
  catch(error)
  {
    console.error(error);
  }
}


document.addEventListener('submit',function(e)
{
  e.preventDefault();
  displayData();

});



document.addEventListener("DOMContentLoaded", function () {

    const now = new Date();

    // Tahun sekarang
    document.getElementById("idtahun").value = now.getFullYear();

    // Bulan sekarang (1-12)
    document.getElementById("idbulan").value = (now.getMonth() + 1).toString();

});



</script>
  </body>
</html>

