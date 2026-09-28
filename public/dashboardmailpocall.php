
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
  <title>Purchase Order Change</title>
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

.card-label{
    font-size:13px;
    font-weight:600;
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
    PURCHASE ORDER CHANGE DASHBOARD 

    <form action="">&nbsp;&nbsp;
<label><input type="radio" name="filterby" value="month" checked onchange="updateFilterMode()"> YEAR / MONTH</label>
&nbsp;&nbsp;
<label for="idtahun">YEAR :</label>
<input type="number" id="idtahun" name="tahun" style="width:90px;">

&nbsp;&nbsp;

<label for="idbulan">MONTH :</label>
<select id="idbulan" name="bulan">
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

&nbsp;&nbsp;|&nbsp;&nbsp;

<label><input type="radio" name="filterby" value="range" onchange="updateFilterMode()"> DATE RANGE</label>
&nbsp;&nbsp;
<label for="idtglawal">DATE BETWEEN :</label>
<input type="date" id="idtglawal" name="tglawal" disabled>
&nbsp;&nbsp;
<label for="idtglakhir">AND</label>
<input type="date" id="idtglakhir" name="tglakhir" disabled>

<input type="submit" value="Display">
</form>
 
<div class="container-fluid mt-3">
  <div class="row g-3">

    <div class="col-md-3">
      <div class="card shadow border-0">
        <div class="card-body text-center">
          <h1 id="poc_total" class="display-5 fw-bold text-primary">0</h1>
          <div class="card-label">Total POC</div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow border-0">
        <div class="card-body text-center">
          <h1 id="poc_up" class="display-5 fw-bold text-info">0</h1>
          <div class="card-label">UP</div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow border-0">
        <div class="card-body text-center">
          <h1 id="poc_down" class="display-5 fw-bold text-secondary">0</h1>
          <div class="card-label">DOWN</div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow border-0">
        <div class="card-body text-center">
          <h1 id="poc_cancellation" class="display-5 fw-bold text-danger">0</h1>
          <div class="card-label">CANCELLATION</div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow border-0">
        <div class="card-body text-center">
          <h1 id="poc_reduce_qty" class="display-5 fw-bold text-warning">0</h1>
          <div class="card-label">REDUCE QTY</div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow border-0">
        <div class="card-body text-center">
          <h1 id="poc_increase_qty" class="display-5 fw-bold text-success">0</h1>
          <div class="card-label">INCREASE QTY</div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow border-0">
        <div class="card-body text-center">
          <h1 id="poc_up_reduce_qty" class="display-5 fw-bold text-info">0</h1>
          <div class="card-label">UP &amp; REDUCE QTY</div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow border-0">
        <div class="card-body text-center">
          <h1 id="poc_up_increase_qty" class="display-5 fw-bold text-success">0</h1>
          <div class="card-label">UP &amp; INCREASE QTY</div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow border-0">
        <div class="card-body text-center">
          <h1 id="poc_down_reduce_qty" class="display-5 fw-bold text-warning">0</h1>
          <div class="card-label">DOWN &amp; REDUCE QTY</div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow border-0">
        <div class="card-body text-center">
          <h1 id="poc_down_increase_qty" class="display-5 fw-bold text-primary">0</h1>
          <div class="card-label">DOWN &amp; INCREASE QTY</div>
        </div>
      </div>
    </div>

  </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
<script>

let user = '';
let level = '';
let appkey = '';
let urlsupp = '';

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
})
.catch(err => console.error(err));


function getFilterBy()
{
    const selected = document.querySelector('input[name="filterby"]:checked');
    return selected ? selected.value : "month";
}

function updateFilterMode()
{
    const isRange = getFilterBy() === "range";
    document.getElementById("idtahun").disabled = isRange;
    document.getElementById("idbulan").disabled = isRange;
    document.getElementById("idtglawal").disabled = !isRange;
    document.getElementById("idtglakhir").disabled = !isRange;
}

function displayData()
{
    const filterby = getFilterBy();

    if (filterby === "range")
    {
        const tglawal = document.getElementById("idtglawal").value;
        const tglakhir = document.getElementById("idtglakhir").value;

        if (tglawal === "" || tglakhir === "")
        {
            alert("Input date range");
            return;
        }

        if (tglawal > tglakhir)
        {
            alert("Start date must be before end date");
            return;
        }

        getMailpoc({
            filterby: "range",
            tglawal: tglawal,
            tglakhir: tglakhir
        });
        return;
    }

    const tahun = document.getElementById("idtahun").value;
    const bulan = document.getElementById("idbulan").value;

    if (tahun === "" || bulan === "")
    {
        alert("Input year and month");
        return;
    }

    getMailpoc({
        filterby: "month",
        tahun: tahun,
        bulan: bulan
    });
}

async function getMailpoc(params)
{
  try
  {
    const response = await fetch("../api/apidashboardmailpocall.php",
    {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:new URLSearchParams(params)
    });

    const result = await response.json();
    const d = result.data;
    document.getElementById("poc_total").innerHTML = d.poc_total;
    document.getElementById("poc_up").innerHTML = d.poc_up;
    document.getElementById("poc_down").innerHTML = d.poc_down;
    document.getElementById("poc_cancellation").innerHTML = d.poc_cancellation;
    document.getElementById("poc_reduce_qty").innerHTML = d.poc_reduce_qty;
    document.getElementById("poc_increase_qty").innerHTML = d.poc_increase_qty;
    document.getElementById("poc_up_reduce_qty").innerHTML = d.poc_up_reduce_qty;
    document.getElementById("poc_up_increase_qty").innerHTML = d.poc_up_increase_qty;
    document.getElementById("poc_down_reduce_qty").innerHTML = d.poc_down_reduce_qty;
    document.getElementById("poc_down_increase_qty").innerHTML = d.poc_down_increase_qty;
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
    document.getElementById("idtahun").value = now.getFullYear();
    document.getElementById("idbulan").value = (now.getMonth() + 1).toString();
    updateFilterMode();
    displayData();
});

</script>
  </body>
</html>
