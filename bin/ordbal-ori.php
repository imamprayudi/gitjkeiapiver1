<?php
	/*
	****	create by Mohamad Yunus
	****	on 06 Juni 2017
	****	remark: -
	*/  
	include('/var/www/html/ADODB/con_ediweb.php');
        echo 'delete data';
        echo '<br>';	
	//	delete data
	$rs = $db->Execute("delete from ordbal;");
	$rs->Close();	
	
	//	read text file
	$fh = fopen('/var/www/html/uploads/pck11web.txt','r');
	while ($line = fgets($fh)) {
		set_time_limit(0);
		$transdate 		= substr($line,6,4).'-'.substr($line,0,2).'-'.substr($line,3,2);
		$suppcode 		= trim(substr($line,10,8));
		$partnumber 	= substr($line,18,15);
		$partname 		= substr($line,33,20);
		$orderqty 		= substr($line,53,4);
		$reqdate  		= substr($line,63,4).'-'.substr($line,57,2).'-'.substr($line,60,2);
		$ponumber 		= trim(substr($line,67,7));
		$posq 			= substr($line,74,2);
		$orderbalance 	= substr($line,76,9);
		$supprest 		= substr($line,85,9);
		$model 			= substr($line,94,15);
		$issuedate 		= substr($line,115,4).'-'.substr($line,109,2).'-'.substr($line,112,2);
		$potype 		= trim(substr($line,119,30));
		$statuspart 	= substr($line,149,3);
		$remark 		= trim(substr($line,152,12));
		
		$rs 		= $db->Execute("insert into ordbal(transdate, suppcode, partnumber, partname, orderqty, reqdate, ponumber, posq, orderbalance, supprest, model, issuedate, potype, statuspart, remark)
									select '{$transdate}', '{$suppcode}', '{$partnumber}', '{$partname}', '{$orderqty}', '{$reqdate}', '{$ponumber}', '{$posq}', '{$orderbalance}', '{$supprest}', '{$model}', '{$issuedate}', '{$potype}', '{$statuspart}', '{$remark}'");
		$rs->Close();
	}
	fclose($fh);
	
	//	connection close
        echo 'connection close';
	$db->Close();
?>
