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
  <title>Order Balance</title>
  <link id="favicon" rel="icon" type="image/png" href="assets/gambar/g-green.png">
  </head>
  <body>
    <?php include 'menu.php'; ?>
    <br />
    <img src="assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION" 
    style="float:left;width:220px;height:35px;">
    PT JVCKENWOOD ELECTRONICS INDONESIA<br />
    PO HISTORY <br /><br />
<input type="text" id="pono" placeholder="Input PO Number">

<button onclick="searchPO()">
Search
</button>

<hr>

<div id="result"></div>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<script>
async function searchPO() {

    const pono = document.getElementById("pono").value.trim();

    console.log("PO Number : " + pono);

    if (pono == "") {
        alert("Input PO Number");
        return;
    }

    const params = new URLSearchParams();
    params.append("pono", pono);

    const response = await fetch("../api/apipohist.php", {
        method: "POST",
        body: params
    });

    const result = await response.json();

    console.log(result);

    let html = "";

    if(result.status=="success")
    {

        //-----------------------------------
        // Original PO
        //-----------------------------------

        if(result.mailpo)
        {

            html+="<h3>Original PO</h3>";

            html+="<table border='1' cellpadding='5'>";

            html+="<tr><td>PO</td><td>"+result.mailpo.pono+"</td></tr>";
            html+="<tr><td>Supplier</td><td>"+result.mailpo.suppliername+"</td></tr>";
            html+="<tr><td>Part</td><td>"+result.mailpo.partno+"</td></tr>";
            html+="<tr><td>Qty</td><td>"+result.mailpo.newqty+"</td></tr>";
            html+="<tr><td>Delivery</td><td>"+result.mailpo.newdate+"</td></tr>";

            html+="</table><br>";

        }

        //-----------------------------------
        // Revision
        //-----------------------------------

        html+="<h3>Revision History</h3>";

        if(result.mailpoc.length>0)
        {

            html+="<table border='1' cellpadding='5'>";

            html+="<tr>";
            html+="<th>No</th>";
            html+="<th>Date</th>";
            html+="<th>Old Qty</th>";
            html+="<th>New Qty</th>";
            html+="<th>Old Date</th>";
            html+="<th>New Date</th>";
            html+="</tr>";

            result.mailpoc.forEach(function(r,index){

                html+="<tr>";

                html+="<td>"+(index+1)+"</td>";
                html+="<td>"+r.rdate+"</td>";
                html+="<td>"+r.oldqty+"</td>";
                html+="<td>"+r.newqty+"</td>";
                html+="<td>"+r.olddate+"</td>";
                html+="<td>"+r.newdate+"</td>";

                html+="</tr>";

            });

            html+="</table>";

        }
        else
        {
            html+="Tidak ada revisi";
        }

    }
    else
    {
        html=result.message;
    }

    document.getElementById("result").innerHTML=html;

}
</script>
   </body>
  </html>