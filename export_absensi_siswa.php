<?php
session_start();
include('config/db.php');

// Siapkan header Excel SEBELUM output apapun
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=DataAbsensiSiswa.xls");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Export Data Absensi Siswa Ke Excel</title>
	<style type="text/css">
	body{ font-family: sans-serif; }
	table{ margin: 20px auto; border-collapse: collapse; }
	table th, table td{ border: 1px solid #3c3c3c; padding: 3px 8px; }
	</style>
</head>
<body>
	<left><h1>Data Absensi Siswa Metro Teknologi Informatika</h1></left>
	<table border="1">
		<tr>
      <th>No</th>
      <th>Nama Peserta Didik</th>
      <th>NIS</th>
      <th>Waktu Masuk</th>
      <th>Waktu Keluar</th>
      <th>Telat</th>
      <th>Kamera</th>
      <th>Status</th>
      <th>Reg/PKL</th>
		</tr>
    <?php

    $kelas_filter_sql = "";
    if (isset($_SESSION['valuekelas']) && $_SESSION['valuekelas'] != 'All' && !empty($_SESSION['valuekelas'])) {
        $div = mysqli_real_escape_string($con, $_SESSION['valuekelas']);
        $kelas_filter_sql = " AND e.kelas = '$div' "; 
    }

    $tanggal_dari_filter_sql = "";
    if (isset($_SESSION['from']) && !empty($_SESSION['from'])) {
        try {
            $date = new DateTime($_SESSION['from']);
            $dt1 = $date->format('Y-m-d');
            $tanggal_dari_filter_sql = " AND DATE(fa.waktu_masuk) >= '$dt1' "; // Filter berdasarkan face_absensi (alias fa)
        } catch (Exception $e) {}
    }

    $tanggal_sampai_filter_sql = "";
    if (isset($_SESSION['to']) && !empty($_SESSION['to'])) {
       try {
           $date = new DateTime($_SESSION['to']);
           $dt2 = $date->format('Y-m-d');
           $tanggal_sampai_filter_sql = " AND DATE(fa.waktu_masuk) <= '$dt2' "; // Filter berdasarkan face_absensi (alias fa)
       } catch (Exception $e) {}
    }

    $sql_export = "SELECT fa.*, e.kelas, e.nis AS nis_from_siswa 
                   FROM absensi_siswa fa 
                   LEFT JOIN siswa e ON fa.nama_siswa = e.nama_siswa 
                   WHERE 1=1 " 
                   . $kelas_filter_sql 
                   . $tanggal_dari_filter_sql 
                   . $tanggal_sampai_filter_sql 
                   . " ORDER BY fa.waktu_masuk DESC";

    $query_export = mysqli_query($con, $sql_export); 
    
    $noe = 1;
    while ($row = mysqli_fetch_assoc($query_export)) {
      echo '<tr>';
      echo '<td>'. $noe++ . '</td>';
      echo '<td>'. htmlspecialchars($row['nama_siswa']) . '</td>';
      // Gunakan NIP dari tabel absensi jika ada, jika tidak, dari siswa
      $nip_display = !empty($row['nis']) ? $row['nis'] : $row['nis_from_siswa'];
      echo '<td>'. htmlspecialchars($nip_display) . '</td>';
      echo '<td>'. htmlspecialchars($row['waktu_masuk']) . '</td>';
      echo '<td>'. htmlspecialchars($row['waktu_keluar']) . '</td>';
      echo '<td>'. htmlspecialchars($row['selisih_waktu']) . '</td>';
      echo '<td>'. htmlspecialchars($row['kamera']) . '</td>';
      echo '<td>'. htmlspecialchars($row['note']) . '</td>';
      echo '<td>'. htmlspecialchars($row['kelas']) . '</td>';
      echo '</tr>';
    }
    ?>
	</table>
</body>
</html>