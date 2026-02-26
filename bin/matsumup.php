<?php
	/*
	 *	create by Imam Prayudi
	 *	on Apr 2020
	 *	Material Summary Read from text file and insert to Database
	 */  
include('/var/www/html/ADODB/con_ediweb.php');

//	delete data
$rs = $db->Execute("delete from sc01temp;");
$rs->Close();	
	
//	read text file
$fh = fopen('/var/www/html/uploads/sc01.txt','r');
while ($line = fgets($fh)) {
  set_time_limit(0);
  $period       = substr($line,0,8);
  $whcode       = substr($line,16,3);
  $loccode      = substr($line,22,10);
  $partno       = substr($line,44,20);
  $partname     = substr($line,82,20);
  $prevblncqty  = substr($line,152,13);
  $recqty       = substr($line,166,13);
  $shipqty      = substr($line,180,13);
  $thisblncqty  = substr($line,194,13); 
  $rs = $db->Execute("insert into sc01temp(period,whcode,loccode,partno,partname, 
    prevblncqty,recqty,shipqty,thisblncqty) select '{$period}','{$whcode}','{$loccode}',
    '{$partno}','{$partname}','{$prevblncqty}','{$recqty}','{$shipqty}','{$thisblncqty}';");
  $rs->Close();
}
fclose($fh);

// Period update
$rsdel = $db->Execute("delete from sc01period where period = (select distinct period from sc01temp);");
$rsdel->Close();

$rsins = $db->Execute("insert into sc01period select distinct period from sc01temp;");
$rsins->Close();

$rsdel = $db->Execute("delete from sc01 where period = (select distinct period from sc01temp);");
$rsdel->Close();

$rsins = $db->Execute("insert into sc01(period,loccode,partno,partname,prevblncqty,recqty,shipqty,thisblncqty)
  select period,loccode,partno,partname,prevblncqty,recqty,shipqty,thisblncqty from sc01temp where whcode = 'MC1';");
$rsins->Close();

//	connection close
$db->Close();    
?>
