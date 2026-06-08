<?php
header("Content-Type: application/json");
// ==========================
// Koneksi database
// ==========================
$env = parse_ini_file(__DIR__ . '/config/.env');

try {
    $dsn = "sqlsrv:Server={$env['DB_HOST']};Database={$env['DB_NAME']};Encrypt=no;TrustServerCertificate=yes";
    $pdo = new PDO($dsn, $env['DB_USER'], $env['DB_PASSWORD'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Koneksi DB gagal: ".$e->getMessage()]);
    exit();
}

// Ambil parameter supplier
//$vsupp = $_GET['supp'] ?? null;
// ASAHI = J1 = 10693
// $vsupp = '10698'; // hardcode untuk testing 
// $vsupp = '10693'; ASAHI BEST BASE INDONESIA PT        = 'J1'  

// Validasi ringan

// $myusername = $_SESSION['dinew_smyid'];
// $vsupp 		= $_POST['supp'];
// $vtgl  		= ($_POST['didate'] <= 9 ? '0'.$_POST['didate'] : $_POST['didate']) ;
// $vbln  		= ($_POST['dimonth'] <= 9 ? '0'.$_POST['dimonth'] : $_POST['dimonth']) ;
// $vthn  		= $_POST['diyear'];
// $vsq   		= $_POST['disq'];
// $vthn 		= substr($vthn,2,2); 
// $vtglbln	= $vtgl . "/" . $vbln . "/" . $vthn;
// $vtglj2 	= $vtgl . "/" . $vbln;

//$vsupp = '10698' ;   // CAHAYA PRIMA SENTOSA PT, declaration = 'J2'
//$vtglj2 = '17/04'; // hardcode untuk testing
$vsupp = $_POST['supp'] ?? null;
$ptgl  = $_POST['tanggal'] ?? null;   // tanggal dari posting
$vtglj2 = null;
if ($ptgl) 
{
  $date = date_create($ptgl);
  if ($date) 
  {
    $vtglj2 = date_format($date, 'd/m');
  }
}

if (!$vsupp) {
    echo json_encode(["status" => "error", "message" => "Parameter supp kosong"]);
    exit();
}

// Query PDO
$sql = "SELECT SuppCode, SuppName, Declaration 
        FROM supplier 
        WHERE SuppCode = :supp";

// Prepare
$stmt = $pdo->prepare($sql);

// Bind parameter
$stmt->bindParam(':supp', $vsupp, PDO::PARAM_STR);

// Execute
$stmt->execute();

// Ambil data
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Supplier tidak ditemukan"]);
    exit();
}

// Extract
$suppcode = $data['SuppCode'];
$suppname = $data['SuppName'];
$suppdec  = $data['Declaration'];
// echo json_encode([
//     "status" => "success",
//     "data" => [
//         "SuppCode" => $suppcode,
//         "SuppName" => $suppname,
//         "Declaration" => $suppdec
//     ]
// ]); 

//$vsq   		= $_POST['disq'];
// depannya v = nilai dari posting, jadi vbln = nilai bulan, vthn = nilai tahun, vtgl = nilai tanggal, vsq = nilai sequence
$vsq   		= '1'; // hardcode untuk testing
$vthn 		= '26'; // hardcode untuk testing
$vbln 		= '04'; // hardcode untuk testing   
$vtgl 		= '17'; // hardcode untuk testing
$suppdec = 'J2';

$totalInsert = 0;
// note $tglserver berulang : dikeluarin dari if($suppdec == 'J1') 

$stmt = $pdo->query("SELECT GETDATE()");
$tglserver = $stmt->fetchColumn();
$ythn = substr($tglserver,2,2);
$ybln = substr($tglserver,5,2);
$ytgl = substr($tglserver,8,2);
$tglcek = $ybln . "/" . $ytgl . "/" . $ythn;    
$vblntglthn = $vbln . "/" . $vtgl . "/" . $vthn;  
$obsupp 	= trim($vsupp);

if ($suppdec == 'J1')
{
  //cek diget
  $sdhget = 0;
  $proses = 1;
  $sqprev = $vsq - 1;  // nilai sequence sebelumnya
  //	jika hasil nol tdk di proses cek sequence
  if ($sqprev > 0 )
  {
    $cekget 	= $obsupp . $vthn . $vbln . $vtgl . $sqprev . "%";
    $sqlcekget = "SELECT COUNT(*) AS tget 
              FROM di 
              WHERE supptglpo LIKE :cekget";
    $stmt = $pdo->prepare($sqlcekget);
    $stmt->execute(['cekget' => $cekget]);
    $sdhget = $stmt->fetchColumn();
	  if ($sdhget == 0)
	  {
	    $proses = 0; 
    }
	  else
	  {
	    $proses = 1;
	  }	
  } // end of $sqprev > 0		
  //	jika proses 1
  
  if($proses == 1 )
  {
    //---------------- hapus tabel diget sebelum insert -------------------
    $sqldeldiget = "DELETE FROM diget 
                WHERE supp = :supp 
                AND tgl = :tgl 
                AND sq = :sq";
    $stmt = $pdo->prepare($sqldeldiget);
    $stmt->execute([
      'supp' => $vsupp,
      'tgl'  => $vblntglthn,
      'sq'   => $vsq
    ]);
	  //----------------- end of hapus tabel diget ---------------------------			
	  //------------- hapus di status 0 ----------------------------
    $vdel = trim($vsupp) . $vthn . $vbln . $vtgl . $vsq . '%';
    $sqldelnob = "DELETE FROM di 
              WHERE supptglpo LIKE :vdel
              AND status = '0'";
    $stmt = $pdo->prepare($sqldelnob);
    $stmt->execute([
    'vdel' => $vdel
    ]);
    //---------- end of hapus di status 0 ------------------------	
    //--------------------- hapus digetsum -------------------------
    $sqldelgetsum = "DELETE FROM digetsum WHERE supp = :supp";
    $stmt = $pdo->prepare($sqldelgetsum);
    $stmt->execute([
    'supp' => $obsupp
    ]);
    // --------------- end of hapus digetsum ----------------------	
    //------------------- ins data ke table getsum -----------------
    $sqlinsgetsum = "INSERT INTO digetsum (supp, partno, qty)
                 SELECT MAX(supp) AS supp,
                        partno,
                        SUM(qty) AS qty
                 FROM diget
                 WHERE supp = :supp
                 AND tgl >= :tgl
                 GROUP BY partno
                 ORDER BY partno";
    $stmt = $pdo->prepare($sqlinsgetsum);
    $stmt->execute([
      'supp' => $obsupp,
      'tgl'  => $tglcek
    ]);
    // --------------- end of ins data ke table getsum ----------------------
    //----------------------- hapus diupload ---------------------
    $sqldiupdel = "DELETE FROM diupload WHERE supp = :supp";
    $stmt = $pdo->prepare($sqldiupdel);
    $stmt->execute([
      'supp' => $obsupp
    ]);
    // --------------- end of ins data ke table getsum ----------------------
	  // ambil summary per partno dari data yg sudah di upload
	  // dan kemudian input ke tabel diupload
	  //----------------------------------------------------------
    $sqldiup = "INSERT INTO diupload (supp, partno, qty)
            SELECT MAX(supp) AS supp,
                   partno,
                   SUM(qty) AS qty
            FROM di
            WHERE supp = :supp
              AND tgld >= :tgl
              AND status <> '0'
            GROUP BY partno
            ORDER BY partno";
    $stmt = $pdo->prepare($sqldiup);
    $stmt->execute([
      'supp' => $obsupp,
      'tgl'  => $tglcek
    ]);
    //--------------- end of ins data ke table upload ----------
	  // hapus dibal berdasarkan supplier
	  //----------------------------------------------------------
    $sqldibaldel = "DELETE FROM dibal WHERE supp = :supp";
    $stmt = $pdo->prepare($sqldibaldel);
    $stmt->execute([
      'supp' => $obsupp
    ]);
    //---------------- end of hapus dibal ----------------------
    // cari balance qty antara di yg sudah diupload dengan di original
    // kemudian insert data ke table dibal
    //------------------------------------------------------------------
    // --- SELECT dengan parameter ---
    $sqldibal = "SELECT digetsum.supp,
                    digetsum.partno,
                    digetsum.qty - diupload.qty AS qty
             FROM digetsum
             INNER JOIN diupload ON digetsum.partno = diupload.partno
             WHERE digetsum.supp = :supp
             ORDER BY digetsum.partno";
    $stmt = $pdo->prepare($sqldibal);
    $stmt->execute(['supp' => $obsupp]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // --- Siapkan statement insert ---
    $sqlinsdibal = "INSERT INTO dibal (supp, partno, balqty)
                VALUES (:supp, :partno, :balqty)";
    $insert = $pdo->prepare($sqlinsdibal);
    // --- Loop dan insert (lebih cepat daripada prepare dalam loop) ---
    foreach ($rows as $row) {
      $insert->execute([
        'supp'   => $row['supp'],
        'partno' => $row['partno'],
        'balqty' => $row['qty']
      ]);
    }
    //-------------- end of ins data ke table dibal --------------------
    //---------------  delete virtual order balance --------------------

    $sqldelvob = "DELETE FROM ordbalvir WHERE suppcode = :suppcode";
    $stmt = $pdo->prepare($sqldelvob);
    $stmt->execute([
      'suppcode' => $vsupp
    ]);
    //-----------------  end of delete virtual order balance --------------
    $sqlinsvob = "INSERT INTO ordbalvir
              SELECT transdate, suppcode, partnumber, partname,
                     orderqty, reqdate, ponumber, posq, orderbalance,
                     supprest, model, issuedate, potype,
                     statuspart, remark, statusread
              FROM ordbalact
              WHERE suppcode = :suppcode";
    $stmt = $pdo->prepare($sqlinsvob);
    $stmt->execute([
    'suppcode' => $vsupp
    ]);
    // ---------------- end of copy record form ordbalact to ordbalvir ------------------------
    $sqldelobup = "DELETE FROM ordbalactupd WHERE supp = :supp";
    $stmt = $pdo->prepare($sqldelobup);
    $stmt->execute([
      'supp' => $vsupp
    ]);
    //-----------------------------------------------------------------------------------------
    // mencari orderbalance dikurangi di yg sudah upload
    // --- SELECT utama ---
    $sqlobvir = "SELECT di.supp,
                    di.po,
                    ordbalvir.orderbalance - di.qty AS balqty
             FROM ordbalvir
             INNER JOIN di ON ordbalvir.ponumber = di.po
             WHERE di.status <> '0'
               AND di.supp = :supp
               AND di.tgld >= :tgld
             ORDER BY partno, disq";

    $stmt = $pdo->prepare($sqlobvir);
    $stmt->execute([
      'supp' => $vsupp,
       'tgld' => $tglcek
    ]);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // --- Prepare insert sekali saja (lebih cepat) ---
    $sqlinsobup = "INSERT INTO ordbalactupd (supp, po, balqty)
               VALUES (:supp, :po, :balqty)";
    $insert = $pdo->prepare($sqlinsobup);

    // --- Loop hasil select ---
    foreach ($rows as $row) {
      $insert->execute([
        'supp'   => $row['supp'],
        'po'     => $row['po'],
        'balqty' => $row['balqty']
      ]);
    }

    // mencari po yg sudah dipakai
    $sqlobupd = "SELECT supp, po, balqty FROM ordbalactupd WHERE supp = :supp";
    $stmtUpd = $pdo->prepare($sqlobupd);
    $stmtUpd->execute(['supp' => $vsupp]);

    while ($row = $stmtUpd->fetch(PDO::FETCH_ASSOC)) {
      $csupp = $row['supp'];
      $cpo   = $row['po'];
      $cqty  = $row['balqty'];
      // Jika qty = 0 → delete
      if ($cqty == 0) 
      {
        $sqlDel = "DELETE FROM ordbalvir WHERE ponumber = :po";
        $stmtDel = $pdo->prepare($sqlDel);
        $stmtDel->execute(['po' => $cpo]);
      }
      // Jika qty > 0 → update
      if ($cqty > 0) 
      {
      $sqlUpd = "UPDATE ordbalvir SET orderbalance = :qty WHERE ponumber = :po";
      $stmtUpd2 = $pdo->prepare($sqlUpd);
      $stmtUpd2->execute([
        'qty' => $cqty,
        'po'  => $cpo
        ]);
      }
    }

    // ---------------------------------------------------------------------
    // Proses make DIGET hanya jika sequence = '1' ( normal 1X delivery)
    // ----------------------------------------------------------------------      
    $kolomtotal = 0;
    if ($vsq == '1') 
    {
      // ==============================
      // 1) CARI TANGGAL DARI HEADER
      // ==============================
      $sql = "SELECT transdate,hd,tm,suppcode,partno,partname,balqty,
        qty1,qty2,qty3,qty4,qty5,qty6,qty7,qty8,qty9,qty10,qty11,qty12,
        qty13,qty14,qty15,qty16,qty17,qty18,qty19,qty20,qty21,qty22,qty23,
        qty24,qty25,qty26,qty27,qty28,qty29,qty30,qty31,qty32
        FROM TDSACT 
        WHERE SuppCode = :supp AND HD = 'H'
        ORDER BY PartNo";

      $stmt = $pdo->prepare($sql);
      $stmt->execute(['supp' => $vsupp]);
      $cek = $vtglj2;
      $tgl = $vtglj2;
      $kolomtgl = 0;
      // Loop header
      while ($row = $stmt->fetch(PDO::FETCH_NUM)) 
      {
        for ($i = 0; $i <= 30; $i++) 
        {
          $cek = substr($row[$i], 0, 5);
          if ($cek == $tgl) 
          {
            $kolomtgl = $i;
            if ($vsq == '1') 
            {
              $jamdel = substr($row[$i], 6, 2);
            }
            if ($vsq == '2') {
              $jamdel = substr($row[$i + 1], 6, 2);
            }
            break;
          }
        }
      }
      // ==============================
      // 2) MAKE DIGET
      // ==============================
      $sqlbps = "SELECT transdate,hd,tm,suppcode,partno,partname,balqty,
        qty1,qty2,qty3,qty4,qty5,qty6,qty7,qty8,qty9,qty10,qty11,qty12,
        qty13,qty14,qty15,qty16,qty17,qty18,qty19,qty20,qty21,qty22,qty23,
        qty24,qty25,qty26,qty27,qty28,qty29,qty30,qty31,qty32
        FROM TDSACT
        WHERE SuppCode = :supp AND HD = 'D'
        ORDER BY PartNo";

      $stmtbps = $pdo->prepare($sqlbps);
      $stmtbps->execute(['supp' => $vsupp]);

      while ($row2 = $stmtbps->fetch(PDO::FETCH_NUM)) 
      {
        $vpartno  = $row2[4];
        $kolombal = $row2[6];
        // Ambil nilai kolom berdasarkan index tgl
        if ($vsq == '1') 
        {
          $kolomnilai = $row2[$kolomtgl];
        } else {
            $kolomnilai = $row2[$kolomtgl + 1];
        }

        // Hitung total
        if ($kolomtgl == 7) 
        {
          if ($vsq == '1') 
          {
            $kolomtotal = $kolombal + $kolomnilai;
          } else {
                $kolomtotal = $kolomnilai;
          }

        } elseif ($kolomtgl > 7) {
          $kolomtotal = $kolomnilai;
        }

        // Insert ke diget jika > 0
        if ($kolomtotal > 0) 
        {
          $sqlinsdiget = "INSERT INTO diget(supp, tgl, sq, partno, qty, jamdel, jamsq)
            VALUES (:supp, :tgl, :sq, :partno, :qty, :jamdel, '1')";
          echo "<br>$sqlinsdiget";
          $stmtIns = $pdo->prepare($sqlinsdiget);
          $stmtIns->execute([
                'supp'   => $vsupp,
                'tgl'    => $vblntglthn,
                'sq'     => $vsq,
                'partno' => $vpartno,
                'qty'    => $kolomtotal,
                'jamdel' => $jamdel
          ]);
        }
      } // while data D

    } // end if vsq=1

    // -----------------------------------------
    // SELECT data dari dibal
    // -----------------------------------------
    $sqldibal = "SELECT supp, partno, balqty 
             FROM dibal 
             WHERE supp = :supp AND balqty <> 0";

    $stmtDibal = $pdo->prepare($sqldibal);
    $stmtDibal->execute(['supp' => $vsupp]);

    // Loop setiap part
    while ($row = $stmtDibal->fetch(PDO::FETCH_ASSOC)) 
    {
      $balpartno = $row['partno'];
      $bal       = $row['balqty'];
      // -----------------------------------------
      // Cek apakah part sudah ada di table diget
      // -----------------------------------------
      $sqlcekdiget = "SELECT COUNT(*) AS ada 
                    FROM diget
                    WHERE supp = :supp 
                      AND partno = :partno
                      AND tgl = :tgl
                      AND sq  = :sq";

      $stmtCek = $pdo->prepare($sqlcekdiget);
      $stmtCek->execute([
        'supp'   => $vsupp,
        'partno' => $balpartno,
        'tgl'    => $vblntglthn,
        'sq'     => $vsq
      ]);

      $adapart = $stmtCek->fetchColumn();  // langsung ambil angka count()
      // -----------------------------------------
      // Jika belum ada → insert
      // -----------------------------------------
      if ($adapart == 0) 
      {
        $sqldiadd = "INSERT INTO diget (supp, tgl, sq, partno, qty)
                     VALUES (:supp, :tgl, :sq, :partno, :qty)";

        $stmtIns = $pdo->prepare($sqldiadd);
        $stmtIns->execute([
            'supp'   => $vsupp,
            'tgl'    => $vblntglthn,
            'sq'     => $vsq,
            'partno' => $balpartno,
            'qty'    => $bal
        ]);
      } 
      // -----------------------------------------
      // Jika sudah ada → update (qty + bal)
      // -----------------------------------------
      else 
      {
        $sqldiupd = "UPDATE diget 
                     SET qty = qty + :qty
                     WHERE supp = :supp
                       AND partno = :partno
                       AND tgl = :tgl
                       AND sq  = :sq";

        $stmtUpd = $pdo->prepare($sqldiupd);
        $stmtUpd->execute([
            'qty'    => $bal,
            'supp'   => $vsupp,
            'partno' => $balpartno,
            'tgl'    => $vblntglthn,
            'sq'     => $vsq
        ]);
      }
    } // end while dibal loop
 
    // -----------------------------------------------------
    // SELECT dari table diget
    // -----------------------------------------------------
    $sqldiget = "SELECT supp, tgl, sq, partno, qty, jamdel, jamsq 
             FROM diget 
             WHERE supp = :supp 
               AND tgl  = :tgl 
               AND sq   = :sq
             ORDER BY sq, partno, jamsq";

    $stmtDiget = $pdo->prepare($sqldiget);
    $stmtDiget->execute([
    'supp' => $vsupp,
    'tgl'  => $vblntglthn,
    'sq'   => $vsq
    ]);

    while ($rowDiget = $stmtDiget->fetch(PDO::FETCH_NUM)) 
    {
      $getpartno = $rowDiget[3];
      $getjamdel = $rowDiget[5];
      $getjamsq  = $rowDiget[6];
      // Variabel hitung big part vs order balance
      $b = $rowDiget[4];   // qty big part
      $cekkecil = 0;
      $jumord   = 0;
      // -----------------------------------------------------
      // SELECT order balance dari ordbalvir
      // -----------------------------------------------------
      $sqlordbal = "SELECT PartNumber,
                         CONVERT(VARCHAR, ReqDate, 1),
                         PONumber,
                         OrderBalance
                  FROM ordbalvir
                  WHERE SuppCode = :supp 
                    AND PartNumber = :partno
                  ORDER BY PartNumber, ReqDate";

      $stmtOB = $pdo->prepare($sqlordbal);
      $stmtOB->execute([
        'supp'   => $vsupp,
        'partno' => $getpartno
      ]);

      while ($rowOB = $stmtOB->fetch(PDO::FETCH_NUM)) 
      {
        $o = $rowOB[3];   // order balance dari ordbalvir
        $po = $rowOB[2];
        $tglReq = $rowOB[1];
        // Generate supptglpo
        $supptglpo = trim($vsupp) . $vthn . $vbln . $vtgl . trim($vsq) . $po;

        // -----------------------------------------------------
        // CASE 1: Jika big part qty < order balance
        // -----------------------------------------------------
        if ($b <= $o && $b > 0) 
        {
          $sqlInsDI = "INSERT INTO di(
                            supptglpo, supp, tgli, po, partno, qty,
                            tgld, invoice, status, ditime, disq
                         )
                         VALUES(
                            :supptglpo, :supp, :tgli, :po, :partno, :qty,
                            :tgld, '', '0', :ditime, :disq
                         )";

          $stmtIns = $pdo->prepare($sqlInsDI);
          $stmtIns->execute([
                'supptglpo' => $supptglpo,
                'supp'      => $vsupp,
                'tgli'      => $tglReq,
                'po'        => $po,
                'partno'    => $getpartno,
                'qty'       => $b,
                'tgld'      => $vblntglthn,
                'ditime'    => $getjamdel,
                'disq'      => $vsq
          ]);
        }

        // -----------------------------------------------------
        // CASE 2: Jika big part qty > order balance
        // -----------------------------------------------------
        if ($b > $o) 
        {
          $sqlInsDI = "INSERT INTO di(
                            supptglpo, supp, tgli, po, partno, qty,
                            tgld, invoice, status, ditime, disq
                         )
                         VALUES(
                            :supptglpo, :supp, :tgli, :po, :partno, :qty,
                            :tgld, '', '0', :ditime, :disq
                         )";

          $stmtIns = $pdo->prepare($sqlInsDI);
          $stmtIns->execute([
                'supptglpo' => $supptglpo,
                'supp'      => $vsupp,
                'tgli'      => $tglReq,
                'po'        => $po,
                'partno'    => $getpartno,
                'qty'       => $o,          // qty = order balance
                'tgld'      => $vblntglthn,
                'ditime'    => $getjamdel,
                'disq'      => $vsq
          ]);
        }

        // Rumus dari kode asli: kurangi big part dengan order balance
        $b = $b - $o;
      } // end while ordbalvir
    } // end while diget

    //--------------------- end of buat DI -------------------------------------
  }	//	end of jika proses == 1      
}	// ----------- end of jenis supplier = J1  -------------------------------

// ------------- start jenis supplier = J2 ----------------------------
// proses jika jenis supplier = J2 , delivery follow Big Part Schedule	

if ($suppdec == 'J2')
{
  // Ambil tanggal server
  $sqltgl = "SELECT GETDATE() AS tgl";
  $stmt = $pdo->prepare($sqltgl);
  $stmt->execute();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $tglserver = $row['tgl'];
  // Format tanggal (pastikan format string)
  $tglserver = date('Y-m-d H:i:s', strtotime($tglserver));
  // Ambil bagian tahun, bulan, tanggal
  $ythn = substr($tglserver, 2, 2);
  $ybln = substr($tglserver, 5, 2);
  $ytgl = substr($tglserver, 8, 2);
  // Format MM/DD/YY
  $tglcek = $ybln . "/" . $ytgl . "/" . $ythn;
  // Variabel dari input
  $vblntglthn = $vbln . "/" . $vtgl . "/" . $vthn;
  // Trim supplier
  $obsupp = trim($vsupp);
  //cek diget
  $sdhget = 0;
  $proses = 1;
  $sqprev = $vsq - 1;  // nilai sequence sebelumnya

  if ($sqprev > 0)  // jika hasil nol tdk di proses cek sequence
  {
    $cekget = $obsupp . $vthn . $vbln . $vtgl . $sqprev . "%";

    $sqlcekget = "SELECT COUNT(*) AS tget FROM di WHERE supptglpo LIKE :cekget";
    $stmt = $pdo->prepare($sqlcekget);
    $stmt->execute([
        ':cekget' => $cekget
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $sdhget = $row['tget'];

    if ($sdhget == 0)
    {
      $proses = 0;
    }
    else
    {
      $proses = 1;
    }
  }  // end of if ($sqprev > 0)

  if ($proses == 1 )
  {
    //---------------- hapus tabel diget sebelum insert -------------------
    $sqldeldiget = "DELETE FROM diget 
                WHERE supp = :supp 
                AND tgl = :tgl 
                AND sq = :sq";
    $stmt = $pdo->prepare($sqldeldiget);
    $stmt->execute([
      ':supp' => $vsupp,
      ':tgl'  => $vblntglthn,
      ':sq'   => $vsq
    ]);
    //----------------- end of hapus tabel diget ---------------------------
	  //------------- hapus di status 0 ---------------------------- 
    $vdel = trim($vsupp) . $vthn . $vbln . $vtgl . $vsq . '%';
    $sqldelnob = "DELETE FROM di 
              WHERE supptglpo LIKE :vdel 
              AND status = :status";
    $stmt = $pdo->prepare($sqldelnob);
    $stmt->execute([
      ':vdel'   => $vdel,
      ':status' => '0'
    ]);
    //---------- end of hapus di status 0 ------------------------
    
    // hapus digetsum
    // -----------------------------------------------------------
    $sqldelgetsum = "DELETE FROM digetsum WHERE supp = :supp";
    $stmt = $pdo->prepare($sqldelgetsum);
    $stmt->execute([
      ':supp' => $obsupp
    ]);
    // --------------- end of hapus digetsum ----------------------
    //---------------- ins data ke table getsum -------------------
    $sqlinsgetsum = "INSERT INTO digetsum (supp, partno, qty)
                 SELECT MAX(supp) AS supp, partno, SUM(qty) AS qty
                 FROM diget
                 WHERE supp = :supp
                 AND tgl >= :tgl
                 GROUP BY partno";
    $stmt = $pdo->prepare($sqlinsgetsum);
    $stmt->execute([
      ':supp' => $obsupp,
      ':tgl'  => $tglcek
    ]);

    //--------------- end of ins data ke table getsum  ---------
    // ---------------------------------------------------------
    // hapus diupload...
    // ---------------------------------------------------------
    $sqldiupdel = "DELETE FROM diupload WHERE supp = :supp";
    $stmt = $pdo->prepare($sqldiupdel);
    $stmt->execute([
      ':supp' => $obsupp
    ]);
    // ------------- end of hapus diupload ---------------------
    //----------------------------------------------------------
    // ambil summary per partno dari data yg sudah di upload
    // dan kemudian input ke tabel diupload
    //----------------------------------------------------------
    $sqldiup = "INSERT INTO diupload (supp, partno, qty)
            SELECT MAX(supp) AS supp, partno, SUM(qty) AS qty
            FROM di
            WHERE supp = :supp
              AND tgld >= :tgl
              AND status <> :status
            GROUP BY partno";
    $stmt = $pdo->prepare($sqldiup);
    $stmt->execute([
      ':supp'   => $obsupp,
      ':tgl'    => $tglcek,
      ':status' => '0'
    ]);
    //--------------- end of ins data ke table upload ----------
    //----------------------------------------------------------
    // hapus dibal berdasarkan supplier
    //----------------------------------------------------------
    $sqldibaldel = "DELETE FROM dibal WHERE supp = :supp";
    $stmt = $pdo->prepare($sqldibaldel);
    $stmt->execute([
      ':supp' => $obsupp
    ]);
    //---------------- end of hapus dibal ----------------------
    //------------------------------------------------------------------
    // cari balance qty antara di yg sudah diupload dengan di original
    // kemudian insert data ke table dibal
    //------------------------------------------------------------------ 
    $sql = "INSERT INTO dibal (supp, partno, balqty)
        SELECT ds.supp,
               ds.partno,
               ds.qty - du.qty AS balqty
        FROM digetsum ds
        INNER JOIN diupload du ON ds.partno = du.partno";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    //-------------- end of ins data ke table dibal -------------------- 
    //---------------  delete virtual order balance --------------------
    $sqldelvob = "DELETE FROM ordbalvir WHERE suppcode = :suppcode";
    $stmt = $pdo->prepare($sqldelvob);
    $stmt->execute([
      ':suppcode' => $vsupp
    ]);
    //-----------------  end of delete virtual order balance --------------
    $sqlinsvob = "INSERT INTO ordbalvir (
                transdate, suppcode, partnumber, partname, orderqty,
                reqdate, ponumber, posq, orderbalance, supprest,
                model, issuedate, potype, statuspart, remark, statusread
              )
              SELECT 
                transdate, suppcode, partnumber, partname, orderqty,
                reqdate, ponumber, posq, orderbalance, supprest,
                model, issuedate, potype, statuspart, remark, statusread
              FROM ordbalact
              WHERE suppcode = :suppcode";
    $stmt = $pdo->prepare($sqlinsvob);
    $stmt->execute([
      ':suppcode' => $vsupp
    ]);
    $sqldelobup = "DELETE FROM ordbalactupd WHERE supp = :supp";
    $stmt = $pdo->prepare($sqldelobup);
    $stmt->execute([
      ':supp' => $vsupp
    ]);
    $sqlobvir = "
      SELECT di.supp, di.po, ordbalvir.orderbalance - di.qty AS balqty
      FROM ordbalvir
      INNER JOIN di ON ordbalvir.ponumber = di.po
      WHERE di.status <> '0'
      AND di.supp = :vsupp
      AND di.tgld >= :tglcek
      ORDER BY partno, disq";
    $stmtSelect = $pdo->prepare($sqlobvir);
    $stmtSelect->execute([
      ':vsupp' => $vsupp,
      ':tglcek' => $tglcek
    ]);
    // Prepare INSERT sekali saja (lebih efisien)
    $sqlinsobup = "
      INSERT INTO ordbalactupd (supp, po, balqty)
      VALUES (:supp, :po, :balqty)";
    $stmtInsert = $pdo->prepare($sqlinsobup);
    // Loop hasil SELECT
    while ($row = $stmtSelect->fetch(PDO::FETCH_NUM)) 
    {
      $stmtInsert->execute([
        ':supp'   => $row[0],
        ':po'     => $row[1],
        ':balqty' => $row[2]
      ]);
    }
    // ----------
    $sqlobupd = "
      SELECT supp, po, balqty
      FROM ordbalactupd
      WHERE supp = :vsupp";
    $stmtSelect = $pdo->prepare($sqlobupd);
    $stmtSelect->execute([
      ':vsupp' => $vsupp
    ]);
    // Prepare DELETE & UPDATE sekali saja
    $sqlDelete = "DELETE FROM ordbalvir WHERE ponumber = :po";
    $stmtDelete = $pdo->prepare($sqlDelete);
    $sqlUpdate = "
    UPDATE ordbalvir
    SET orderbalance = :balqty
    WHERE ponumber = :po";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    // Loop data
    while ($row = $stmtSelect->fetch(PDO::FETCH_NUM)) 
    {
      $csupp = $row[0];
      $cpo   = $row[1];
      $cqty  = $row[2];

      if ($cqty == 0) {
        $stmtDelete->execute([
            ':po' => $cpo
        ]);
      }
      if ($cqty > 0) {
        $stmtUpdate->execute([
            ':balqty' => $cqty,
            ':po'     => $cpo
        ]);
      }
    }  // end of while ($row = $stmtSelect->fetch(PDO::FETCH_NUM)) 
    // ---------------------------------------------------------------------
    // Proses make DIGET hanya jika sequence = '1' / '2' ( normal 2X delivery)
    // ----------------------------------------------------------------------
    if ( $vsq == '1' || $vsq == '2' )
    {
      // cari tanggal dari header
      $sql = "
      SELECT transdate, hd, tm, suppcode, partno, partname, balqty,
           qty1, qty2, qty3, qty4, qty5, qty6, qty7, qty8, qty9, qty10,
           qty11, qty12, qty13, qty14, qty15, qty16, qty17, qty18, qty19,
           qty20, qty21, qty22, qty23, qty24, qty25, qty26, qty27,
           qty28, qty29, qty30, qty31, qty32
      FROM TDSACT
      WHERE SuppCode = :vsupp
      AND HD = 'H'
      ORDER BY PartNo";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
      ':vsupp' => $vsupp
      ]);
      $test = 'kosong';
      $cek  = $vtglj2;
      $tgl  = $vtglj2;
      $kolomtgl = 0;
      while ($row = $stmt->fetch(PDO::FETCH_NUM)) 
      {
        for ($i = 0; $i <= 40; $i++) 
        {
          if (!isset($row[$i])) continue; // jaga2 index kosong
          $cek = substr($row[$i], 0, 5);
          if ($cek == $tgl) 
          {
            $kolomtgl = $i;
            if ($vsq == '1') 
            {
                $jamdel = substr($row[$i], 6, 2);
            }

            if ($vsq == '2' && isset($row[$i+1])) 
            {
                $jamdel = substr($row[$i+1], 6, 2);
            }
            break;
          }
        }
      }
	    //------------------------------------------------------------------------
      // MAKE DIGET
      // start convert to pdo
      $sqlbps = "
      SELECT transdate, hd, tm, suppcode, partno, partname, balqty,
           qty1, qty2, qty3, qty4, qty5, qty6, qty7, qty8, qty9, qty10,
           qty11, qty12, qty13, qty14, qty15, qty16, qty17, qty18, qty19,
           qty20, qty21, qty22, qty23, qty24, qty25, qty26, qty27,
           qty28, qty29, qty30, qty31, qty32
      FROM tdsact
      WHERE SuppCode = :vsupp
      AND HD = 'D'
      ORDER BY PartNo";
      $stmt = $pdo->prepare($sqlbps);
      $stmt->execute([
      ':vsupp' => $vsupp]);
      // Prepare INSERT sekali saja
      $sqlInsert = "
      INSERT INTO diget (supp, tgl, sq, partno, qty, jamdel, jamsq)
      VALUES (:supp, :tgl, :sq, :partno, :qty, :jamdel, :jamsq)
      ";
      $stmtInsert = $pdo->prepare($sqlInsert);
      while ($row = $stmt->fetch(PDO::FETCH_NUM)) 
      {
        $vpartno   = $row[4];
        $ppartname = $row[5];
        $kolombal  = $row[6];
        // Ambil nilai berdasarkan SQ
        if ($vsq == '1') 
        {
          $kolomnilai = $row[$kolomtgl] ?? 0;
        } 
        else {
          $kolomnilai = $row[$kolomtgl + 1] ?? 0;
        }
        // Hitung total
        if ($kolomtgl == 7) 
        {
          if ($vsq == '1') 
        {
            $kolomtotal = $kolombal + $kolomnilai;
        } 
        else {
            $kolomtotal = $kolomnilai;
        }
        } elseif ($kolomtgl > 7) {
          $kolomtotal = $kolomnilai;
        } else {
          $kolomtotal = 0;
        }
        // Insert jika > 0
        if ($kolomtotal > 0) 
        {
          $stmtInsert->execute([
            ':supp'   => $vsupp,
            ':tgl'    => $vblntglthn,
            ':sq'     => $vsq,
            ':partno' => $vpartno,
            ':qty'    => $kolomtotal,
            ':jamdel' => $jamdel,
            ':jamsq'  => $vsq
          ]);
        }
      }
      // ---------------- batas make diget ------------------------------------
    } // end of if ( $vsq == '1' || $vsq == '2' )
    // ---------------- batas proses diget jika sq = '1' / sq = '2' ---------
    //------------  update diget jika ada balance --------------------------
    // ambil data dibal
    $sqldibal = "SELECT supp, partno, balqty 
             FROM dibal 
             WHERE supp = :supp AND balqty <> 0";
    $stmtDibal = $pdo->prepare($sqldibal);
    $stmtDibal->execute(['supp' => $vsupp]);
    while ($row = $stmtDibal->fetch(PDO::FETCH_ASSOC)) 
    {
      $balpartno = $row['partno'];
      $bal       = $row['balqty'];
      // cek apakah sudah ada di diget
      $sqlcekdiget = "SELECT COUNT(*) as ada 
                    FROM diget 
                    WHERE supp = :supp 
                      AND partno = :partno 
                      AND tgl = :tgl 
                      AND sq = :sq";

      $stmtCek = $pdo->prepare($sqlcekdiget);
      $stmtCek->execute([
        'supp'   => $vsupp,
        'partno' => $balpartno,
        'tgl'    => $vblntglthn,
        'sq'     => $vsq
      ]);
      $adapart = $stmtCek->fetchColumn();
      if ($adapart == 0) 
      {
        // insert
        $sqldiadd = "INSERT INTO diget (supp, tgl, sq, partno, qty) 
                     VALUES (:supp, :tgl, :sq, :partno, :qty)";
        $stmtInsert = $pdo->prepare($sqldiadd);
        $stmtInsert->execute([
            'supp'   => $vsupp,
            'tgl'    => $vblntglthn,
            'sq'     => $vsq,
            'partno' => $balpartno,
            'qty'    => $bal
        ]);
      } else 
      {
        // update
        $sqldiadd = "UPDATE diget 
                     SET qty = qty + :qty 
                     WHERE supp = :supp 
                       AND partno = :partno 
                       AND tgl = :tgl 
                       AND sq = :sq";
        $stmtUpdate = $pdo->prepare($sqldiadd);
        $stmtUpdate->execute([
            'qty'    => $bal,
            'supp'   => $vsupp,
            'partno' => $balpartno,
            'tgl'    => $vblntglthn,
            'sq'     => $vsq
        ]);
      }
    }  // end of while ($row = $stmtDibal->fetch(PDO::FETCH_ASSOC)) 
    //------------------ end of update diget jika ada balance --------------------
	  // variable hitung big part vs order balance	
	  //----------------- buat DI di ambil record dari diget ------------------------
    // ambil data diget
    $sqldiget = "SELECT supp, tgl, sq, partno, qty, jamdel, jamsq 
             FROM diget 
             WHERE supp = :supp 
               AND tgl = :tgl 
               AND sq = :sq 
             ORDER BY sq, partno, jamsq";

    $stmtDiget = $pdo->prepare($sqldiget);
    $stmtDiget->execute([
    'supp' => $vsupp,
    'tgl'  => $vblntglthn,
    'sq'   => $vsq
    ]);
    while ($rowDiget = $stmtDiget->fetch(PDO::FETCH_ASSOC)) 
    {
      $getpartno = $rowDiget['partno'];
      $getjamdel = $rowDiget['jamdel'];
      $getjamsq  = $rowDiget['jamsq'];
      $b = $rowDiget['qty']; // qty big part
      $cekkecil = 0;
      $jumord   = 0;
      // ambil order balance
      $sqlordbal = "SELECT PartNumber,
                         CONVERT(VARCHAR, ReqDate, 1) AS ReqDate,
                         PONumber,
                         OrderBalance
                  FROM ordbalvir
                  WHERE SuppCode = :supp 
                    AND PartNumber = :partno
                  ORDER BY PartNumber, ReqDate";
      $stmtOb = $pdo->prepare($sqlordbal);
      $stmtOb->execute([
        'supp'   => $vsupp,
        'partno' => $getpartno
      ]);
      while ($rowOb = $stmtOb->fetch(PDO::FETCH_ASSOC)) 
      {
        $o = $rowOb['OrderBalance'];
        $supptglpo = trim($vsupp) . $vthn . $vbln . $vtgl . trim($vsq) . $rowOb['PONumber'];
        if ($b <= $o && $b > 0) 
        {
          $sqlinsdi = "INSERT INTO di
                        (supptglpo, supp, tgli, po, partno, qty, tgld, invoice, status, ditime, disq)
                        VALUES
                        (:supptglpo, :supp, :tgli, :po, :partno, :qty, :tgld, '', '0', :ditime, :disq)";

          $stmtIns = $pdo->prepare($sqlinsdi);
          $stmtIns->execute([
                'supptglpo' => $supptglpo,
                'supp'      => $vsupp,
                'tgli'      => $rowOb['ReqDate'],
                'po'        => $rowOb['PONumber'],
                'partno'    => $getpartno,
                'qty'       => $b,
                'tgld'      => $vblntglthn,
                'ditime'    => $getjamdel,
                'disq'      => $vsq
            ]);
        } elseif ($b > $o) 
        {
          $sqlinsdi = "INSERT INTO di
                        (supptglpo, supp, tgli, po, partno, qty, tgld, invoice, status, ditime, disq)
                        VALUES
                        (:supptglpo, :supp, :tgli, :po, :partno, :qty, :tgld, '', '0', :ditime, :disq)";

          $stmtIns = $pdo->prepare($sqlinsdi);
          $stmtIns->execute([
                'supptglpo' => $supptglpo,
                'supp'      => $vsupp,
                'tgli'      => $rowOb['ReqDate'],
                'po'        => $rowOb['PONumber'],
                'partno'    => $getpartno,
                'qty'       => $o,
                'tgld'      => $vblntglthn,
                'ditime'    => $getjamdel,
                'disq'      => $vsq
          ]);
        }
        // pengurangan qty
        $b = $b - $o;
      } // end of while ($rowOb = $stmtOb->fetch(PDO::FETCH_ASSOC))  
    }  // while ($rowDiget = $stmtDiget->fetch(PDO::FETCH_ASSOC))  
  } // end of if ($proses == 1 )
} // end of if ($suppdec == 'J2')

// DELIVERY FOLLOW PO
if ($suppdec == 'N' || $suppdec == 'Y')
{ 
  // Ambil tanggal server dari SQL Server
  $sqltgl = "SELECT GETDATE()";
  $stmtTgl = $pdo->query($sqltgl);
  $tglserver = $stmtTgl->fetchColumn();
  // Format tanggal
  $ythn = substr($tglserver, 2, 2);
  $ybln = substr($tglserver, 5, 2);
  $ytgl = substr($tglserver, 8, 2);
  $tglcek = $ybln . "/" . $ytgl . "/" . $ythn;
  $vblntglthn = $vbln . "/" . $vtgl . "/" . $vthn;
  $obsupp = trim($vsupp);
  $cekid = $obsupp . $vthn . $vbln . $vtgl . "%";
  // cek diget
  $sdhget = 0;
  $cekget = $obsupp . $vthn . $vbln . $vtgl . "%";
  $sqlcekget = "SELECT COUNT(*) AS tget 
              FROM di 
              WHERE supptglpo LIKE :cekget 
              AND status = '0'";
  $stmtCekGet = $pdo->prepare($sqlcekget);
  $stmtCekGet->execute([
    ':cekget' => $cekget
  ]);
  $sdhget = $stmtCekGet->fetchColumn();
  if($sdhget > 0)
  {
    // ...
  }
  if ($sdhget==0)
  {
    // delete virtual order balance
    $sqldelvob = "DELETE FROM ordbalvir WHERE suppcode = :vsupp";
    $stmtDelVob = $pdo->prepare($sqldelvob);
    $stmtDelVob->execute([
      ':vsupp' => $vsupp
    ]);
    // copy record from ordbalact to ordbalvir
    $sqlinsvob = "
    INSERT INTO ordbalvir (
    transdate,suppcode,partnumber,partname,orderqty,reqdate,ponumber,posq,orderbalance,
    supprest,model,issuedate,potype,statuspart,remark,statusread)
    SELECT transdate,suppcode,partnumber,partname,orderqty,reqdate,ponumber,posq,
    orderbalance,supprest,model,issuedate,potype,statuspart,remark,statusread
    FROM ordbalact WHERE suppcode = :vsupp";
    $stmtInsVob = $pdo->prepare($sqlinsvob);
    $stmtInsVob->execute([
      ':vsupp' => $vsupp
    ]);
    // cukup disini tambahannya --> cek aktual sudah ok
    // hapus ordbalactupd
    $sqldelobup = "DELETE FROM ordbalactupd WHERE supp = :vsupp";
    $stmtDelObup = $pdo->prepare($sqldelobup);
    $stmtDelObup->execute([
      ':vsupp' => $vsupp
    ]);
    // mencari orderbalance dikurangi di yg sudah upload
    $sqlobvir = "
      SELECT 
      di.supp,di.po,ordbalvir.orderbalance - di.qty AS balqty
      FROM ordbalvir
      INNER JOIN di ON ordbalvir.ponumber = di.po
      WHERE di.status <> '0'
      AND di.supp = :vsupp
      AND di.tgld >= :tglcek";
    $stmtObvir = $pdo->prepare($sqlobvir);
    $stmtObvir->execute([
      ':vsupp'  => $vsupp,
      ':tglcek' => $tglcek
    ]);
    $sqlinsobup = "
      INSERT INTO ordbalactupd (supp, po, balqty)
      VALUES (:supp, :po, :balqty)";
    $stmtInsObup = $pdo->prepare($sqlinsobup);
    while ($row = $stmtObvir->fetch(PDO::FETCH_NUM)) 
    {
      $stmtInsObup->execute([
        ':supp'   => $row[0],
        ':po'     => $row[1],
        ':balqty' => $row[2]
      ]);
    }
    // mencari po yg sudah dipakai
    $sqlobupd = "
      SELECT supp, po, balqty
      FROM ordbalactupd
      WHERE supp = :vsupp";
    $stmtObupd = $pdo->prepare($sqlobupd);
    $stmtObupd->execute([
      ':vsupp' => $vsupp
    ]);
    $sqlDelete = "DELETE FROM ordbalvir WHERE ponumber = :po";
    $stmtDelete = $pdo->prepare($sqlDelete);
    $sqlUpdate = "
      UPDATE ordbalvir
      SET orderbalance = :balqty
      WHERE ponumber = :po";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    while ($row = $stmtObupd->fetch(PDO::FETCH_NUM)) 
    {
      $csupp = $row[0];
      $cpo   = $row[1];
      $cqty  = $row[2];
      if ($cqty == 0) 
      {
        $stmtDelete->execute([
            ':po' => $cpo
        ]);
      }
      if ($cqty > 0) 
      {
        $stmtUpdate->execute([
            ':balqty' => $cqty,
            ':po'     => $cpo
        ]);
      }
    }
    // delete existing record
    $vdel = trim($vsupp) . $vthn . $vbln . $vtgl . '%';
    // hapus data lama di
    $sqldelnob = "
      DELETE FROM di
      WHERE supptglpo LIKE :vdel
      AND status = '0'
      ";
    $stmtDelNob = $pdo->prepare($sqldelnob);
    $stmtDelNob->execute([
      ':vdel' => $vdel
    ]);
    // ambil data orderbalance virtual
    $sqlnob = "
      SELECT SuppCode,PartNumber,OrderBalance,CONVERT(VARCHAR, ReqDate, 1) AS ReqDateFmt,
      PONumber FROM ordbalvir WHERE SuppCode = :vsupp
      ORDER BY ReqDate, PartNumber, PONumber";
    $stmtNob = $pdo->prepare($sqlnob);
    $stmtNob->execute([
      ':vsupp' => $vsupp
    ]);
    $sqlinsnob = "
      INSERT INTO di (supptglpo,supp,tgli,po,partno,qty,tgld,invoice,status)
      VALUES (:supptglpo,:supp,:tgli,:po,:partno,:qty,:tgld,'','0')";
    $stmtInsNob = $pdo->prepare($sqlinsnob);
    
    while ($row = $stmtNob->fetch(PDO::FETCH_NUM)) 
    {
      $bulan  = substr($row[3], 0, 2);
      $hari   = substr($row[3], 3, 2);
      $tahun  = substr($row[3], 6, 2);
      $tglob     = $tahun . $bulan . $hari;
      $tglpilih  = $vthn . $vbln . $vtgl;
      if ($tglob <= $tglpilih) 
      {
        $supptglpo = trim($row[0]) . $vthn . $vbln . $vtgl . $vsq . $row[4];
        $sqlDateTime = $row[3];
        $tgldel = $vbln . "/" . $vtgl . "/" . $vthn;
        $stmtInsNob->execute([
            ':supptglpo' => $supptglpo,
            ':supp'      => $vsupp,
            ':tgli'      => $sqlDateTime,
            ':po'        => $row[4],
            ':partno'    => $row[1],
            ':qty'       => $row[2],
            ':tgld'      => $tgldel
        ]);
        $totalInsert++;
      }
    }
  } // end of $sdhget==0  
}	// end of ($suppdec == 'N' || $suppdec == 'Y')

echo json_encode([
    "success" => true,
    "message" => "Proses selesai",
    "supplier" => $vsupp,
    "type" => $suppdec
]);
exit;
?>