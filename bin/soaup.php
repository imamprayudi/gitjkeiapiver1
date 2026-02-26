<?php
	/*
	****	create by IMAM PRAYUDI
	****	on 6 MARET 2019
    ****	remark: -
            
	*/  
	include('/var/www/html/ADODB/con_ediweb.php');
    echo '<br />Starting....Please wait....';
    date_default_timezone_set('Asia/Bangkok');
    $tgljam = date('Y-m-d H:i:s');
    echo '<br />Starting Process : ';
    echo $tgljam;
    echo '<br />';
    //	read text file 1 baris untuk insert ke soadate
    $lanjut = TRUE;
    $fh = fopen('/var/www/html/uploads/pdh01web.txt','r');
    $line = fgets($fh);
    $transdate 	= substr($line,0,4).'-'.substr($line,5,2).'-'.substr($line,8,2);
    echo '<br />' .  $transdate ;
    echo '<br />';
    try{
      $rs = $db->Execute("insert into soadate(tanggal) select '{$transdate}'");      
      $rs->Close();
    }
    catch (exception $e)
    {   
        $lanjut = FALSE; 
        echo ' ada error / data sudah ada...';
        echo '<br />';
    }
   
    fclose($fh);
    $hitung = 0;
	$fh = fopen('/var/www/html/uploads/pdh01web.txt','r');
	while (($line = fgets($fh)) AND ($lanjut == TRUE)) {
      set_time_limit(0);
      $hitung++;
	  $transdate = substr($line,0,4).'-'.substr($line,5,2).'-'.substr($line,8,2);
      $hd = substr($line,10,2);
      $tm = substr($line,12,2);
      $blnthn = substr($line,14,7);
      $suppcode = substr($line,22,7);
      $ok = substr($line,30,1);
      $tgl = substr($line,31,8);
      $po = substr($line,39,7);
      $posq = substr($line,46,2);
      $invoice = substr($line,48,15);
      $partno = substr($line,63,15);
      $partname = substr($line,78,20);
      $qty = substr($line,98,8);
      $price = substr($line,106,10);
      $amount = substr($line,116,15);
      $dncnd = substr($line,131,15);
      $lastpay = substr($line,146,15);
      $purchase = substr($line,161,15);
      $dncns = substr($line,176,15);
      $netpur = substr($line,191,15);
      $vat = substr($line,206,15);
      $salesvat = substr($line,221,15);
      $payment = substr($line,236,15);
      $balance = substr($line,251,15);
      $col025 = substr(266,25);
      $col026 = substr(291,12);
      $col027 = substr(303,13);

       /*
        if($hitung>50)
        {
            break;
        }
        */
       
      $sqlsoa = "insert into soa(transdate,hd,tm,blnthn,suppcode,ok,tgl,po,posq,
        invoice,partno,partname,qty,price,amount,dncnd,lastpay,
        purchase,dncns,netpur,vat,salesvat,payment,balance,
        col025,col026,col027) 
        select '{$transdate}','{$hd}','{$tm}','{$blnthn}','{$suppcode}',
        '{$ok}','{$tgl}','{$po}','{$posq}','{$invoice}','{$partno}',
        '{$partname}','{$qty}','{$price}','{$amount}','{$dncnd}','{$lastpay}',
        '{$purchase}','{$dncns}','{$netpur}','{$vat}','{$salesvat}','{$payment}',
        '{$balance}','{$col025}','{$col026}','{$col027}';";
        
        $rs = $db->Execute($sqlsoa); 
	    $rs->Close();    
    }
    
    echo '<br />';   
	fclose($fh);
    echo '<br />Finish jumlah record : ' . $hitung;
    echo '<br />';
    echo 'End Process : ' . $tgljam;
?>
