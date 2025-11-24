<?php
session_start();
include('config/db.php');

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Datasiswa.xls");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Export Data Peserta Didik Ke Excel</title>
	<style type="text/css">
	body{ font-family: sans-serif; }
	table{ margin: 20px auto; border-collapse: collapse; }
	table th, table td{ border: 1px solid #3c3c3c; padding: 3px 8px; }
	</style>
</head>
<body>
	<left><h1>Data Peserta Didik Metro Teknologi Informatika</h1></left>
	<table border="1">
		<tr>
      <th>No</th>
      <th>Nama Peserta Didik</th>
      <th>NIS</th>
      <th>Email</th>
      <th>No HP</th>
      <th>Reg/PKL</th>
      <th>Jurusan</th>
      <th>Warning 1</th>
      <th>Warning 2</th>
      <th>Warning 3</th>
		</tr>
    <?php
    
    $kelas_filter_sql = "";
    if (isset($_SESSION['valuekelas']) && $_SESSION['valuekelas'] != 'All' && !empty($_SESSION['valuekelas'])) {
        $div = mysqli_real_escape_string($con, $_SESSION['valuekelas']);
        $kelas_filter_sql = " AND kelas = '$div' "; 
    }

    $sql_export = "SELECT * FROM siswa
                   WHERE 1=1 " 
                   . $kelas_filter_sql 
                   . " ORDER BY nama_siswa ASC";

    $query_export = mysqli_query($con, $sql_export); 
    
    $noe = 1;
    while ($row = mysqli_fetch_assoc($query_export)) {
        echo '<tr>';
        echo '<td>'. $noe++ . '</td>';
        echo '<td>'. htmlspecialchars($row['nama_siswa']) . '</td>';
        echo '<td>'. htmlspecialchars($row['nis']) . '</td>';
        echo '<td>'. htmlspecialchars($row['email']) . '</td>';
        echo '<td>'. htmlspecialchars($row['no_hp']) . '</td>';
        echo '<td>'. htmlspecialchars($row['kelas']) . '</td>';
        echo '<td>'. htmlspecialchars($row['jurusan']) . '</td>';
        echo '<td>'. htmlspecialchars($row['warning1']) . '</td>';
        echo '<td>'. htmlspecialchars($row['warning2']) . '</td>';
        echo '<td>'. htmlspecialchars($row['warning3']) . '</td>';
        echo '</tr>';
    }
    ?>
	</table>
</body>
</html>