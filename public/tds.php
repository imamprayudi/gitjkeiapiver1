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
  //console.log('Supplier: ' + supplier + ' Tanggal: ' + tanggal);
  //console.log('URL TDS: ' + urltds);
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
    //console.log('Reply: ' + reply);
    const isidata = JSON.parse(reply);  
    //console.log(isidata.data);
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

    // data.forEach(item => {
    // tbody.innerHTML = `        <tr>
    //   <td>1</td>
    //   <td>${item.partno}<br>
    //   ${item.partname}</td>
    //   <td>${item.balqty}</td>
    //   <td>${item.qty1}</td>
    //   <td>${item.qty2}</td>
    //   <td>${item.qty3}</td>
    //   <td>${item.qty4}</td>
    //   <td>${item.qty5}</td>
    //   <td>${item.qty6}</td>
      
    //   </tr>`;
    // });

 let number = 1;
    data.forEach(item => {
      
        const row = document.createElement("tr");
       
        const tdNumber = document.createElement("td");
        tdNumber.textContent = number++;
        row.appendChild(tdNumber);
        //Object.values(item).forEach(value => {
            const td = document.createElement("td");
            // td.textContent = value;
            //td.textContent = `${item.partno.trim()} - ${item.partname.trim()}`;
            //td.innerHTML = `<pre>${item.partno?.trim() || ''}<br>${item.partname?.trim() || ''}`;
            td.innerHTML = `<pre>${item.partno?.trim() || ''}</pre><br>${item.partname?.trim() || ''}`;
            row.appendChild(td);
        //const tdPart = document.createElement("td");
        //tdPart.textContent = `${item.partno?.trim() || ''} - ${item.partname?.trim() || ''}`;
        //row.appendChild(tdPart);

            Object.keys(item).forEach(key => 
            {
              if (key !== "partno" && key !== "partname") 
              {
                const td = document.createElement("td");
                td.textContent = item[key];
                 row.appendChild(td);
              }
            });

        tbody.appendChild(row);
    });

    //tdPart.textContent = `${item.partno.trim()} - ${item.partname.trim()}`;
    // judul.forEach(item => {
    //     const tr = document.createElement("tr");

    //     tabelBody.innerHTML = `
    //         <td>NO</td>
    //         <td>PARTNO</td>
    //         <td>BAL</td>
    //         <td>${item.qty1}</td>
    //     `;

    //     tabelBody.appendChild(tr);
    // });

    // dat.forEach(item => {
    //     const tr = document.createElement("tr");

    //     tabelBody.innerHTML = `
    //         <td>NO</td>
    //         <td>PARTNO</td>
    //         <td>BAL</td>
    //         <td>${item.qty1}</td>
    //     `;

    //     tabelBody.appendChild(tr);
    // });
   // judul.forEach((item) => 
   // {
      //document.write("<h1>Hello World!</h1>");
      // const row = document.createElement('tr');
      // //row.style.backgroundColor = '#D3D3D3'; // Warna latar belakang abu-abu
      // const cellNomor = document.createElement('td');
      // cellNomor.textContent = 'NO';
      // row.appendChild(cellNomor);
      // const cellPartNumber = document.createElement('td');
      // cellPartNumber.textContent = 'PartNumber';
      // row.appendChild(cellPartNumber);
      // const cellBal = document.createElement('td');
      // cellBal.textContent = 'BAL';
      // row.appendChild(cellBal);
      // const cellHd1 = document.createElement('td');
      // cellHd1.textContent = item.qty1;
      // row.appendChild(cellHd1);
      // const cellHd2 = document.createElement('td');
      // cellHd2.textContent = item.qty2;
      // row.appendChild(cellHd2);
      // const cellHd3 = document.createElement('td');
      // cellHd3.textContent = item.qty3;
      // row.appendChild(cellHd3);
      // const cellHd4 = document.createElement('td');
      // cellHd4.textContent = item.qty4;
      // row.appendChild(cellHd4);
      // const cellHd5 = document.createElement('td');
      // cellHd5.textContent = item.qty5;
      // row.appendChild(cellHd5);
      // const cellHd6 = document.createElement('td');
      // cellHd6.textContent = item.qty6;
      // row.appendChild(cellHd6);
      // const cellHd7 = document.createElement('td');
      // cellHd7.textContent = item.qty7;
      // row.appendChild(cellHd7);
      // const cellHd8 = document.createElement('td');
      // cellHd8.textContent = item.qty8;
      // row.appendChild(cellHd8);
      // const cellHd9 = document.createElement('td');
      // cellHd9.textContent = item.qty9;
      // row.appendChild(cellHd9);
      // const cellHd10 = document.createElement('td');
      // cellHd10.textContent = item.qty10;
      // row.appendChild(cellHd10);
      // const cellHd11 = document.createElement('td');
      // cellHd11.textContent = item.qty11;
      // row.appendChild(cellHd11);
      // const cellHd12 = document.createElement('td');
      // cellHd12.textContent = item.qty12;
      // row.appendChild(cellHd12);
      // const cellHd13 = document.createElement('td');
      // cellHd13.textContent = item.qty13;
      // row.appendChild(cellHd13);
      // const cellHd14 = document.createElement('td');
      // cellHd14.textContent = item.qty14;
      // row.appendChild(cellHd14);
      // const cellHd15 = document.createElement('td');
      // cellHd15.textContent = item.qty15;
      // row.appendChild(cellHd15);
      // const cellHd16 = document.createElement('td');
      // cellHd16.textContent = item.qty16;
      // row.appendChild(cellHd16);
      // const cellHd17 = document.createElement('td');
      // cellHd17.textContent = item.qty17;
      // row.appendChild(cellHd17);
      // const cellHd18 = document.createElement('td');
      // cellHd18.textContent = item.qty18;
      // row.appendChild(cellHd18);
      // const cellHd19 = document.createElement('td');
      // cellHd19.textContent = item.qty19;
      // row.appendChild(cellHd19);
      // const cellHd20 = document.createElement('td');
      // cellHd20.textContent = item.qty20;
      // row.appendChild(cellHd20);
      // const cellHd21 = document.createElement('td');
      // cellHd21.textContent = item.qty21;
      // row.appendChild(cellHd21);
      // const cellHd22 = document.createElement('td');
      // cellHd22.textContent = item.qty22;
      // row.appendChild(cellHd22);
      // const cellHd23 = document.createElement('td');
      // cellHd23.textContent = item.qty23;
      // row.appendChild(cellHd23);
      // const cellHd24 = document.createElement('td');
      // cellHd24.textContent = item.qty24;
      // row.appendChild(cellHd24);
      // const cellHd25 = document.createElement('td');
      // cellHd25.textContent = item.qty25;
      // row.appendChild(cellHd25);
      // const cellHd26 = document.createElement('td');
      // cellHd26.textContent = item.qty26;
      // row.appendChild(cellHd26);
      // const cellHd27 = document.createElement('td');
      // cellHd27.textContent = item.qty27;
      // row.appendChild(cellHd27);
      // const cellHd28 = document.createElement('td');
      // cellHd28.textContent = item.qty28;
      // row.appendChild(cellHd28);
      // const cellHd29 = document.createElement('td');
      // cellHd29.textContent = item.qty29;
      // row.appendChild(cellHd29);
      // const cellHd30 = document.createElement('td');
      // cellHd30.textContent = item.qty30;
      // row.appendChild(cellHd30);
      // const cellHd31 = document.createElement('td');
      // cellHd31.textContent = item.qty31;
      // row.appendChild(cellHd31);
      // const cellHd32 = document.createElement('td');
      // cellHd32.textContent = item.qty32;
      // row.appendChild(cellHd32);
      //row.appendChild('</th>');
     // tabelBody.appendChild(row);
    
    
    
    // ---------------------
    //});
    
    // data.forEach((item, index) => 
    // {
    //   const row = document.createElement('tr');
    //   const cellNomor = document.createElement('td');
    //   cellNomor.textContent = index + 1;
    //   row.appendChild(cellNomor);
    //   const cellPartNo = document.createElement('td');
    //   cellPartNo.textContent = item.partno;
    //   row.appendChild(cellPartNo);
    //   const cellBal = document.createElement('td');
    //   cellBal.textContent = item.bal;
    //   row.appendChild(cellBal);
    //   for (let i = 1; i <= 32; i++) 
    //   {
    //     const cellHd = document.createElement('td');
    //     const qtyKey = 'qty' + i;
    //     cellHd.textContent = item[qtyKey];
    //     row.appendChild(cellHd);
    //   }
    // tabelBody.appendChild(row); 
    // });
    
  } catch (error) 
  {        
    console.error(error);
  }

}
// ------------------------------------------------------------------------
const btn = document.getElementById('btn');
btn.addEventListener('click', function() 
{
 //alert('Javascript Ajax code here');
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

