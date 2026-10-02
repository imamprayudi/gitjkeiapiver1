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

$p = $_GET['p'] ?? '';

$query = base64_decode(urldecode($p));

parse_str($query, $params);

$filterby = $params['filterby'] ?? 'month';
$tahun  = $params['tahun'] ?? '';
$bulan  = $params['bulan'] ?? '';
$tglawal = $params['tglawal'] ?? '';
$tglakhir = $params['tglakhir'] ?? '';
$supp   = $params['supp'] ?? '';
$status = $params['status'] ?? '';

// if (!in_array($status, $validstatus)) {
//     $status = '';
// }

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

.table-container{
  max-height:600px;
  overflow:auto;
  border:1px solid #ddd;
}

/* FREEZE HEADER */
#poTable thead th{
  position: sticky;
  top: 0;
  z-index: 5;
  background:#212529; /* warna bootstrap table-dark */
  color:white;
  white-space:nowrap;
}

#poTable thead th[data-field]{
  cursor:pointer;
  user-select:none;
}

#poTable thead th[data-field]:hover{
  background:#343a40;
}

#poTable thead th.sort-asc::after{
  content:" ▲";
  font-size:11px;
}

#poTable thead th.sort-desc::after{
  content:" ▼";
  font-size:11px;
}


</style>

</head>

<body>

<br />
<img src="assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION"
style="float:left;width:220px;height:35px;">

PT JVCKENWOOD ELECTRONICS INDONESIA<br>
PURCHASE ORDER CHANGE DETAIL&nbsp;&nbsp;*** The Purchase Order Change consider accepted if there is no reply within 5 days ***<br><br>

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

<div class="d-flex align-items-center gap-2 mb-4">

<input type="text"
id="suppreason"
class="form-control"
style="width:400px;"
placeholder="INPUT REASON IF REJECTED">

<button class="btn btn-primary" onclick="updateStatus()">
UPDATE
</button>

<button class="btn btn-success" onclick="downloadTable()">
Download Excel
</button>

</div>
<div class="table-container">
<table class="table table-bordered table-striped" id="poTable">
<thead class="table-dark">
<tr>
<th><input type="checkbox" id="checkAllTop"></th>
<th>NO</th>
<th data-field="idno">TRANSMISSION NUMBER</th>
<th data-field="pono">PO NUMBER</th>
<th data-field="partno">PART NUMBER</th>
<th data-field="partname">PART NAME</th>
<th data-field="newqty">NEW QTY</th>
<th data-field="newdate">NEW DATE</th>
<th data-field="oldqty">OLD QTY</th>
<th data-field="olddate">OLD DATE</th>
<th data-field="price">PRICE</th>
<th data-field="model">MODEL</th>
<th data-field="potype">PO TYPE</th>
<th data-field="altno">ALT NO</th>
<th data-field="status">PO STATUS</th>
<th data-field="supconfstatus">SUPP STATUS</th>
<th data-field="supconfreason">SUPP REASON</th>
<th data-field="supconfby">BY</th>
<th data-field="supconfat">AT</th>
<th data-field="purconfstatus">PUR STATUS</th>
<th data-field="purconfreason">PUR REASON</th>
<th data-field="purconfby">BY</th>
<th data-field="purconfat">AT</th>
<th data-field="mcconfstatus">MC STATUS</th>
<th data-field="mcconfreason">MC REASON</th>
<th data-field="mcconfby">BY</th>
<th data-field="mcconfat">AT</th>
<th data-field="planconfstatus">PLAN STATUS</th>
<th data-field="planconfreason">PLAN REASON</th>
<th data-field="planconfby">BY</th>
<th data-field="planconfat">AT</th>

</tr>
</thead>

<tbody>
<!-- data akan ditampilkan di sini -->
</tbody>

</table>
</div>
</div>

<script>
const filterby = "<?= htmlspecialchars($filterby, ENT_QUOTES) ?>";
const tahun  = "<?= htmlspecialchars($tahun, ENT_QUOTES) ?>";
const bulan  = "<?= htmlspecialchars($bulan, ENT_QUOTES) ?>";
const tglawal = "<?= htmlspecialchars($tglawal, ENT_QUOTES) ?>";
const tglakhir = "<?= htmlspecialchars($tglakhir, ENT_QUOTES) ?>";
const supp   = "<?= htmlspecialchars($supp, ENT_QUOTES) ?>";
const status = 'REJECTED';
let urlmailpocdtl = '';
let tableData = [];
let sortField = "";
let sortDir = "asc";

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
 // await updateReadStatus();
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
    const response = await fetch('../api/apimailpocdtl1.php', {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: new URLSearchParams({
        filterby:filterby,
        tahun:tahun,
        bulan:bulan,
        tglawal:tglawal,
        tglakhir:tglakhir,
        supp:supp,
        status:status
      })
    });

    const result = await response.json();
    tableData = Array.isArray(result.data) ? result.data : [];
    if (tableData.length > 0) {
      const supplier = tableData[0].supplier;
      const suppliername = tableData[0].suppliername;
      document.getElementById("supplierinfo").innerHTML =
      "SUPPLIER : " + supplier + " - " + suppliername;
    }
    renderTable();

  }
  catch(err)
  {
    console.error(err);
  }
}

function sortValue(item, field)
{
  const value = item[field];
  if (value === null || value === undefined) {
    return "";
  }
  return value;
}

function compareRows(a, b, field, dir)
{
  const numberFields = ["newqty", "oldqty", "price"];
  let va = sortValue(a, field);
  let vb = sortValue(b, field);

  if (numberFields.indexOf(field) !== -1)
  {
    va = Number(va);
    vb = Number(vb);
    if (isNaN(va)) va = 0;
    if (isNaN(vb)) vb = 0;
    return dir === "asc" ? va - vb : vb - va;
  }

  va = String(va).trim().toUpperCase();
  vb = String(vb).trim().toUpperCase();
  if (va < vb) return dir === "asc" ? -1 : 1;
  if (va > vb) return dir === "asc" ? 1 : -1;
  return 0;
}

function updateSortHeader()
{
  document.querySelectorAll("#poTable thead th[data-field]").forEach(function(th)
  {
    th.classList.remove("sort-asc", "sort-desc");
    if (th.getAttribute("data-field") === sortField)
    {
      th.classList.add(sortDir === "asc" ? "sort-asc" : "sort-desc");
    }
  });
}

function renderTable()
{
  const rowsData = tableData.slice();
  if (sortField)
  {
    rowsData.sort(function(a, b)
    {
      return compareRows(a, b, sortField, sortDir);
    });
  }

  const tbody = document.querySelector("#poTable tbody");
  let rows = "";

  rowsData.forEach(function(item, index)
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
        <td>${item.planconfstatus ?? ''}</td>
        <td>${item.planconfreason ?? ''}</td>
        <td>${item.planconfby ?? ''}</td>
        <td>${item.planconfat ?? ''}</td>
      </tr>`;
  });

  tbody.innerHTML = rows;
  updateSortHeader();
}

document.querySelector("#poTable thead").addEventListener("click", function(e)
{
  const th = e.target.closest("th[data-field]");
  if (!th) return;

  const field = th.getAttribute("data-field");
  if (sortField === field)
  {
    sortDir = (sortDir === "asc") ? "desc" : "asc";
  }
  else
  {
    sortField = field;
    sortDir = "asc";
  }
  renderTable();
});

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


function downloadTable()
{

  const table = document.getElementById("poTable");
  let csv = [];

  for (let i = 0; i < table.rows.length; i++)
  {

    let row = [];
    let cols = table.rows[i].querySelectorAll("th, td");

    for (let j = 0; j < cols.length; j++)
    {

      // skip kolom checkbox
      if(j === 0) continue;

      let text = cols[j].innerText.replace(/\n/g," ").trim();
      row.push('"' + text + '"');

    }

    csv.push(row.join(","));

  }

  const csvFile = new Blob([csv.join("\n")], {type: "text/csv"});

  const downloadLink = document.createElement("a");

  downloadLink.download = "poc.csv";
  downloadLink.href = window.URL.createObjectURL(csvFile);

  downloadLink.style.display = "none";

  document.body.appendChild(downloadLink);

  downloadLink.click();

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