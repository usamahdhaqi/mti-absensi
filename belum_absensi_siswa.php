<?php
// Start the session
session_start();
include 'config/db.php';
if (!isset($_SESSION['nama_log'])){ header("location: index.php"); }
if(isset($_POST['btn-submit'])){ $_SESSION['valuedivisi'] = $_POST['valuedivisi']; }
?>
<!DOCTYPE html>
<html>
<head>
  <title>MTI Absensi | Belum Absen Siswa</title>
  <?php include('scriptcss.php'); ?>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include('header.php'); include('sidebar.php'); ?>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Siswa Belum Absen Hari Ini</h1>
    </section>
    <section class="content">
      <div class="box">
        <div class="box-body">
          <form action="belum_absensi_siswa.php" method="post">
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
                  <th>No</th> <th>NIS</th> <th>Nama Siswa</th> <th>Kelas</th> <th>Jurusan</th> <th>No. HP</th>
                </tr>
              </thead>
              <tbody>
              <?php
                if (isset($_GET['pageno'])) { $pageno = $_GET['pageno']; } else { $pageno = 1; }
                $no_of_records_per_page = 25;
                $offset = ($pageno-1) * $no_of_records_per_page;
                
                date_default_timezone_set('Asia/Jakarta');
                $tanggal_hari_ini = date('Y-m-d');

                $filter_divisi_sql = "";
                if (isset($_SESSION['valuedivisi']) && strlen($_SESSION['valuedivisi']) >= 1) {
                    if ($_SESSION['valuedivisi'] != 'All') {
                        $div = mysqli_real_escape_string($con, $_SESSION['valuedivisi']);
                        $filter_divisi_sql = " AND e.kelas = '$div' "; // GANTI divisi -> kelas
                    }
                }
                
                // GANTI SEMUA TABEL
                $base_sql = "
                  FROM siswa e
                  LEFT JOIN absensi_siswa fa ON e.nis = fa.employee_id AND DATE(fa.waktu_masuk) = '$tanggal_hari_ini'
                  LEFT JOIN tidak_absensi_siswa na ON e.nis = na.employee_id AND na.tanggal_absen = '$tanggal_hari_ini'
                  WHERE 
                      fa.id IS NULL
                  AND 
                      na.id IS NULL
                  $filter_divisi_sql
                ";
                $total_pages_sql = "SELECT COUNT(e.id) " . $base_sql;
                $result = mysqli_query($con, $total_pages_sql);
                $total_rows = mysqli_fetch_array($result)[0];
                $total_pages = ceil($total_rows / $no_of_records_per_page);
                $sqlemp = "SELECT e.* " . $base_sql . " ORDER BY e.nama_pegawai ASC LIMIT $offset, $no_of_records_per_page";
                
                $query = mysqli_query($con, $sqlemp);
                if (mysqli_num_rows($query) == 0) {
                    echo '<tr><td colspan="6" class="text-center">Semua siswa di kelas ini sudah absen atau izin.</td></tr>';
                }
                $noe = $offset + 1;
                while ($row = mysqli_fetch_assoc($query)) {
                    echo '<tr>';
                    echo '<td>'. $noe++ . '</td>';
                    echo '<td>'. htmlspecialchars($row['nis']) . '</td>';
                    echo '<td>'. htmlspecialchars($row['nama_pegawai']) . '</td>';
                    echo '<td>'. htmlspecialchars($row['kelas']) . '</td>';
                    echo '<td>'. htmlspecialchars($row['jurusan']) . '</td>';
                    echo '<td>'. htmlspecialchars($row['no_hp']) . '</td>';
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