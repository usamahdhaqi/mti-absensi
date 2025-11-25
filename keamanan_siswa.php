<?php
session_start();
include 'config/db.php';

// Cek Login
if (!isset($_SESSION['nama_log'])){
  header("location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MTI Absensi | Log Keamanan Siswa</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <?php include('scriptcss.php'); ?>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <?php include('header.php'); ?>
  <?php include('sidebar.php'); ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Log Keamanan Siswa
        <small>Catatan Upaya Absensi yang Gagal Verifikasi</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Log Keamanan</li>
      </ol>
    </section>

    <section class="content">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-table"></i> Log Kegagalan Verifikasi</h3>
          <div class="box-tools pull-right" style="top: 10px";>
            <a target="_blank" href="export_keamanan_siswa.php" class="btn btn-success btn-sm" style="background-color: var(--color-secondary-green); border: none;">
              <i class="fa fa-file-excel-o"></i><span class="btn-text-mobile-hide"> EXPORT KE EXCEL</span>
            </a>
          </div>
        </div>
        <div class="box-body">
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Waktu Kejadian</th>
                  <th>ID siswa (Input)</th>
                  <th>Pesan Error</th>
                  <th>Foto Upaya (Gagal)</th>
                  <th>Foto Profil (Master)</th>
                </tr>
              </thead>
              <tbody>
              <?php
                // Logika Pagination (Sederhana)
                if (isset($_GET['pageno'])) { $pageno = $_GET['pageno']; } 
                else { $pageno = 1; }
                $no_of_records_per_page = 25;
                $offset = ($pageno-1) * $no_of_records_per_page;
                
                // Kueri Total Halaman
                $total_pages_sql = "SELECT COUNT(*) FROM log_keamanan_siswa";
                $result = mysqli_query($con, $total_pages_sql);
                $total_rows = mysqli_fetch_array($result)[0];
                $total_pages = ceil($total_rows / $no_of_records_per_page);

                // Kueri Data
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
                    echo '<td>'. htmlspecialchars($row['nis_input']) . '</td>';
                    echo '<td>'. htmlspecialchars($row['pesan_error']) . '</td>';
                    
                    // Link untuk melihat foto GAGAL
                    echo '<td><a href="hasil_keamanan_siswa/'. htmlspecialchars($row['path_foto_gagal']) .'" target="_blank" class="btn btn-danger btn-xs"><i class="fa fa-eye"></i> Lihat Foto Gagal</a></td>';
                    
                    // Link untuk melihat foto MASTER
                    echo '<td><a href="'. htmlspecialchars($row['path_foto_master']) .'" target="_blank" class="btn btn-info btn-xs"><i class="fa fa-user-circle"></i> Lihat Master</a></td>';

                    echo '</tr>';
                }
              ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="box-footer clearfix">
          <ul class="pagination pagination-sm no-margin pull-right">
              <li><a href="?pageno=1">First</a></li>
              <li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>">
                  <a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pageno=".($pageno - 1); } ?>">Prev</a>
              </li>
              <li class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
                  <a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?pageno=".($pageno + 1); } ?>">Next</a>
              </li>
              <li><a href="?pageno=<?php echo $total_pages; ?>">Last</a></li>
          </ul>
        </div>
      </div>
    </section>
  </div>

  <?php include('footer.php'); ?>
</div>

</body>
</html>