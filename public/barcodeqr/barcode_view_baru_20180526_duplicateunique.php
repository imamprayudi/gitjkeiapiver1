<!--
----	create by Mohamad Yunus
----	on 06 Feb 2017
----	revise: -

	****	modify by Harris
	****	on 23 Mar 2018
	****	revise: Revise format qrcode
-->
<html>
<head>
<title>QRCODE PRINT</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
</head>
<body bgcolor="#ffffff">
<?php
include '../con_svrdbn.php';
date_default_timezone_set('Asia/Jakarta');

if(isset($_GET['partno']))
{

    $partno 	= $_GET['partno'];
    $pjgpartno 	= strlen($partno);

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

    $po     = $_GET['po'];
    if($po == '')
	{
	  $po = '       ';
	}

	$stdpack2 = $_GET['pack'];
	$pjgpack2 = strlen($stdpack2);
	switch ($pjgpack2)
	{
		case 1:
		$stdpack2 .= '    ';
		break;
		case 2:
		$stdpack2 .= '   ';
		break;
		case 3:
		$stdpack2 .= '  ';
		break;
		case 4:
		$stdpack2 .= ' ';
		break;
		default:
	}

	$qtybal2	= $_GET['qtybal'];
	$pjgqtybal2 = strlen($qtybal2);
	switch ($pjgqtybal2)
	{
		case 1:
		$qtybal2 .= '    ';
		break;
		case 2:
		$qtybal2 .= '   ';
		break;
		case 3:
		$qtybal2 .= '  ';
		break;
		case 4:
		$qtybal2 .= ' ';
		break;
		default:
	}

	$lokasi 	= $_GET['lokasi'];
	$suppname 	= $_GET['suppname'];
	$pack   	= $_GET['pack'];
	$qtystd 	= $_GET['qtystd'];
	$qtybal 	= $_GET['qtybal'];
	$vsupp 		= $_GET['supp'];

	$partnm 	= $_GET['partnm'];
	$qty 		= $_GET['qty'];
	$deldate 	= $_GET['deldate'];
	$proddate 	= $_GET['proddate'];
	$kategori   = $_GET['kategori'];

	$stsinsp3 	= isset($_GET['stsinsp']) ? $_GET['stsinsp'] : '';
	$stsinsp2	= strtoupper($stsinsp3);
	//$stsinsp2	= 'DIRECT';
	//$stsinsp2	= 'INSPECTION';
	$stsinsp	= substr($stsinsp2,0,1);


    //	class qrcode
    include 'phpqrcode/qrlib.php';
	/*
	//generate
	$tempDir = 'imageQR/';
	$filename= $barcode;
	$content = $barcode;
	QRcode::png($barcode, $tempDir . $filename .'.png', QR_ECLEVEL_L, 2);
	echo '<img style="max-height: 50px;" src="imageQR/'.$filename.'.png" />';
	*/

	//  Preview Format Barcode berdasarkan Kategori Supplier
	switch ($kategori)
	{
		case '1':  // Format barcode kategori  --- >>>  Stamping Metal Part
			$mtrl 		= $_GET['mtrl'];
			$shift 		= $_GET['shift'];
			$qcc 		= $_GET['qcc'];

			$stdpack 	= $pack; // standard packing tiap palet
			$totalqty 	= $qty; // total production quantity
			$sisa 		= $totalqty % $stdpack; // sisa hasil bagi
			$jml 		=ceil($totalqty / $stdpack); //hasil bagi dibulatkan ke atas
			$kurang 	= 1;
			//$jml =floor($totalqty / $stdpack); //hasil bagi dibulatkan ke bawah
			//echo $jml ." dan " .$sisa;

			$sisa2 = $sisa;
			$pjgsisa = strlen($sisa);
			switch ($pjgsisa)
			{
				case 1:
				$sisa2 .= '    ';
				break;
				case 2:
				$sisa2 .= '   ';
				break;
				case 3:
				$sisa2 .= '  ';
				break;
				case 4:
				$sisa2 .= ' ';
				break;
				default:
			}

			echo '<table border=0 cellspacing=0 width=650>';
			if ($jml==1){
				if ($stdpack == $totalqty){
					
					//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
						$srcdb1	   			= 'P';
						$supp1 	   			= str_pad($vsupp,6," ", STR_PAD_RIGHT);
						$partno1   			= str_pad($partno,15," ", STR_PAD_RIGHT);
						$date1 				= date("YmdHis");
						$micro_date1		= microtime();
						$date_array_temp1	= explode(" ",$micro_date1);
						$date_array1		= substr($date_array_temp1[0], 2, -4);
						$orderCodeDate1		= $date1 . $date_array1;
						$datetime1 			= $orderCodeDate1;
						$printcode1 		= str_pad($jml,6,"0", STR_PAD_LEFT);
						$unique1   		= $srcdb1 . $supp1 . $partno1 . $datetime1 . $printcode1;
					
					//	generate label
						$namafile 	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique1) . '.jpg';
						$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique1;
						$tempDir 	= '1 - Label Stamping Metal Part/';
						QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

						echo '<tr><td>';
						echo '<table border=1 cellspacing=0 width=300>';
							echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="1 - Label Stamping Metal Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
							echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
							echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Material</p></td> <td align=center><p>'.$mtrl.'</p></td> </tr>';
							echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
							echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$stdpack.' PCS</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Shift</p></td> <td align=center><p>'.$shift.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
							echo '<tr> <td align=center><p>QC Check</p></td> <td align=center><p>'.$qcc.'</b></p></td> </tr>';
						echo '</table>';
						echo '</td></tr>';
				}
				else{
					//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
						$srcdb2	   			= 'P';
						$supp2 	   			= str_pad($vsupp,6," ", STR_PAD_RIGHT);
						$partno2   			= str_pad($partno,15," ", STR_PAD_RIGHT);
						$date2 				= date("YmdHis");
						$micro_date2		= microtime();
						$date_array_temp2	= explode(" ",$micro_date2);
						$date_array2		= substr($date_array_temp2[0], 2, -4);
						$orderCodeDate2		= $date2 . $date_array2;
						$datetime2 			= $orderCodeDate2;
						$printcode2 		= str_pad($jml,6,"0", STR_PAD_LEFT);
						$unique2   		= $srcdb2 . $supp2 . $partno2 . $datetime2 . $printcode2;
						
						//	generate label
						$extfile 	= $jml + 1;
						$namafile 	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($qtybal) . '_' . trim($unique2) . '.jpg';
						$barcode 	= $partno . ' ' . $po . ' ' . $qtybal2 . ' ' . $unique2;
						$tempDir 	= '1 - Label Stamping Metal Part/';
						QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

						echo '<tr><td>';
						echo '<table border=1 cellspacing=0 width=300>';
							echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="1 - Label Stamping Metal Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
							echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
							echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Material</p></td> <td align=center><p>'.$mtrl.'</p></td> </tr>';
							echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
							echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$qtybal.' PCS</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Shift</p></td> <td align=center><p>'.$shift.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
							echo '<tr> <td align=center><p>QC Check</p></td> <td align=center><p>'.$qcc.'</b></p></td> </tr>';
						echo '</table>';
						echo '</td></tr>';
					}
				} // end if ($jml==1)
				if ($jml==2){
					//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
						$srcdb3	   			= 'P';
						$supp3 	   			= str_pad($vsupp,6," ", STR_PAD_RIGHT);
						$partno3   			= str_pad($partno,15," ", STR_PAD_RIGHT);
						$date3 				= date("YmdHis");
						$micro_date3		= microtime();
						$date_array_temp3	= explode(" ",$micro_date3);
						$date_array3		= substr($date_array_temp3[0], 2, -4);
						$orderCodeDate3		= $date3 . $date_array3;
						$datetime3 			= $orderCodeDate3;
						$printcode3 		= str_pad("1",6,"0", STR_PAD_LEFT);
						$unique3   			= $srcdb3 . $supp3 . $partno3 . $datetime3 . $printcode3;
						

					//	generate label
						$namafile 	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique3) . '.jpg';
						$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique3;
						$tempDir 	= '1 - Label Stamping Metal Part/';
						QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

						echo '<tr><td>';
							echo '<table border=1 cellspacing=0 width=300>';
								echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="1 - Label Stamping Metal Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td></tr>';
								echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
								echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Material</p></td> <td align=center><p>'.$mtrl.'</p></td> </tr>';
								echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
								echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$stdpack.' PCS</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Shift</p></td> <td align=center><p>'.$shift.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
								echo '<tr> <td align=center><p>QC Check</p></td> <td align=center><p>'.$qcc.'</b></p></td> </tr>';
							echo '</table>';
						echo '</td>';
						if ($sisa!=0){
							//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
							$srcdb4	   			= 'P';
							$supp4 	   			= str_pad($vsupp,6," ", STR_PAD_RIGHT);
							$partno4   			= str_pad($partno,15," ", STR_PAD_RIGHT);
							$date4 				= date("YmdHis");
							$micro_date4		= microtime();
							$date_array_temp4	= explode(" ",$micro_date4);
							$date_array4		= substr($date_array_temp4[0], 2, -4);
							$orderCodeDate4		= $date4 . $date_array4;
							$datetime4 			= $orderCodeDate4;
							$printcode4 		= str_pad("2",6,"0", STR_PAD_LEFT);
							$unique4   		= $srcdb4 . $supp4 . $partno4 . $datetime4 . $printcode4;


							//	generate label
							$extfile 	= $sisa;
							$namafile	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($sisa) . '_' . trim($unique4) . '.jpg';
							$barcode 	= $partno . ' ' . $po . ' ' . $sisa2 . ' ' . $unique4;
							$tempDir 	= '1 - Label Stamping Metal Part/';
							QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

							echo '<td align=right>';
							echo '<table border=1 cellspacing=0 width=300>';
								echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="1 - Label Stamping Metal Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
								echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
								echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Material</p></td> <td align=center><p>'.$mtrl.'</p></td> </tr>';
								echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
								echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$sisa.' PCS</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Shift</p></td> <td align=center><p>'.$shift.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
								echo '<tr> <td align=center><p>QC Check</p></td> <td align=center><p>'.$qcc.'</b></p></td> </tr>';
							echo '</table>';
							echo '</td></tr>';
						} // end if ($sisa!=0)
						else{
							//echo("<td>". $stdpack ."</td></tr>");
							//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
								$srcdb5	   			= 'P';
								$supp5 	   			= str_pad($vsupp,6," ", STR_PAD_RIGHT);
								$partno5   			= str_pad($partno,15," ", STR_PAD_RIGHT);
								$date5 				= date("YmdHis");
								$micro_date5		= microtime();
								$date_array_temp5	= explode(" ",$micro_date5);
								$date_array5		= substr($date_array_temp5[0], 2, -4);
								$orderCodeDate5		= $date5.$date_array5;
								$datetime5 			= $orderCodeDate5;
								$printcode5 		= str_pad("2",6,"0", STR_PAD_LEFT);
								$unique5   		= $srcdb5 . $supp5 . $partno5 . $datetime5 . $printcode5;

							//	generate label
							$namafile 	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique5) . '.jpg';
							$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique5;
							$tempDir 	= '1 - Label Stamping Metal Part/';
							QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

							echo '<td align=right>';
							echo '<table border=1 cellspacing=0 width=300>';
								echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="1 - Label Stamping Metal Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
								echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
								echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Material</p></td> <td align=center><p>'.$mtrl.'</p></td> </tr>';
								echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
								echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$stdpack.' PCS</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Shift</p></td> <td align=center><p>'.$shift.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
								echo '<tr> <td align=center><p>QC Check</p></td> <td align=center><p>'.$qcc.'</b></p></td> </tr>';
							echo '</table>';
							echo '</td></tr>';
						} //end  else
				} // end if ($jml==2)

				if($jml>2){
					for ($i=1;$i<=$jml;$i++){
						//echo'<br>'.$i.'<br>';
						$kurang = $i * $stdpack;
						if($i % 2!=0){
							if (($totalqty - $kurang)>$sisa){
								//echo("<tr><td>". $stdpack ."</td>");
								//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
									$srcdb6	   			= 'P';
									$supp6 	   			= str_pad($vsupp,6," ", STR_PAD_RIGHT);
									$partno6   			= str_pad($partno,15," ", STR_PAD_RIGHT);
									$date6 				= date("YmdHis");
									$micro_date6		= microtime();
									$date_array_temp6	= explode(" ",$micro_date6);
									$date_array6		= substr($date_array_temp6[0], 2, -4);
									$orderCodeDate6		= $date6.$date_array6;
									$datetime6 			= $orderCodeDate6;
									$printcode6 		= str_pad($i,6,"0", STR_PAD_LEFT);
									$unique6   		= $srcdb6 . $supp6 . $partno6 . $datetime6 . $printcode6;

								//	generate label
								$a 			= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique6) . '.jpg';
								$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique6;
								$tempDir 	= '1 - Label Stamping Metal Part/';
								QRcode::png($barcode, $tempDir . $a, QR_ECLEVEL_L, 2);

								if(($i+1) % 6==0){
									echo '<tr style="page-break-after: always;"><td>';
								}elseif($i % 3==0)	{
									echo '<tr><td height=400>';
								}else{
									echo '<tr><td>';
								}
								echo '<table border=1 cellspacing=0 width=300>';
									echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="1 - Label Stamping Metal Part/' . $a . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
									echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
									echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
									echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
									echo '<tr> <td align=center><p>Material</p></td> <td align=center><p>'.$mtrl.'</p></td> </tr>';
									echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
									echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$stdpack.' PCS</b></p></td> </tr>';
									echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
									echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
									echo '<tr> <td align=center><p>Shift</p></td> <td align=center><p>'.$shift.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
									echo '<tr> <td align=center><p>QC Check</p></td> <td align=center><p>'.$qcc.'</b></p></td> </tr>';
								echo '</table>';
								echo '</td>';
							} // end if (($totalqty - $kurang)>$sisa)
							else{
								if($sisa==0){
									//echo("<tr><td>". $stdpack ."</td>");
									//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
										$srcdb7	   			= 'P';
										$supp7 	   			= str_pad($vsupp,6," ", STR_PAD_RIGHT);
										$partno7   			= str_pad($partno,15," ", STR_PAD_RIGHT);
										$date7 				= date("YmdHis");
										$micro_date7		= microtime();
										$date_array_temp7	= explode(" ",$micro_date7);
										$date_array7		= substr($date_array_temp7[0], 2, -4);
										$orderCodeDate7		= $date7.$date_array7;
										$datetime7 			= $orderCodeDate7;
										$printcode7 		= str_pad($i,6,"0", STR_PAD_LEFT);
										$unique7   		= $srcdb7 . $supp7 . $partno7 . $datetime7 . $printcode7;
									//	generate label
									$a 			= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique7) . '.jpg';
									$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique7;
									$tempDir 	= '1 - Label Stamping Metal Part/';
									QRcode::png($barcode, $tempDir . $a, QR_ECLEVEL_L, 2);

									if(($i+1) % 6==0){
										echo '<tr style="page-break-after: always;"><td>';
									}elseif($i % 3==0)	{
										echo '<tr><td height=400>';
									}else{
										echo '<tr><td>';
									}
									echo '<table border=1 cellspacing=0 width=300>';
										echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="1 - Label Stamping Metal Part/' . $a . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
										echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
										echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
										echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
										echo '<tr> <td align=center><p>Material</p></td> <td align=center><p>'.$mtrl.'</p></td> </tr>';
										echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
										echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$stdpack.' PCS</b></p></td> </tr>';
										echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
										echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
										echo '<tr> <td align=center><p>Shift</p></td> <td align=center><p>'.$shift.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
										echo '<tr> <td align=center><p>QC Check</p></td> <td align=center><p>'.$qcc.'</b></p></td> </tr>';
									echo '</table>';
									echo '</td>';
								}else{
									//echo("<tr><td>". $sisa ."</td>");
									//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
										$srcdb8	   			= 'P';
										$supp8 	   			= str_pad($vsupp,6," ", STR_PAD_RIGHT);
										$partno8   			= str_pad($partno,15," ", STR_PAD_RIGHT);
										$date8 				= date("YmdHis");
										$micro_date8		= microtime();
										$date_array_temp8	= explode(" ",$micro_date8);
										$date_array8		= substr($date_array_temp8[0], 2, -4);
										$orderCodeDate8		= $date8.$date_array8;
										$datetime8 			= $orderCodeDate8;
										$printcode8 		= str_pad($i,6,"0", STR_PAD_LEFT);
										$unique8   		= $srcdb8 . $supp8 . $partno8 . $datetime8 . $printcode8;

									//	generate label
									$extfile 	= $sisa;
									$namafile 	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($sisa) . '_' . trim($unique8) . '.jpg';
									$barcode 	= $partno . ' ' . $po . ' ' . $sisa2 . ' ' . $unique8;
									$tempDir 	= '1 - Label Stamping Metal Part/';
									QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

									if(($i+1) % 6==0){
										echo '<tr style="page-break-after: always;"><td>';
									}elseif($i % 3==0)	{
										echo '<tr><td height=400>';
									}else{
										echo '<tr><td>';
									}
									echo '<table border=1 cellspacing=0 width=300>';
										echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="1 - Label Stamping Metal Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
										echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
										echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
										echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
										echo '<tr> <td align=center><p>Material</p></td> <td align=center><p>'.$mtrl.'</p></td> </tr>';
										echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
										echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$sisa.' PCS</b></p></td> </tr>';
										echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
										echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
										echo '<tr> <td align=center><p>Shift</p></td> <td align=center><p>'.$shift.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
										echo '<tr> <td align=center><p>QC Check</p></td> <td align=center><p>'.$qcc.'</b></p></td> </tr>';
									echo '</table>';
									echo '</td>';
								}
							} //end else
						} // end if($i % 2!=0)
						if ($i % 2==0){
								//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
									$srcdb_kanan1	   		= 'P';
									$supp_kanan1	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
									$partno_kanan1   		= str_pad($partno,15," ", STR_PAD_RIGHT);
									$date_kanan1			= date("YmdHis");
									$micro_date_kanan1		= microtime();
									$date_array_temp_kanan1	= explode(" ",$micro_date_kanan1);
									$date_array_kanan1		= substr($date_array_temp_kanan1[0], 2, -4);
									$orderCodeDate_kanan1	= $date_kanan1.$date_array_kanan1;
									$datetime_kanan1 		= $orderCodeDate_kanan1;
									$printcode_kanan1 		= str_pad($i,6,"0", STR_PAD_LEFT);
									$unique_kanan1   		= $srcdb_kanan1 . $supp_kanan1 . $partno_kanan1 . $datetime_kanan1 . $printcode_kanan1;

								//	generate label
								$b_kanan1 = trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . $unique_kanan1 . '.jpg';
								$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique_kanan1;
								$tempDir 	= '1 - Label Stamping Metal Part/';
								QRcode::png($barcode, $tempDir . $b_kanan1, QR_ECLEVEL_L, 2);

							echo '<td align=right >';
							echo '<table border=1 cellspacing=0 width=300>';
								echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="1 - Label Stamping Metal Part/' . $b_kanan1 . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
								echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
								echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Material</p></td> <td align=center><p>'.$mtrl.'</p></td> </tr>';
								echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
								echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$stdpack.' PCS</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Shift</p></td> <td align=center><p>'.$shift.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
								echo '<tr> <td align=center><p>QC Check</p></td> <td align=center><p>'.$qcc.'</b></p></td> </tr>';
							echo '</table>';
							echo '</td></tr>';
						} // if ($i % 2==0)
					} // end for ($i=1;$i<=$jml;$i++)
				} // end if($jml>2)
			echo '</table>';
		break;
		// End Format barcode kategori  --- >>>  Stamping Metal Part

		case '2':  // Format barcode kategori  --- >>>  Injection Part
			$mtrl 		= $_GET['mtrl'];
			$shift 		= $_GET['shift'];
			$qcc 		= $_GET['qcc'];
			$ulcode   	= $_GET['ulcode'];

			$stdpack 	= $pack; // standard packing tiap palet
			$totalqty 	= $qty; // total production quantity
			$sisa 		= $totalqty % $stdpack; // sisa hasil bagi
			$jml 		=ceil($totalqty / $stdpack); //hasil bagi dibulatkan ke atas
			$kurang 	= 1;
			//$jml =floor($totalqty / $stdpack); //hasil bagi dibulatkan ke bawah
			//echo $jml ." dan " .$sisa;

      $sisa3 = $sisa;
      $pjgsisa3 = strlen($sisa);
      switch ($pjgsisa3)
      {
        case 1:
        $sisa3 .= '    ';
        break;
        case 2:
        $sisa3 .= '   ';
        break;
        case 3:
        $sisa3 .= '  ';
        break;
        case 4:
        $sisa3 .= ' ';
        break;
        default:
      }


			echo '<table border=0 cellspacing=0 width=650>';
				if ($jml==1){
					if ($stdpack == $totalqty){
						//echo("<tr><td>". $totalqty ."</td></tr>");
						//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
							$srcdb9	   			= 'P';
							$supp9	   			= str_pad($vsupp,6," ", STR_PAD_RIGHT);
							$partno9   			= str_pad($partno,15," ", STR_PAD_RIGHT);
							$date9				= date("YmdHis");
							$micro_date9		= microtime();
							$date_array_temp9	= explode(" ",$micro_date9);
							$date_array9		= substr($date_array_temp9[0], 2, -4);
							$orderCodeDate9		= $date9.$date_array9;
							$datetime9 			= $orderCodeDate9;
							$printcode9 		= str_pad($jml,6,"0", STR_PAD_LEFT);
							$unique9   			= $srcdb9 . $supp9 . $partno9 . $datetime9 . $printcode9;

						//	generate label
						$namafile 	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique9) . '.jpg';
						$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique9;
						$tempDir 	= '2 - Label Injection Part/';
						QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

						echo '<tr><td>';
						echo '<table border=1 cellspacing=0 width=300>';
							echo '<tr> <td width=70 align=center rowspan=2><img style="max-height: 60px;" src="2 - Label Injection Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p2><b>'.$suppname.'</b></p2></td> <td width=70 align=center rowspan=2><p2><b>RoHS <br> OK</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2><b>'.$ulcode.'</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Customer</p2></td> <td align=center colspan=2><p2>PT. JVC ELECTRONICS INDONESIA</p2></td> </tr>';
							echo '<tr> <td align=center><p2>Part Name.</p2></td> <td align=center><p2>'.$partnm.'</p2></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
							echo '<tr> <td align=center><p2>Part No.</p2></td> <td align=center><p2><b>'.$partno.'</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Material</p2></td> <td align=center><p2>'.$mtrl.'</p2></td> </tr>';
							echo '<tr> <td align=center><p2>PO No.</p2></td> <td align=center><p2>'.$po.'</p2></td> </tr>';
							echo '<tr> <td align=center><p2>Qty</p2></td> <td align=center><p2><b>'.$stdpack.' PCS</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Del. Date</p2></td> <td align=center><p2>'.$deldate.'</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Prod. Date</p2></td> <td align=center><p2>'.$proddate.'</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Shift</p2></td> <td align=center><p2>'.$shift.'</p2></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
							echo '<tr> <td align=center><p2>QC Check</p2></td> <td align=center><p2>'.$qcc.'</b></p2></td> </tr>';
						echo '</table>';
						echo '</td></tr>';
					}
					else{
						//echo("<tr><td>". $sisa ."</td></tr>");
						//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
							$srcdb10   			= 'P';
							$supp10	   			= str_pad($vsupp,6," ", STR_PAD_RIGHT);
							$partno10  			= str_pad($partno,15," ", STR_PAD_RIGHT);
							$date10				= date("YmdHis");
							$micro_date10		= microtime();
							$date_array_temp10	= explode(" ",$micro_date10);
							$date_array10		= substr($date_array_temp10[0], 2, -4);
							$orderCodeDate10	= $date10.$date_array10;
							$datetime10 		= $orderCodeDate10;
							$printcode10 		= str_pad($jml,6,"0", STR_PAD_LEFT);
							$unique10   		= $srcdb10 . $supp10 . $partno10 . $datetime10 . $printcode10;
							
						//	generate label
						$extfile 	= $jml + 1;
						$namafile 	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($qtybal) . '_' . trim($unique10) . '.jpg';
						$barcode 	= $partno . ' ' . $po . ' ' . $qtybal2 . ' ' . $unique10;
						$tempDir 	= '2 - Label Injection Part/';
						QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

						echo '<tr><td>';
						echo '<table border=1 cellspacing=0 width=300>';
							echo '<tr> <td width=70 align=center rowspan=2><img style="max-height: 60px;" src="2 - Label Injection Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p2><b>'.$suppname.'</b></p2></td> <td width=70 align=center rowspan=2><p2><b>RoHS <br> OK</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2><b>'.$ulcode.'</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Customer</p2></td> <td align=center colspan=2><p2>PT. JVC ELECTRONICS INDONESIA</p2></td> </tr>';
							echo '<tr> <td align=center><p2>Part Name.</p2></td> <td align=center><p2>'.$partnm.'</p2></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
							echo '<tr> <td align=center><p2>Part No.</p2></td> <td align=center><p2><b>'.$partno.'</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Material</p2></td> <td align=center><p2>'.$mtrl.'</p2></td> </tr>';
							echo '<tr> <td align=center><p2>PO No.</p2></td> <td align=center><p2>'.$po.'</p2></td> </tr>';
							echo '<tr> <td align=center><p2>Qty</p2></td> <td align=center><p2><b>'.$qtybal.' PCS</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Del. Date</p2></td> <td align=center><p2>'.$deldate.'</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Prod. Date</p2></td> <td align=center><p2>'.$proddate.'</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Shift</p2></td> <td align=center><p2>'.$shift.'</p2></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
							echo '<tr> <td align=center><p2>QC Check</p2></td> <td align=center><p2>'.$qcc.'</b></p2></td> </tr>';
						echo '</table>';
						echo '</td></tr>';
					}
				} // end if ($jml==1)
				if ($jml==2){
					//echo("<tr><td>". $stdpack ."</td>");
					//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
						$srcdb11   			= 'P';
						$supp11	   			= str_pad($vsupp,6," ", STR_PAD_RIGHT);
						$partno11  			= str_pad($partno,15," ", STR_PAD_RIGHT);
						$date11				= date("YmdHis");
						$micro_date11		= microtime();
						$date_array_temp11	= explode(" ",$micro_date11);
						$date_array11		= substr($date_array_temp11[0], 2, -4);
						$orderCodeDate11	= $date11.$date_array11;
						$datetime11 		= $orderCodeDate11;
						$printcode11 		= str_pad("1",6,"0", STR_PAD_LEFT);
						$unique11   		= $srcdb11 . $supp11 . $partno11 . $datetime11 . $printcode11;
						

					//	generate label
					$namafile 	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique11) . '.jpg';
					$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique11;
					$tempDir 	= '2 - Label Injection Part/';
					QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

					echo '<tr><td>';
						echo '<table border=1 cellspacing=0 width=300>';
							echo '<tr> <td width=70 align=center rowspan=2><img style="max-height: 60px;" src="2 - Label Injection Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p2><b>'.$suppname.'</b></p2></td> <td width=70 align=center rowspan=2><p2><b>RoHS <br> OK</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2><b>'.$ulcode.'</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Customer</p2></td> <td align=center colspan=2><p2>PT. JVC ELECTRONICS INDONESIA</p2></td> </tr>';
							echo '<tr> <td align=center><p2>Part Name.</p2></td> <td align=center><p2>'.$partnm.'</p2></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
							echo '<tr> <td align=center><p2>Part No.</p2></td> <td align=center><p2><b>'.$partno.'</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Material</p2></td> <td align=center><p2>'.$mtrl.'</p2></td> </tr>';
							echo '<tr> <td align=center><p2>PO No.</p2></td> <td align=center><p2>'.$po.'</p2></td> </tr>';
							echo '<tr> <td align=center><p2>Qty</p2></td> <td align=center><p2><b>'.$stdpack.' PCS</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Del. Date</p2></td> <td align=center><p2>'.$deldate.'</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Prod. Date</p2></td> <td align=center><p2>'.$proddate.'</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Shift</p2></td> <td align=center><p2>'.$shift.'</p2></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
							echo '<tr> <td align=center><p2>QC Check</p2></td> <td align=center><p2>'.$qcc.'</b></p2></td> </tr>';
						echo '</table>';
					echo '</td>';
					if ($sisa!=0){
						//echo("<td>". $sisa ."</td></tr>");
						//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
							$srcdb12   			= 'P';
							$supp12	   			= str_pad($vsupp,6," ", STR_PAD_RIGHT);
							$partno12  			= str_pad($partno,15," ", STR_PAD_RIGHT);
							$date12				= date("YmdHis");
							$micro_date12		= microtime();
							$date_array_temp12	= explode(" ",$micro_date12);
							$date_array12		= substr($date_array_temp12[0], 2, -4);
							$orderCodeDate12	= $date12.$date_array12;
							$datetime12 		= $orderCodeDate12;
							$printcode12 		= str_pad("2",6,"0", STR_PAD_LEFT);
							$unique12	  		= $srcdb12 . $supp12 . $partno12 . $datetime12 . $printcode12;
						

						//	generate label
						$extfile 	= $sisa;
						$namafile	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($sisa) . '_' . trim($unique12) . '.jpg';
						$barcode 	= $partno . ' ' . $po . ' ' . $sisa3 . ' ' . $unique12 ;
						$tempDir 	= '2 - Label Injection Part/';
						QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

						echo '<td align=right>';
						echo '<table border=1 cellspacing=0 width=300>';
							echo '<tr> <td width=70 align=center rowspan=2><img style="max-height: 60px;" src="2 - Label Injection Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p2><b>'.$suppname.'</b></p2></td> <td width=70 align=center rowspan=2><p2><b>RoHS <br> OK</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2><b>'.$ulcode.'</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Customer</p2></td> <td align=center colspan=2><p2>PT. JVC ELECTRONICS INDONESIA</p2></td> </tr>';
							echo '<tr> <td align=center><p2>Part Name.</p2></td> <td align=center><p2>'.$partnm.'</p2></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
							echo '<tr> <td align=center><p2>Part No.</p2></td> <td align=center><p2><b>'.$partno.'</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Material</p2></td> <td align=center><p2>'.$mtrl.'</p2></td> </tr>';
							echo '<tr> <td align=center><p2>PO No.</p2></td> <td align=center><p2>'.$po.'</p2></td> </tr>';
							echo '<tr> <td align=center><p2>Qty</p2></td> <td align=center><p2><b>'.$sisa.' PCS</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Del. Date</p2></td> <td align=center><p2>'.$deldate.'</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Prod. Date</p2></td> <td align=center><p2>'.$proddate.'</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Shift</p2></td> <td align=center><p2>'.$shift.'</p2></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
							echo '<tr> <td align=center><p2>QC Check</p2></td> <td align=center><p2>'.$qcc.'</b></p2></td> </tr>';
						echo '</table>';
						echo '</td></tr>';
					} // end if ($sisa!=0)
					else{
						//echo("<td>". $stdpack ."</td></tr>");
						//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
							$srcdb13   			= 'P';
							$supp13	   			= str_pad($vsupp,6," ", STR_PAD_RIGHT);
							$partno13  			= str_pad($partno,15," ", STR_PAD_RIGHT);
							$date13				= date("YmdHis");
							$micro_date13		= microtime();
							$date_array_temp13	= explode(" ",$micro_date13);
							$date_array13		= substr($date_array_temp13[0], 2, -4);
							$orderCodeDate13	= $date13.$date_array13;
							$datetime13 		= $orderCodeDate13;
							$printcode13 		= str_pad("2",6,"0", STR_PAD_LEFT);
							$unique13	  		= $srcdb13 . $supp13 . $partno13 . $datetime13 . $printcode13;

						//	generate label
						$namafile 	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique13) . '.jpg';
						$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique13;
						$tempDir 	= '2 - Label Injection Part/';
						QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

						echo '<td align=right>';
						echo '<table border=1 cellspacing=0 width=300>';
							echo '<tr> <td width=70 align=center rowspan=2><img style="max-height: 60px;" src="2 - Label Injection Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p2><b>'.$suppname.'</b></p2></td> <td width=70 align=center rowspan=2><p2><b>RoHS <br> OK</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2><b>'.$ulcode.'</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Customer</p2></td> <td align=center colspan=2><p2>PT. JVC ELECTRONICS INDONESIA</p2></td> </tr>';
							echo '<tr> <td align=center><p2>Part Name.</p2></td> <td align=center><p2>'.$partnm.'</p2></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
							echo '<tr> <td align=center><p2>Part No.</p2></td> <td align=center><p2><b>'.$partno.'</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Material</p2></td> <td align=center><p2>'.$mtrl.'</p2></td> </tr>';
							echo '<tr> <td align=center><p2>PO No.</p2></td> <td align=center><p2>'.$po.'</p2></td> </tr>';
							echo '<tr> <td align=center><p2>Qty</p2></td> <td align=center><p2><b>'.$stdpack.' PCS</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Del. Date</p2></td> <td align=center><p2>'.$deldate.'</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Prod. Date</p2></td> <td align=center><p2>'.$proddate.'</b></p2></td> </tr>';
							echo '<tr> <td align=center><p2>Shift</p2></td> <td align=center><p2>'.$shift.'</p2></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
							echo '<tr> <td align=center><p2>QC Check</p2></td> <td align=center><p2>'.$qcc.'</b></p2></td> </tr>';
						echo '</table>';
						echo '</td></tr>';
					} //end  else
				} // end if ($jml==2)

				if($jml>2){
					for ($i=1;$i<=$jml;$i++){
						$kurang = $i * $stdpack;
						if($i % 2!=0){
							if (($totalqty - $kurang)>$sisa){
								//echo("<tr><td>". $stdpack ."</td>");
								//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
									$srcdb14   			= 'P';
									$supp14	   			= str_pad($vsupp,6," ", STR_PAD_RIGHT);
									$partno14  			= str_pad($partno,15," ", STR_PAD_RIGHT);
									$date14				= date("YmdHis");
									$micro_date14		= microtime();
									$date_array_temp14	= explode(" ",$micro_date14);
									$date_array14		= substr($date_array_temp14[0], 2, -4);
									$orderCodeDate14	= $date14.$date_array14;
									$datetime14 		= $orderCodeDate14;
									$printcode14 		= str_pad($i,6,"0", STR_PAD_LEFT);
									$unique14	  		= $srcdb14 . $supp14 . $partno14 . $datetime14. $printcode14;
								

								//	generate label
								$a 			= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique14) . '.jpg';
								$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique14;
								$tempDir 	= '2 - Label Injection Part/';
								QRcode::png($barcode, $tempDir . $a, QR_ECLEVEL_L, 2);

								if(($i+1) % 6==0){
									echo '<tr style="page-break-after: always;"><td>';
								}elseif($i % 3==0)	{
									echo '<tr><td height=380>';
								}else{
									echo '<tr><td>';
								}
								echo '<table border=1 cellspacing=0 width=300>';
									echo '<tr> <td width=70 align=center rowspan=2><img style="max-height: 60px;" src="2 - Label Injection Part/' . $a . '" alt="gambar barcode" /></td> <td align=center><p2><b>'.$suppname.'</b></p2></td> <td width=70 align=center rowspan=2><p2><b>RoHS <br> OK</b></p2></td> </tr>';
									echo '<tr> <td align=center><p2><b>'.$ulcode.'</b></p2></td> </tr>';
									echo '<tr> <td align=center><p2>Customer</p2></td> <td align=center colspan=2><p2>PT. JVC ELECTRONICS INDONESIA</p2></td> </tr>';
									echo '<tr> <td align=center><p2>Part Name.</p2></td> <td align=center><p2>'.$partnm.'</p2></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
									echo '<tr> <td align=center><p2>Part No.</p2></td> <td align=center><p2><b>'.$partno.'</b></p2></td> </tr>';
									echo '<tr> <td align=center><p2>Material</p2></td> <td align=center><p2>'.$mtrl.'</p2></td> </tr>';
									echo '<tr> <td align=center><p2>PO No.</p2></td> <td align=center><p2>'.$po.'</p2></td> </tr>';
									echo '<tr> <td align=center><p2>Qty</p2></td> <td align=center><p2><b>'.$stdpack.' PCS</b></p2></td> </tr>';
									echo '<tr> <td align=center><p2>Del. Date</p2></td> <td align=center><p2>'.$deldate.'</b></p2></td> </tr>';
									echo '<tr> <td align=center><p2>Prod. Date</p2></td> <td align=center><p2>'.$proddate.'</b></p2></td> </tr>';
									echo '<tr> <td align=center><p2>Shift</p2></td> <td align=center><p2>'.$shift.'</p2></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
									echo '<tr> <td align=center><p2>QC Check</p2></td> <td align=center><p2>'.$qcc.'</b></p2></td> </tr>';
								echo '</table>';
								echo '</td>';
							} // end if (($totalqty - $kurang)>$sisa)
							else{
								if($sisa==0){
									//echo("<tr><td>". $stdpack ."</td>");
									//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
										$srcdb15   			= 'P';
										$supp15	   			= str_pad($vsupp,6," ", STR_PAD_RIGHT);
										$partno15  			= str_pad($partno,15," ", STR_PAD_RIGHT);
										$date15				= date("YmdHis");
										$micro_date15		= microtime();
										$date_array_temp15	= explode(" ",$micro_date15);
										$date_array15		= substr($date_array_temp15[0], 2, -4);
										$orderCodeDate15	= $date15.$date_array15;
										$datetime15 		= $orderCodeDate15;
										$printcode15 		= str_pad($i,6,"0", STR_PAD_LEFT);
										$unique15	  		= $srcdb15 . $supp15 . $partno15 . $datetime15. $printcode15;
									
									//	generate label
									$a 			= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique15) . '.jpg';
									$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique15;
									$tempDir 	= '2 - Label Injection Part/';
									QRcode::png($barcode, $tempDir . $a, QR_ECLEVEL_L, 2);

									if(($i+1) % 6==0){
										echo '<tr style="page-break-after: always;"><td>';
									}elseif($i % 3==0)	{
										echo '<tr><td height=380>';
									}else{
										echo '<tr><td>';
									}
									echo '<table border=1 cellspacing=0 width=300>';
										echo '<tr> <td width=70 align=center rowspan=2><img style="max-height: 60px;" src="2 - Label Injection Part/' . $a . '" alt="gambar barcode" /></td> <td align=center><p2><b>'.$suppname.'</b></p2></td> <td width=70 align=center rowspan=2><p2><b>RoHS <br> OK</b></p2></td> </tr>';
										echo '<tr> <td align=center><p2><b>'.$ulcode.'</b></p2></td> </tr>';
										echo '<tr> <td align=center><p2>Customer</p2></td> <td align=center colspan=2><p2>PT. JVC ELECTRONICS INDONESIA</p2></td> </tr>';
										echo '<tr> <td align=center><p2>Part Name.</p2></td> <td align=center><p2>'.$partnm.'</p2></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
										echo '<tr> <td align=center><p2>Part No.</p2></td> <td align=center><p2><b>'.$partno.'</b></p2></td> </tr>';
										echo '<tr> <td align=center><p2>Material</p2></td> <td align=center><p2>'.$mtrl.'</p2></td> </tr>';
										echo '<tr> <td align=center><p2>PO No.</p2></td> <td align=center><p2>'.$po.'</p2></td> </tr>';
										echo '<tr> <td align=center><p2>Qty</p2></td> <td align=center><p2><b>'.$stdpack.' PCS</b></p2></td> </tr>';
										echo '<tr> <td align=center><p2>Del. Date</p2></td> <td align=center><p2>'.$deldate.'</b></p2></td> </tr>';
										echo '<tr> <td align=center><p2>Prod. Date</p2></td> <td align=center><p2>'.$proddate.'</b></p2></td> </tr>';
										echo '<tr> <td align=center><p2>Shift</p2></td> <td align=center><p2>'.$shift.'</p2></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
										echo '<tr> <td align=center><p2>QC Check</p2></td> <td align=center><p2>'.$qcc.'</b></p2></td> </tr>';
									echo '</table>';
									echo '</td>';
								}else{
									//echo("<tr><td>". $sisa ."</td>");
									//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
										$srcdb16   			= 'P';
										$supp16	   			= str_pad($vsupp,6," ", STR_PAD_RIGHT);
										$partno16 			= str_pad($partno,15," ", STR_PAD_RIGHT);
										$date16				= date("YmdHis");
										$micro_date16		= microtime();
										$date_array_temp16	= explode(" ",$micro_date16);
										$date_array16		= substr($date_array_temp16[0], 2, -4);
										$orderCodeDate16	= $date16.$date_array16;
										$datetime16 		= $orderCodeDate16;
										$printcode16 		= str_pad($i,6,"0", STR_PAD_LEFT);
										$unique16	  		= $srcdb16 . $supp16 . $partno16 . $datetime16 . $printcode16;
									
									//	generate label
									$extfile	= $sisa;
									$namafile 	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($sisa) . '_' . trim($unique16) . '.jpg';
									$barcode 	= $partno . ' ' . $po . ' ' . $sisa3 . ' ' . $unique16;
									$tempDir 	= '2 - Label Injection Part/';
									QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

									if(($i+1) % 6==0){
										echo '<tr style="page-break-after: always;"><td>';
									}elseif($i % 3==0)	{
										echo '<tr><td height=380>';
									}else{
										echo '<tr><td>';
									}
									echo '<table border=1 cellspacing=0 width=300>';
										echo '<tr> <td width=70 align=center rowspan=2><img style="max-height: 60px;" src="2 - Label Injection Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p2><b>'.$suppname.'</b></p2></td> <td width=70 align=center rowspan=2><p2><b>RoHS <br> OK</b></p2></td> </tr>';
										echo '<tr> <td align=center><p2><b>'.$ulcode.'</b></p2></td> </tr>';
										echo '<tr> <td align=center><p2>Customer</p2></td> <td align=center colspan=2><p2>PT. JVC ELECTRONICS INDONESIA</p2></tr>';
										echo '<tr> <td align=center><p2>Part Name.</p2></td> <td align=center><p2>'.$partnm.'</p2></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
										echo '<tr> <td align=center><p2>Part No.</p2></td> <td align=center><p2><b>'.$partno.'</b></p2></td> </tr>';
										echo '<tr> <td align=center><p2>Material</p2></td> <td align=center><p2>'.$mtrl.'</p2></td> </tr>';
										echo '<tr> <td align=center><p2>PO No.</p2></td> <td align=center><p2>'.$po.'</p2></td> </tr>';
										echo '<tr> <td align=center><p2>Qty</p2></td> <td align=center><p2><b>'.$sisa.' PCS</b></p2></td> </tr>';
										echo '<tr> <td align=center><p2>Del. Date</p2></td> <td align=center><p2>'.$deldate.'</b></p2></td> </tr>';
										echo '<tr> <td align=center><p2>Prod. Date</p2></td> <td align=center><p2>'.$proddate.'</b></p2></td> </tr>';
										echo '<tr> <td align=center><p2>Shift</p2></td> <td align=center><p2>'.$shift.'</p2></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
										echo '<tr> <td align=center><p2>QC Check</p2></td> <td align=center><p2>'.$qcc.'</b></p2></td> </tr>';
									echo '</table>';
									echo '</td>';
								}
							} //end else
						} // end if($i % 2!=0)
						if ($i % 2==0){
							//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
								$srcdb_kanan2   		= 'P';
								$supp_kanan2	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
								$partno_kanan2 			= str_pad($partno,15," ", STR_PAD_RIGHT);
								$date_kanan2			= date("YmdHis");
								$micro_date_kanan2		= microtime();
								$date_array_temp_kanan2	= explode(" ",$micro_date_kanan2);
								$date_array_kanan2		= substr($date_array_temp_kanan2[0], 2, -4);
								$orderCodeDate_kanan2	= $date_kanan2.$date_array_kanan2;
								$datetime_kanan2 		= $orderCodeDate_kanan2;
								$printcode_kanan2 		= str_pad($i,6,"0", STR_PAD_LEFT);
								$unique_kanan2	  		= $srcdb_kanan2 . $supp_kanan2 . $partno_kanan2 . $datetime_kanan2 . $printcode_kanan2;

							  //	generate label
							  $b_kanan2	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique_kanan2) . '.jpg';
							  $barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique_kanan2;
							  $tempDir 	= '2 - Label Injection Part/';
							  QRcode::png($barcode, $tempDir . $b_kanan2, QR_ECLEVEL_L, 2);


              echo '<td align=right >';
							echo '<table border=1 cellspacing=0 width=300>';
								echo '<tr> <td width=70 align=center rowspan=2><img style="max-height: 60px;" src="2 - Label Injection Part/' . $b_kanan2 . '" alt="gambar barcode" /></td> <td align=center><p2><b>'.$suppname.'</b></p2></td> <td width=70 align=center rowspan=2><p2><b>RoHS <br> OK</b></p2></td> </tr>';
								echo '<tr> <td align=center><p2><b>'.$ulcode.'</b></p2></td> </tr>';
								echo '<tr> <td align=center><p2>Customer</p2></td> <td align=center colspan=2><p2>PT. JVC ELECTRONICS INDONESIA</p2></td> </tr>';
								echo '<tr> <td align=center><p2>Part Name.</p2></td> <td align=center><p2>'.$partnm.'</p2></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
								echo '<tr> <td align=center><p2>Part No.</p2></td> <td align=center><p2><b>'.$partno.'</b></p2></td> </tr>';
								echo '<tr> <td align=center><p2>Material</p2></td> <td align=center><p2>'.$mtrl.'</p2></td> </tr>';
								echo '<tr> <td align=center><p2>PO No.</p2></td> <td align=center><p2>'.$po.'</p2></td> </tr>';
								echo '<tr> <td align=center><p2>Qty</p2></td> <td align=center><p2><b>'.$stdpack.' PCS</b></p2></td> </tr>';
								echo '<tr> <td align=center><p2>Del. Date</p2></td> <td align=center><p2>'.$deldate.'</b></p2></td> </tr>';
								echo '<tr> <td align=center><p2>Prod. Date</p2></td> <td align=center><p2>'.$proddate.'</b></p2></td> </tr>';
								echo '<tr> <td align=center><p2>Shift</p2></td> <td align=center><p2>'.$shift.'</p2></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
								echo '<tr> <td align=center><p2>QC Check</p2></td> <td align=center><p2>'.$qcc.'</b></p2></td> </tr>';
							echo '</table>';
							echo '</td></tr>';
						} // if ($i % 2==0)
					} // end for ($i=1;$i<=$jml;$i++)
				} // end if($jml>2)
			echo '</table>';
		break;
		// End Format barcode kategori  --- >>>  Injection Part

		case '3':  // Format barcode kategori  --- >>>  Printing Part
			$mtrl 		= $_GET['mtrl'];
			$shift 		= $_GET['shift'];
			$qcc 		= $_GET['qcc'];

			$stdpack 	= $pack; // standard packing tiap palet
			$totalqty 	= $qty; // total production quantity
			$sisa 		= $totalqty % $stdpack; // sisa hasil bagi
			$jml 		=ceil($totalqty / $stdpack); //hasil bagi dibulatkan ke atas
			$kurang 	= 1;
			//$jml =floor($totalqty / $stdpack); //hasil bagi dibulatkan ke bawah
			//echo $jml ." dan " .$sisa;
      $sisa4 = $sisa;
      $pjgsisa4 = strlen($sisa);
      switch ($pjgsisa4)
      {
        case 1:
        $sisa4 .= '    ';
        break;
        case 2:
        $sisa4 .= '   ';
        break;
        case 3:
        $sisa4 .= '  ';
        break;
        case 4:
        $sisa4 .= ' ';
        break;
        default:
      }



			echo '<table border=0 cellspacing=0 width=650>';
				if ($jml==1){
          // echo'<br>1) '. $jml.'=1<br>';
					if ($stdpack == $totalqty){
						// echo'<br>2) stdpack=totalqty'.$stdpack.'='.$stdpack.'<br>';
									//echo("<tr><td>". $totalqty ."</td></tr>");
						//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
							$srcdb17	   		= 'P';
							$supp17 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
							$partno17   		= str_pad($partno,15," ", STR_PAD_RIGHT);
							$date17 			= date("YmdHis");
							$micro_date17		= microtime();
							$date_array_temp17	= explode(" ",$micro_date17);
							$date_array17		= substr($date_array_temp17[0], 2, -4);
							$orderCodeDate17	= $date17 . $date_array17;
							$datetime17 		= $orderCodeDate17;
							$printcode17 		= str_pad($jml,6,"0", STR_PAD_LEFT);
							$unique17   		= $srcdb17 . $supp17 . $partno17 . $datetime17 . $printcode17;

						//	generate label
						$namafile 	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique17) . '.jpg';
						$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique17;
						$tempDir 	= '3 - Label Printing Part/';
						QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

						echo '<tr><td>';
						echo '<table border=1 cellspacing=0 width=300>';
							echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="3 - Label Printing Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
							echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
							echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Material</p></td> <td align=center><p>'.$mtrl.'</p></td> </tr>';
							echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
							echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$stdpack.' PCS</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Shift</p></td> <td align=center><p>'.$shift.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
							echo '<tr> <td align=center><p>QC Check</p></td> <td align=center><p>'.$qcc.'</b></p></td> </tr>';
						echo '</table>';
						echo '</td></tr>';
					}
					else{
						// echo'<br>3) stdpack!=totalqty'.$stdpack.'!='.$stdpack.'<br>';

						//echo("<tr><td>". $sisa ."</td></tr>");
					    //	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
							$srcdb18	   		= 'P';
							$supp18 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
							$partno18   		= str_pad($partno,15," ", STR_PAD_RIGHT);
							$date18 			= date("YmdHis");
							$micro_date18		= microtime();
							$date_array_temp18	= explode(" ",$micro_date18);
							$date_array18		= substr($date_array_temp18[0], 2, -4);
							$orderCodeDate18	= $date18 . $date_array18;
							$datetime18 		= $orderCodeDate18;
							$printcode18 		= str_pad($jml,6,"0", STR_PAD_LEFT);
							$unique18   		= $srcdb18 . $supp18 . $partno18 . $datetime18 . $printcode18;

						//	generate label
						$extfile 	= $jml + 1;
						$namafile 	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($qtybal) . '_' . trim($unique18) . '.jpg';
						$barcode 	= $partno . ' ' . $po . ' ' . $qtybal2 . ' ' . $unique18;
						$tempDir 	= '3 - Label Printing Part/';
						QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

						echo '<tr><td>';
						echo '<table border=1 cellspacing=0 width=300>';
							echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="3 - Label Printing Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
							echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
							echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Material</p></td> <td align=center><p>'.$mtrl.'</p></td> </tr>';
							echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
							echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$qtybal.' PCS</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Shift</p></td> <td align=center><p>'.$shift.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
							echo '<tr> <td align=center><p>QC Check</p></td> <td align=center><p>'.$qcc.'</b></p></td> </tr>';
						echo '</table>';
						echo '</td></tr>';
					}
				} // end if ($jml==1)
				if ($jml==2){
					//echo("<tr><td>". $stdpack ."</td>");
					// echo'<br>4) jml=2 '.$jml.'=2<br>';

					//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
						$srcdb19	   		= 'P';
						$supp19 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
						$partno19   		= str_pad($partno,15," ", STR_PAD_RIGHT);
						$date19 			= date("YmdHis");
						$micro_date19		= microtime();
						$date_array_temp19	= explode(" ",$micro_date19);
						$date_array19		= substr($date_array_temp19[0], 2, -4);
						$orderCodeDate19	= $date19 . $date_array19;
						$datetime19 		= $orderCodeDate19;
						$printcode19		= str_pad("1",6,"0", STR_PAD_LEFT);
						$unique19   		= $srcdb19 . $supp19 . $partno19 . $datetime19 . $printcode19;

					//	generate label
					$namafile 	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique19) . '.jpg';
					$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique19;
					$tempDir 	= '3 - Label Printing Part/';
					QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

					echo '<tr><td>';
						echo '<table border=1 cellspacing=0 width=300>';
							echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="3 - Label Printing Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
							echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
							echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Material</p></td> <td align=center><p>'.$mtrl.'</p></td> </tr>';
							echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
							echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$stdpack.' PCS</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Shift</p></td> <td align=center><p>'.$shift.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
							echo '<tr> <td align=center><p>QC Check</p></td> <td align=center><p>'.$qcc.'</b></p></td> </tr>';
						echo '</table>';
					echo '</td>';
					if ($sisa!=0){
						// echo'<br>5) sisa!=0 '.$sisa.'=0<br>';

						//echo("<td>". $sisa ."</td></tr>");
						//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
							$srcdb20	   		= 'P';
							$supp20 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
							$partno20   		= str_pad($partno,15," ", STR_PAD_RIGHT);
							$date20 			= date("YmdHis");
							$micro_date20		= microtime();
							$date_array_temp20	= explode(" ",$micro_date20);
							$date_array20		= substr($date_array_temp20[0], 2, -4);
							$orderCodeDate20	= $date20 . $date_array20;
							$datetime20 		= $orderCodeDate20;
							$printcode20		= str_pad("2",6,"0", STR_PAD_LEFT);
							$unique20  		= $srcdb20 . $supp20 . $partno20 . $datetime20 . $printcode20;

						//	generate label
						$extfile 	= $sisa;
						$namafile	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($sisa) . '_' . trim($unique20) . '.jpg';
						$barcode 	= $partno . ' ' . $po . ' ' . $sisa4 . ' ' . $unique20;
						$tempDir 	= '3 - Label Printing Part/';
						QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

						echo '<td align=right>';
						echo '<table border=1 cellspacing=0 width=300>';
							echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="3 - Label Printing Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
							echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
							echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Material</p></td> <td align=center><p>'.$mtrl.'</p></td> </tr>';
							echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
							echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$sisa.' PCS</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Shift</p></td> <td align=center><p>'.$shift.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
							echo '<tr> <td align=center><p>QC Check</p></td> <td align=center><p>'.$qcc.'</b></p></td> </tr>';
						echo '</table>';
						echo '</td></tr>';
					} // end if ($sisa!=0)
					else{
						// echo'<br>6) sisa==0 '.$sisa.'==0<br>';
						//echo("<td>". $stdpack ."</td></tr>");
						//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
							$srcdb21	   		= 'P';
							$supp21 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
							$partno21   		= str_pad($partno,15," ", STR_PAD_RIGHT);
							$date21 			= date("YmdHis");
							$micro_date21		= microtime();
							$date_array_temp21	= explode(" ",$micro_date21);
							$date_array21		= substr($date_array_temp21[0], 2, -4);
							$orderCodeDate21	= $date21 . $date_array21;
							$datetime21 		= $orderCodeDate21;
							$printcode21		= str_pad("2",6,"0", STR_PAD_LEFT);
							$unique21  		= $srcdb21 . $supp21 . $partno21 . $datetime21 . $printcode21;
						
						//	generate label
						$namafile 	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique21) . '.jpg';
						$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique21;
						$tempDir 	= '3 - Label Printing Part/';
						QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

						echo '<td align=right>';
						echo '<table border=1 cellspacing=0 width=300>';
							echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="3 - Label Printing Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
							echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
							echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Material</p></td> <td align=center><p>'.$mtrl.'</p></td> </tr>';
							echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
							echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$stdpack.' PCS</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Shift</p></td> <td align=center><p>'.$shift.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
							echo '<tr> <td align=center><p>QC Check</p></td> <td align=center><p>'.$qcc.'</b></p></td> </tr>';
						echo '</table>';
						echo '</td></tr>';
					} //end  else
				} // end if ($jml==2)

				if($jml>2){
          // echo'<br>7) jml>2 '.$jml.'>2<br>';
          for ($i=1;$i<=$jml;$i++){
            // echo'<br>1.'.$i.'<br>';
			$kurang = $i * $stdpack;
			if($i % 2!=0){
              // echo'<br>8) i%2!=0 '.($i%2).'!=0 --- 2.'.$i.'<br>';
				if (($totalqty - $kurang)>$sisa){
					// echo'<br>9) (totalqty-kurang)>sisa '.($totalqty-$kurang).'>'.$sisa.' --- 3.'.$i.'<br>';
					//echo("<tr><td>". $stdpack ."</td>");
					
					//	generate label
					if(($i+1) % 6==0){
						//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
							$srcdb22_1	   		= 'P';
							$supp22_1 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
							$partno22_1   		= str_pad($partno,15," ", STR_PAD_RIGHT);
							$date22_1 			= date("YmdHis");
							$micro_date22_1		= microtime();
							$date_array_temp22_1= explode(" ",$micro_date22_1);
							$date_array22_1		= substr($date_array_temp22_1[0], 2, -4);
							$orderCodeDate22_1	= $date22_1 . $date_array22_1;
							$datetime22_1 		= $orderCodeDate22_1;
							$printcode22_1		= str_pad($i,6,"0", STR_PAD_LEFT);
							$unique22_1 		= $srcdb22_1 . $supp22_1 . $partno22_1 . $datetime22_1 . $printcode22_1;
						

							$b1 = trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique22_1) . '.jpg';
							$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique22_1;
							$tempDir 	= '3 - Label Printing Part/';
							QRcode::png($barcode, $tempDir . $b1, QR_ECLEVEL_L, 2);

						  // echo'<br>4.'.$i.'<br>';
								echo '<tr style="page-break-after: always;"><td>';

							  echo '<table border=1 cellspacing=0 width=300>';
							  echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="3 - Label Printing Part/' . $b1 . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';

					}elseif($i % 3==0)	{
						//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
							$srcdb22_2	   		= 'P';
							$supp22_2 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
							$partno22_2   		= str_pad($partno,15," ", STR_PAD_RIGHT);
							$date22_2 			= date("YmdHis");
							$micro_date22_2		= microtime();
							$date_array_temp22_2= explode(" ",$micro_date22_2);
							$date_array22_2		= substr($date_array_temp22_2[0], 2, -4);
							$orderCodeDate22_2	= $date22_2 . $date_array22_2;
							$datetime22_2 		= $orderCodeDate22_2;
							$printcode22_2		= str_pad($i,6,"0", STR_PAD_LEFT);
							$unique22_2 		= $srcdb22_2 . $supp22_2 . $partno22_2 . $datetime22_2 . $printcode22_2;
						
							  $c1 = trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique22_2) . '.jpg';
							  $barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique22_2;
							  $tempDir 	= '3 - Label Printing Part/';
							  QRcode::png($barcode, $tempDir . $c1, QR_ECLEVEL_L, 2);

							  // echo'<br>5.'.$i.'<br>';
								echo '<tr><td height=400>';

							  echo '<table border=1 cellspacing=0 width=300>';
							  echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="3 - Label Printing Part/' . $c1 . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';

					}else{
						//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
							$srcdb22_3	   		= 'P';
							$supp22_3 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
							$partno22_3   		= str_pad($partno,15," ", STR_PAD_RIGHT);
							$date22_3 			= date("YmdHis");
							$micro_date22_3		= microtime();
							$date_array_temp22_3= explode(" ",$micro_date22_3);
							$date_array22_3		= substr($date_array_temp22_3[0], 2, -4);
							$orderCodeDate22_3	= $date22_3 . $date_array22_3;
							$datetime22_3 		= $orderCodeDate22_3;
							$printcode22_3		= str_pad($i,6,"0", STR_PAD_LEFT);
							$unique22_3 		= $srcdb22_3 . $supp22_3 . $partno22_3 . $datetime22_3 . $printcode22_3;

						  $d1 = trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique22_3) . '.jpg';
						  $barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique22_3;
						  $tempDir 	= '3 - Label Printing Part/';
						  QRcode::png($barcode, $tempDir . $d1, QR_ECLEVEL_L, 2);

						  // echo'<br>6.'.$i.'<br>';
							echo '<tr><td>';

						  echo '<table border=1 cellspacing=0 width=300>';
						  echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="3 - Label Printing Part/' . $d1 . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
					}
						  echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
						  echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
						  echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
						  echo '<tr> <td align=center><p>Material</p></td> <td align=center><p>'.$mtrl.'</p></td> </tr>';
						  echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
						  echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$stdpack.' PCS</b></p></td> </tr>';
						  echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
						  echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
						  echo '<tr> <td align=center><p>Shift</p></td> <td align=center><p>'.$shift.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
						  echo '<tr> <td align=center><p>QC Check</p></td> <td align=center><p>'.$qcc.'</b></p></td> </tr>';
						  echo '</table>';
					echo '</td>';
				} // end if (($totalqty - $kurang)>$sisa)
				else{
                // echo'<br>10) (totalqty-kurang)<=sisa '.($totalqty-$kurang).'<='.$sisa.' --- 7.'.$i.'<br>';

					if($sisa==0){
					  // echo'<br>11) (sisa=0 '.$sisa.'=0 --- 8.'.$i.'<br>';
						//echo("<tr><td>". $stdpack ."</td>");
						
						if(($i+1) % 6==0){
							//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
								$srcdb23_1	   		= 'P';
								$supp23_1 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
								$partno23_1   		= str_pad($partno,15," ", STR_PAD_RIGHT);
								$date23_1 			= date("YmdHis");
								$micro_date23_1		= microtime();
								$date_array_temp23_1= explode(" ",$micro_date23_1);
								$date_array23_1		= substr($date_array_temp23_1[0], 2, -4);
								$orderCodeDate23_1	= $date23_1 . $date_array23_1;
								$datetime23_1		= $orderCodeDate23_1;
								$printcode23_1		= str_pad($i,6,"0", STR_PAD_LEFT);
								$unique23_1			= $srcdb23_1 . $supp23_1 . $partno23_1 . $datetime23_1 . $printcode23_1;

							//	generate label
								$b2 = trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique23_1) . '.jpg';
								$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique23_1;
								$tempDir 	= '3 - Label Printing Part/';
								QRcode::png($barcode, $tempDir . $b2, QR_ECLEVEL_L, 2);

							// echo'<br>5.'.$i.'<br>';
								echo '<tr style="page-break-after: always;"><td>';

								echo '<table border=1 cellspacing=0 width=300>';
									echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="3 - Label Printing Part/' . $b2 . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
						}elseif($i % 3==0)	{
							//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
								$srcdb23_2	   		= 'P';
								$supp23_2 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
								$partno23_2   		= str_pad($partno,15," ", STR_PAD_RIGHT);
								$date23_2 			= date("YmdHis");
								$micro_date23_2		= microtime();
								$date_array_temp23_2= explode(" ",$micro_date23_2);
								$date_array23_2		= substr($date_array_temp23_2[0], 2, -4);
								$orderCodeDate23_2	= $date23_2 . $date_array23_2;
								$datetime23_2		= $orderCodeDate23_2;
								$printcode23_2		= str_pad($i,6,"0", STR_PAD_LEFT);
								$unique23_2			= $srcdb23_2 . $supp23_2 . $partno23_2 . $datetime23_2 . $printcode23_2;

							//	generate label
							$c2 = trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique23_2) . '.jpg';
							$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique23_2;
							$tempDir 	= '3 - Label Printing Part/';
							QRcode::png($barcode, $tempDir . $c2, QR_ECLEVEL_L, 2);

							// echo'<br>6.'.$i.'<br>';
							echo '<tr><td height=400>';

							echo '<table border=1 cellspacing=0 width=300>';
							echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="3 - Label Printing Part/' . $c2 . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
						}else{
							//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
								$srcdb23_3	   		= 'P';
								$supp23_3 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
								$partno23_3   		= str_pad($partno,15," ", STR_PAD_RIGHT);
								$date23_3 			= date("YmdHis");
								$micro_date23_3		= microtime();
								$date_array_temp23_3= explode(" ",$micro_date23_3);
								$date_array23_3		= substr($date_array_temp23_3[0], 2, -4);
								$orderCodeDate23_3	= $date23_3 . $date_array23_3;
								$datetime23_3		= $orderCodeDate23_3;
								$printcode23_3		= str_pad($i,6,"0", STR_PAD_LEFT);
								$unique23_3			= $srcdb23_3 . $supp23_3 . $partno23_3 . $datetime23_3 . $printcode23_3;

						//	generate label
							$d2 = trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique23_3) . '.jpg';
							$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique23_3;
							$tempDir 	= '3 - Label Printing Part/';
							QRcode::png($barcode, $tempDir . $d2, QR_ECLEVEL_L, 2);

							echo '<tr><td>';

							echo '<table border=1 cellspacing=0 width=300>';
							echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="3 - Label Printing Part/' . $d2 . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
						}

							echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
							echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
							echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Material</p></td> <td align=center><p>'.$mtrl.'</p></td> </tr>';
							echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
							echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$stdpack.' PCS</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Shift</p></td> <td align=center><p>'.$shift.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
							echo '<tr> <td align=center><p>QC Check</p></td> <td align=center><p>'.$qcc.'</b></p></td> </tr>';
						echo '</table>';
						echo '</td>';
					}else{
					  // echo'<br>12) (sisa!=0 '.$sisa.'!=0 --- 9.'.$i.'<br>';

										//echo("<tr><td>". $sisa ."</td>");
						
						if(($i+1) % 6==0){
							//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
								$srcdb24_1	   		= 'P';
								$supp24_1 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
								$partno24_1   		= str_pad($partno,15," ", STR_PAD_RIGHT);
								$date24_1 			= date("YmdHis");
								$micro_date24_1		= microtime();
								$date_array_temp24_1= explode(" ",$micro_date24_1);
								$date_array24_1		= substr($date_array_temp24_1[0], 2, -4);
								$orderCodeDate24_1	= $date24_1 . $date_array24_1;
								$datetime24_1		= $orderCodeDate24_1;
								$printcode24_1		= str_pad($i,6,"0", STR_PAD_LEFT);
								$unique24_1			= $srcdb24_1 . $supp24_1 . $partno24_1 . $datetime24_1 . $printcode24_1;

							//	generate label
								$extfile 	= $sisa;
								$namafile1 = trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($sisa) . '_' . trim($unique24_1) . '.jpg';
								$barcode 	= $partno . ' ' . $po . ' ' . $sisa4 . ' ' . $unique24_1;
								$tempDir = '3 - Label Printing Part/';
								QRcode::png($barcode, $tempDir . $namafile1, QR_ECLEVEL_L, 2);

							// echo'<br>10.'.$i.'<br>';
							echo '<tr style="page-break-after: always;"><td>';

							echo '<table border=1 cellspacing=0 width=300>';
								echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="3 - Label Printing Part/' . $namafile1 . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
						
						}elseif($i % 3==0)	{
						
							//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
								$srcdb24_2	   		= 'P';
								$supp24_2 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
								$partno24_2   		= str_pad($partno,15," ", STR_PAD_RIGHT);
								$date24_2 			= date("YmdHis");
								$micro_date24_2		= microtime();
								$date_array_temp24_2= explode(" ",$micro_date24_2);
								$date_array24_2		= substr($date_array_temp24_2[0], 2, -4);
								$orderCodeDate24_2	= $date24_3 . $date_array24_2;
								$datetime24_2		= $orderCodeDate24_2;
								$printcode24_2		= str_pad($i,6,"0", STR_PAD_LEFT);
								$unique24_2			= $srcdb24_2 . $supp24_2 . $partno24_2 . $datetime24_2 . $printcode24_2;

							//	generate label
								$extfile 	= $sisa;
								$namafile2 = trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($sisa) . '_' . trim($unique24_2) . '.jpg';
								$barcode 	= $partno . ' ' . $po . ' ' . $sisa4 . ' ' . $unique24_2;
								$tempDir = '3 - Label Printing Part/';
								QRcode::png($barcode, $tempDir . $namafile2, QR_ECLEVEL_L, 2);


							// echo'<br>11.'.$i.'<br>';
							echo '<tr><td height=400>';

							echo '<table border=1 cellspacing=0 width=300>';
							echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="3 - Label Printing Part/' . $namafile2 . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
						
						}else{
							
							//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
								$srcdb24_3	   		= 'P';
								$supp24_3 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
								$partno24_3   		= str_pad($partno,15," ", STR_PAD_RIGHT);
								$date24_3 			= date("YmdHis");
								$micro_date24_3		= microtime();
								$date_array_temp24_3= explode(" ",$micro_date24_3);
								$date_array24_3		= substr($date_array_temp24_3[0], 2, -4);
								$orderCodeDate24_3	= $date24_3 . $date_array24_3;
								$datetime24_3		= $orderCodeDate24_3;
								$printcode24_3		= str_pad($i,6,"0", STR_PAD_LEFT);
								$unique24_3			= $srcdb24_3 . $supp24_3 . $partno24_3 . $datetime24_3 . $printcode24_3;

						
							//	generate label
								$extfile 	= $sisa;
								$namafile3 = trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($sisa) . '_' . trim($unique24_3) . '.jpg';
								$barcode 	= $partno . ' ' . $po . ' ' . $sisa4 . ' ' . $unique24_3;
								$tempDir = '3 - Label Printing Part/';
								QRcode::png($barcode, $tempDir . $namafile3, QR_ECLEVEL_L, 2);


							echo '<tr><td>';
								echo '<table border=1 cellspacing=0 width=300>';
									echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="3 - Label Printing Part/' . $namafile3 . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
						}

									echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
									echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
									echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
									echo '<tr> <td align=center><p>Material</p></td> <td align=center><p>'.$mtrl.'</p></td> </tr>';
									echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
									echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$sisa.' PCS</b></p></td> </tr>';
									echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
									echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
									echo '<tr> <td align=center><p>Shift</p></td> <td align=center><p>'.$shift.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
									echo '<tr> <td align=center><p>QC Check</p></td> <td align=center><p>'.$qcc.'</b></p></td> </tr>';
								echo '</table>';
							echo '</td>';
					}
				} 	//end else
			} 	// end if($i % 2!=0)
			
		if ($i % 2==0){
			// echo'<br>13) i%2==0 '.($i%2).'==0 --- 12.'.$i.'<br>';

			//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
				$srcdb_kanan   		= 'P';
				$supp_kanan 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
				$partno_kanan   		= str_pad($partno,15," ", STR_PAD_RIGHT);
				$date_kanan 			= date("YmdHis");
				$micro_date_kanan		= microtime();
				$date_array_temp_kanan	= explode(" ",$micro_date_kanan);
				$date_array_kanan		= substr($date_array_temp_kanan[0], 2, -4);
				$orderCodeDate_kanan	= $date_kanan . $date_array_kanan;
				$datetime_kanan			= $orderCodeDate_kanan;
				$printcode_kanan		= str_pad($i,6,"0", STR_PAD_LEFT);
				$unique_kanan			= $srcdb_kanan . $supp_kanan . $partno_kanan . $datetime_kanan . $printcode_kanan;

				$a_kanan = trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique_kanan) . '.jpg';
				$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique_kanan;
				$tempDir 	= '3 - Label Printing Part/';
				QRcode::png($barcode, $tempDir . $a_kanan, QR_ECLEVEL_L, 2);


							echo '<td align=right >';
							echo '<table border=1 cellspacing=0 width=300>';
								echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="3 - Label Printing Part/' . $a_kanan . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
								echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
								echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Material</p></td> <td align=center><p>'.$mtrl.'</p></td> </tr>';
								echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
								echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$stdpack.' PCS</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Shift</p></td> <td align=center><p>'.$shift.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
								echo '<tr> <td align=center><p>QC Check</p></td> <td align=center><p>'.$qcc.'</b></p></td> </tr>';
							echo '</table>';
							echo '</td></tr>';
						} // if ($i % 2==0)
					} // end for ($i=1;$i<=$jml;$i++)
				} // end if($jml>2)
			echo '</table>';
		break;
		// End Format barcode kategori  --- >>>  Printing Part

		case '4':  // Format barcode kategori  --- >>>  Wire Part
			// for kategori = 4 / WIRE
			$invno    	= $_GET['invno'];
			$et    		= $_GET['et'];
			$bn    		= $_GET['bn'];

			$stdpack 	= $pack; // standard packing tiap palet
			$totalqty 	= $qty; // total production quantity
			$sisa 		= $totalqty % $stdpack; // sisa hasil bagi
			$jml 		=ceil($totalqty / $stdpack); //hasil bagi dibulatkan ke atas
			$kurang 	= 1;
			//$jml =floor($totalqty / $stdpack); //hasil bagi dibulatkan ke bawah
			//echo $jml ." dan " .$sisa;
			$sisa5 = $sisa;
			$pjgsisa5 = strlen($sisa);
			switch ($pjgsisa5)
			{
				case 1:
				$sisa5 .= '    ';
				break;
				case 2:
				$sisa5 .= '   ';
				break;
				case 3:
				$sisa5 .= '  ';
				break;
				case 4:
				$sisa5 .= ' ';
				break;
				default:
			}

			echo '<table border=0 cellspacing=0 width=650>';
				if ($jml==1){
					if ($stdpack == $totalqty){
						//echo("<tr><td>". $totalqty ."</td></tr>");
						//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
							$srcdb25	   		= 'P';
							$supp25 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
							$partno25   		= str_pad($partno,15," ", STR_PAD_RIGHT);
							$date25 			= date("YmdHis");
							$micro_date25		= microtime();
							$date_array_temp25  = explode(" ",$micro_date25);
							$date_array25		= substr($date_array_temp25[0], 2, -4);
							$orderCodeDate25	= $date25 . $date_array25;
							$datetime25			= $orderCodeDate25;
							$printcode25		= str_pad("1",6,"0", STR_PAD_LEFT);
							$unique25			= $srcdb25 . $supp25 . $partno25 . $datetime25 . $printcode25;


						//	generate label
						$namafile 	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique25) . '.jpg';
						$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique25;
						$tempDir 	= '4 - Label Wire Part/';
						QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

						echo '<tr><td>';
						echo '<table border=1 cellspacing=0 width=300>';
							echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="4 - Label Wire Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
							echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
							echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Invoice No.</p></td> <td align=center><p>'.$invno.'</p></td> </tr>';
							echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
							echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$stdpack.' PCS</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Elec Tes</p></td> <td align=center><p>'.$et.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
							echo '<tr> <td align=center><p>Box No.</p></td> <td align=center><p>1 / '.$jml.'</b></p></td> </tr>';
						echo '</table>';
						echo '</td></tr>';
					}
					else{
						//echo("<tr><td>". $sisa ."</td></tr>");
						//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
							$srcdb26	   		= 'P';
							$supp26 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
							$partno26   		= str_pad($partno,15," ", STR_PAD_RIGHT);
							$date26 			= date("YmdHis");
							$micro_date26		= microtime();
							$date_array_temp26  = explode(" ",$micro_date26);
							$date_array26		= substr($date_array_temp26[0], 2, -4);
							$orderCodeDate26	= $date26 . $date_array26;
							$datetime26			= $orderCodeDate26;
							$printcode26		= str_pad("1",6,"0", STR_PAD_LEFT);
							$unique26			= $srcdb26 . $supp26 . $partno26 . $datetime26 . $printcode26;

						//	generate label
						$extfile 	= $jml + 1;
						$namafile 	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($qtybal) . '_' . trim($unique26) . '.jpg';
						$barcode 	= $partno . ' ' . $po . ' ' . $qtybal2 . ' ' . $unique26;
						$tempDir 	= '4 - Label Wire Part/';
						QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

						echo '<tr><td>';
						echo '<table border=1 cellspacing=0 width=300>';
							echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="4 - Label Wire Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
							echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
							echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Invoice No.</p></td> <td align=center><p>'.$invno.'</p></td> </tr>';
							echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
							echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$qtybal.' PCS</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Elect Test</p></td> <td align=center><p>'.$et.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
							echo '<tr> <td align=center><p>Box No.</p></td> <td align=center><p>1 / '.$jml.'</b></p></td> </tr>';
						echo '</table>';
						echo '</td></tr>';
					}
				} // end if ($jml==1)
				if ($jml==2){
					//echo("<tr><td>". $stdpack ."</td>");
					//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
						$srcdb27	   		= 'P';
						$supp27 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
						$partno27   		= str_pad($partno,15," ", STR_PAD_RIGHT);
						$date27 			= date("YmdHis");
						$micro_date27		= microtime();
						$date_array_temp27  = explode(" ",$micro_date27);
						$date_array27		= substr($date_array_temp27[0], 2, -4);
						$orderCodeDate27	= $date27 . $date_array27;
						$datetime27			= $orderCodeDate27;
						$printcode27		= str_pad("1",6,"0", STR_PAD_LEFT);
						$unique27			= $srcdb27 . $supp27 . $partno27 . $datetime27 . $printcode27;

					//	generate label
						$namafile 	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique27) . '.jpg';
						$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique27;
						$tempDir 	= '4 - Label Wire Part/';
						QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

					echo '<tr><td>';
						echo '<table border=1 cellspacing=0 width=300>';
							echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="4 - Label Wire Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
							echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
							echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Invoice No.</p></td> <td align=center><p>'.$invno.'</p></td> </tr>';
							echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
							echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$stdpack.' PCS</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Elec Test</p></td> <td align=center><p>'.$et.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
							echo '<tr> <td align=center><p>Box No.</p></td> <td align=center><p>1 / '.$jml.'</b></p></td> </tr>';
						echo '</table>';
					echo '</td>';
					if ($sisa!=0){
						//echo("<td>". $sisa ."</td></tr>");
						//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
							$srcdb28	   		= 'P';
							$supp28 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
							$partno28   		= str_pad($partno,15," ", STR_PAD_RIGHT);
							$date28 			= date("YmdHis");
							$micro_date28		= microtime();
							$date_array_temp28  = explode(" ",$micro_date28);
							$date_array28		= substr($date_array_temp28[0], 2, -4);
							$orderCodeDate28	= $date28 . $date_array28;
							$datetime28			= $orderCodeDate28;
							$printcode28		= str_pad("2",6,"0", STR_PAD_LEFT);
							$unique28			= $srcdb28 . $supp28 . $partno28 . $datetime28 . $printcode28;

						//	generate label
						$extfile 	= $sisa;
						$namafile	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($sisa) . '_' . trim($unique28) . '.jpg';
						$barcode 	= $partno . ' ' . $po . ' ' . $sisa5 . ' ' . $unique28;
						$tempDir 	= '4 - Label Wire Part/';
						QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

						echo '<td align=right>';
						echo '<table border=1 cellspacing=0 width=300>';
							echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="4 - Label Wire Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
							echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
							echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Invoice No.</p></td> <td align=center><p>'.$invno.'</p></td> </tr>';
							echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
							echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$sisa.' PCS</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Elec Test</p></td> <td align=center><p>'.$et.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
							echo '<tr> <td align=center><p>Box No.</p></td> <td align=center><p>2 / '.$jml.'</b></p></td> </tr>';
						echo '</table>';
						echo '</td></tr>';
					} // end if ($sisa!=0)
					else{
						//echo("<td>". $stdpack ."</td></tr>");
						//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
							$srcdb29	   		= 'P';
							$supp29 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
							$partno29   		= str_pad($partno,15," ", STR_PAD_RIGHT);
							$date29 			= date("YmdHis");
							$micro_date29		= microtime();
							$date_array_temp29  = explode(" ",$micro_date29);
							$date_array29		= substr($date_array_temp29[0], 2, -4);
							$orderCodeDate29	= $date29 . $date_array29;
							$datetime29			= $orderCodeDate29;
							$printcode29		= str_pad("2",6,"0", STR_PAD_LEFT);
							$unique29			= $srcdb29 . $supp29 . $partno29 . $datetime29 . $printcode29;


						//	generate label
						$namafile 	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique29) . '.jpg';
						$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique29;
						$tempDir 	= '4 - Label Wire Part/';
						QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

						echo '<td align=right>';
						echo '<table border=1 cellspacing=0 width=300>';
							echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="4 - Label Wire Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
							echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
							echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Invoice No.</p></td> <td align=center><p>'.$invno.'</p></td> </tr>';
							echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
							echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$stdpack.' PCS</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
							echo '<tr> <td align=center><p>Elec Test</p></td> <td align=center><p>'.$et.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
							echo '<tr> <td align=center><p>Box No.</p></td> <td align=center><p>2 / '.$jml.'</b></p></td> </tr>';
						echo '</table>';
						echo '</td></tr>';
					} //end  else
				} // end if ($jml==2)

				if($jml>2){
					for ($i=1;$i<=$jml;$i++){
						$kurang = $i * $stdpack;
						if($i % 2!=0){
							if (($totalqty - $kurang)>$sisa){
								//echo("<tr><td>". $stdpack ."</td>");
								//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
									$srcdb30	   		= 'P';
									$supp30 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
									$partno30   		= str_pad($partno,15," ", STR_PAD_RIGHT);
									$date30 			= date("YmdHis");
									$micro_date30		= microtime();
									$date_array_temp30  = explode(" ",$micro_date30);
									$date_array30		= substr($date_array_temp30[0], 2, -4);
									$orderCodeDate30	= $date30 . $date_array30;
									$datetime30			= $orderCodeDate30;
									$printcode30		= str_pad($i,6,"0", STR_PAD_LEFT);
									$unique30			= $srcdb30 . $supp30 . $partno30 . $datetime30 . $printcode30;


								//	generate label
								$a 			= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique30) . '.jpg';
								$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique30;
								$tempDir 	= '4 - Label Wire Part/';
								QRcode::png($barcode, $tempDir . $a, QR_ECLEVEL_L, 2);

								if(($i+1) % 6==0){
									echo '<tr style="page-break-after: always;"><td>';
								}elseif($i % 3==0)	{
									echo '<tr><td height=400>';
								}else{
									echo '<tr><td>';
								}
								echo '<table border=1 cellspacing=0 width=300>';
									echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="4 - Label Wire Part/' . $a . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
									echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
									echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
									echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
									echo '<tr> <td align=center><p>Invoice No.</p></td> <td align=center><p>'.$invno.'</p></td> </tr>';
									echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
									echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$stdpack.' PCS</b></p></td> </tr>';
									echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
									echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
									echo '<tr> <td align=center><p>Elec Test</p></td> <td align=center><p>'.$et.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
									echo '<tr> <td align=center><p>Box No.</p></td> <td align=center><p>'.$i.' / '.$jml.'</b></p></td> </tr>';
								echo '</table>';
								echo '</td>';
							} // end if (($totalqty - $kurang)>$sisa)
							else{
								if($sisa==0){
									//echo("<tr><td>". $stdpack ."</td>");
									//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
										$srcdb31	   		= 'P';
										$supp31 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
										$partno31   		= str_pad($partno,15," ", STR_PAD_RIGHT);
										$date31 			= date("YmdHis");
										$micro_date31		= microtime();
										$date_array_temp31  = explode(" ",$micro_date31);
										$date_array31		= substr($date_array_temp31[0], 2, -4);
										$orderCodeDate31	= $date31 . $date_array31;
										$datetime31			= $orderCodeDate31;
										$printcode31		= str_pad($i,6,"0", STR_PAD_LEFT);
										$unique31			= $srcdb31 . $supp31 . $partno31 . $datetime31 . $printcode31;


									//	generate label
									$a 			= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique31) . '.jpg';
									$barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique31;
									$tempDir 	= '4 - Label Wire Part/';
									QRcode::png($barcode, $tempDir . $a, QR_ECLEVEL_L, 2);

									if(($i+1) % 6==0){
										echo '<tr style="page-break-after: always;"><td>';
									}elseif($i % 3==0)	{
										echo '<tr><td height=400>';
									}else{
										echo '<tr><td>';
									}
									echo '<table border=1 cellspacing=0 width=300>';
										echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="4 - Label Wire Part/' . $a . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
										echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
										echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
										echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
										echo '<tr> <td align=center><p>Invoice No.</p></td> <td align=center><p>'.$invno.'</p></td> </tr>';
										echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
										echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$stdpack.' PCS</b></p></td> </tr>';
										echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
										echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
										echo '<tr> <td align=center><p>Elec Test</p></td> <td align=center><p>'.$et.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
										echo '<tr> <td align=center><p>Box No.</p></td> <td align=center><p>'.$i.' / '.$jml.'</b></p></td> </tr>';
									echo '</table>';
									echo '</td>';
								}else{
									//echo("<tr><td>". $sisa ."</td>");
									//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
										$srcdb32	   		= 'P';
										$supp32 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
										$partno32   		= str_pad($partno,15," ", STR_PAD_RIGHT);
										$date32 			= date("YmdHis");
										$micro_date32		= microtime();
										$date_array_temp32  = explode(" ",$micro_date32);
										$date_array32		= substr($date_array_temp32[0], 2, -4);
										$orderCodeDate32	= $date32 . $date_array32;
										$datetime32			= $orderCodeDate32;
										$printcode32		= str_pad($i,6,"0", STR_PAD_LEFT);
										$unique32			= $srcdb32 . $supp32 . $partno32 . $datetime32 . $printcode32;


									//	generate label
									$extfile 	= $sisa;
									$namafile 	= trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($sisa) . '_' . trim($unique32) . '.jpg';
									$barcode 	= $partno . ' ' . $po . ' ' . $sisa5 . ' ' . $unique32;
									$tempDir 	= '4 - Label Wire Part/';
									QRcode::png($barcode, $tempDir . $namafile, QR_ECLEVEL_L, 2);

									if(($i+1) % 6==0){
										echo '<tr style="page-break-after: always;"><td>';
									}elseif($i % 3==0)	{
										echo '<tr><td height=400>';
									}else{
										echo '<tr><td>';
									}
									echo '<table border=1 cellspacing=0 width=300>';
										echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="4 - Label Wire Part/' . $namafile . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
										echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
										echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
										echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
										echo '<tr> <td align=center><p>Invoice No.</p></td> <td align=center><p>'.$invno.'</p></td> </tr>';
										echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
										echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$sisa.' PCS</b></p></td> </tr>';
										echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
										echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
										echo '<tr> <td align=center><p>Elec Test</p></td> <td align=center><p>'.$et.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
										echo '<tr> <td align=center><p>Box No.</p></td> <td align=center><p>'.$i.' / '.$jml.'</b></p></td> </tr>';
									echo '</table>';
									echo '</td>';
								}
							} //end else
						} // end if($i % 2!=0)
						if ($i % 2==0){
							//	soucrdb ( suppQR : P | ediweb : B | 48JEIN : I ) 
								$srcdb_kanan4	   		= 'P';
								$supp_kanan4 	   		= str_pad($vsupp,6," ", STR_PAD_RIGHT);
								$partno_kanan4   		= str_pad($partno,15," ", STR_PAD_RIGHT);
								$date_kanan4 			= date("YmdHis");
								$micro_date_kanan4		= microtime();
								$date_array_temp_kanan4  = explode(" ",$micro_date_kanan4);
								$date_array_kanan4		= substr($date_array_temp_kanan4[0], 2, -4);
								$orderCodeDate_kanan4	= $date_kanan4 . $date_array_kanan4;
								$datetime_kanan4			= $orderCodeDate_kanan4;
								$printcode_kanan4		= str_pad($i,6,"0", STR_PAD_LEFT);
								$unique_kanan4			= $srcdb_kanan4 . $supp_kanan4 . $partno_kanan4 . $datetime_kanan4 . $printcode_kanan4;

							  //	generate label
							  $b_kanan4 = trim($vsupp).'_'.trim($partno).'_'.trim($po).'_'.trim($stdpack) . '_' . trim($unique_kanan4) . '.jpg';
							  $barcode 	= $partno . ' ' . $po . ' ' . $stdpack2 . ' ' . $unique_kanan4;
							  $tempDir 	= '4 - Label Wire Part/';
							  QRcode::png($barcode, $tempDir . $b_kanan4, QR_ECLEVEL_L, 2);


							echo '<td align=right >';
							echo '<table border=1 cellspacing=0 width=300>';
								echo '<tr> <td width=70 align=center><img style="max-height: 60px;" src="4 - Label Wire Part/' . $b_kanan4 . '" alt="gambar barcode" /></td> <td align=center><p><b>'.$suppname.'</b></p></td> <td width=70 align=center><p><b>RoHS <br> OK</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Customer</p></td> <td align=center colspan=2><p>PT. JVC ELECTRONICS INDONESIA</p></td> </tr>';
								echo '<tr> <td align=center><p>Part Name.</p></td> <td align=center><p>'.$partnm.'</p></td> <td rowspan=7 align=center valign=bottom><h2>'.$stsinsp.'</h2></td> </tr>';
								echo '<tr> <td align=center><p>Part No.</p></td> <td align=center><p><b>'.$partno.'</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Invoice No.</p></td> <td align=center><p>'.$invno.'</p></td> </tr>';
								echo '<tr> <td align=center><p>PO No.</p></td> <td align=center><p>'.$po.'</p></td> </tr>';
								echo '<tr> <td align=center><p>Qty</p></td> <td align=center><p><b>'.$stdpack.' PCS</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Del. Date</p></td> <td align=center><p>'.$deldate.'</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Prod. Date</p></td> <td align=center><p>'.$proddate.'</b></p></td> </tr>';
								echo '<tr> <td align=center><p>Elec Test</p></td> <td align=center><p>'.$et.'</p></td> <td rowspan=2 align=center valign=top><b>Location</b><br><font size=2>'.$lokasi.'</font></td> </tr>';
								echo '<tr> <td align=center><p>Box No.</p></td> <td align=center><p>'.$i.' / '.$jml.'</b></p></td> </tr>';
							echo '</table>';
							echo '</td></tr>';
						} // if ($i % 2==0)
					} // end for ($i=1;$i<=$jml;$i++)
				} // end if($jml>2)
			echo '</table>';
		break;
		// End Format barcode kategori  --- >>>  Wire Part

		default:  // Format barcode Other Supplier
			echo '<table border=0 cellpadding=10 width=100%>';
				echo '<tr><td valign=top>';
					echo '<font size=7 color=red>Preview label gagal dilakukan !*</font>';
					echo '<br><hr>';
					echo '<font size=5 color=red>*Perusahaan anda tidak tergolong pada kategori manapun..</font>';
					echo '<br><br>';
				echo '</td></tr>';
			echo '</table>';
	}
	//	end switch ($kategori)

} // end if(isset($_GET['partno']))
?>
</body>
</html>
