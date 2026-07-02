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
    PO HISTORY <br /><br />&nbsp;&nbsp;Please input PO Number : &nbsp;&nbsp;
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
    html += "<h3>Original PO</h3>";

    html += "<table class='table table-bordered table-striped'>";

    html += "<thead class='table-dark'>";
    html += "<tr>";
    html += "<th>TRANSMISSION NUMBER</th>";
    html += "<th>TRANSMISSION DATE</th>";
    html += "<th>PO NUMBER</th>";
    html += "<th>PART NUMBER</th>";
    html += "<th>PART NAME</th>";
    html += "<th>PO QTY</th>";
    html += "<th>PO DATE</th>";
    html += "<th>PRICE</th>";
    html += "<th>MODEL</th>";
    html += "<th>PO TYPE</th>";
    html += "<th>SUPP STATUS</th>";
    html += "<th>SUPP REASON</th>";
    html += "<th>BY</th>";
    html += "<th>AT</th>";
    html += "<th>PUR STATUS</th>";
    html += "<th>PUR REASON</th>";
    html += "<th>BY</th>";
    html += "<th>AT</th>";
    html += "<th>MC STATUS</th>";
    html += "<th>MC REASON</th>";
    html += "<th>BY</th>";
    html += "<th>AT</th>";
    html += "</tr>";
    html += "</thead>";

    html += "<tbody>";
    html += "<tr>";
    html += "<td>" + result.mailpo.idno + "</td>";
    html += "<td>" + result.mailpo.rdate + "</td>";
    html += "<td>" + result.mailpo.pono + "</td>";
    html += "<td><pre>" + result.mailpo.partno + "</pre></td>";
    html += "<td>" + result.mailpo.partname + "</td>";
    html += "<td class='text-end'>" + result.mailpo.newqty + "</td>";
    html += "<td>" + result.mailpo.newdate + "</td>";
    html += "<td>" + result.mailpo.price + "</td>";
    html += "<td>" + result.mailpo.model + "</td>";
    html += "<td>" + result.mailpo.potype + "</td>";
    html += "<td>" + result.mailpo.supconfstatus + "</td>";
    html += "<td>" + result.mailpo.supconfreason + "</td>";
    html += "<td>" + result.mailpo.supconfby + "</td>";
    html += "<td>" + result.mailpo.supconfat + "</td>";
    html += "<td>" + result.mailpo.purconfstatus + "</td>";
    html += "<td>" + result.mailpo.purconfreason + "</td>"; 
    html += "<td>" + result.mailpo.purconfby + "</td>";
    html += "<td>" + result.mailpo.purconfat + "</td>";
    html += "<td>" + result.mailpo.mcconfstatus + "</td>";
    html += "<td>" + result.mailpo.mcconfreason + "</td>";
    html += "<td>" + result.mailpo.mcconfby + "</td>";
    html += "<td>" + result.mailpo.mcconfat + "</td>";
    html += "</tr>";
    html += "</tbody>";

    html += "</table><br>";
}

        //-----------------------------------
        // Revision
        //-----------------------------------

        html+="<h3>Revision History</h3>";

        if(result.mailpoc.length>0)
        {

            html+="<table class='table table-bordered table-striped'>";
            html+="<thead class='table-dark'>";
            html+="<tr>";
            html+="<th>NO</th>";
            html+="<th>TRANSMISSION NUMBER</th>";
            html+="<th>TRANSMISSION DATE</th>";
            html+="<th>PO NUMBER</th>";
            html+="<th>PART NUMBER</th>";
            html+="<th>PART NAME</th>";
            html+="<th>NEW QTY</th>";
            html+="<th>NEW DATE</th>";
            html+="<th>OLD QTY</th>";
            html+="<th>OLD DATE</th>";
            html+="<th>PRICE</th>";
            html+="<th>MODEL</th>";
            html+="<th>PO TYPE</th>";
            html+="<th>ALT NO</th>";
            html+="<th>PO STATUS</th>";
            html+="<th>SUPP STATUS</th>";
            html+="<th>SUPP REASON</th>";
            html+="<th>BY</th>";
            html+="<th>AT</th>";
            html+="<th>PUR STATUS</th>";
            html+="<th>PUR REASON</th>";
            html+="<th>BY</th>";
            html+="<th>AT</th>";
            html+="<th>MC STATUS</th>";
            html+="<th>MC REASON</th>";
            html+="<th>BY</th>";
            html+="<th>AT</th>";
            html+="</tr>";
            html += "</thead>";

            result.mailpoc.forEach(function(r,index){

                html+="<tr>";

                html+="<td>"+(index+1)+"</td>";
                html+="<td>"+r.idno+"</td>";
                html+="<td>"+r.rdate+"</td>";
                html+="<td>"+r.pono+"</td>";
                html+="<td><pre>"+r.partno+"</pre></td>";
                html+="<td>"+r.partname+"</td>";
                html+="<td>"+r.newqty+"</td>";
                html+="<td>"+r.newdate+"</td>";
                html+="<td>"+r.oldqty+"</td>";
                html+="<td>"+r.olddate+"</td>";
                html+="<td>"+r.price+"</td>";
                html+="<td>"+r.model+"</td>";
                html+="<td>"+r.potype+"</td>";
                html+="<td>"+r.altno+"</td>";
                html+="<td>"+r.status+"</td>";
                html+="<td>"+r.supconfstatus+"</td>";
                html+="<td>"+r.supconfreason+"</td>";
                html+="<td>"+r.supconfby+"</td>";
                html+="<td>"+r.supconfat+"</td>";
                html+="<td>"+r.purconfstatus+"</td>";
                html+="<td>"+r.purconfreason+"</td>";
                html+="<td>"+r.purconfby+"</td>";
                html+="<td>"+r.purconfat+"</td>";
                html+="<td>"+r.mcconfstatus+"</td>";
                html+="<td>"+r.mcconfreason+"</td>";
                html+="<td>"+r.mcconfby+"</td>";
                html+="<td>"+r.mcconfat+"</td>";
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