<?php
session_start();
include('config/db.php');

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=DataSiswaTidakAbsensi.xls");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Export Data Siswa Tidak Absensi Ke Excel</title>
	<style type="text/css">
	body{ font-family: sans-serif; }
	table{ margin: 20px auto; border-collapse: collapse; }
	table th, table td{ border: 1px solid #3c3c3c; padding: 3px 8px; }
	</style>
</head>
<body>
	<left><h1>Data Siswa Tidak Absensi Metro Teknologi Informatika</h1></left>
	<table border="1">
		<tr>
      <th>No</th>
      <th>Nama siswa</th>
      <th>NIS</th>
      <th>Tanggal Izin</th>
      <th>Reg/PKL</th>
      <th>Jenis Izin</th>      <th>Keterangan</th>    <th>Disetujui Oleh</th> </tr>
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

    // Ambil data dari ijin_absensi yang sudah 'Approved'
    $sql_export = "SELECT i.*, e.kelas, e.nis
                   FROM ijin_siswa i
                   LEFT JOIN siswa e ON i.nama_siswa = e.nama_siswa
                   WHERE i.app = 'Approved' " 
                   . $kelas_filter_sql 
                   . $tanggal_dari_filter_sql 
                   . $tanggal_sampai_filter_sql 
                   . " ORDER BY i.tanggal_ijin DESC";

    $query_export = mysqli_query($con, $sql_export); 
    
    $noe = 1;
    while ($row = mysqli_fetch_assoc($query_export)) {
      echo '<tr>';
      echo '<td>'. $noe++ . '</td>';
      echo '<td>'. htmlspecialchars($row['nama_siswa']) . '</td>';
      echo '<td>'. htmlspecialchars($row['nis']) . '</td>';
      echo '<td>'. htmlspecialchars($row['tanggal_ijin']) . '</td>';
      echo '<td>'. htmlspecialchars($row['kelas']) . '</td>';
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