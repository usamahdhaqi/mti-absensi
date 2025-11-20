<?php
// Start the session
session_start();
include 'config/db.php';

// Cek Login (ambil dari file Anda yang lain)
if (!isset($_SESSION['nama_log'])){
  header("location: index.php");
}

// Logika Filter
if(isset($_POST['btn-submit'])){
  $_SESSION['valuedivisi'] = $_POST['valuedivisi'];
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MTI Absensi | Siswa Belum Absen</title>
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
        Peserta Didik Belum Absen
        <small>Data Siswa yang Belum Absen Hari Ini</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Belum Absen</li>
      </ol>
    </section>

    <section class="content">

      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-filter"></i> Filter Divisi</h3>
        </div>
        <div class="box-body">
          <form action="belumabsence.php" method="post">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label>Pilih Filter Divisi</label>
                  <select name="valuedivisi" class="form-control">
                      <option name="divisi" value="All">All</option>
                      <?php
                        $divisisql="SELECT DISTINCT kelas FROM siswa WHERE 1 ORDER BY divisi";
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
          <h3 class="box-title"><i class="fa fa-table"></i> Peserta Didik Belum Absen</h3>
        </div>
        <div class="box-body">
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
              <thead>
                <tr>
                  <th>No</th>
                  <th>ID Peserta Didik (NIS)</th>
                  <th>Nama Siswa</th>
                  <th>Reg/PKL</th>
                  <th>Jurusan</th>
                  <th>No. HP</th>
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

                // 1. Tentukan Tanggal Hari Ini (WIB)
                date_default_timezone_set('Asia/Jakarta');
                $tanggal_hari_ini = date('Y-m-d');

                // 2. Siapkan Filter Divisi
                $filter_divisi_sql = "";
                // Perbaikan dari kode lama Anda (mengecek isset dan strlen)
                if (isset($_SESSION['valuedivisi']) && strlen($_SESSION['valuedivisi']) >= 1) {
                    if ($_SESSION['valuedivisi'] != 'All') {
                        $div = mysqli_real_escape_string($con, $_SESSION['valuedivisi']);
                        $filter_divisi_sql = " AND e.divisi = '$div' ";
                    }
                }

                // 3. Buat Kueri Basis (Base Query)
                $base_sql = "
                  FROM employee e
                  LEFT JOIN face_absensi fa ON e.id_pegawai = fa.employee_id AND DATE(fa.waktu_masuk) = '$tanggal_hari_ini'
                  LEFT JOIN not_absensi na ON e.id_pegawai = na.employee_id AND na.tanggal_absen = '$tanggal_hari_ini'
                  WHERE 
                      fa.id IS NULL  -- Filter: Yang belum ada di tabel 'face_absensi'
                  AND 
                      na.id IS NULL  -- Filter: Dan juga belum ada di tabel 'not_absensi'
                  $filter_divisi_sql
                ";

                // 4. Kueri untuk Total Halaman (Pagination)
                $total_pages_sql = "SELECT COUNT(e.id) " . $base_sql;
                $result = mysqli_query($con, $total_pages_sql);
                $total_rows = mysqli_fetch_array($result)[0];
                $total_pages = ceil($total_rows / $no_of_records_per_page);

                // 5. Kueri untuk Data Karyawan (Tampilan)
                $sqlemp = "SELECT e.* " . $base_sql . " ORDER BY e.nama_pegawai ASC LIMIT $offset, $no_of_records_per_page";

                $query = mysqli_query($con, $sqlemp);
                
                if (mysqli_num_rows($query) == 0) {
                    echo '<tr><td colspan="6" class="text-center">Semua karyawan di divisi ini sudah absen atau izin.</td></tr>';
                }

                $noe = $offset + 1; // Penomoran baris
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

        <div class="box-footer clearfix">
          <ul class="pagination pagination-sm no-margin pull-right">
              <li><a href="?pageno=1">First</a></li>
              <li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>">
                  <a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pageno=".($pageno - 1); } ?>">Prev</a>
              </li>
              <?php
                // Loop pagination
                for ($i=1; $i<=$total_pages; $i++){
                    if($pageno == $i){ $cls="active"; } else { $cls=""; }
                    echo "<li class='{$cls}'><a href='?pageno={$i}'>{$i}</a></li>";
                }
              ?>
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
<?php include('scriptjs.php'); ?>
</body>
</html>