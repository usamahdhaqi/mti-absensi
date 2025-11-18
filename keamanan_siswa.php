<?php
// Start the session
session_start();
include 'config/db.php';
if (!isset($_SESSION['nama_log'])){ header("location: index.php"); }
?>
<!DOCTYPE html>
<html>
<head>
  <title>MTI Absensi | Log Keamanan Siswa</title>
  <?php include('scriptcss.php'); ?>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include('header.php'); include('sidebar.php'); ?>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Log Keamanan (Peserta Didik)</h1>
    </section>
    <section class="content">
      <div class="box">
        <div class="box-body">
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
              <thead>
                <tr>
                  <th>No</th> <th>Waktu Kejadian</th> <th>NIS (Input)</th> <th>Pesan Error</th> <th>Foto Upaya (Gagal)</th> <th>Foto Profil (Master)</th>
                </tr>
              </thead>
              <tbody>
              <?php
                if (isset($_GET['pageno'])) { $pageno = $_GET['pageno']; } else { $pageno = 1; }
                $no_of_records_per_page = 25;
                $offset = ($pageno-1) * $no_of_records_per_page;
                
                // GANTI TABEL
                $total_pages_sql = "SELECT COUNT(*) FROM log_keamanan_siswa";
                $result = mysqli_query($con, $total_pages_sql);
                $total_rows = mysqli_fetch_array($result)[0];
                $total_pages = ceil($total_rows / $no_of_records_per_page);

                // GANTI TABEL
                $sql = "SELECT * FROM log_keamanan_siswa ORDER BY waktu_kejadian DESC LIMIT $offset, $no_of_records_per_page";
                $query = mysqli_query($con, $sql);
                
                if (mysqli_num_rows($query) == 0) {
                    echo '<tr><td colspan="6" class="text-center">Belum ada catatan kegagalan verifikasi.</td></tr>';
                }
                
                $no = $offset + 1;
                while ($row = mysqli_fetch_assoc($query)) {
                    echo '<tr>';
                    echo '<td>'. $no++ . '</td>';
                    echo '<td>'. date('d M Y H:i:s', strtotime($row['waktu_kejadian'])) . '</td>';
                    echo '<td>'. htmlspecialchars($row['id_pegawai_input']) . '</td>'; // Ini adalah NIS yang diinput
                    echo '<td>'. htmlspecialchars($row['pesan_error']) . '</td>';
                    // GANTI FOLDER
                    echo '<td><a href="hasil_keamanan_siswa/'. htmlspecialchars($row['path_foto_gagal']) .'" target="_blank" class="btn btn-danger btn-xs"><i class="fa fa-eye"></i> Lihat Foto Gagal</a></td>';
                    // GANTI FOLDER
                    echo '<td><a href="foto_profil_siswa/'. htmlspecialchars(basename($row['path_foto_master'])) .'" target="_blank" class="btn btn-info btn-xs"><i class="fa fa-user-circle"></i> Lihat Master</a></td>';
                    echo '</tr>';
                }
              ?>
              </tbody>
            </table>
          </div>
        </div>
        </div>
    </section>
  </div>
  <?php include('footer.php'); ?>
</div>
<?php include('scriptjs.php'); ?>
</body>
</html>