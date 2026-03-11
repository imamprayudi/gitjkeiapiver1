<?php
session_start();

if (empty($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html>
<head>

<title>Print Barcode Label</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{background:#f4f6f9}
.card{max-width:720px;margin:auto}
</style>

</head>
<body>
  
  <?php include 'menu.php'; ?>

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-primary text-white">
PRINT BARCODE LABEL
</div>

<div class="card-body">

<form>

<!-- SUPPLIER -->
<label>Supplier</label>
<select id="suppname" class="form-select mb-3"></select>


<!-- PART -->
<label>Part</label>
<select id="part" class="form-select mb-3"></select>

<!-- INVOICE -->
<label>Invoice</label>
<input id="invoice" class="form-control mb-3">

<!-- PO -->
<label>PO</label>
<select id="po" class="form-select mb-3"></select>


<!-- QTY -->
<label>Qty</label>
<input id="qty" class="form-control mb-3">


<!-- DATE -->
<label>Delivery</label>
<input type="date" id="deldate" class="form-control mb-3">

<label>Prod</label>
<input type="date" id="proddate" class="form-control mb-3">

<label>Elec Test</label>
<input type="text" id="elec" class="form-control mb-3"> 


<button type="button" id="btnView" class="btn btn-success w-100">
View Label
</button>

</form>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  
/* =========================
CONFIG
========================= */
const urlqty = '../api/apiqty.php';
const urlpo = '../api/apipo.php';
const urlpart = '../api/apipart.php';
const urlsupp = '../api/apisupp.php';   // API lama POST
const api     = '../api/apibarcode.php';  // API baru GET
const currentUser = "<?= $user ?>";


/* =========================
Helper
========================= */
async function fetchJSON(url){
  const res = await fetch(url,{credentials:'include'});
  return await res.json();
}


/* =========================
SUPPLIER (POST lama)
========================= */
async function getSupplier(user)
{
  try {

    const response = await fetch(urlsupp, {
      method: 'POST',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({ nama:user })
    });

    const data = await response.json();

    const sel = document.getElementById('suppname');
    sel.innerHTML='';

    data.forEach((item,index)=>{

      /* text: nama + kode, value: kode */
      const opt = new Option(
        item.nama + ' - ' + item.kode,
        item.kode
      );

      if(index===0) opt.selected=true;

      sel.appendChild(opt);
    });

    /* auto load part pertama */
    if(sel.value) loadPart(sel.value);

  } catch(e){
    console.error(e);
  }
}


/* =========================
PART
========================= */

async function loadPart(supp)
{
  const response = await fetch(urlpart,{
    method:'POST',
    credentials:'include',
    headers:{
      'Content-Type':'application/x-www-form-urlencoded'
    },
    body:new URLSearchParams({
      suppcode:supp
    })
  });

  const data = await response.json();

  const sel = document.getElementById('part');
  sel.innerHTML='';

  data.forEach((item,index)=>{
    const opt = new Option(item.partno,item.partno);
    if(index===0) opt.selected=true;
    sel.appendChild(opt);
  });

  /* 🔥 PENTING → langsung load PO */
  if(sel.value){
    loadPO();
  }
}

/* =========================
PO
========================= */
async function loadPO()
{
  const supp = suppname.value;
  const part = document.getElementById('part').value;

  const response = await fetch(urlpo,{
    method:'POST',
    credentials:'include',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:new URLSearchParams({
      suppcode:supp,
      partnumber:part
    })
  });

  const data = await response.json();

  const sel = document.getElementById('po');
  sel.innerHTML='';

  data.forEach((r,i)=>{
    const opt = new Option(r.ponumber,r.ponumber);

    if(i===0) opt.selected = true;

    sel.appendChild(opt);
  });

  /* =========================
     🔥 PENTING
     auto load qty
  ========================= */
  if(sel.value){
    loadQty();
  }
}


/* =========================
QTY
========================= */
async function loadQty()
{
  const supp = suppname.value;
  const part = document.getElementById('part').value;
  const po   = document.getElementById('po').value;

  if(!supp || !part || !po) return;

  const response = await fetch(urlqty,{
    method:'POST',
    credentials:'include',
    headers:{
      'Content-Type':'application/x-www-form-urlencoded'
    },
    body:new URLSearchParams({
      suppcode:supp,
      partnumber:part,
      ponumber:po
    })
  });

  const data = await response.json();
  if(data.length){
    document.getElementById('qty').value = data[0].qty;
  }
}


/* =========================
EVENT
========================= */
document.addEventListener('DOMContentLoaded',()=>{

  getSupplier(currentUser);

  suppname.onchange = ()=> loadPart(suppname.value);
  part.onchange = loadPO;
  po.onchange = loadQty;
  btnView.onclick = ()=>{

  const part      = document.getElementById('part').value;
  const suppcode  = document.getElementById('suppname').value;
  const suppname  = document.getElementById('suppname').selectedOptions[0].text;
  const po        = document.getElementById('po').value;
  const qty       = document.getElementById('qty').value;
  const deldate   = document.getElementById('deldate').value;
  const proddate  = document.getElementById('proddate').value;
  const invno     = document.getElementById('invoice').value;
  const et        = document.getElementById('elec').value;

  const params = new URLSearchParams({
    part: part,
    suppname: suppname,
    suppcode: suppcode,
    po: po,
    qty: qty,
    deldate: deldate,
    proddate: proddate,
    invno: invno,
    et: et
  });

  window.open("barcodeview.php?" + params.toString());
}

});
</script>

</body>
</html>