<?php
	/*
	****	create by Imam Prayudi
	****	on Mar 2020
	****	remark: 
	*/  
include('/var/www/html/ADODB/con_ediweb.php');

//	delete data
$rs = $db->Execute("delete from tdstemp;");
$rs->Close();	
	
//	read text file
$fh = fopen('/var/www/html/uploads/pbh01tds.txt','r');
while ($line = fgets($fh)) {
  set_time_limit(0);
  $bulan       = substr($line,0,2);
  $tanggal     = substr($line,3,2);
  $tahun       = substr($line,6,4);
  $transdate   = $tahun . '-' . $bulan . '-' . $tanggal;
  $hd          = substr($line,10,2);
  $tm          = substr($line,12,2);
  $suppcode    = substr($line,14,8);
  $partno      = substr($line,22,15);
  $partname    = substr($line,37,15);
  $balqty      = substr($line,52,9);
  $qty1        = substr($line,61,9);
  $qty2        = substr($line,70,9);
  $qty3        = substr($line,79,9);
  $qty4        = substr($line,88,9);
  $qty5        = substr($line,97,9);
  $qty6        = substr($line,106,9);
  $qty7        = substr($line,115,9);
  $qty8        = substr($line,124,9);
  $qty9        = substr($line,133,9);
  $qty10       = substr($line,142,9);
  $qty11       = substr($line,151,9);
  $qty12       = substr($line,160,9);
  $qty13       = substr($line,169,9);
  $qty14       = substr($line,178,9);
  $qty15       = substr($line,187,9);
  $qty16       = substr($line,196,9);
  $qty17       = substr($line,205,9);
  $qty18       = substr($line,214,9);
  $qty19       = substr($line,223,9);
  $qty20       = substr($line,232,9);
  $qty21       = substr($line,241,9);
  $qty22       = substr($line,250,9);
  $qty23       = substr($line,259,9);
  $qty24       = substr($line,268,9);
  $qty25       = substr($line,277,9);
  $qty26       = substr($line,286,9);
  $qty27       = substr($line,295,9);
  $qty28       = substr($line,304,9);
  $qty29       = substr($line,313,9);
  $qty30       = substr($line,322,9);
  $qty31       = substr($line,331,9);
  $qty32       = substr($line,340,9);
  $scold       = substr($line,349,10);

  $rs = $db->Execute("insert into tdstemp(transdate,hd,tm,suppcode,partno,partname,balqty, 
    qty1,qty2,qty3,qty4,qty5,qty6,qty7,qty8,qty9,qty10,qty11,qty12,qty13,qty14,qty15,qty16,
    qty17,qty18,qty19,qty20,qty21,qty22,qty23,qty24,qty25,qty26,qty27,qty28,qty29,qty30,
    qty31,qty32,scold) select '{$transdate}','{$hd}','{$tm}','{$suppcode}','{$partno}',
    '{$partname}','{$balqty}','{$qty1}','{$qty2}','{$qty3}','{$qty4}','{$qty5}','{$qty6}',
    '{$qty7}','{$qty8}','{$qty9}','{$qty10}','{$qty11}','{$qty12}','{$qty13}','{$qty14}',
    '{$qty15}','{$qty16}','{$qty17}','{$qty18}','{$qty19}','{$qty20}','{$qty21}','{$qty22}',
    '{$qty23}','{$qty24}','{$qty25}','{$qty26}','{$qty27}','{$qty28}','{$qty29}','{$qty30}',
    '{$qty31}','{$qty32}','{$scold}';");
  $rs->Close();
}
fclose($fh);

// Check tds is updated
$rscek = $db->Execute("Select transdate,qty1 from tdstemp where hd='H' and tm='H' limit 1;");
$transdate = substr($rscek->fields[0],0,10);
$tdstglbln   = $rscek->fields[1];
echo 'transdate : ' . $transdate;
echo '<br />';
$tdstgl = substr($tdstglbln,0,2);
$tdsbln = substr($tdstglbln,3,2);
echo 'tdstgl : ' . $tdstgl . '  tdsbln : ' . $tdsbln ;
$tdsdate = $tdsbln . $tdstgl;
echo '<br />';
echo 'tdsdate : ' . $tdsdate;
$rscek->Close();

// insert into tdsdate to prevent double insert into tds;
$rs = $db->Execute("insert into tdsdate(tdsdate,transdate) values('{$tdsdate}','{$transdate}');");
$rs->Close();
// continue insert into tds whenever no error;
$rs = $db->Execute("insert into tds(transdate,hd,tm,suppcode,partno,partname,balqty, 
  qty1,qty2,qty3,qty4,qty5,qty6,qty7,qty8,qty9,qty10,qty11,qty12,qty13,qty14,qty15,qty16,
  qty17,qty18,qty19,qty20,qty21,qty22,qty23,qty24,qty25,qty26,qty27,qty28,qty29,qty30,
  qty31,qty32,scold) select transdate,hd,tm,suppcode,partno,partname,balqty, 
  qty1,qty2,qty3,qty4,qty5,qty6,qty7,qty8,qty9,qty10,qty11,qty12,qty13,qty14,qty15,qty16,
  qty17,qty18,qty19,qty20,qty21,qty22,qty23,qty24,qty25,qty26,qty27,qty28,qty29,qty30,
  qty31,qty32,scold from tdstemp;");
$rs->Close();
echo '<br />';
echo 'Insert tds from pbh01tds.txt success<br />';
	
//	connection close
$db->Close();
    
?>
