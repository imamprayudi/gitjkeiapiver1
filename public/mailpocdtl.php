<!DOCTYPE html>
<?php
session_start();
if (isset($_SESSION['user'])) {
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

echo "level : " . $level . "<br>";

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

<style>
#poTable tbody tr:hover{
  background-color:#e9f5ff;
}

.row-confirmed{
  background-color:#d4edda !important;
}

.row-rejected{
  background-color:#f8d7da !important;
}

#poTable tbody tr:hover{
  background-color:#e9f5ff;
}

.row-selected{
  background-color:#fff3cd !important;
}

</style>

</head>

<body>

<br />
<img src="assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION"
style="float:left;width:220px;height:35px;">

PT JVCKENWOOD ELECTRONICS INDONESIA<br>
PURCHASE ORDER CHANGE DETAIL<br><br>

<div class="container mt-3">
<div id="supplierinfo" class="mb-2 fw-bold"></div>

<div class="mb-3">

<label><b>STATUS :</b></label>

<select id="suppstatus" class="form-select" style="width:200px;display:inline-block;">
<option value="CONFIRMED">CONFIRMED</option>
<option value="REJECTED">REJECTED</option>
</select>

<br><br>

<label><b>REASON :</b></label>

<input type="text"
id="suppreason"
class="form-control"
style="width:400px;display:inline-block;"
placeholder="INPUT REASON IF REJECTED">

<button class="btn btn-primary" onclick="updateStatus()">UPDATE</button>

</div>

<table class="table table-bordered table-striped" id="poTable">
<thead class="table-dark">
<tr>
<th><input type="checkbox" id="checkAllTop"></th>
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

const reasonawal = document.getElementById("suppreason");
reasonawal.disabled = true;

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
  urlmailpocdtl = data.urlmailpocdtl;
  await updateReadStatus();
  loadData();
}) 
.catch(err => console.error(err));


async function updateReadStatus()
{

  if(level != 3) return;

  try
  {
    await fetch("../api/apimailpocread.php",
    {
      method:"POST",
      headers:{
        "Content-Type":"application/json"
      },

      body:JSON.stringify({
        rdate:rdate,
        supp:supp
      })

    });

  }
  catch(err)
  {
    console.error(err);
  }

}


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

    data.forEach((item, index) =>
    {

     let rowClass = "";

if(item.supconfstatus === "CONFIRMED")
  rowClass = "row-confirmed";

if(item.supconfstatus === "REJECTED")
  rowClass = "row-rejected";

rows += `<tr class="${rowClass}">


   <td><input type="checkbox" class="rowcheck" value="${item.idno}"></td>
        <td>${index+1}</td>
        <td>${item.idno}</td>
        <td>${item.pono.trim()}</td>
        <td><pre>${item.partno.trim()}</pre></td>
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

async function updateStatus()
{

  const status = document.getElementById("suppstatus").value;
  const reason = document.getElementById("suppreason").value.trim();

  if(status === "")
  {
    alert("Please select status");
    return;
  }

  if(status === "REJECTED" && reason === "")
  {
    alert("Please input reason");
    return;
  }

  const checked = document.querySelectorAll(".rowcheck:checked");

  if(checked.length === 0)
  {
    alert("Please select record");
    return;
  }

  let ids = [];

  checked.forEach(cb=>{
    ids.push(cb.value);
  });

  try
  {

    const response = await fetch("../api/apimailpocupdate.php",
    {
      method:"POST",
      headers:{
        "Content-Type":"application/json"
      },

      body:JSON.stringify({
        ids:ids,
        status:status,
        reason:reason,
        level:level
      })
    });

    const result = await response.json();

    alert(result.message);

    loadData();

  }
  catch(err)
  {
    console.error(err);
  }

}



function highlightRow(cb)
{
  const row = cb.closest("tr");

  if(cb.checked)
    row.classList.add("row-selected");
  else
    row.classList.remove("row-selected");
}



document.addEventListener("change", function(e)
{

  if(e.target.id === "checkAllTop")
  {

    const checked = e.target.checked;

    document.querySelectorAll(".rowcheck").forEach(cb =>
    {
      cb.checked = checked;
      highlightRow(cb);
    });

  }


  if(e.target.classList.contains("rowcheck"))
  {
    highlightRow(e.target);
  }

});



document.getElementById("suppstatus").addEventListener("change", function()
{

  const reason = document.getElementById("suppreason");

  if(this.value === "REJECTED")
  {
    reason.disabled = false;
  }
  else
  {
    reason.value = "";
    reason.disabled = true;
  }

});



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