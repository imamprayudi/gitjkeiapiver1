<!DOCTYPE html>
<?php
session_start();
if (isset($_SESSION['user'])) {
  $appkey = $_SESSION['appkey'];
  $env = parse_ini_file(__DIR__ . '/../config/.env');
  $suppurl = $env['API_SUPP_URL'];
  $tdstglurl = $env['API_TDS_TGL_URL'];
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
  <title>Time Delivery</title>
  <link id="favicon" rel="icon" type="image/png" href="assets/gambar/g-green.png">
  </head>
  <body>
    <?php include 'menu.php'; ?>
    <br />
    <img src="assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION" 
    style="float:left;width:220px;height:35px;">
    PT JVCKENWOOD ELECTRONICS INDONESIA<br />
    TIME DELIVERY SCHEDULE<br /><br />

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
let urltdstgl = '';
let urltds = '';

fetch('getsession.php', {
  method: 'GET',
  headers: {
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
  urltdstgl = data.urltdstgl;
  urltds = data.urltds;
  getSupplier(user);
  getTanggal(user);
}) 
.catch(err => console.error(err));

      
   async function getSupplier(user)
{
  try 
  {
    const response = await fetch(urlsupp, {
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
    const response = await fetch(urltdstgl, 
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


async function getTds(supp,tgl)
{

  let supplier = supp;
  let tanggal = tgl;
  try 
  {
    const response = await fetch(urltds, 
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
    const isidata = JSON.parse(reply);  
    const judul = isidata.header;
    const data = isidata.data;
    //console.log(data);
    const table = document.getElementById("dataTable");
    const thead = table.querySelector("thead");
    const tbody = table.querySelector("tbody");
    thead.innerHTML = "";
    tbody.innerHTML = "";

    judul.forEach(item => {
    thead.innerHTML = `
        <tr>
            <th>NO</th>
            <th>PARTNO</th>
            <th>BAL</th>  
            <th>${item.qty1}</th>
            <th>${item.qty2}</th>
            <th>${item.qty3}</th>
            <th>${item.qty4}</th>
            <th>${item.qty5}</th>
            <th>${item.qty6}</th>
            <th>${item.qty7}</th>
            <th>${item.qty8}</th>
            <th>${item.qty9}</th>
            <th>${item.qty10}</th>  
            <th>${item.qty11}</th>
            <th>${item.qty12}</th>
            <th>${item.qty13}</th>
            <th>${item.qty14}</th>
            <th>${item.qty15}</th>
            <th>${item.qty16}</th>
            <th>${item.qty17}</th>
            <th>${item.qty18}</th>
            <th>${item.qty19}</th>
            <th>${item.qty20}</th>
            <th>${item.qty21}</th>
            <th>${item.qty22}</th>
            <th>${item.qty23}</th>
            <th>${item.qty24}</th>
            <th>${item.qty25}</th>
            <th>${item.qty26}</th>
            <th>${item.qty27}</th>
            <th>${item.qty28}</th>
            <th>${item.qty29}</th>
            <th>${item.qty30}</th>
            <th>${item.qty31}</th>
            <th>${item.qty32}</th>
        </tr>`;
    });

   

 let number = 1;
    data.forEach(item => {
      
        const row = document.createElement("tr");
       
        const tdNumber = document.createElement("td");
        tdNumber.textContent = number++;
        row.appendChild(tdNumber);
            const td = document.createElement("td");
            td.innerHTML = `<pre>${item.partno?.trim() || ''}</pre><br>${item.partname?.trim() || ''}`;
            row.appendChild(td);
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
  getTds(document.getElementById('idsupp').value,document.getElementById('idtanggal').value);
});
    </script>
  </body>
  </html>
<?php
} else {
  header("Location: index.php");
}
?>

