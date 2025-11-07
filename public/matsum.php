<!DOCTYPE html>
<?php
session_start();
if (isset($_SESSION['user'])) {
  $appkey = $_SESSION['appkey'];
  $env = parse_ini_file(__DIR__ . '/../config/.env');
  $suppurl = $env['API_SUPP_URL'];
  $matsumtglurl = $env['API_MATSUM_TGL_URL'];
  $envappkey = $env['APP_KEY'];
  if ($appkey !== $envappkey) {
    header("Location: login.php");
    exit();
  }
?>
  <html lang="en">
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <title>Material Summary</title>
  <link id="favicon" rel="icon" type="image/png" href="assets/gambar/g-green.png">
  </head>
  <body>
    <?php include 'menu.php'; ?>
    <br />
    <img src="assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION" 
    style="float:left;width:220px;height:35px;">
    PT JVCKENWOOD ELECTRONICS INDONESIA<br />
    MATERIAL STATUS REPORT<br /><br />

    <form action="">
      Supplier : &nbsp;&nbsp;
      <select name="supp" id="idsupp">
      </select>&nbsp;&nbsp;
      Transmission Date : &nbsp;&nbsp;
      <select name="tanggal" id="idtanggal">
      </select>&nbsp;&nbsp;  
      <input type=BUTTON value="Display" name="mybtn" id="btn"></input>
    </form>
    <table id="dataTable" border="1" cellpadding="5" class="table table-hover">
      <thead></thead>
      <tbody></tbody>
    </table>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
let user = '';
let level = '';
let appkey = '';   
let urlsupp = '';
let urlbpstgl = '';
let urlbps = '';
let urlmatsumtgl = '';
let urlmatsum = '';

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
  urlbpstgl = data.urlbpstgl;
  urlbps = data.urlbps;
  urlmatsumtgl = data.urlmatsumtgl;
  urlmatsum = data.urlmatsum;
  getSupplier(user);
  getTanggal(user);
}) 
.catch(err => console.error(err));

      
async function getSupplier(user)
{
  try 
  {
    const response = await fetch(urlsupp, 
    {
      method: 'POST',
      credentials: "include",
      headers: 
      {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        nama: user,
      })
    });

    const reply = await response.text(); // ambil balasan dari PHP
    const isidata = JSON.parse(reply);  
    const selectsupp = document.getElementById('idsupp');
    isidata.forEach((item, index) => 
    {
      const option = document.createElement('option');
      option.value = item.kode;       // nilai option
      option.textContent = item.nama + ' - ' + item.kode; // teks yang 
      if (index === 0) 
      {
        option.selected = true;
      }
      selectsupp.appendChild(option);
    });
      
  } catch (error) 
  {
    console.error(error);
  }
}
// ------------------------------------------------------------------------
async function getTanggal(user)
{
  try 
  {
    const response = await fetch(urlmatsumtgl, 
    {
      method: 'POST',
      credentials: "include",
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        nama: user,
      })
    });

    const reply = await response.text(); // ambil balasan dari PHP
    const isidata = JSON.parse(reply);  
    const selecttgl = document.getElementById('idtanggal');
    isidata.forEach((item, index) => {
    const option = document.createElement('option');
    option.value = item.tanggal;       // nilai option
    option.textContent = item.tanggal; // teks yang 
    if (index === 0) {
      option.selected = true;
    }
    selecttgl.appendChild(option);
    });    
  } catch (error) 
  {        
    console.error(error);
  }
}
// ------------------------------------------------------------------------


async function getMatsum(supp,tgl)
{
  let supplier = supp;
  let tanggal = tgl;
  try 
  {
    const response = await fetch(urlmatsum, 
    {
      method: 'POST',
      credentials: "include",
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        supp: supplier,
        tgl: tanggal
      })
    });

    const reply = await response.text(); // ambil balasan dari PHP
    console.log('Reply: ' + reply);
    const isidata = JSON.parse(reply);  
    console.log(isidata.data);
    const judul = isidata.header;
    const data = isidata.data;
    console.log(data);
    const table = document.getElementById("dataTable");
    const thead = table.querySelector("thead");
    const tbody = table.querySelector("tbody");
    thead.innerHTML = "";
    tbody.innerHTML = "";
    thead.innerHTML = `
        <tr>
            <th>NO</th>
            <th>PARTNO</th>
            <th>PART NAME</th>  
            <th>Previous Month Bal Qty</th>
            <th>Receive QTY</th>
            <th>Issue QTY</th>
            <th>This Month Bal QTY</th>
          </tr>`;
    let number = 1;
    data.forEach(item => {  
      const row = document.createElement("tr");
      const tdNumber = document.createElement("td");
      tdNumber.textContent = number++;
      row.appendChild(tdNumber);
      const tdPartno = document.createElement("td");
      tdPartno.innerHTML = `<pre>${item.partno?.trim() || ''}</pre>`;
      row.appendChild(tdPartno);
      const tdPartname = document.createElement("td");
      tdPartname.innerHTML = `${item.partname?.trim() || ''}`;
      row.appendChild(tdPartname);
      Object.keys(item).forEach(key => 
      {
        if (key !== "partno" && key !== "partname") 
        {
          const td = document.createElement("td");
          td.textContent = item[key];
          td.style.textAlign = "right";
          row.appendChild(td);
        }
      });
      tbody.appendChild(row);
    });
  } catch (error) 
  {        
    console.error(error);
  }

}
// ------------------------------------------------------------------------
const btn = document.getElementById('btn');
btn.addEventListener('click', function() 
{
  getMatsum(document.getElementById('idsupp').value,document.getElementById('idtanggal').value);
});
    </script>
  </body>
  </html>
<?php
} else {
  header("Location: index.php");
}
?>

