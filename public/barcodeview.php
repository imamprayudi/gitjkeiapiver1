<?php
/*
    Created by Imam Prayudi
    Rev: PDO version (replace ADOdb)
*/

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];


/* =========================
   ENV + PDO CONNECTION
========================= */
$env = parse_ini_file(__DIR__ . '/../config/.env');

$dsn = "mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']};charset=utf8mb4";

$pdo = new PDO($dsn, $env['DB_USER'], $env['DB_PASSWORD'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);


/* =========================
   GET PARAMETER
========================= */
$partno   = trim($_REQUEST['part'] ?? '');
$suppname = trim($_REQUEST['suppname'] ?? '');
$vsupp    = trim($_REQUEST['suppcode'] ?? '');
$po       = trim($_REQUEST['po'] ?? '');
$qty      = (int)($_REQUEST['qty'] ?? 0);
$deldate  = $_REQUEST['deldate'] ?? '';
$proddate = $_REQUEST['proddate'] ?? '';
$invoice  = $_REQUEST['invno'] ?? '';
$et = $_REQUEST['et'] ?? '';


/* =========================
   QUERY WITH PDO
========================= */

// partname
$stmt = $pdo->prepare("SELECT partname FROM stdpack WHERE partnumber=?");
$stmt->execute([$partno]);
$partnm = $stmt->fetchColumn();

// kategori
$stmt = $pdo->prepare("SELECT kategori FROM supplier WHERE suppcode=?");
$stmt->execute([$vsupp]);
$kategori = $stmt->fetchColumn();

// inspection
$stmt = $pdo->prepare("
    SELECT CASE imincl
        WHEN '1' THEN 'Direct'
        ELSE 'Inspection'
    END AS sts
    FROM stdpack
    WHERE partnumber=?
");
$stmt->execute([$partno]);
$sts_inspection = $stmt->fetchColumn();

// pack + lokasi
$stmt = $pdo->prepare("
    SELECT stdpack_supp, lokasi
    FROM stdpack
    WHERE partnumber=? AND suppcode=?
");
$stmt->execute([$partno, $vsupp]);
$row = $stmt->fetch();

$pack   = $row['stdpack_supp'] ?? 1;
$lokasi = trim($row['lokasi'] ?? '');


/* =========================
   CALCULATION
========================= */
$label  = intdiv($qty, $pack);
$sisa   = $qty % $pack;

$qtystd = $label;
$qtybal = $sisa;

if ($sisa > 0) $label++;

$suppname = str_replace("&","and",$suppname);
$suppname2 = substr($suppname, 0, 7);
?>
<!DOCTYPE html>
<html>
<head>
    <title>JEIN - PRINT LABEL BARCODE VIEW</title>

   <!--  <link href="../assets/css/styles.css" rel="stylesheet"> -->
    <script src="../assets/js/jquery.js"></script>
</head>

<body>

<div id="section">

<img src="assets/gambar/jvc.gif" style="width:220px;height:35px;">
<h3>PT.JVC ELECTRONICS INDONESIA</h3>
<h4>PRINT LABEL BARCODE VIEW</h4>

<hr>

<table border="0" cellpadding="4">

<tr>
<td width="150">Supplier Name</td>
<td><b><?= htmlspecialchars($suppname) ?></b></td>
</tr>

<tr>
<td>Part Number</td>
<td><input type="text" value="<?= $partno ?>" readonly></td>
</tr>

<tr>
<td>INVOICE</td>
<td><input type="text" value="<?= $invoice ?>" readonly></td>
</tr>

<tr>
<td>PO</td>
<td><input type="text" value="<?= $po ?>" readonly></td>
</tr>

<tr>
<td>Qty</td>
<td><input type="text" value="<?= $qty ?>" readonly></td>
</tr>

<tr>
<td>Standard Pack</td>
<td><input type="text" value="<?= $pack ?>" readonly></td>
</tr>

<tr>
<td>Lokasi</td>
<td><input type="text" value="<?= $lokasi ?>" readonly></td>
</tr>

<tr>
<td>Delivery Date</td>
<td><input type="text" value="<?= $deldate ?>" readonly></td>
</tr>

<tr>
<td>Prod Date</td>
<td><input type="text" value="<?= $proddate ?>" readonly></td>
</tr>

<tr>
<td>Elec Test</td>
<td><input type="text" value="<?= $et ?>" readonly></td>
</tr>

</table>

<br><br>


<!-- =========================
     PRINT BUTTONS
========================= -->

<table>
<tr>

<td align="center">
<b>QR Inner Box</b><br><br>

<a target="_blank"
href="barcodeqr/printinner.php?
lokasi=<?= $lokasi ?>
&partno=<?= $partno ?>
&po=<?= $po ?>
&pack=<?= $pack ?>
&qtystd=<?= $qtystd ?>
&qtybal=<?= $qtybal ?>
&suppname=<?= $suppname2 ?>
&supp=<?= $vsupp ?>
&qty=<?= $qty ?>
&partnm=<?= $partnm ?>
&deldate=<?= $deldate ?>
&proddate=<?= $proddate ?>
&et=<?= $et ?>
&kategori=<?= $kategori ?>
&bn=
&invno=<?= $invoice ?>
&stsinsp=<?= $sts_inspection ?>
">
<img src="assets/gambar/innerboxqr.png">
</a>

</td>

</tr>
<tr>

<td align="center">
<b>QR Outer Box</b><br><br>

<a target="_blank"
href="barcodeqr/printouter.php?
lokasi=<?= $lokasi ?>
&partno=<?= $partno ?>
&po=<?= $po ?>
&pack=<?= $pack ?>
&qtystd=<?= $qtystd ?>
&qtybal=<?= $qtybal ?>
&suppname=<?= $suppname2 ?>
&supp=<?= $vsupp ?>
&qty=<?= $qty ?>
&partnm=<?= $partnm ?>
&deldate=<?= $deldate ?>
&proddate=<?= $proddate ?>
&et=<?= $et ?>
&kategori=<?= $kategori ?>
&bn=
&invno=<?= $invoice ?>
&stsinsp=<?= $sts_inspection ?>
">
<img src="assets/gambar/outerboxqr.png">
</a>

</td>

</tr>
</table>

</div>

</body>
</html>