
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
  <title>Delivery Instructions Edit</title>
  <link id="favicon" rel="icon" type="image/png" href="assets/gambar/g-green.png">
  </head>
  <body>
    <?php include 'menu.php'; ?>
    <br />
    <img src="assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION" 
    style="float:left;width:220px;height:35px;">
    PT JVCKENWOOD ELECTRONICS INDONESIA<br />
    DELIVERY INSTRUCTIONS VIEW<br /><br />

    <form id="formdi">
      Supplier : &nbsp;&nbsp;
      <select name="supp" id="idsupp">
      </select>&nbsp;&nbsp;
      <label for="idtgl">SELECT DATE : </label>
      <input type="date" id="idtgl" name="tgl">&nbsp;&nbsp;
      &nbsp;&nbsp;
      <label>Status :</label>
      <select id="idstatus" name="status">
        <option value="0">Not Yet Upload</option>
        <option value="1">Already Upload</option>
        <option value="3">Already Received</option>
      </select>
      <input type="submit" value="VIEW DI">
    </form>
    <br />
    <!-- GANTI boxhasil lama dengan ini -->
<div class="mt-3" id="boxhasil" style="display:none;">
  <h5></h5>


  <div class="table-responsive">
    <table class="table table-bordered table-striped table-sm" id="tabelhasil">
      <thead class="table-dark">
<tr>
  <th>No</th>
  <th>ID</th>
  <th>Supp Code</th>
  <th>PO Req Date</th>
  <th>PO</th>
  <th>PART NUMBER</th>
  <th>QTY</th>
  <th>Delivery Date</th>
  <th>INVOICE</th>
  <th>Status</th>
  <th>Time</th>
  <th>SQ</th>
</tr>
</thead>
      <tbody id="bodyhasil"></tbody>
    </table>
  </div>

  <pre id="hasiljson"
  style="background:#111;color:#0f0;padding:15px;border-radius:5px;display:none;">
  </pre>
</div>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
<script>

let user = '';
let level = '';
let appkey = '';   
let urlsupp = '';
let urlmailpotgl = '';

const tomorrow = new Date();
tomorrow.setDate(tomorrow.getDate() + 1); // tambah 1 hari
const yyyy = tomorrow.getFullYear();
const mm = String(tomorrow.getMonth() + 1).padStart(2, '0');
const dd = String(tomorrow.getDate()).padStart(2, '0');
const formatTanggal = `${yyyy}-${mm}-${dd}`;
document.getElementById("idtgl").value = formatTanggal;

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

  }
  catch(error)
  {
    console.error(error);
  }
}


async function makeDi(supp, tgl, status){
 try{

   const box = document.getElementById("boxhasil");
   const tbody = document.getElementById("bodyhasil");

   box.style.display = "block";
   tbody.innerHTML = `
     <tr>
       <td colspan="9" class="text-center">Processing...</td>
     </tr>
   `;

   const response = await fetch(
   'http://136.198.117.117/api/apijkeidev/apidiedit.php',
   {
      method:'POST',
      body:new URLSearchParams({
         supp:supp,
         tgld:tgl,
         status:status
      })
   });

   const result = await response.json();

   // kosongkan table
   tbody.innerHTML = '';

   if(result.success && result.data.length > 0){

  tbody.innerHTML = "";

  result.data.forEach((row,index)=>{

    tbody.innerHTML += `
      <tr>
        <td>${index+1}</td>
        <td>${row.supptglpo?.trim() ?? ''}</td>
        <td>${row.supp?.trim() ?? ''}</td>
        <td>${row.tgli?.substring(0,10) ?? ''}</td>
        <td>${row.po?.trim() ?? ''}</td>
        <td>${row.partno?.trim() ?? ''}</td>
        <td>${row.qty ?? ''}</td>
        <td>${row.tgld?.substring(0,10) ?? ''}</td>
        <td>${row.invoice?.trim() ?? ''}</td>
        <td>${row.status ?? ''}</td>
        <td>${row.ditime?.trim() ?? ''}</td>
        <td>${row.disq ?? ''}</td>
      </tr>
    `;

  });

}
   else{

      tbody.innerHTML = `
        <tr>
          <td colspan="9" class="text-center text-danger">
            Data tidak ditemukan
          </td>
        </tr>
      `;
   }

 }catch(err){

   document.getElementById("boxhasil").style.display = "block";
   document.getElementById("bodyhasil").innerHTML = `
      <tr>
        <td colspan="9" class="text-danger text-center">
          ERROR : ${err}
        </td>
      </tr>
   `;
 }
}

document.getElementById("formdi").addEventListener("submit", function(e){
  e.preventDefault();

  const suppid = document.getElementById('idsupp').value;
  const tgl = document.getElementById('idtgl').value;
  const status = document.getElementById('idstatus').value;

  makeDi(suppid, tgl, status);

});

document.addEventListener("change", function(e){

  if(e.target.id === "checkall")
  {
      const status = e.target.checked;

      document.querySelectorAll(".pilihrow").forEach(chk=>{
          chk.checked = status;
      });
  }

});

document.addEventListener("change", function(e){

  if(e.target.classList.contains("pilihrow"))
  {
      const total = document.querySelectorAll(".pilihrow").length;
      const checked = document.querySelectorAll(".pilihrow:checked").length;

      document.getElementById("checkall").checked = (total === checked);
  }

});

</script>
  </body>
</html>

