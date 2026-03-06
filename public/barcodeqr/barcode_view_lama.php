<!--
----	create by Mohamad Yunus
----	on 06 Feb 2017
----	revise: -
-->
<html>
<head>
<title>QRCODE PRINT</title>
</head><body bgcolor="#ffffff">
<table border="0" width="500">
	<tr>
		<td width="250"> <img height="50px" src="../../assets/gambar/jvclogo.png" /> </td>
	</tr>
</table>
<br><br>
<?php
	include '../con_svrdbn.php';
	date_default_timezone_set('Asia/Jakarta');

	if(isset($_GET['partno']))
	{

		$lokasi = isset($_GET['lokasi']) ? $_GET['lokasi'] : '';
		$partno = isset($_GET['partno']) ? $_GET['partno'] : '';
		$pjgpartno = strlen($partno);

		switch ($pjgpartno)
		{
		  case 1:
		  $partno .= '              ';
		  break;
		  case 2:
		  $partno .= '             ';
		  break;
		  case 3:
		  $partno .= '            ';
		  break;
		  case 4:
		  $partno .= '           ';
		  break;
		  case 5:
		  $partno .= '          ';
		  break;
		  case 6:
		  $partno .= '         ';
		  break;
		  case 7:
		  $partno .= '        ';
		  break;
		  case 8:
		  $partno .= '       ';
		  break;
		  case 9:
		  $partno .= '      ';
		  break;
		  case 10:
		  $partno .= '     ';
		  break;
		  case 11:
		  $partno .= '    ';
		  break;
		  case 12:
		  $partno .= '   ';
		  break;
		  case 13:
		  $partno .= '  ';
		  break;
		  case 14:
		  $partno .= ' ';
		  break;
		  default:
		  // no action
		}
	
		$po     = isset($_GET['po']) ? $_GET['po'] : '';
		if($po == '')
		{
		  $po = '       ';
		}


		$pack		 = $_GET['pack'];
		$pjgpack = strlen($pack);
		switch ($pjgpack)
		{
			case 1:
			$pack .= '    ';
			break;
			case 2:
			$pack .= '   ';
			break;
			case 3:
			$pack .= '  ';
			break;
			case 4:
			$pack .= ' ';
			break;
			default:
		}

		$qtybal	   = $_GET['qtybal'];
		$pjgqtybal = strlen($qtybal);
		switch ($pjgqtybal)
		{
			case 1:
			$qtybal .= '    ';
			break;
			case 2:
			$qtybal .= '   ';
			break;
			case 3:
			$qtybal .= '  ';
			break;
			case 4:
			$qtybal .= ' ';
			break;
			default:
		}


		$suppname 	= isset($_GET['suppname']) ? $_GET['suppname'] : '';
		//$pack  		= isset($_GET['pack']) ? $_GET['pack'] : '';
		$qtystd 	= isset($_GET['qtystd']) ? $_GET['qtystd'] : '';
		//$qtybal 	= isset($_GET['qtybal']) ? $_GET['qtybal'] : '';
		$vsupp 		= isset($_GET['supp']) ? $_GET['supp'] : '';
		$invno 		= isset($_GET['invno']) ? $_GET['invno'] : '';
		$stsinsp3 	= isset($_GET['stsinsp']) ? $_GET['stsinsp'] : '';
		$stsinsp	= strtoupper($stsinsp3);
		//$stsinsp	= 'DIRECT';
		//$stsinsp	= 'INSPECTION';
		//	class qrcode
		include 'phpqrcode/qrlib.php';

		echo '<table border="0" cellpadding="4" cellspacing="0">';
		//	qty standard pack
		for ($y=1; $y<=$qtystd; $y++){
			//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
				$srcdb[$y]	   		= 'P';											//1
				$supp[$y] 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);			//6
				$partno_code[$y]   	= str_pad($partno,15," ", STR_PAD_RIGHT);		//15
				$date[$y] 			= date("YmdHis");								
				$micro_date[$y]		= microtime();
				$date_array_temp[$y]= explode(" ",$micro_date[$y]);
				$date_array[$y]		= substr($date_array_temp[$y][0], 2, -4);
				$orderCodeDate[$y]	= $date[$y] . $date_array[$y];					//20180328 082058 7427 = 18 
				$datetime[$y] 		= $orderCodeDate[$y];
				$printcode[$y] 		= str_pad($y,6,"0", STR_PAD_LEFT);
				$unique[$y]   		= $srcdb[$y] . $supp[$y] . $partno_code[$y] . $datetime[$y] . $printcode[$y];		//6 ( 1+6+15+18+6 = 46 )
		
			
			
			//$barcode 	= $partno . ' ' . $po . ' ' . $pack;
			$barcode 	= $partno . ' ' . $po . ' ' . $pack . ' ' . $unique[$y];
			$namafile 	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($y) . '_'. trim($unique[$y]) . '.jpg';
			$tempDir 	= '../../printqr/image/';
			QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

			echo '<tr>';
				echo '<td valign="top">';
					echo '<img style="max-height: 60px;" title="barcode" src="../../printqr/image/' . $namafile . '" alt="gambar barcode" />';
				echo '</td>';
				echo '<td valign="top">';
					echo '<font style="font-weight:bold; line-height: 1.6;">'.$partno.' &emsp;&nbsp;&nbsp;&nbsp; Loc: '.$lokasi.'</font> <br>';
					echo '<font style="font-size:9pt; font-family:Sans-serif;">PO: '.$po.' &emsp;QTY: '.$pack.' &emsp;Supp: '.$suppname.'</font> <br>';
					echo '<font style="font-size:9pt; font-family:Sans-serif;">Invc: '.$invno.' &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp; Type: <b>'.$stsinsp.'<b></font> <br>';
				echo '</td>';
			echo '</tr>';
		}
		//	qty balance
		if($qtybal > 0)
		{
			$extfile 	= $qtystd + 1;

			//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
				$srcdb300	   		= 'P';
				$supp300 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
				$partno300   		= str_pad($partno,15," ", STR_PAD_RIGHT);
				$date300 			= date("YmdHis");
				$micro_date300		= microtime();
				$date_array_temp300	= explode(" ",$micro_date300);
				$date_array300		= substr($date_array_temp300[0], 2, -4);
				$orderCodeDate300	= $date300 . $date_array300;
				$datetime300 		= $orderCodeDate300;
				$printcode300 		= str_pad($y,6,"0", STR_PAD_LEFT);
				$unique300   		= $srcdb300 . $supp300 . $partno300 . $datetime300 . $printcode300;
		

			$barcode 	= $partno . ' ' . $po . ' ' . $qtybal . ' ' . $unique300;
			$namafile 	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($extfile) . '_'. trim($unique300) . '.jpg';
			$tempDir 	= '../../printqr/image/';
			QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

			echo '<tr>';
				echo '<td valign="top">';
					echo '<img style="max-height: 60px;" title="barcode" src="../../printqr/image/' . $namafile . '" alt="gambar barcode" />';
				echo '</td>';
				echo '<td valign="top">';
					echo '<font style="font-weight:bold; line-height: 1.6;">'.$partno.' &emsp;&nbsp;&nbsp;&nbsp; Loc: '.$lokasi.'</font> <br>';
					echo '<font style="font-size:9pt; font-family:Sans-serif;">PO: '.$po.' &emsp;QTY: '.$qtybal.' &emsp;Supp: '.$suppname.'</font> <br>';
					echo '<font style="font-size:9pt; font-family:Sans-serif;">Invc: '.$invno.' &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&nbsp;&nbsp; Type: <b>'.$stsinsp.'<b></font> <br>';
				echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
	}	// end of if (isset = get
?>
</body>
</html>
