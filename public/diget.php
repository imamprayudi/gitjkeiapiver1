
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
  <title>Delivery Instructions</title>
  <link id="favicon" rel="icon" type="image/png" href="assets/gambar/g-green.png">
  </head>
  <body>
    <?php include 'menu.php'; ?>
    <br />
    <img src="assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION" 
    style="float:left;width:220px;height:35px;">
    PT JVCKENWOOD ELECTRONICS INDONESIA<br />
    GET DELIVERY INSTRUCTIONS<br /><br />
<div class="mt-3" id="boxhasil" style="display:none;">
    <h5>Hasil API</h5>
    <pre id="hasiljson"
    style="background:#111;color:#0f0;padding:15px;border-radius:5px;">
    </pre>
</div>
    <form id="formdi">
      Supplier : &nbsp;&nbsp;
      <select name="supp" id="idsupp">
      </select>&nbsp;&nbsp;
      <label for="idtgl">SELECT DATE : </label>
      <input type="date" id="idtgl" name="tgl">&nbsp;&nbsp;
      &nbsp;&nbsp;
      <label for="idsq">SEQUENCE :</label>
<select id="idsq" name="squence">
    <option value="1">1</option>
    <option value="2">2</option>
    <option value="3">3</option>
    <option value="4">4</option>
</select>
      <input type="submit" value="GET DI">
    </form>
    <br />
 Petunjuk :<br />
pilih sequence = 1 , untuk mendapatkan Delivery Instruction secara kondisi normal <br>
Untuk Supplier dengan 1X delivery per hari follow Time Delivery Schedule, <br>
pertama pilih sequence 1 untuk mendapatkan delivery instruction dengan quantity sesuai Time Delivery Schedule<br>
Setelah itu update data dengan input invoice,<br>
Sesuaikan quantity dengan actual delivery<br>
Upload data.....<br />
Jika pada sequence 1 ada perubahan quantity , maka balance akan muncul pada Delivery Instruction sequence selanjutnya<br>
maka perlu untuk Get Delivery Instruction sequence selanjutnya....<br>
<br>
Pastikan data sequence 1 telah terupload semua !!!<br>
kembali ke menu Get Delivery Instruction<br>
pilih tanggal yg sama kemudian pilih sequence 2<br>
maka Delivery Instruction akan muncul dengan balance quantity.<br>
update data dengan input invoice dan upload......<br>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
<script>

let user = '';
let level = '';
let appkey = '';   
let urlsupp = '';
let urlmailpotgl = '';
let urldimakejkei = '';

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
  urldimakejkei = data.urldimakejkei;
  console.log('urldimakejkei:', urldimakejkei);
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

async function makeDi(supp,tgl,sequence){
 try{

   const box = document.getElementById("boxhasil");
   const hasil = document.getElementById("hasiljson");
   // tampilkan box saat proses dimulai
   box.style.display = "block";
   hasil.textContent = "Processing...";

   const response = await fetch(
    urldimakejkei,
   {
      method:'POST',
      body:new URLSearchParams({
         supp:supp,
         tgl:tgl,
         disq:sequence
      })
   });

   const data = await response.json();

   hasil.textContent = JSON.stringify(data,null,2);

 }catch(err){

   document.getElementById("boxhasil").style.display = "block";
   document.getElementById("hasiljson").textContent =
   "ERROR : " + err;

 }
}

document.getElementById("formdi").addEventListener("submit", function(e){
  e.preventDefault();

  const suppid = document.getElementById('idsupp').value;
  const tgl = document.getElementById('idtgl').value;
  const sequence = document.getElementById('idsq').value;

  makeDi(suppid, tgl, sequence);

});

</script>
  </body>
</html>

