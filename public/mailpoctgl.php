
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
  <link id="favicon" rel="icon" type="image/png" href="assets/gambar/g-green.png">
  </head>
  <body>
    <?php include 'menu.php'; ?>
    <br />
    <img src="assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION" 
    style="float:left;width:220px;height:35px;">
    PT JVCKENWOOD ELECTRONICS INDONESIA<br />
    PURCHASE ORDER CHANGE&nbsp;&nbsp;*** The Purchase Order Change consider accepted if there is no reply within 5 days ***<br /><br />

    <form action="">
      Supplier : &nbsp;&nbsp;
      <select name="supp" id="idsupp">
      </select>&nbsp;&nbsp;
      <label for="idtglawal">DATE BETWEEN : </label>
      <input type="date" id="idtglawal" name="tglawal">&nbsp;&nbsp;
      <label for="idtglakhir">AND&nbsp;&nbsp;</label>
      <input type="date" id="idtglakhir" name="tglakhir">&nbsp;&nbsp;
      <label for="idstatus">STATUS :</label>
<select id="idstatus" name="status">
    <option value="">ALL</option>
    <option value="UNREAD">UNREAD</option>
    <option value="READ">READ</option>
    <option value="CONFIRMED">CONFIRMED</option>
    <option value="REJECTED">REJECTED</option>
</select>
      <input type="submit" value="Display">
    </form>
    <table id="dataTable" border="1" cellpadding="5" class="table table-hover">
      <thead></thead>
      <tbody></tbody>
    </table>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
<script>

let user = '';
let level = '';
let appkey = '';   
let urlsupp = '';
let urlmailpoctgl = '';

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
  urlmailpoctgl = data.urlmailpoctgl;

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


async function getMailpo(supp, tglawal, tglakhir, status)
{
  try
  {
    const response = await fetch(urlmailpoctgl,
    {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:new URLSearchParams({
        supp:supp,
        tglawal:tglawal,
        tglakhir:tglakhir,
        status:status
      })
    });

    const result = await response.json();
    const data = result.data;

    const table = document.getElementById("dataTable");
    const thead = table.querySelector("thead");
    const tbody = table.querySelector("tbody");

    thead.innerHTML="";
    tbody.innerHTML="";

    thead.innerHTML = `
    <tr>
      <th>NO</th>
      <th>TRANSMISSION DATE</th>
      <th>VIEW DETAIL</th>
    </tr>`;

    let datarow = '';

    data.forEach((item,index)=>
    {

      const param = encryptParam(`rdate=${item.rdate}&supp=${supp}&status=${status}`);

      datarow += `
      <tr>
        <td align="right">${index+1}</td>
        <td>${item.rdate}</td>
        <td>
          <a href="mailpocdtl.php?p=${param}"
          class="btn btn-sm btn-primary"
          target="_blank">
          VIEW
          </a>
        </td>
      </tr>`;
    });

    tbody.innerHTML = datarow;

  }
  catch(error)
  {
    console.error(error);
  }
}


document.addEventListener('submit',function(e)
{
  e.preventDefault();

  const suppid = document.getElementById('idsupp').value;
  const tglawal = document.getElementById('idtglawal').value;
  const tglakhir = document.getElementById('idtglakhir').value;
  const status = document.getElementById('idstatus').value;

  if(!tglawal || !tglakhir)
  {
    alert("Start date and end date must be filled in!");
    return;
  }

  let awal = new Date(tglawal);
  let akhir = new Date(tglakhir);

  if(awal > akhir)
  {
    alert("Start date cannot be greater than end date!");
    return;
  }

  getMailpo(suppid, tglawal, tglakhir, status);

});

</script>
  </body>
</html>
