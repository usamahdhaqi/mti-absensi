<?php
// Start the session
session_start();
include('config/db.php'); // Pastikan $conn ada di sini

// Siapkan header Excel SEBELUM output apapun
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=DataPegawai.xls");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Export Data Karyawan Ke Excel</title>
	<style type="text/css">
	body{ font-family: sans-serif; }
	table{ margin: 20px auto; border-collapse: collapse; }
	table th, table td{ border: 1px solid #3c3c3c; padding: 3px 8px; }
	</style>
</head>
<body>
	<left><h1>Data Karyawan Metro Teknologi Informatika</h1></left>
	<table border="1">
		<tr>
      <th>No</th>
      <th>Nama Pegawai</th>
      <th>NIP</th>
      <th>Email</th>
      <th>No HP</th>
      <th>Divisi</th>
      <th>Jabatan</th>
      <th>Warning 1</th>
      <th>Warning 2</th>
      <th>Warning 3</th>
		</tr>
    <?php
    // === PERBAIKAN FILTER DIVISI ===
    $divisi_filter_sql = "";
    if (isset($_SESSION['valuedivisi']) && $_SESSION['valuedivisi'] != 'All' && !empty($_SESSION['valuedivisi'])) {
        $div = mysqli_real_escape_string($conn, $_SESSION['valuedivisi']);
        $divisi_filter_sql = " AND divisi = '$div' "; 
    }
    // === AKHIR PERBAIKAN FILTER ===

    $sql_export = "SELECT * FROM employee 
                   WHERE 1=1 " 
                   . $divisi_filter_sql 
                   . " ORDER BY nama_pegawai ASC";

    $query_export = mysqli_query($conn, $sql_export); 
    
    $noe = 1;
    while ($row = mysqli_fetch_assoc($query_export)) {
        echo '<tr>';
        echo '<td>'. $noe++ . '</td>';
        echo '<td>'. htmlspecialchars($row['nama_pegawai']) . '</td>';
        echo '<td>'. htmlspecialchars($row['id_pegawai']) . '</td>';
        echo '<td>'. htmlspecialchars($row['email']) . '</td>';
        echo '<td>'. htmlspecialchars($row['no_hp']) . '</td>';
        echo '<td>'. htmlspecialchars($row['divisi']) . '</td>';
        echo '<td>'. htmlspecialchars($row['jabatan']) . '</td>';
        echo '<td>'. htmlspecialchars($row['warning1']) . '</td>';
        echo '<td>'. htmlspecialchars($row['warning2']) . '</td>';
        echo '<td>'. htmlspecialchars($row['warning3']) . '</td>';
        echo '</tr>';
    }
    ?>
	</table>
</body>
</html>