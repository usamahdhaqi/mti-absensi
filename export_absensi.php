<?php
// Start the session
session_start();
include('config/db.php'); // Pastikan $conn ada di sini

// Siapkan header Excel SEBELUM output apapun
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=DataAbsensi.xls");
?>
<!DOCTYPE html>
<html>
<head>
	<title>Export Data Absensi Ke Excel</title>
	<style type="text/css">
	body{ font-family: sans-serif; }
	table{ margin: 20px auto; border-collapse: collapse; }
	table th, table td{ border: 1px solid #3c3c3c; padding: 3px 8px; }
	</style>
</head>
<body>
	<left><h1>Data Absensi Metro Teknologi Informatika</h1></left>
	<table border="1">
		<tr>
      <th>No</th>
      <th>Nama Pegawai</th>
      <th>NIP</th>
      <th>Waktu Masuk</th>
      <th>Waktu Keluar</th>
      <th>Telat</th>
      <th>Kamera</th>
      <th>Status</th>
      <th>Divisi</th>
		</tr>
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
    // === AKHIR PERBAIKAN FILTER ===

    $sql_export = "SELECT fa.*, e.divisi, e.id_pegawai AS employee_id_from_employee 
                   FROM face_absensi fa 
                   LEFT JOIN employee e ON fa.nama_pegawai = e.nama_pegawai 
                   WHERE 1=1 " 
                   . $divisi_filter_sql 
                   . $tanggal_dari_filter_sql 
                   . $tanggal_sampai_filter_sql 
                   . " ORDER BY fa.waktu_masuk DESC";

    $query_export = mysqli_query($conn, $sql_export); 
    
    $noe = 1;
    while ($row = mysqli_fetch_assoc($query_export)) {
      echo '<tr>';
      echo '<td>'. $noe++ . '</td>';
      echo '<td>'. htmlspecialchars($row['nama_pegawai']) . '</td>';
      // Gunakan NIP dari tabel absensi jika ada, jika tidak, dari employee
      $nip_display = !empty($row['employee_id']) ? $row['employee_id'] : $row['employee_id_from_employee'];
      echo '<td>'. htmlspecialchars($nip_display) . '</td>';
      echo '<td>'. htmlspecialchars($row['waktu_masuk']) . '</td>';
      echo '<td>'. htmlspecialchars($row['waktu_keluar']) . '</td>';
      echo '<td>'. htmlspecialchars($row['selisih_waktu']) . '</td>';
      echo '<td>'. htmlspecialchars($row['kamera']) . '</td>';
      echo '<td>'. htmlspecialchars($row['note']) . '</td>';
      echo '<td>'. htmlspecialchars($row['divisi']) . '</td>';
      echo '</tr>';
    }
    ?>
	</table>
</body>
</html>