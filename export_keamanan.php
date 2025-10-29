<?php
// Start the session
session_start();
include('config/db.php'); // Pastikan $con ada di sini

// Siapkan header Excel SEBELUM output apapun
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=DataLogKeamanan.xls");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Export Data Log Keamanan Ke Excel</title>
	<style type="text/css">
	body{ font-family: sans-serif; }
	table{ margin: 20px auto; border-collapse: collapse; }
	table th, table td{ border: 1px solid #3c3c3c; padding: 3px 8px; }
	</style>
</head>
<body>
	<left><h1>Data Log Keamanan Metro Teknologi Informatika</h1></left>
	<table border="1">
		<tr>
      <th>No</th>
      <th>Waktu Kejadian</th>
      <th>ID Pegawai (Input)</th>
      <th>Pesan Error</th>
      <th>Path Foto Gagal</th>
      <th>Path Foto Master</th>
		</tr>
    <?php
    // === AWAL PERBAIKAN FILTER (HANYA TANGGAL) ===
    // Filter Divisi tidak relevan karena log_keamanan tidak punya data divisi
    
    $tanggal_dari_filter_sql = "";
    if (isset($_SESSION['from']) && !empty($_SESSION['from'])) {
        try {
            $date = new DateTime($_SESSION['from']);
            $dt1 = $date->format('Y-m-d');
            $tanggal_dari_filter_sql = " AND DATE(waktu_kejadian) >= '$dt1' "; // Filter berdasarkan waktu_kejadian
        } catch (Exception $e) {}
    }

    $tanggal_sampai_filter_sql = "";
    if (isset($_SESSION['to']) && !empty($_SESSION['to'])) {
       try {
           $date = new DateTime($_SESSION['to']);
           $dt2 = $date->format('Y-m-d');
           $tanggal_sampai_filter_sql = " AND DATE(waktu_kejadian) <= '$dt2' "; // Filter berdasarkan waktu_kejadian
       } catch (Exception $e) {}
    }
    // === AKHIR PERBAIKAN FILTER ===

    // Ambil data dari log_keamanan
    $sql_export = "SELECT * FROM log_keamanan 
                   WHERE 1=1 " 
                   . $tanggal_dari_filter_sql 
                   . $tanggal_sampai_filter_sql 
                   . " ORDER BY waktu_kejadian DESC";

    $query_export = mysqli_query($con, $sql_export); 
    
    $noe = 1;
    while ($row = mysqli_fetch_assoc($query_export)) {
      echo '<tr>';
      echo '<td>'. $noe++ . '</td>';
      echo '<td>'. htmlspecialchars($row['waktu_kejadian']) . '</td>';
      echo '<td>'. htmlspecialchars($row['id_pegawai_input']) . '</td>';
      echo '<td>'. htmlspecialchars($row['pesan_error']) . '</td>';
      echo '<td>'. htmlspecialchars($row['path_foto_gagal']) . '</td>';
      echo '<td>'. htmlspecialchars($row['path_foto_master']) . '</td>';
      echo '</tr>';
    }
    ?>
	</table>
</body>
</html>