<!DOCTYPE html>
<?php
session_start();
if (isset($_SESSION['user'])) {
  $appkey = $_SESSION['appkey'];
  $env = parse_ini_file(__DIR__ . '/../config/.env');
  $suppurl = $env['API_SUPP_URL'];
  $mailpotglurl = $env['API_MAILPO_TGL_URL'];
  $envappkey = $env['APP_KEY'];
  if ($appkey !== $envappkey) {
    header("Location: login.php");
    exit();
  }



$p = $_GET['p'] ?? '';

$query = base64_decode(urldecode($p));

parse_str($query,$params);

$rdate = $params['rdate'] ?? '';
$supp  = $params['supp'] ?? '';

?>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<title>Purchase Order Change</title>
<link id="favicon" rel="icon" type="image/png" href="assets/gambar/g-green.png">
</head>

<body>

<br />
<img src="assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION"
style="float:left;width:220px;height:35px;">

PT JVCKENWOOD ELECTRONICS INDONESIA<br>
PURCHASE ORDER CHANGE DETAIL<br><br>

<div class="container mt-3">
<div id="supplierinfo" class="mb-2 fw-bold"></div>

<table class="table table-bordered table-striped" id="poTable">
<thead class="table-dark">
<tr>
<th>NO</th>
<th>TRANSMISSION NUMBER</th>
<th>PO NUMBER</th>
<th>PART NUMBER</th>
<th>PART NAME</th>
<th>NEW QTY</th>
<th>NEW DATE</th>
<th>OLD QTY</th>
<th>OLD DATE</th>
<th>PRICE</th>
<th>MODEL</th>
<th>PO TYPE</th>
<th>ALT NO</th>
<th>PO STATUS</th>
<th>SUPP STATUS</th>
<th>SUPP REASON</th>
<th>BY</th>
<th>AT</th>
<th>PUR STATUS</th>
<th>PUR REASON</th>
<th>BY</th>
<th>AT</th>
<th>MC STATUS</th>
<th>MC REASON</th>
<th>BY</th>
<th>AT</th>

</tr>
</thead>

<tbody>
<!-- data akan ditampilkan di sini -->
</tbody>

</table>

</div>

<script>
const rdate = "<?= $rdate ?>";
const supp  = "<?= $supp ?>";
let urlmailpodtl = '';

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
  urlmailpocdtl = data.urlmailpocdtl;
  loadData();
}) 
.catch(err => console.error(err));

async function loadData()
{
  try
  {
    const response = await fetch(urlmailpocdtl, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: new URLSearchParams({
        rdate: rdate,
        supp: supp
      })
    });

    const result = await response.json();
    const data = result.data;
    if (data.length > 0) {
      const supplier = data[0].supplier;
      const suppliername = data[0].suppliername;
      document.getElementById("supplierinfo").innerHTML =
      "SUPPLIER : " + supplier + " - " + suppliername;
    }
    const tbody = document.querySelector("#poTable tbody");

    let rows = "";

    data.forEach((item, index) => {

      rows += `<tr>
        <td>${index+1}</td>
        <td>${item.idno}</td>
        <td>${item.pono.trim()}</td>
        <td>${item.partno.trim()}</td>
        <td>${item.partname.trim()}</td>
        <td align="right">${item.newqty}</td>
        <td>${item.newdate}</td>
        <td align="right">${item.oldqty}</td>
        <td>${item.olddate}</td>
        <td align="right">${Number(item.price).toFixed(5)}</td>
        <td>${item.model.trim()}</td>
        <td>${item.potype.trim()}</td>
        <td>${item.altno ?? ''}</td>
        <td>${item.status ?? ''}</td>
        <td>${item.supconfstatus ?? ''}</td>
        <td>${item.supconfreason ?? ''}</td>
        <td>${item.supconfby ?? ''}</td>
        <td>${item.supconfat ?? ''}</td>
        <td>${item.purconfstatus ?? ''}</td>
        <td>${item.purconfreason ?? ''}</td>
        <td>${item.purconfby ?? ''}</td>
        <td>${item.purconfat ?? ''}</td>
        <td>${item.mcconfstatus ?? ''}</td>
        <td>${item.mcconfreason ?? ''}</td>
        <td>${item.mcconfby ?? ''}</td>
        <td>${item.mcconfat ?? ''}</td>
      </tr>`;

    });

    tbody.innerHTML = rows;

  }
  catch(err)
  {
    console.error(err);
  }
}



</script>

</script>
</body>
</html>

<?php




} else {
  header("Location: index.php");
  exit();
}
?>