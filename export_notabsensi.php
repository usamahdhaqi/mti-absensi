<?php
// Start the session
session_start();
include('config/db.php'); // Pastikan $conn ada di sini

// Siapkan header Excel SEBELUM output apapun
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=DataTidakAbsensi.xls");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Export Data Tidak Absensi Ke Excel</title>
	<style type="text/css">
	body{ font-family: sans-serif; }
	table{ margin: 20px auto; border-collapse: collapse; }
	table th, table td{ border: 1px solid #3c3c3c; padding: 3px 8px; }
	</style>
</head>
<body>
	<left><h1>Data Tidak Absensi Metro Teknologi Informatika</h1></left>
	<table border="1">
		<tr>
      <th>No</th>
      <th>Nama Pegawai</th>
      <th>NIP</th>
      <th>Tanggal Izin</th>
      <th>Divisi</th>
      <th>Jenis Izin</th>      <th>Keterangan</th>    <th>Disetujui Oleh</th> </tr>
    <?php
    // === AWAL PERBAIKAN FILTER ===
    $divisi_filter_sql = "";
    if (isset($_SESSION['valuedivisi']) && $_SESSION['valuedivisi'] != 'All' && !empty($_SESSION['valuedivisi'])) {
        $div = mysqli_real_escape_string($conn, $_SESSION['valuedivisi']);
        $divisi_filter_sql = " AND e.divisi = '$div' "; 
    }

    $tanggal_dari_filter_sql = "";
    if (isset($_SESSION['from']) && !empty($_SESSION['from'])) {
        try {
            $date = new DateTime($_SESSION['from']);
            $dt1 = $date->format('Y-m-d');
            // Filter berdasarkan tanggal izin di tabel ijin_absensi
            $tanggal_dari_filter_sql = " AND i.tanggal_ijin >= '$dt1' "; 
        } catch (Exception $e) {}
    }

    $tanggal_sampai_filter_sql = "";
    if (isset($_SESSION['to']) && !empty($_SESSION['to'])) {
       try {
           $date = new DateTime($_SESSION['to']);
           $dt2 = $date->format('Y-m-d');
           // Filter berdasarkan tanggal izin di tabel ijin_absensi
           $tanggal_sampai_filter_sql = " AND i.tanggal_ijin <= '$dt2' "; 
       } catch (Exception $e) {}
    }
    // === AKHIR PERBAIKAN FILTER ===

    // Ambil data dari ijin_absensi yang sudah 'Approved'
    $sql_export = "SELECT i.*, e.divisi, e.id_pegawai
                   FROM ijin_absensi i
                   LEFT JOIN employee e ON i.nama_pegawai = e.nama_pegawai
                   WHERE i.app = 'Approved' " 
                   . $divisi_filter_sql 
                   . $tanggal_dari_filter_sql 
                   . $tanggal_sampai_filter_sql 
                   . " ORDER BY i.tanggal_ijin DESC";

    $query_export = mysqli_query($conn, $sql_export); 
    
    $noe = 1;
    while ($row = mysqli_fetch_assoc($query_export)) {
      echo '<tr>';
      echo '<td>'. $noe++ . '</td>';
      echo '<td>'. htmlspecialchars($row['nama_pegawai']) . '</td>';
      echo '<td>'. htmlspecialchars($row['id_pegawai']) . '</td>';
      echo '<td>'. htmlspecialchars($row['tanggal_ijin']) . '</td>';
      echo '<td>'. htmlspecialchars($row['divisi']) . '</td>';
      // Kolom Tambahan
      echo '<td>'. htmlspecialchars($row['ijin']) . '</td>';
      echo '<td>'. htmlspecialchars($row['alasan_ijin']) . '</td>';
      echo '<td>'. htmlspecialchars($row['app_by']) . '</td>';
      echo '</tr>';
    }
    ?>
	</table>
</body>
</html>