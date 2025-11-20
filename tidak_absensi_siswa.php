<?php
// Start the session
session_start();
include 'config/db.php';
if (!isset($_SESSION['nama_log'])){ header("location: index.php"); }
if(isset($_POST['btn-submit'])){
  $_SESSION['valuedivisi'] = $_POST['valuedivisi'];
  $_SESSION['from'] = $_POST['from'];
  $_SESSION['to'] = $_POST['to'];
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>MTI Absensi | Log Izin Siswa</title>
  <?php include('scriptcss.php'); ?>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include('header.php'); include('sidebar.php'); ?>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Log Izin Peserta Didik (Disetujui)</h1>
    </section>
    <section class="content">
      <div class="box">
        <div class="box-body">
          <form action="tidak_absensi_siswa.php" method="post">
             <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label>Pilih Filter Kelas</label>
                  <select name="valuedivisi" class="form-control">
                      <option value="All">All</option>
                      <?php
                        $divisisql="SELECT DISTINCT kelas FROM siswa WHERE 1 ORDER BY kelas";
                        $query2 = mysqli_query($con, $divisisql);
                        while ($row = mysqli_fetch_assoc($query2)) {
                          $div=$row['kelas'];
                          $selected = (isset($_SESSION['valuedivisi']) && $_SESSION['valuedivisi'] == $div) ? 'selected' : '';
                          echo "<option value='". $div."' $selected>" . $div. "</option>\n";
                        }
                       ?>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group"><label>From (Tanggal Izin):</label>
                  <input type="text" id="datepicker" name="from" class="form-control" value="<?php echo isset($_SESSION['from']) ? $_SESSION['from'] : ''; ?>" autocomplete="off">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group"><label>To (Tanggal Izin):</label>
                  <input type="text" id="datepicker2" name="to" class="form-control" value="<?php echo isset($_SESSION['to']) ? $_SESSION['to'] : ''; ?>" autocomplete="off">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group"><label>&nbsp;</label> <input type="submit" name="btn-submit" value="Search" class="btn btn-primary btn-block">
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
      <div class="box">
        <div class="box-body">
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
              <thead>
                <tr>
                  <th>No</th> <th>NIS</th> <th>Nama Siswa</th> <th>Kelas</th> <th>Jenis Izin</th> <th>Tanggal Izin</th> <th>Keterangan</th> <th>Lampiran</th> <th>Disetujui Oleh</th>
                </tr>
              </thead>
              <tbody>
              <?php
                if (isset($_GET['pageno'])) { $pageno = $_GET['pageno']; } else { $pageno = 1; }
                $no_of_records_per_page = 25;
                $offset = ($pageno-1) * $no_of_records_per_page;
                
                // GANTI KUERI (baca dari 'ijin_siswa' JOIN 'siswa')
                $base_sql = "FROM ijin_siswa i LEFT JOIN siswa e ON i.nama_pegawai = e.nama_siswa WHERE i.app = 'Approved' ";
                
                if (isset($_SESSION['valuedivisi']) && strlen($_SESSION['valuedivisi']) >= 1) {
                    if ($_SESSION['valuedivisi'] != 'All') {
                        $div = mysqli_real_escape_string($con, $_SESSION['valuedivisi']);
                        $base_sql .= " AND e.kelas = '$div' ";
                    }
                }
                if (isset($_SESSION['from']) && strlen($_SESSION['from']) > 5) {
                   $date = new DateTime($_SESSION['from']); $dt1 = $date->format('Y-m-d');
                   $base_sql .= " AND i.tanggal_ijin >= '$dt1' ";
                }
                if (isset($_SESSION['to']) && strlen($_SESSION['to']) > 5) {
                   $date = new DateTime($_SESSION['to']); $dt2 = $date->format('Y-m-d');
                   $base_sql .= " AND i.tanggal_ijin <= '$dt2' ";
                }
                $total_pages_sql = "SELECT COUNT(i.id) " . $base_sql;
                $result = mysqli_query($con, $total_pages_sql);
                $total_rows = mysqli_fetch_array($result)[0];
                $total_pages = ceil($total_rows / $no_of_records_per_page);
                $sqlemp = "SELECT i.*, e.nis, e.kelas " . $base_sql . " ORDER BY i.tanggal_ijin DESC LIMIT $offset, $no_of_records_per_page";
                
                $query = mysqli_query($con, $sqlemp);
                if (mysqli_num_rows($query) == 0) {
                    echo '<tr><td colspan="9" class="text-center">Tidak ada data izin yang disetujui.</td></tr>';
                }
                $noe = $offset + 1;
                while ($row = mysqli_fetch_assoc($query)) {
                    echo '<tr>';
                    echo '<td>'. $noe++ . '</td>';
                    echo '<td>'. htmlspecialchars($row['nis']) . '</td>'; 
                    echo '<td>'. htmlspecialchars($row['nama_siswa']) . '</td>';
                    echo '<td>'. htmlspecialchars($row['kelas']) . '</td>';
                    echo '<td>'. htmlspecialchars($row['ijin']) . '</td>';
                    echo '<td>'. date('d M Y', strtotime($row['tanggal_ijin'])) . '</td>';
                    echo '<td>'. htmlspecialchars($row['alasan_ijin']) . '</td>'; 
                    echo '<td><a href="Lampiran_siswa/'. htmlspecialchars($row['lampiran']) .'" target="_blank" class="btn btn-info btn-xs"><i class="fa fa-eye"></i> Lihat</a></td>'; // GANTI FOLDER
                    echo '<td>'. htmlspecialchars($row['app_by']) . '</td>';
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

</body>
</html>