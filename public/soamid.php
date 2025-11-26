<!DOCTYPE html>
<?php
session_start();
if (isset($_SESSION['user'])) {
  $appkey = $_SESSION['appkey'];
  $env = parse_ini_file(__DIR__ . '/../config/.env');
  $suppurl = $env['API_SUPP_URL'];
  $soatglurl = $env['API_SOA_TGL_URL'];
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
  <title>Statement of Account</title>
  <link id="favicon" rel="icon" type="image/png" href="assets/gambar/g-green.png">
  </head>
  <body>
    <?php include 'menu.php'; ?>
    <br />
    <img src="assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION" 
    style="float:left;width:220px;height:35px;">
    PT JVCKENWOOD ELECTRONICS INDONESIA<br />
    STATEMENT OF ACCOUNT - MID TERM PAYMENT<br /><br />
  <div id="formulir">
    <form id="frmdata">
      <label for="idsupp">Supplier : </label>&nbsp;&nbsp;
      <select name="supp" id="idsupp">
      </select>&nbsp;&nbsp;
     <label for="idtanggal">Transmission Date :</label>
      <select name="tanggal" id="idtanggal">
      </select>&nbsp;&nbsp;  
      <input type="submit" value="Display">
    </form>
</div>
<div id="komentar">
  <form id="frmkomentar">
  <textarea id="txtMemo1" name="memo1" cols="50" rows="4" placeholder="Tulis memo..."></textarea>
  <textarea id="txtMemo2" name="memo2" cols="50" rows="4" placeholder="Tulis memo..."></textarea>
  <input type="submit" value="Update Comment">
</form>
</div>
<table id="sumTable" border="1" cellpadding="5" class="table table-hover">
      <thead></thead>
      <tbody></tbody>
    </table>
<table id="termTable" border="1" cellpadding="5" class="table table-hover">
      <thead></thead>
      <tbody></tbody>
    </table>

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
let urlsoatgl = '';
let urlsoamid = '';
let urlsoacommid = '';
let gblBlnThn = '';
let gblSuppId = '';

document.getElementById("komentar").style.display = "none";

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
  urlsoatgl = data.urlsoatgl;
  urlsoamid = data.urlsoamid;
  urlsoacommid = data.urlsoacommid;
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
    const response = await fetch(urlsoatgl, 
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
async function getSoamid(supp,tgl)
{
  let supplier = supp;
  let tanggal = tgl;
  try 
  {
    const response = await fetch(urlsoamid, 
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
    const status = isidata.status;
    const data = isidata.data;
    const datacom = isidata.datacom;
    const judul = isidata.judul;
    document.getElementById("komentar").style.display = "block";
    document.getElementById("txtMemo1").value = datacom[0].suppcom;
    document.getElementById("txtMemo2").value = datacom[0].jeincom;
    gblBlnThn = datacom[0].blnthn;
    gblSuppId = datacom[0].suppcode;
    if (level==="3")
    {
      document.getElementById("txtMemo2").disabled = true;
    }else
    {
      document.getElementById("txtMemo1").disabled = true;
    }
    const sumTable = document.getElementById("sumTable");
    const sumthead = sumTable.querySelector("thead");
    const sumtbody = sumTable.querySelector("tbody");
    sumthead.innerHTML = "";
    sumtbody.innerHTML = "";
    const termTable = document.getElementById("termTable");
    const termthead = termTable.querySelector("thead");
    const termtbody = termTable.querySelector("tbody");
    termthead.innerHTML = "";
    termtbody.innerHTML = "";
    let sumjudul = `
      <tr>
      <th style="text-align:right;">LAST PAYMENT</th>
      <th style="text-align:right;">PURCHASE</th>
      <th style="text-align:right;">OUR DN CN</th>
      <th style="text-align:right;">NET PURCHASE</th>  
      <th style="text-align:right;">VAT</th>  
      <th style="text-align:right;">DN CN (PUR)</th>  
      <th style="text-align:right;">PAYMENT</th>
      <th style="text-align:right;">THIS BALANCE</th>
      </tr>`;
    sumthead.innerHTML += sumjudul;
    let termjudul = `<tr>
      <th style="text-align:right;">15 Days</th>
      <th style="text-align:right;">30 Days</th>
      <th style="text-align:right;">45 Days</th>
      <th style="text-align:right;">60 Days</th>
      <th style="text-align:right;">75 Days</th>
      <th style="text-align:right;">90 Days</th>
      <th style="text-align:right;">TOTAL</th>
      </tr>`;
    termthead.innerHTML += termjudul;
    let judulrow = ``;
    let termrow = ``;
    judul.forEach((item, index) => 
    {
      judulrow += `<tr>
      <td align="right">${item.lastpay || ""}</td>
      <td align="right">${item.purchase || ""}</td>
      <td align="right">${item.dncn || ""}</td>
      <td align="right">${item.netpur || ""}</td>
      <td align="right">${item.vat || ""}</td>
      <td align="right">${item.salesvat || ""}</td>
      <td align="right">${item.payment || ""}</td>
      <td align="right">${item.this || ""}</td>
      </tr>`;
      termrow += `<tr>
      <td align="right">${item.term15 || ""}</td>
      <td align="right">${item.term30 || ""}</td>
      <td align="right">${item.term45 || ""}</td>
      <td align="right">${item.term60 || ""}</td>
      <td align="right">${item.term75 || ""}</td>
      <td align="right">${item.term90 || ""}</td>
      <td align="right">${item.termtotal || ""}</td>
      </tr>`;
    });
    sumtbody.innerHTML += judulrow;
    termtbody.innerHTML += termrow;
    const dataTable = document.getElementById("dataTable");
    const datathead = dataTable.querySelector("thead");
    const datatbody = dataTable.querySelector("tbody");
    datathead.innerHTML = "";
    datatbody.innerHTML = "";
    let datajudul = `
        <tr>
            <th style="text-align:right;">NO</th>
            <th>DATE</th>
            <th>PO NUMBER<br>SO NUMBER</th>
            <th>SQ</th>
            <th>INVOICE NUMBER<br>ROG SLIP NO</th>  
            <th>PART NUMBER</th>  
            <th>DESCRIPTION</th>  
            <th style="text-align:right;">QTY</th>
            <th style="text-align:right;">UNIT PRICE</th>
            <th style="text-align:right;">AMOUNT</th>
            <th style="text-align:right;">OUR DN CN</th>
        </tr>`;
    datathead.innerHTML += datajudul;
    let datarow = ``;
    let nomor = 0;
    data.forEach((item, index) => 
    {
      nomor += 1;
      if (item.ok==="T")
      {
        nomor = 0;
      }
      datarow += `<tr>
      <td align="right">${nomor === 0 ? "" : nomor}</td>
      <td>${item.tgl?.substring(0,10) || ""}</td>
      <td align="center">${item.po || ""}</td>
      <td>${item.sq || ""}</td>
      <td>${item.invoice || ""}</td>
      <td><pre>${item.partno || ""}</pre></td>
      <td>${item.partname || ""}</td>
      <td align="right">${item.qty || ""}</td>
      <td align="right">${item.price || ""}</td>
      <td align="right">${item.amount || ""}</td>
      <td align="right">${item.dncn || ""}</td>
      </tr>`;
    });
    datatbody.innerHTML += datarow;


  } catch (error) 
  {        
    console.error(error);
  }

}

async function postCom(blnthn,supp,suppkomen,jeinkomen)
{
  try 
  {
    const response = await fetch(urlsoacommid, 
    {
      method: 'POST',
      credentials: "include",
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        bulantahun : blnthn,
        suppid : supp,
        suppcom : suppkomen,
        jeincom : jeinkomen
      })
    });
     
    const reply = await response.text(); // ambil balasan dari PHP
    const isidata = JSON.parse(reply);
    const status = isidata.status;
    alert(status);
  } catch (error) 
  {        
    console.error(error);
  }
}

// ------------------------------------------------------------------------
document.getElementById('frmdata').addEventListener('submit', function(e)
{
  e.preventDefault();
  const suppid = document.getElementById('idsupp').value;
  const tanggal = document.getElementById('idtanggal').value;
  getSoamid(suppid,tanggal);
});

document.getElementById('frmkomentar').addEventListener('submit', function(e)
{
  e.preventDefault();
  const suppKomen = document.getElementById("txtMemo1").value;
  const jeinKomen = document.getElementById("txtMemo2").value;
  postCom(gblBlnThn,gblSuppId,suppKomen,jeinKomen);
});  
      
// --------------------------------------------------------------------------

    </script>
  </body>
  </html>
<?php
} else {
  header("Location: index.php");
}
?>

