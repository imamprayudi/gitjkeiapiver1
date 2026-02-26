<?php
	/*
	****	create by Imam Prayudi
	****	on Januari 2024
	****  
	****	
	*/  
	// on linux
	include('/var/www/html/ADODB/con_ediweb.php');
	// ---------
	// delete data
  $rs = $db->Execute("delete from fc2ytemp;");
	$rs->Close();	
	echo '<br />delete from fc2ytemp';	

  //	read text file
	$fh = fopen('/var/www/html/uploads/pnb01web.txt','r');
	while ($line = fgets($fh)) {
		set_time_limit(0);
		$transdate 		= substr($line,6,4).'-'.substr($line,0,2).'-'.substr($line,3,2);
		$rt 		    = substr($line,10,2);
		$suppcode 		= substr($line,12,5);
		$subsuppcode	= substr($line,17,11);
		$subsuppname	= substr($line,28,40);
		$partno         = substr($line,68,15);
		$partname       = substr($line,83,15);
		$leadtime       = substr($line,98,8);
		$dt1qt1       	= substr($line,106,9);
		$dt1qt2       	= substr($line,115,9);
		$dt1qt3       	= substr($line,124,9);
		$dt1qt4       	= substr($line,133,9);
		$dt1qt5       	= substr($line,142,9);
		$dt1qt6       	= substr($line,151,9);
		$dt1qt7       	= substr($line,160,9);
		$dt1qt8       	= substr($line,169,9);
		$dt1qt9       	= substr($line,178,9);
		$dt1qt10      	= substr($line,187,9);
		$dt1qt11      	= substr($line,196,9);
		$dt1qt12      	= substr($line,205,9);
		$dt1qt13      	= substr($line,214,9);
		$dt1qt14      	= substr($line,223,9);
		$dt1qt15      	= substr($line,232,9);
		$dt1qt16      	= substr($line,241,9);
		$dt1qt17      	= substr($line,250,9);
		$dt1qt18      	= substr($line,259,9);
		$dt1qt19      	= substr($line,268,9);
		$dt1qt20      	= substr($line,277,9);
		$dt1qt21      	= substr($line,286,9);
		$dt1qt22      	= substr($line,295,9);
		$dt1qt23      	= substr($line,304,9);
		$dt1qt24      	= substr($line,313,9);
		$dt1qt25      	= substr($line,322,9);
		$dt1qt26      	= substr($line,331,9);
		$dt1qt27      	= substr($line,340,9);
		$dt1qt28      	= substr($line,349,9);
		$dt1qt29      	= substr($line,358,9);
		$dt1qt30      	= substr($line,367,9);
		$dt1qt31      	= substr($line,376,9);
		$dt1qt32      	= substr($line,385,9);
		$dt1qt33      	= substr($line,394,9);
		$dt1qt34      	= substr($line,403,9);
    $dt1qt35        = substr($line,412,9);
    $dt1qt36        = substr($line,421,9);
		$dt1qt37        = substr($line,430,9);
		$dt1qt38      	= substr($line,439,9);
    $dt1qt39        = substr($line,448,9);
    $dt1qt40        = substr($line,457,9);
    $dt1qt41        = substr($line,466,9);
    $dt1qt42        = substr($line,475,9);
    $dt1qt43        = substr($line,484,9);
    $dt1qt44        = substr($line,493,9);
    $dt1qt45        = substr($line,502,9);
    $dt1qt46        = substr($line,511,9);
    $dt1qt47        = substr($line,520,9);
    $dt1qt48        = substr($line,529,9);
    $dt1qt49        = substr($line,538,9);
    $dt1qt50        = substr($line,547,9);
    $dt1qt51        = substr($line,556,9);
    $dt1qt52        = substr($line,565,9);
    $dt1qt53        = substr($line,574,9);
    $dt2qt1         = substr($line,583,9);
    $dt2qt2         = substr($line,592,9);
		$dt2qt3         = substr($line,601,9);
		$dt2qt4         = substr($line,610,9);
		$dt2qt5         = substr($line,619,9);
		$dt2qt6         = substr($line,628,9);
		$dt2qt7         = substr($line,637,9);
		$dt2qt8         = substr($line,646,9);
		$dt2qt9         = substr($line,655,9);
		$dt2qt10        = substr($line,664,9);
		$dt2qt11        = substr($line,673,9);
		$dt2qt12        = substr($line,682,9);
		$dt2qt13        = substr($line,691,9);
		$dt2qt14        = substr($line,700,9);
		$dt2qt15        = substr($line,709,9);
		$dt2qt16        = substr($line,718,9);
		$dt2qt17        = substr($line,727,9);
		$dt2qt18        = substr($line,736,9);
		$dt2qt19        = substr($line,745,9);
		$dt2qt20        = substr($line,754,9);
		$dt2qt21        = substr($line,763,9);
		$dt2qt22        = substr($line,772,9);
		$dt2qt23        = substr($line,781,9);
		$dt2qt24        = substr($line,790,9);
		$dt2qt25        = substr($line,799,9);
		$dt2qt26        = substr($line,808,9);
		$dt2qt27        = substr($line,817,9);
		$dt2qt28        = substr($line,826,9);
		$dt2qt29        = substr($line,835,9);
		$dt2qt30        = substr($line,844,9);
		$dt2qt31        = substr($line,853,9);
		$dt2qt32        = substr($line,862,9);
		$dt2qt33        = substr($line,871,9);
		$dt2qt34        = substr($line,880,9);
    $dt2qt35        = substr($line,889,9);
    $dt2qt36        = substr($line,898,9);
    $dt2qt37        = substr($line,907,9);
    $dt2qt38        = substr($line,916,9);
    $dt2qt39        = substr($line,925,9);
    $dt2qt40        = substr($line,934,9);
    $dt2qt41        = substr($line,943,9);
    $dt2qt42        = substr($line,952,9);
    $dt2qt43        = substr($line,961,9);
    $dt2qt44        = substr($line,970,9);
    $dt2qt45        = substr($line,979,9);
    $dt2qt46        = substr($line,988,9);
    $dt2qt47        = substr($line,997,9);
    $dt2qt48        = substr($line,1006,9);
    $dt2qt49        = substr($line,1015,9);
    $dt2qt50        = substr($line,1024,9);
    $dt2qt51        = substr($line,1033,9);
    $dt2qt52        = substr($line,1042,9);
    $dt2qt53        = substr($line,1051,9);
		$dt3qt1         = substr($line,1060,9);
		$dt3qt2         = substr($line,1069,9);
		$dt3qt3         = substr($line,1078,9);
		$dt3qt4         = substr($line,1087,9);
		$dt3qt5         = substr($line,1096,9);
		$dt3qt6         = substr($line,1105,9);
		$dt3qt7         = substr($line,1114,9);
		$dt3qt8         = substr($line,1123,9);
		$dt3qt9         = substr($line,1132,9);
		$dt3qt10        = substr($line,1141,9);
		$dt3qt11        = substr($line,1150,9);
		$dt3qt12        = substr($line,1159,9);
		$dt3qt13        = substr($line,1168,9);
		$dt3qt14        = substr($line,1177,9);
		$dt3qt15        = substr($line,1186,9);
		$dt3qt16        = substr($line,1195,9);
		$dt3qt17     	  = substr($line,1204,9);
		$dt3qt18        = substr($line,1213,9);
		$dt3qt19        = substr($line,1222,9);
		$dt3qt20        = substr($line,1231,9);
		$dt3qt21        = substr($line,1240,9);
		$dt3qt22        = substr($line,1249,9);
		$dt3qt23        = substr($line,1258,9);
		$dt3qt24        = substr($line,1267,9);
		$dt3qt25        = substr($line,1276,9);
		$dt3qt26        = substr($line,1285,9);
		$dt3qt27        = substr($line,1294,9);
		$dt3qt28        = substr($line,1303,9);
		$dt3qt29        = substr($line,1312,9);
		$dt3qt30        = substr($line,1321,9);
		$dt3qt31        = substr($line,1330,9);
		$dt3qt32        = substr($line,1339,9);
		$dt3qt33        = substr($line,1348,9);
		$dt3qt34        = substr($line,1357,9);
    $dt3qt35        = substr($line,1366,9);
    $dt3qt36        = substr($line,1375,9);
    $dt3qt37        = substr($line,1384,9);
    $dt3qt38        = substr($line,1393,9);
    $dt3qt39        = substr($line,1402,9);
    $dt3qt40        = substr($line,1411,9);
    $dt3qt41        = substr($line,1420,9);
    $dt3qt42        = substr($line,1429,9);
    $dt3qt43        = substr($line,1438,9);
    $dt3qt44        = substr($line,1447,9);
    $dt3qt45        = substr($line,1456,9);
    $dt3qt46        = substr($line,1465,9);
    $dt3qt47        = substr($line,1474,9);
    $dt3qt48        = substr($line,1483,9);
    $dt3qt49        = substr($line,1492,9);
    $dt3qt50        = substr($line,1501,9);
    $dt3qt51        = substr($line,1510,9);
    $dt3qt52        = substr($line,1519,9);
    $dt3qt53        = substr($line,1528,9);
    $dt4qt1         = substr($line,1537,9);
		$dt4qt2         = substr($line,1546,9);
		$dt4qt3         = substr($line,1555,9);
		$dt4qt4         = substr($line,1564,9);
		$dt4qt5         = substr($line,1573,9);
		$dt4qt6         = substr($line,1582,9);
		$dt4qt7         = substr($line,1591,9);
		$dt4qt8         = substr($line,1600,9);
		$dt4qt9         = substr($line,1609,9);
		$dt4qt10        = substr($line,1618,9);
		$dt4qt11        = substr($line,1627,9);
		$dt4qt12        = substr($line,1636,9);
		$dt4qt13        = substr($line,1645,9);
		$dt4qt14        = substr($line,1654,9);
		$dt4qt15        = substr($line,1663,9);
		$dt4qt16        = substr($line,1672,9);
		$dt4qt17        = substr($line,1681,9);
		$dt4qt18        = substr($line,1690,9);
		$dt4qt19        = substr($line,1699,9);
		$dt4qt20        = substr($line,1708,9);
		$dt4qt21        = substr($line,1717,9);
		$dt4qt22        = substr($line,1726,9);
		$dt4qt23     	  = substr($line,1735,9);
		$dt4qt24        = substr($line,1744,9);
		$dt4qt25        = substr($line,1753,9);
		$dt4qt26        = substr($line,1762,9);
		$dt4qt27        = substr($line,1771,9);
		$dt4qt28        = substr($line,1780,9);
		$dt4qt29        = substr($line,1789,9);
		$dt4qt30        = substr($line,1798,9);
		$dt4qt31        = substr($line,1807,9);
		$dt4qt32        = substr($line,1816,9);
		$dt4qt33        = substr($line,1825,9);
		$dt4qt34        = substr($line,1834,9);
    $dt4qt35        = substr($line,1843,9);
    $dt4qt36        = substr($line,1852,9);
    $dt4qt37        = substr($line,1861,9);
    $dt4qt38        = substr($line,1870,9);
    $dt4qt39        = substr($line,1879,9);
    $dt4qt40        = substr($line,1888,9);
    $dt4qt41        = substr($line,1897,9);
    $dt4qt42        = substr($line,1906,9);
    $dt4qt43        = substr($line,1915,9);
    $dt4qt44        = substr($line,1924,9);
    $dt4qt45        = substr($line,1933,9);
    $dt4qt46        = substr($line,1942,9);
    $dt4qt47        = substr($line,1951,9);
    $dt4qt48        = substr($line,1960,9);
    $dt4qt49        = substr($line,1969,9);
    $dt4qt50        = substr($line,1978,9);
    $dt4qt51        = substr($line,1987,9);
    $dt4qt52        = substr($line,1996,9);
    $dt4qt53        = substr($line,2005,9);
		$webcode        = substr($line,2014,20);
		
		//	insert to temp tabel

		$rs = $db->Execute("insert into fc2ytemp(	transdate, rt, suppcode, subsuppcode, subsuppname, partno, partname, leadtime, 
		    dt1qt1, dt1qt2, dt1qt3, dt1qt4, dt1qt5, dt1qt6, dt1qt7, dt1qt8, dt1qt9, dt1qt10,
			dt1qt11, dt1qt12, dt1qt13, dt1qt14, dt1qt15, dt1qt16, dt1qt17, dt1qt18, dt1qt19, dt1qt20, 
			dt1qt21, dt1qt22, dt1qt23, dt1qt24, dt1qt25, dt1qt26, dt1qt27, dt1qt28, dt1qt29, dt1qt30, 
			dt1qt31, dt1qt32, dt1qt33, dt1qt34,	dt1qt35, dt1qt36, dt1qt37, dt1qt38, dt1qt39, dt1qt40,
      dt1qt41, dt1qt42, dt1qt43, dt1qt44,	dt1qt45, dt1qt46, dt1qt47, dt1qt48, dt1qt49, dt1qt50,
      dt1qt51, dt1qt52, dt1qt53,
			dt2qt1, dt2qt2, dt2qt3, dt2qt4, dt2qt5, dt2qt6, dt2qt7, dt2qt8, dt2qt9, dt2qt10, 
			dt2qt11, dt2qt12, dt2qt13, dt2qt14, dt2qt15, dt2qt16, dt2qt17, dt2qt18, dt2qt19, dt2qt20, 
			dt2qt21, dt2qt22, dt2qt23, dt2qt24, dt2qt25, dt2qt26, dt2qt27, dt2qt28, dt2qt29, dt2qt30, 
			dt2qt31, dt2qt32, dt2qt33, dt2qt34,	dt2qt35, dt2qt36, dt2qt37, dt2qt38, dt2qt39, dt2qt40, 
      dt2qt41, dt2qt42, dt2qt43, dt2qt44,	dt2qt45, dt2qt46, dt2qt47, dt2qt48, dt2qt49, dt2qt50, 
      dt2qt51, dt2qt52, dt2qt53,
			dt3qt1, dt3qt2, dt3qt3, dt3qt4, dt3qt5, dt3qt6, dt3qt7, dt3qt8, dt3qt9, dt3qt10, 
			dt3qt11, dt3qt12, dt3qt13, dt3qt14, dt3qt15, dt3qt16, dt3qt17, dt3qt18, dt3qt19, dt3qt20, 
			dt3qt21, dt3qt22, dt3qt23, dt3qt24, dt3qt25, dt3qt26, dt3qt27, dt3qt28, dt3qt29, dt3qt30, 
			dt3qt31, dt3qt32, dt3qt33, dt3qt34,	dt3qt35, dt3qt36, dt3qt37, dt3qt38, dt3qt39, dt3qt40, 
      dt3qt41, dt3qt42, dt3qt43, dt3qt44,	dt3qt45, dt3qt46, dt3qt47, dt3qt48, dt3qt49, dt3qt50, 
      dt3qt51, dt3qt52, dt3qt53,
			dt4qt1, dt4qt2, dt4qt3, dt4qt4, dt4qt5, dt4qt6, dt4qt7, dt4qt8, dt4qt9, dt4qt10, 
			dt4qt11, dt4qt12, dt4qt13, dt4qt14, dt4qt15, dt4qt16, dt4qt17, dt4qt18, dt4qt19, dt4qt20, 
			dt4qt21, dt4qt22, dt4qt23, dt4qt24, dt4qt25, dt4qt26, dt4qt27, dt4qt28, dt4qt29, dt4qt30, 
			dt4qt31, dt4qt32, dt4qt33, dt4qt34,	dt4qt35, dt4qt36, dt4qt37, dt4qt38, dt4qt39, dt4qt40, 
      dt4qt41, dt4qt42, dt4qt43, dt4qt44,	dt4qt45, dt4qt46, dt4qt47, dt4qt48, dt4qt49, dt4qt50, 
      dt4qt51, dt4qt52, dt4qt53, webcode)
			select	'{$transdate}', '{$rt}', '{$suppcode}', '{$subsuppcode}', '{$subsuppname}', '{$partno}', '{$partname}', '{$leadtime}', 
			'{$dt1qt1}', '{$dt1qt2}', '{$dt1qt3}', '{$dt1qt4}', '{$dt1qt5}', '{$dt1qt6}', '{$dt1qt7}', '{$dt1qt8}', '{$dt1qt9}', '{$dt1qt10}',
			'{$dt1qt11}', '{$dt1qt12}', '{$dt1qt13}', '{$dt1qt14}', '{$dt1qt15}', '{$dt1qt16}', '{$dt1qt17}', '{$dt1qt18}', '{$dt1qt19}', '{$dt1qt20}', 
			'{$dt1qt21}', '{$dt1qt22}', '{$dt1qt23}', '{$dt1qt24}', '{$dt1qt25}', '{$dt1qt26}', '{$dt1qt27}', '{$dt1qt28}', '{$dt1qt29}', '{$dt1qt30}', 
			'{$dt1qt31}', '{$dt1qt32}', '{$dt1qt33}', '{$dt1qt34}', '{$dt1qt35}', '{$dt1qt36}', '{$dt1qt37}', '{$dt1qt38}', '{$dt1qt39}', '{$dt1qt40}', 
      '{$dt1qt41}', '{$dt1qt42}', '{$dt1qt43}', '{$dt1qt44}', '{$dt1qt45}', '{$dt1qt46}', '{$dt1qt47}', '{$dt1qt48}', '{$dt1qt49}', '{$dt1qt50}', 
      '{$dt1qt51}', '{$dt1qt52}', '{$dt1qt53}', 
			'{$dt2qt1}', '{$dt2qt2}', '{$dt2qt3}', '{$dt2qt4}', '{$dt2qt5}', '{$dt2qt6}', '{$dt2qt7}', '{$dt2qt8}', '{$dt2qt9}', '{$dt2qt10}', 
			'{$dt2qt11}', '{$dt2qt12}', '{$dt2qt13}', '{$dt2qt14}', '{$dt2qt15}', '{$dt2qt16}', '{$dt2qt17}', '{$dt2qt18}', '{$dt2qt19}', '{$dt2qt20}', 
			'{$dt2qt21}', '{$dt2qt22}', '{$dt2qt23}', '{$dt2qt24}', '{$dt2qt25}', '{$dt2qt26}', '{$dt2qt27}', '{$dt2qt28}', '{$dt2qt29}', '{$dt2qt30}', 
			'{$dt2qt31}', '{$dt2qt32}', '{$dt2qt33}', '{$dt2qt34}', '{$dt2qt35}', '{$dt2qt36}', '{$dt2qt37}', '{$dt2qt38}', '{$dt2qt39}', '{$dt2qt40}', 
      '{$dt2qt41}', '{$dt2qt42}', '{$dt2qt43}', '{$dt2qt44}', '{$dt2qt45}', '{$dt2qt46}', '{$dt2qt47}', '{$dt2qt48}', '{$dt2qt49}', '{$dt2qt50}', 
      '{$dt2qt51}', '{$dt2qt52}', '{$dt2qt53}', 
			'{$dt3qt1}', '{$dt3qt2}', '{$dt3qt3}', '{$dt3qt4}', '{$dt3qt5}', '{$dt3qt6}', '{$dt3qt7}', '{$dt3qt8}', '{$dt3qt9}', '{$dt3qt10}', 
			'{$dt3qt11}', '{$dt3qt12}', '{$dt3qt13}', '{$dt3qt14}', '{$dt3qt15}', '{$dt3qt16}', '{$dt3qt17}', '{$dt3qt18}', '{$dt3qt19}', '{$dt3qt20}', 
			'{$dt3qt21}', '{$dt3qt22}', '{$dt3qt23}', '{$dt3qt24}', '{$dt3qt25}', '{$dt3qt26}', '{$dt3qt27}', '{$dt3qt28}', '{$dt3qt29}', '{$dt3qt30}', 
			'{$dt3qt31}', '{$dt3qt32}', '{$dt3qt33}', '{$dt3qt34}', '{$dt3qt35}', '{$dt3qt36}', '{$dt3qt37}', '{$dt3qt38}', '{$dt3qt39}', '{$dt3qt40}', 
      '{$dt3qt41}', '{$dt3qt42}', '{$dt3qt43}', '{$dt3qt44}', '{$dt3qt45}', '{$dt3qt46}', '{$dt3qt47}', '{$dt3qt48}', '{$dt3qt49}', '{$dt3qt50}', 
      '{$dt3qt51}', '{$dt3qt52}', '{$dt3qt53}',
			'{$dt4qt1}', '{$dt4qt2}', '{$dt4qt3}', '{$dt4qt4}', '{$dt4qt5}', '{$dt4qt6}', '{$dt4qt7}', '{$dt4qt8}', '{$dt4qt9}', '{$dt4qt10}', 
			'{$dt4qt11}', '{$dt4qt12}', '{$dt4qt13}', '{$dt4qt14}', '{$dt4qt15}', '{$dt4qt16}', '{$dt4qt17}', '{$dt4qt18}', '{$dt4qt19}', '{$dt4qt20}', 
			'{$dt4qt21}', '{$dt4qt22}', '{$dt4qt23}', '{$dt4qt24}', '{$dt4qt25}', '{$dt4qt26}', '{$dt4qt27}', '{$dt4qt28}', '{$dt4qt29}', '{$dt4qt30}', 
			'{$dt4qt31}', '{$dt4qt32}', '{$dt4qt33}', '{$dt4qt34}', '{$dt4qt35}', '{$dt4qt36}', '{$dt4qt37}', '{$dt4qt38}', '{$dt4qt39}', '{$dt4qt40}', 
      '{$dt4qt41}', '{$dt4qt42}', '{$dt4qt43}', '{$dt4qt44}', '{$dt4qt45}', '{$dt4qt46}', '{$dt4qt47}', '{$dt4qt48}', '{$dt4qt49}', '{$dt4qt50}', 
      '{$dt4qt51}', '{$dt4qt52}', '{$dt4qt53}',
			'{$webcode}';");
		$rs->Close();
	}
	fclose($fh);
echo '<br />insert into fc2ytemp success';	


  // delete non standard date on forecastntemp
	$rsdel = $db->Execute("delete from fc2ytemp where transdate like '%EO%';");
	$rsdel->Close();
	echo '<br />delete from fc2ytemp where transdate like EO';	

	$rsdel = $db->Execute("delete from fc2y;");
	$rsdel->Close();
	echo '<br />delete from FC2Y';	

	$rsins = $db->Execute("insert into fc2y(	transdate, rt, suppcode, subsuppcode, subsuppname, 
      partno, partname, leadtime, 
		  dt1qt1, dt1qt2, dt1qt3, dt1qt4, dt1qt5, dt1qt6, dt1qt7, dt1qt8, dt1qt9, dt1qt10, 
			dt1qt11, dt1qt12, dt1qt13, dt1qt14, dt1qt15, dt1qt16, dt1qt17, dt1qt18, dt1qt19, dt1qt20, 
			dt1qt21, dt1qt22, dt1qt23, dt1qt24, dt1qt25, dt1qt26, dt1qt27, dt1qt28, dt1qt29, dt1qt30, 
			dt1qt31, dt1qt32, dt1qt33, dt1qt34,	dt1qt35, dt1qt36, dt1qt37, dt1qt38, dt1qt39, dt1qt40,
      dt1qt41, dt1qt42, dt1qt43, dt1qt44,	dt1qt45, dt1qt46, dt1qt47, dt1qt48, dt1qt49, dt1qt50,
      dt1qt51, dt1qt52, dt1qt53,
      dt2qt1, dt2qt2, dt2qt3, dt2qt4, dt2qt5, dt2qt6, dt2qt7, dt2qt8, dt2qt9, dt2qt10, 
			dt2qt11, dt2qt12, dt2qt13, dt2qt14, dt2qt15, dt2qt16, dt2qt17, dt2qt18, dt2qt19, dt2qt20, 
			dt2qt21, dt2qt22, dt2qt23, dt2qt24, dt2qt25, dt2qt26, dt2qt27, dt2qt28, dt2qt29, dt2qt30, 
			dt2qt31, dt2qt32, dt2qt33, dt2qt34,	dt2qt35, dt2qt36, dt2qt37, dt2qt38, dt2qt39, dt2qt40, 
      dt2qt41, dt2qt42, dt2qt43, dt2qt44,	dt2qt45, dt2qt46, dt2qt47, dt2qt48, dt2qt49, dt2qt50, 
      dt2qt51, dt2qt52, dt2qt53,
			dt3qt1, dt3qt2, dt3qt3, dt3qt4, dt3qt5, dt3qt6, dt3qt7, dt3qt8, dt3qt9, dt3qt10, 
			dt3qt11, dt3qt12, dt3qt13, dt3qt14, dt3qt15, dt3qt16, dt3qt17, dt3qt18, dt3qt19, dt3qt20, 
			dt3qt21, dt3qt22, dt3qt23, dt3qt24, dt3qt25, dt3qt26, dt3qt27, dt3qt28, dt3qt29, dt3qt30, 
			dt3qt31, dt3qt32, dt3qt33, dt3qt34,	dt3qt35, dt3qt36, dt3qt37, dt3qt38, dt3qt39, dt3qt40,
      dt3qt41, dt3qt42, dt3qt43, dt3qt44,	dt3qt45, dt3qt46, dt3qt47, dt3qt48, dt3qt49, dt3qt50,
      dt3qt51, dt3qt52, dt3qt53,
			dt4qt1, dt4qt2, dt4qt3, dt4qt4, dt4qt5, dt4qt6, dt4qt7, dt4qt8, dt4qt9, dt4qt10, 
			dt4qt11, dt4qt12, dt4qt13, dt4qt14, dt4qt15, dt4qt16, dt4qt17, dt4qt18, dt4qt19, dt4qt20, 
			dt4qt21, dt4qt22, dt4qt23, dt4qt24, dt4qt25, dt4qt26, dt4qt27, dt4qt28, dt4qt29, dt4qt30, 
			dt4qt31, dt4qt32, dt4qt33, dt4qt34,	dt4qt35, dt4qt36, dt4qt37, dt4qt38, dt4qt39, dt4qt40,
      dt4qt41, dt4qt42, dt4qt43, dt4qt44,	dt4qt45, dt4qt46, dt4qt47, dt4qt48, dt4qt49, dt4qt50,
      dt4qt51, dt4qt52, dt4qt53,webcode)
			select transdate, rt, suppcode, subsuppcode, subsuppname, partno, partname, leadtime, 
		    dt1qt1, dt1qt2, dt1qt3, dt1qt4, dt1qt5, dt1qt6, dt1qt7, dt1qt8, dt1qt9, dt1qt10, 
			dt1qt11, dt1qt12, dt1qt13, dt1qt14, dt1qt15, dt1qt16, dt1qt17, dt1qt18, dt1qt19, dt1qt20, 
			dt1qt21, dt1qt22, dt1qt23, dt1qt24, dt1qt25, dt1qt26, dt1qt27, dt1qt28, dt1qt29, dt1qt30, 
			dt1qt31, dt1qt32, dt1qt33, dt1qt34,	dt1qt35, dt1qt36, dt1qt37, dt1qt38, dt1qt39, dt1qt40,
      dt1qt41, dt1qt42, dt1qt43, dt1qt44,	dt1qt45, dt1qt46, dt1qt47, dt1qt48, dt1qt49, dt1qt50,
      dt1qt51, dt1qt52, dt1qt53,
            dt2qt1, dt2qt2, dt2qt3, dt2qt4, dt2qt5, dt2qt6, dt2qt7, dt2qt8, dt2qt9, dt2qt10, 
			dt2qt11, dt2qt12, dt2qt13, dt2qt14, dt2qt15, dt2qt16, dt2qt17, dt2qt18, dt2qt19, dt2qt20, 
			dt2qt21, dt2qt22, dt2qt23, dt2qt24, dt2qt25, dt2qt26, dt2qt27, dt2qt28, dt2qt29, dt2qt30, 
			dt2qt31, dt2qt32, dt2qt33, dt2qt34,	dt2qt35, dt2qt36, dt2qt37, dt2qt38, dt2qt39, dt2qt40, 
      dt2qt41, dt2qt42, dt2qt43, dt2qt44,	dt2qt45, dt2qt46, dt2qt47, dt2qt48, dt2qt49, dt2qt50, 
      dt2qt51, dt2qt52, dt2qt53,
			dt3qt1, dt3qt2, dt3qt3, dt3qt4, dt3qt5, dt3qt6, dt3qt7, dt3qt8, dt3qt9, dt3qt10, 
			dt3qt11, dt3qt12, dt3qt13, dt3qt14, dt3qt15, dt3qt16, dt3qt17, dt3qt18, dt3qt19, dt3qt20, 
			dt3qt21, dt3qt22, dt3qt23, dt3qt24, dt3qt25, dt3qt26, dt3qt27, dt3qt28, dt3qt29, dt3qt30, 
			dt3qt31, dt3qt32, dt3qt33, dt3qt34,	dt3qt35, dt3qt36, dt3qt37, dt3qt38, dt3qt39, dt3qt40,
      dt3qt41, dt3qt42, dt3qt43, dt3qt44,	dt3qt45, dt3qt46, dt3qt47, dt3qt48, dt3qt49, dt3qt50,
      dt3qt51, dt3qt52, dt3qt53,
			dt4qt1, dt4qt2, dt4qt3, dt4qt4, dt4qt5, dt4qt6, dt4qt7, dt4qt8, dt4qt9, dt4qt10, 
			dt4qt11, dt4qt12, dt4qt13, dt4qt14, dt4qt15, dt4qt16, dt4qt17, dt4qt18, dt4qt19, dt4qt20, 
			dt4qt21, dt4qt22, dt4qt23, dt4qt24, dt4qt25, dt4qt26, dt4qt27, dt4qt28, dt4qt29, dt4qt30, 
			dt4qt31, dt4qt32, dt4qt33, dt4qt34,	dt4qt35, dt4qt36, dt4qt37, dt4qt38, dt4qt39, dt4qt40,
      dt4qt41, dt4qt42, dt4qt43, dt4qt44,	dt4qt45, dt4qt46, dt4qt47, dt4qt48, dt4qt49, dt4qt50,
      dt4qt51, dt4qt52, dt4qt53,webcode from fc2ytemp;");
	$rsins->Close();
	echo '<br />insert into FC2Y success';
	$db->Close();
