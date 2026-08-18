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
  $level = $_SESSION['level'];
  $envappkey = $env['APP_KEY'];

  if ($appkey !== $envappkey) {
    header("Location: login.php");
    exit();
  }

$p = $_GET['p'] ?? '';

$query = base64_decode(urldecode($p));

parse_str($query, $params);

$filterby = $params['filterby'] ?? 'month';
$tahun = $params['tahun'] ?? '';
$bulan = $params['bulan'] ?? '';
$tglawal = $params['tglawal'] ?? '';
$tglakhir = $params['tglakhir'] ?? '';
?>
<!DOCTYPE html>
  <html lang="en">
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <title>Purchase Order Repeat</title>
  <link id="favicon" rel="icon" type="image/png" href="assets/gambar/g-green.png">
  </head>
  <body>
<br>

<img src="assets/gambar/jvc.gif"
     style="float:left;width:220px;height:35px;">

PT JVCKENWOOD ELECTRONICS INDONESIA<br>
PURCHASE ORDER CHANGE REJECT BY SUPPLIER
<br><br>

<div class="container-fluid">

<div class="card">

<div class="card-header bg-danger text-white">

<b>Reject POC Supplier</b>

</div>

<div class="card-body">

<div class="mb-3">
<?php if ($filterby === 'range') { ?>
<b>Date Between :</b> <?= htmlspecialchars($tglawal) ?>
&nbsp;&nbsp;
<b>AND</b>
&nbsp;&nbsp;
<?= htmlspecialchars($tglakhir) ?>
<?php } else { ?>
<b>Tahun :</b> <?= htmlspecialchars($tahun) ?>
&nbsp;&nbsp;&nbsp;
<b>Bulan :</b> <?= htmlspecialchars($bulan) ?>
<?php } ?>
</div>

<table class="table table-bordered table-hover" id="tblRejectSupplier">

<thead class="table-dark">

<tr>

<th width="70">No</th>

<th width="120">Supplier</th>

<th>Supplier Name</th>

<th width="120">Total</th>

<th width="150">Action</th>

</tr>

</thead>

<tbody>

<!-- nanti diisi javascript -->

</tbody>

</table>

</div>

</div>

</div>
<script>

const filterby = "<?= htmlspecialchars($filterby, ENT_QUOTES) ?>";
const tahun = "<?= htmlspecialchars($tahun, ENT_QUOTES) ?>";
const bulan = "<?= htmlspecialchars($bulan, ENT_QUOTES) ?>";
const tglawal = "<?= htmlspecialchars($tglawal, ENT_QUOTES) ?>";
const tglakhir = "<?= htmlspecialchars($tglakhir, ENT_QUOTES) ?>";

function encryptParam(data)
{
    return encodeURIComponent(btoa(data));
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
.then(async data => 
{
  user = data.user;
  level = data.level;
  appkey = data.appkey;
  urlsupp = data.urlsupp;  
  loadData();
}) 
.catch(err => console.error(err));

async function loadData()
{
    try
    {
        const response = await fetch('../api/apimailpocrejectsupplier.php',
        {
            method:'POST',
            headers:{
                'Content-Type':'application/x-www-form-urlencoded'
            },
            body:new URLSearchParams({
                filterby: filterby,
                tahun: tahun,
                bulan: bulan,
                tglawal: tglawal,
                tglakhir: tglakhir
            })
        });

        const result = await response.json();

        const tbody = document.querySelector("#tblRejectSupplier tbody");

        tbody.innerHTML = "";

        result.data.forEach((item,index)=>{

            const param = encryptParam(
                filterby === "range"
                    ? `supp=${item.supplier}&filterby=range&tglawal=${tglawal}&tglakhir=${tglakhir}`
                    : `supp=${item.supplier}&tahun=${tahun}&bulan=${bulan}`
            );

            tbody.innerHTML += `
            <tr>
                <td align="right">${index+1}</td>
                <td>${item.supplier}</td>
                <td>${item.suppliername.trim()}</td>
                <td align="right">${item.total}</td>
                <td>
                    <a href="mailpocdtl1.php?p=${param}"
                       class="btn btn-primary btn-sm"
                       target="_blank">
                       VIEW DETAIL
                    </a>
                </td>
            </tr>`;
        });

    }
    catch(err)
    {
        console.error(err);
    }
}


</script>
  </body>
  </html>