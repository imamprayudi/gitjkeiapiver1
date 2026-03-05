<?php
	/*
	****	Created by Imam Prayudi
	****	on 27 Januari 2026
	****

	remark: -
	*/ 
        error_reporting(E_ALL);
	ini_set('display_errors', 1);
	error_reporting(E_ALL & ~E_DEPRECATED);

 
	include('/var/www/html/ADODB/con_ediweb.php');
	
	//	delete data
	$rs = $db->Execute("delete from stdpack");
	$rs->Close();
	
	//	read csv file
	$file = '/var/www/html/uploads/stdpacks.csv';
	$fh = fopen($file,'r');
	if ($fh === false){
	  die('File csv tidak bisa dibuka...');
	}

	// baca header
	$header = fgetcsv($fh);
	// loop isi data
	while (($row = fgetcsv($fh)) !== false) {
    	  set_time_limit(0);
	  $suppcode     = $db->qstr(trim($row[0]));
	  $partnumber   = $db->qstr(trim($row[1]));
	  $partname     = $db->qstr(trim($row[2]));
	  $stdpack      = trim($row[3]) === '' ? 'NULL' : $db->qstr(trim($row[3]));
	  $kategori     = ($row[4] == '' || $row[4] == 'NULL') ? 'NULL' : $db->qstr(trim($row[4]));
	  $lokasi       = ($row[5] == '' || $row[5] == 'NULL') ? 'NULL' : $db->qstr(trim($row[5]));
	  $stdpack_supp = trim($row[6]) === '' ? 'NULL' : $db->qstr(trim($row[6]));
	  $input_user   = trim($row[7]) === '' ? 'NULL' : $db->qstr(trim($row[7]));
	  $input_date   = trim($row[8]) === '' ? 'NULL' : $db->qstr(trim($row[8]));
	  $imincl       = trim($row[9]) === '' ? 'NULL' : $db->qstr(trim($row[9]));	
 	  $sql = "
          INSERT INTO stdpack
          (suppcode, partnumber, partname, stdpack, kategori, lokasi, stdpack_supp, 
	  input_user, input_date, imincl)
          VALUES ($suppcode,$partnumber,$partname,$stdpack,$kategori,$lokasi,
            $stdpack_supp,$input_user,$input_date,$imincl) ";
	  $db->Execute($sql);			
	}
	fclose($fh);
	echo 'success';
	//	connection close

	$db->Close();

?>
