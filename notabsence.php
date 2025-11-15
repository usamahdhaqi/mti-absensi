<?php
session_start();
include 'config/db.php';

// Cek Login
if (!isset($_SESSION['nama_log'])){
  header("location: index.php");
}

// Logika Filter
if(isset($_POST['btn-submit'])){
  $_SESSION['valuedivisi'] = $_POST['valuedivisi'];
  $_SESSION['from'] = $_POST['from'];
  $_SESSION['to'] = $_POST['to'];
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MTI Absensi | Log Tidak Absen (Izin/Sakit)</title>
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
        Karyawan Tidak Absen
        <small>Log Karyawan yang Izin/Sakit (Sudah Disetujui)</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Tidak Absen</li>
      </ol>
    </section>

    <section class="content">

      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-filter"></i> Filter Data</h3>
        </div>
        <div class="box-body">
          <form action="notabsence.php" method="post">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label>Pilih Filter Divisi</label>
                  <select name="valuedivisi" class="form-control">
                      <option name="divisi" value="All">All</option>
                      <?php
                        $divisisql="SELECT DISTINCT divisi FROM employee WHERE 1 ORDER BY divisi";
                        $query2 = mysqli_query($con, $divisisql);
                        while ($row = mysqli_fetch_assoc($query2)) {
                          $div=$row['divisi'];
                          $selected = (isset($_SESSION['valuedivisi']) && $_SESSION['valuedivisi'] == $div) ? 'selected' : '';
                          echo "<option name='divisi' value='". $div."' $selected>" . $div. "</option>\n";
                        }
                       ?>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>From (Tanggal Izin):</label>
                  <input type="text" id="datepicker" name="from" class="form-control" value="<?php echo isset($_SESSION['from']) ? $_SESSION['from'] : ''; ?>" autocomplete="off">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>To (Tanggal Izin):</label>
                  <input type="text" id="datepicker2" name="to" class="form-control" value="<?php echo isset($_SESSION['to']) ? $_SESSION['to'] : ''; ?>" autocomplete="off">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>&nbsp;</label> <input type="submit" name="btn-submit" value="Search" class="btn btn-primary btn-block" style="background-color: var(--color-secondary-green); border: none;">
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-table"></i> Log Karyawan Tidak Absen</h3>
          <div class="box-tools pull-right" style="top: 10px";>
            <a target="_blank" href="export_employee.php" class="btn btn-success btn-sm" style="background-color: var(--color-secondary-green); border: none;">
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
                  <th>ID Pegawai</th>
                  <th>Nama Pegawai</th>
                  <th>Divisi</th>
                  <th>Jenis Izin</th>
                  <th>Tanggal Izin</th>
                  <th>Keterangan</th> <th>Lampiran</th>
                  <th>Disetujui Oleh</th>
                </tr>
              </thead>
              <tbody>
              <?php
                // Logika Pagination
                if (isset($_GET['pageno'])) {
                    $pageno = $_GET['pageno'];
                } else {
                    $pageno = 1;
                }
                $no_of_records_per_page = 25;
                $offset = ($pageno-1) * $no_of_records_per_page;

                // 1. Siapkan Tanggal Hari Ini (WIB)
                date_default_timezone_set('Asia/Jakarta');
                $tanggal_hari_ini = date('Y-m-d');

                // 2. Buat Kueri Basis
                // Kueri ini MENGGABUNGKAN 'ijin_absensi' (i) dengan 'employee' (e)
                $base_sql = "
                  FROM ijin_absensi i
                  LEFT JOIN employee e ON i.nama_pegawai = e.nama_pegawai 
                  WHERE 
                      i.app = 'Approved'  -- Filter: HANYA yang sudah disetujui
                ";
                
                // 3. Tambahkan Filter Divisi
                if (isset($_SESSION['valuedivisi']) && strlen($_SESSION['valuedivisi']) >= 1) {
                    if ($_SESSION['valuedivisi'] != 'All') {
                        $div = mysqli_real_escape_string($con, $_SESSION['valuedivisi']);
                        $base_sql .= " AND e.divisi = '$div' ";
                    }
                }
                
                // 4. Tambahkan Filter Tanggal
                if (isset($_SESSION['from']) && strlen($_SESSION['from']) > 5) {
                   $date = new DateTime($_SESSION['from']);
                   $dt1 = $date->format('Y-m-d');
                   $base_sql .= " AND i.tanggal_ijin >= '$dt1' ";
                }
                if (isset($_SESSION['to']) && strlen($_SESSION['to']) > 5) {
                   $date = new DateTime($_SESSION['to']);
                   $dt2 = $date->format('Y-m-d');
                   $base_sql .= " AND i.tanggal_ijin <= '$dt2' ";
                }

                // 5. Kueri untuk Total Halaman (Pagination)
                $total_pages_sql = "SELECT COUNT(i.id) " . $base_sql;
                $result = mysqli_query($con, $total_pages_sql);
                $total_rows = mysqli_fetch_array($result)[0];
                $total_pages = ceil($total_rows / $no_of_records_per_page);

                // 6. Kueri untuk Data Karyawan (Tampilan)
                $sqlemp = "SELECT i.*, e.id_pegawai, e.divisi " . $base_sql . " ORDER BY i.tanggal_ijin DESC LIMIT $offset, $no_of_records_per_page";

                $query = mysqli_query($con, $sqlemp);
                
                if (mysqli_num_rows($query) == 0) {
                    echo '<tr><td colspan="9" class="text-center">Tidak ada data izin yang disetujui.</td></tr>';
                }

                $noe = $offset + 1; // Penomoran baris
                while ($row = mysqli_fetch_assoc($query)) {
                    echo '<tr>';
                    echo '<td>'. $noe++ . '</td>';
                    echo '<td>'. htmlspecialchars($row['id_pegawai']) . '</td>';
                    echo '<td>'. htmlspecialchars($row['nama_pegawai']) . '</td>';
                    echo '<td>'. htmlspecialchars($row['divisi']) . '</td>';
                    echo '<td>'. htmlspecialchars($row['ijin']) . '</td>';
                    echo '<td>'. date('d M Y', strtotime($row['tanggal_ijin'])) . '</td>';
                    
                    echo '<td>'. htmlspecialchars($row['alasan_ijin']) . '</td>'; 
                    
                    echo '<td><a href="Lampiran/'. htmlspecialchars($row['lampiran']) .'" target="_blank" class="btn btn-info btn-xs"><i class="fa fa-eye"></i> Lihat</a></td>';
                    echo '<td>'. htmlspecialchars($row['app_by']) . '</td>';
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
<?php 

include('scriptjs.php'); 
?>
<script>
$( function() {
  $( "#datepicker" ).datepicker({
    dateFormat: 'yy-mm-dd'
  });
  $( "#datepicker2" ).datepicker({
    dateFormat: 'yy-mm-dd'
  });
} );
</script>
</body>
</html>