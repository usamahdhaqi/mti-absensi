<?php

session_start();
include('config/db.php');

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=DataIjinAbsensi.xls");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Export Data Ijin Absensi Ke Excel</title>
	<style type="text/css">
	body{ font-family: sans-serif; }
	table{ margin: 20px auto; border-collapse: collapse; }
	table th, table td{ border: 1px solid #3c3c3c; padding: 3px 8px; }
	</style>
</head>
<body>
	<left><h1>Data Ijin Absensi Metro Teknologi Informatika</h1></left>
	<table border="1">
		<tr>
      <th>No</th>
      <th>Nama Pegawai</th>
      <th>Jenis Izin</th>
      <th>Keterangan Ijin</th>
      <th>Tanggal Izin</th>
      <th>Waktu Pengajuan</th>
      <th>Divisi</th>
      <th>Atasan</th>
      <th>Status Persetujuan</th>
      <th>Disetujui/Ditolak Oleh</th>
      <th>Alasan Persetujuan/Penolakan</th>
		</tr>
    <?php
    
    $divisi_filter_sql = "";
    if (isset($_SESSION['valuedivisi']) && $_SESSION['valuedivisi'] != 'All' && !empty($_SESSION['valuedivisi'])) {
        $div = mysqli_real_escape_string($con, $_SESSION['valuedivisi']);
        $divisi_filter_sql = " AND e.divisi = '$div' "; // Filter berdasarkan tabel employee (alias e)
    }

    $tanggal_dari_filter_sql = "";
    if (isset($_SESSION['from']) && !empty($_SESSION['from'])) {
        try {
            $date = new DateTime($_SESSION['from']);
            $dt1 = $date->format('Y-m-d');
            $tanggal_dari_filter_sql = " AND i.tanggal_ijin >= '$dt1' "; // Filter berdasarkan ijin_absensi (alias i)
        } catch (Exception $e) {}
    }

    $tanggal_sampai_filter_sql = "";
    if (isset($_SESSION['to']) && !empty($_SESSION['to'])) {
       try {
           $date = new DateTime($_SESSION['to']);
           $dt2 = $date->format('Y-m-d');
           $tanggal_sampai_filter_sql = " AND i.tanggal_ijin <= '$dt2' "; // Filter berdasarkan ijin_absensi (alias i)
       } catch (Exception $e) {}
    }

    // Bangun Kueri SQL Utama
    $sql_export = "SELECT i.*, e.divisi
                   FROM ijin_absensi i
                   LEFT JOIN employee e ON i.nama_pegawai = e.nama_pegawai
                   WHERE 1=1 "
                   . $divisi_filter_sql 
                   . $tanggal_dari_filter_sql 
                   . $tanggal_sampai_filter_sql 
                   . " ORDER BY i.waktu_buat_ijin DESC"; // Urutkan berdasarkan waktu pengajuan

    $query_export = mysqli_query($con, $sql_export); 
    
    $noe = 1;

    while ($row = mysqli_fetch_assoc($query_export)) {
      echo '<tr>';
      echo '<td>'. $noe++ . '</td>';
      echo '<td>'. htmlspecialchars($row['nama_pegawai']) . '</td>';
      echo '<td>'. htmlspecialchars($row['ijin']) . '</td>';
      echo '<td>'. htmlspecialchars($row['alasan_ijin']) . '</td>';
      echo '<td>'. htmlspecialchars($row['tanggal_ijin']) . '</td>';
      echo '<td>'. htmlspecialchars($row['waktu_buat_ijin']) . '</td>';
      echo '<td>'. htmlspecialchars($row['divisi']) . '</td>';
      echo '<td>'. htmlspecialchars($row['atasan']) . '</td>';
      echo '<td>'. htmlspecialchars($row['app']) . '</td>';
      echo '<td>'. htmlspecialchars($row['app_by']) . '</td>';
      echo '<td>'. htmlspecialchars($row['alasan_app_dpp']) . '</td>';
      echo '</tr>';
    }
    ?>
	</table>
</body>
</html>