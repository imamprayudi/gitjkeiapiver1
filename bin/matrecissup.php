<?php
	/*
	 *	create by Imam Prayudi
	 *	on Apr 2020
	 *	Material Receive & Issue Read from VI07 text file and insert to Database
	 */  
include('/var/www/html/ADODB/con_ediweb.php');
//	delete data
$rs = $db->Execute("delete from vi07temp;");
$rs->Close();	
//	read text file
$fh = fopen('/var/www/html/uploads/vi07.txt','r');
while ($line = fgets($fh)) {
  set_time_limit(0);
  $partno       = substr($line,0,15);
  $tipe         = substr($line,49,2);
  $tgl          = substr($line,55,10);
  $qtystr       = substr($line,66,13);
  $po           = substr($line,95,10);
  $suppcode     = substr($line,146,10);
  $partname     = substr($line,264,20); 
  $tglthn       = substr($tgl,0,4);
  $tglbln       = substr($tgl,4,2);
  $tgltgl       = substr($tgl,6,2);
  $tanggal      = $tglthn . '-' . $tglbln . '-' . $tgltgl;
  $qty          = (int)$qtystr;
  $rs = $db->Execute("insert into vi07temp(partno,tipe,tanggal,qty, 
    po,suppcode,partname) select '{$partno}','{$tipe}',
    '{$tanggal}','{$qty}','{$po}','{$suppcode}','{$partname}';");
  $rs->Close();
}
fclose($fh);

$tglthnbln = $tglthn . '-' . $tglbln;
echo '<br />';
echo 'tahun bulan : ' . $tglthnbln;
// Delete period ( year-month ) from vi07
$rsdel = $db->Execute("delete from vi07 where tanggal like '{$tglthnbln}%'");
$rsdel->Close();
echo '<br />';
echo 'delete from vi07' ;
$rsins = $db->Execute("insert into vi07(partno,tipe,tanggal,qty,po,suppcode,partname)
  select partno,tipe,tanggal,qty,po,suppcode,partname from vi07temp;");
$rsins->Close();
echo '<br />';
echo 'insert into vi07 select from vi07temp' ;
//	connection close
$db->Close();    
?>
