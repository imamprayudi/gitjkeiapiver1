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
  <title>PO History</title>
  <link id="favicon" rel="icon" type="image/png" href="assets/gambar/g-green.png">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
body{
    font-family: 'Inter', sans-serif;
    background:#f4f6f9;
}

.container-box{
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
}

.title-area{
    display:flex;
    align-items:center;
    gap:15px;
    margin-bottom:20px;
}

.title-text{
    font-weight:600;
    font-size:18px;
    line-height:1.4;
}

.filter-box{
    background:#f8fafc;
    padding:15px;
    border-radius:8px;
    margin-bottom:20px;
}

.filter-box label,
.search-label{
    font-weight:500;
}

.radio-group{
    display:flex;
    gap:16px;
    align-items:center;
    margin:10px 0 12px;
}

.keyword-row{
    display:flex;
    gap:8px;
    align-items:center;
    margin-top:8px;
}

.keyword-row:first-child{
    margin-top:0;
}

.keyword{
    max-width:280px;
    border-radius:6px;
}

.btn-add{
    background:#fff;
    border:1px solid #0d6efd;
    color:#0d6efd;
    padding:6px 16px;
    border-radius:6px;
}

.btn-add:hover{
    background:#0d6efd;
    color:#fff;
}

.btn-search{
    background:#0d6efd;
    border:none;
    padding:6px 16px;
    color:white;
    border-radius:6px;
}

.btn-search:hover{
    background:#0b5ed7;
}

.btn-remove{
    background:#fff;
    border:1px solid #dc3545;
    color:#dc3545;
    padding:5px 12px;
    border-radius:6px;
    font-size:13px;
}

.btn-remove:hover{
    background:#dc3545;
    color:#fff;
}

.po-card{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:10px;
    box-shadow:0 2px 10px rgba(0,0,0,0.06);
    margin-bottom:18px;
    overflow:hidden;
}

.po-card-header{
    background:#1f2937;
    color:#fff;
    padding:10px 16px;
    font-weight:600;
    font-size:14px;
}

.po-card-body{
    padding:16px;
}

.section-title{
    font-size:14px;
    font-weight:600;
    color:#334155;
    margin:0 0 10px;
}

.table-scroll{
    overflow:auto;
    border:1px solid #e5e7eb;
    border-radius:8px;
    max-height:420px;
}

.hist-table{
    font-size:13px;
    margin-bottom:0;
    white-space:nowrap;
}

.hist-table thead th{
    position:sticky;
    top:0;
    background:#0077b6 !important;
    color:white;
    text-align:center;
    vertical-align:middle;
    z-index:2;
    border-color:#0077b6;
}

.hist-table tbody tr:hover{
    background:#f1f5f9;
}

.hist-table td,
.hist-table th{
    border-color:#eef2f7;
    vertical-align:middle;
}

.hist-table td pre{
    margin:0;
    font-family:inherit;
    font-size:13px;
    white-space:pre;
}

.empty-state{
    background:#f8fafc;
    color:#64748b;
    border-radius:8px;
    padding:12px 14px;
    font-size:13px;
}

.status-badge{
    font-size:11px;
    font-weight:600;
    letter-spacing:.02em;
}
  </style>
  </head>
  <body>
    <?php include 'menu.php'; ?>

<div class="container-fluid mt-4 px-4">
<div class="container-box">

<div class="title-area">
    <img src="assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION"
    style="width:220px;height:35px;">
    <div class="title-text">
        PT JVCKENWOOD ELECTRONICS INDONESIA<br>
        PO HISTORY
    </div>
</div>

<div class="filter-box">
    <div class="search-label" id="searchLabel">Please input PO Number</div>
    <div class="radio-group">
        <label><input type="radio" name="searchby" value="pono" checked onchange="updateSearchLabel()"> PO NUMBER</label>
        <label><input type="radio" name="searchby" value="partno" onchange="updateSearchLabel()"> PART NUMBER</label>
    </div>
    <div id="keywordBox">
      <div class="keyword-row">
        <input type="text" class="keyword form-control" placeholder="Input PO Number">
      </div>
    </div>
    <div class="mt-2">
        <button type="button" id="btnAddKeyword" class="btn-add" onclick="addKeywordBox()">Add</button>
        <button type="button" class="btn-search" onclick="searchPO()">Search</button>
    </div>
</div>

<div id="result"></div>

</div>
</div>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<script>
function getSearchBy() {
    const selected = document.querySelector('input[name="searchby"]:checked');
    return selected ? selected.value : "pono";
}

function getKeywords() {
    const values = [];
    document.querySelectorAll(".keyword").forEach(function(el) {
        const v = el.value.trim();
        if (v !== "") {
            values.push(v);
        }
    });
    return values;
}

function addKeywordBox() {
    if (getSearchBy() !== "pono") {
        return;
    }
    const box = document.getElementById("keywordBox");
    const row = document.createElement("div");
    row.className = "keyword-row";
    row.innerHTML = '<input type="text" class="keyword form-control" placeholder="Input PO Number"> <button type="button" class="btn-remove" onclick="removeKeywordBox(this)">Remove</button>';
    box.appendChild(row);
}

function removeKeywordBox(btn) {
    const rows = document.querySelectorAll(".keyword-row");
    const row = btn.closest(".keyword-row");
    if (row && rows.length > 1) {
        row.remove();
    }
}

function updateSearchLabel() {
    const isPart = getSearchBy() === "partno";
    const label = isPart ? "Please input Part Number" : "Please input PO Number";
    const placeholder = isPart ? "Input Part Number" : "Input PO Number";
    document.getElementById("searchLabel").textContent = label;
    document.getElementById("btnAddKeyword").style.display = isPart ? "none" : "";

    if (isPart) {
        document.querySelectorAll(".keyword-row").forEach(function(row, i) {
            if (i > 0) {
                row.remove();
            }
        });
    }

    document.querySelectorAll(".keyword").forEach(function(el) {
        el.placeholder = placeholder;
    });
}

function cell(v) {
    return (v === null || v === undefined) ? "" : v;
}

function statusBadge(v) {
    const text = String(cell(v)).trim();
    if (text === "") {
        return "";
    }
    const key = text.toUpperCase();
    let cls = "bg-secondary";
    if (key === "CONFIRMED") cls = "bg-success";
    else if (key === "REJECTED") cls = "bg-danger";
    else if (key === "UNREAD") cls = "bg-warning text-dark";
    else if (key === "READ") cls = "bg-info text-dark";
    return '<span class="badge status-badge ' + cls + '">' + text + '</span>';
}

function wrapTable(inner) {
    return "<div class='table-scroll'><table class='table hist-table'>" + inner + "</table></div>";
}

function renderOriginalPOTable(rows) {
    let html = "<div class='section-title'>Original PO</div>";
    let inner = "<thead><tr>";
    inner += "<th>TRANSMISSION NUMBER</th>";
    inner += "<th>TRANSMISSION DATE</th>";
    inner += "<th>PO NUMBER</th>";
    inner += "<th>PART NUMBER</th>";
    inner += "<th>PART NAME</th>";
    inner += "<th>PO QTY</th>";
    inner += "<th>PO DATE</th>";
    inner += "<th>PRICE</th>";
    inner += "<th>MODEL</th>";
    inner += "<th>PO TYPE</th>";
    inner += "<th>SUPP STATUS</th>";
    inner += "<th>SUPP REASON</th>";
    inner += "<th>BY</th>";
    inner += "<th>AT</th>";
    inner += "<th>PUR STATUS</th>";
    inner += "<th>PUR REASON</th>";
    inner += "<th>BY</th>";
    inner += "<th>AT</th>";
    inner += "<th>MC STATUS</th>";
    inner += "<th>MC REASON</th>";
    inner += "<th>BY</th>";
    inner += "<th>AT</th>";
    inner += "</tr></thead><tbody>";

    rows.forEach(function(r){
        inner += "<tr>";
        inner += "<td>" + cell(r.idno) + "</td>";
        inner += "<td>" + cell(r.rdate) + "</td>";
        inner += "<td>" + cell(r.pono) + "</td>";
        inner += "<td><pre>" + cell(r.partno) + "</pre></td>";
        inner += "<td>" + cell(r.partname) + "</td>";
        inner += "<td class='text-end'>" + cell(r.newqty) + "</td>";
        inner += "<td>" + cell(r.newdate) + "</td>";
        inner += "<td class='text-end'>" + cell(r.price) + "</td>";
        inner += "<td>" + cell(r.model) + "</td>";
        inner += "<td>" + cell(r.potype) + "</td>";
        inner += "<td>" + statusBadge(r.supconfstatus) + "</td>";
        inner += "<td>" + cell(r.supconfreason) + "</td>";
        inner += "<td>" + cell(r.supconfby) + "</td>";
        inner += "<td>" + cell(r.supconfat) + "</td>";
        inner += "<td>" + statusBadge(r.purconfstatus) + "</td>";
        inner += "<td>" + cell(r.purconfreason) + "</td>";
        inner += "<td>" + cell(r.purconfby) + "</td>";
        inner += "<td>" + cell(r.purconfat) + "</td>";
        inner += "<td>" + statusBadge(r.mcconfstatus) + "</td>";
        inner += "<td>" + cell(r.mcconfreason) + "</td>";
        inner += "<td>" + cell(r.mcconfby) + "</td>";
        inner += "<td>" + cell(r.mcconfat) + "</td>";
        inner += "</tr>";
    });

    inner += "</tbody>";
    html += wrapTable(inner);
    return html;
}

function renderRevisionTable(rows) {
    let html = "<div class='section-title mt-3'>Revision History</div>";

    if(rows.length === 0)
    {
        html += "<div class='empty-state'>Tidak ada revisi</div>";
        return html;
    }

    let inner = "<thead><tr>";
    inner += "<th>NO</th>";
    inner += "<th>TRANSMISSION NUMBER</th>";
    inner += "<th>TRANSMISSION DATE</th>";
    inner += "<th>PO NUMBER</th>";
    inner += "<th>PART NUMBER</th>";
    inner += "<th>PART NAME</th>";
    inner += "<th>NEW QTY</th>";
    inner += "<th>NEW DATE</th>";
    inner += "<th>OLD QTY</th>";
    inner += "<th>OLD DATE</th>";
    inner += "<th>PRICE</th>";
    inner += "<th>MODEL</th>";
    inner += "<th>PO TYPE</th>";
    inner += "<th>ALT NO</th>";
    inner += "<th>PO STATUS</th>";
    inner += "<th>SUPP STATUS</th>";
    inner += "<th>SUPP REASON</th>";
    inner += "<th>BY</th>";
    inner += "<th>AT</th>";
    inner += "<th>PUR STATUS</th>";
    inner += "<th>PUR REASON</th>";
    inner += "<th>BY</th>";
    inner += "<th>AT</th>";
    inner += "<th>MC STATUS</th>";
    inner += "<th>MC REASON</th>";
    inner += "<th>BY</th>";
    inner += "<th>AT</th>";
    inner += "</tr></thead><tbody>";

    rows.forEach(function(r,index){
        inner += "<tr>";
        inner += "<td>"+(index+1)+"</td>";
        inner += "<td>"+cell(r.idno)+"</td>";
        inner += "<td>"+cell(r.rdate)+"</td>";
        inner += "<td>"+cell(r.pono)+"</td>";
        inner += "<td><pre>"+cell(r.partno)+"</pre></td>";
        inner += "<td>"+cell(r.partname)+"</td>";
        inner += "<td class='text-end'>"+cell(r.newqty)+"</td>";
        inner += "<td>"+cell(r.newdate)+"</td>";
        inner += "<td class='text-end'>"+cell(r.oldqty)+"</td>";
        inner += "<td>"+cell(r.olddate)+"</td>";
        inner += "<td class='text-end'>"+cell(r.price)+"</td>";
        inner += "<td>"+cell(r.model)+"</td>";
        inner += "<td>"+cell(r.potype)+"</td>";
        inner += "<td>"+cell(r.altno)+"</td>";
        inner += "<td>"+statusBadge(r.status)+"</td>";
        inner += "<td>"+statusBadge(r.supconfstatus)+"</td>";
        inner += "<td>"+cell(r.supconfreason)+"</td>";
        inner += "<td>"+cell(r.supconfby)+"</td>";
        inner += "<td>"+cell(r.supconfat)+"</td>";
        inner += "<td>"+statusBadge(r.purconfstatus)+"</td>";
        inner += "<td>"+cell(r.purconfreason)+"</td>";
        inner += "<td>"+cell(r.purconfby)+"</td>";
        inner += "<td>"+cell(r.purconfat)+"</td>";
        inner += "<td>"+statusBadge(r.mcconfstatus)+"</td>";
        inner += "<td>"+cell(r.mcconfreason)+"</td>";
        inner += "<td>"+cell(r.mcconfby)+"</td>";
        inner += "<td>"+cell(r.mcconfat)+"</td>";
        inner += "</tr>";
    });

    inner += "</tbody>";
    html += wrapTable(inner);
    return html;
}

function renderGroupedPO(mailpo, mailpoc) {
    const groups = [];
    const byPono = {};

    function ensureGroup(pono) {
        if (!byPono[pono]) {
            byPono[pono] = { pono: pono, originals: [], revisions: [] };
            groups.push(byPono[pono]);
        }
        return byPono[pono];
    }

    mailpo.forEach(function(r) {
        ensureGroup(r.pono).originals.push(r);
    });
    mailpoc.forEach(function(r) {
        ensureGroup(r.pono).revisions.push(r);
    });

    let html = "";
    groups.forEach(function(g) {
        html += "<div class='po-card'>";
        html += "<div class='po-card-header'>PO NUMBER : " + cell(g.pono) + "</div>";
        html += "<div class='po-card-body'>";
        if (g.originals.length > 0) {
            html += renderOriginalPOTable(g.originals);
        }
        html += renderRevisionTable(g.revisions);
        html += "</div></div>";
    });
    return html;
}

async function searchPO() {

    const searchby = getSearchBy();
    let keywords = getKeywords();
    const emptyMsg = (searchby === "partno") ? "Input Part Number" : "Input PO Number";

    if (searchby === "partno") {
        keywords = keywords.slice(0, 1);
    }

    console.log("Search by : " + searchby + " / keyword : " + keywords.join(", "));

    if (keywords.length === 0) {
        alert(emptyMsg);
        return;
    }

    const params = new URLSearchParams();
    params.append("searchby", searchby);
    if (searchby === "partno") {
        params.append("keyword", keywords[0]);
    } else {
        keywords.forEach(function(k) {
            params.append("keyword[]", k);
        });
    }

    const response = await fetch("../api/apipohist.php", {
        method: "POST",
        body: params
    });

    const result = await response.json();

    console.log(result);

    let html = "";

    if(result.status=="success")
    {

        const mailpo = Array.isArray(result.mailpo) ? result.mailpo : [];
        const mailpoc = Array.isArray(result.mailpoc) ? result.mailpoc : [];

        if(mailpo.length === 0 && mailpoc.length === 0)
        {
            html = "<div class='empty-state'>Data not found</div>";
        }
        else
        {
            html = renderGroupedPO(mailpo, mailpoc);
        }

    }
    else
    {
        html="<div class='empty-state'>"+result.message+"</div>";
    }

    document.getElementById("result").innerHTML=html;

}
</script>
   </body>
  </html>