<?php
	/*
	****	create by Imam Prayudi
	****	on Mar 2020
	****	remark: 
	*/  
	include('/var/www/html/ADODB/con_ediweb.php');
	
	//	delete data
	$rs = $db->Execute("delete from mailpotoday;");
	$rs->Close();	
	
	//	read text file
  $fh = fopen('/var/www/html/uploads/mail.po.txt','r');
  while ($line = fgets($fh)) {
    set_time_limit(0);
    $idno 		= substr($line,1,13);
    $rdate = substr($line,17,10);
    $rtime = substr($line,30,8);
    $suppcode = substr($line,41,10);
    $suppname = substr($line,54,40);
    $actioncode = substr($line,97,10);
    $pono = substr($line,110,15);
    $partno = substr($line,128,20);
    $partname = substr($line,151,20);
    $newqty = substr($line,173,10);
    $newdate = substr($line,185,10);
    $price = substr($line,221,10);
    $model = substr($line,233,20);
    $potype = substr($line,256,30);
    $rs = $db->Execute("insert into mailpotoday(idno,rdate,rtime, supplier, 
      suppliername,actioncode,pono,partno,partname,newqty,newdate,
      price,model,potype) select '{$idno}','{$rdate}','{$rtime}',
      '{$suppcode}','{$suppname}','{$actioncode}','{$pono}','{$partno}',
      '{$partname}','{$newqty}','{$newdate}','{$price}','{$model}','{$potype}'");
		$rs->Close();
	}
  fclose($fh);
  
  echo 'Insert mailpo from mail.po.txt success<br />';
	
	//	connection close
	$db->Close();
?>
