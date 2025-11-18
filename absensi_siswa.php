<?php
// Start the session
session_start();
// (Kode cek login Anda)
if (!isset($_SESSION['nama_log'])){ header("location: index.php"); }
?>
<!DOCTYPE html>
<html>
<head>
  <title>MTI Absensi | Data Absensi Siswa</title>
  <?php include('scriptcss.php'); include('config/db.php'); ?>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include('header.php'); include('sidebar.php'); ?>
  <?php
  // (Kode filter session Anda)
  if(isset($_POST['btn-submit'])){
    $_SESSION['valuedivisi'] = $_POST['valuedivisi'];
    $_SESSION['from'] = $_POST['from'];
    $_SESSION['to'] = $_POST['to'];
  }
  ?>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Data Absensi Peserta Didik</h1>
    </section>
    <section class="content">
      <div class="box">
        <div class="box-body">
          <form action="absensi_siswa.php" method="post">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label>Pilih Filter Kelas</label>
                  <select name="valuedivisi" class="form-control">
                      <option value="All">All</option>
                      <?php
                        // Ambil data KELAS dari tabel SISWA
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
                <div class="form-group">
                  <label>From:</label>
                  <input type="text" id="datepicker" name="from" class="form-control" value="<?php echo isset($_SESSION['from']) ? $_SESSION['from'] : ''; ?>" autocomplete="off">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>To:</label>
                  <input type="text" id="datepicker2" name="to" class="form-control" value="<?php echo isset($_SESSION['to']) ? $_SESSION['to'] : ''; ?>" autocomplete="off">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>&nbsp;</label> <input type="submit" name="btn-submit" value="Search" class="btn btn-primary btn-block">
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
                  <th>No</th> <th>Nama Siswa</th> <th>NIS</th> <th>Waktu Masuk</th> <th>Waktu Keluar</th> <th>Telat</th> <th>Kamera</th> <th>Status</th> <th>Kelas</th> <th>Foto Masuk</th> <th>Foto Pulang</th>
                </tr>
              </thead>
              <tbody>
              <?php
                // (Logika Pagination sama)
                if (isset($_GET['pageno'])) { $pageno = $_GET['pageno']; } else { $pageno = 1; }
                $no_of_records_per_page = 25;
                $offset = ($pageno-1) * $no_of_records_per_page;
                   
                // Ganti kueri
                $base_sql = "FROM `absensi_siswa` fa JOIN `siswa` e ON fa.nama_pegawai=e.nama_pegawai WHERE 1 "; // GANTI TABEL
                if (isset($_SESSION['valuedivisi']) && strlen($_SESSION['valuedivisi'])>=1) {
                    if ($_SESSION['valuedivisi']!='All') {
                      $div = mysqli_real_escape_string($con, $_SESSION['valuedivisi']);
                      $base_sql .= "AND e.kelas='$div' "; // GANTI DIVISI -> KELAS
                    }
                }
                if (isset($_SESSION['from']) && strlen($_SESSION['from'])>5) {
                   $date = new DateTime($_SESSION['from']); $dt1=$date->format('Y-m-d');
                   $base_sql .= "AND DATE(fa.waktu_masuk)>= '$dt1' " ;
                }
                if (isset($_SESSION['to']) && strlen($_SESSION['to'])>5) {
                   $date = new DateTime($_SESSION['to']); $dt2=$date->format('Y-m-d');
                   $base_sql .= "AND DATE(fa.waktu_masuk)<= '$dt2' " ;
                }
                $total_pages_sql = "SELECT COUNT(*) " . $base_sql;
                $result = mysqli_query($con, $total_pages_sql);
                $total_rows = mysqli_fetch_array($result)[0];
                $total_pages = ceil($total_rows / $no_of_records_per_page);
                $sqlemp = "SELECT fa.*, e.kelas, e.nis " . $base_sql; // GANTI e.divisi -> e.kelas
                $sqlemp .= "ORDER BY fa.waktu_masuk DESC LIMIT $offset, $no_of_records_per_page";
                   
                $query = mysqli_query($con, $sqlemp);
                $no = 1; $noe = $offset+1;
                while ($row = mysqli_fetch_assoc($query)) {
                    echo '<tr>';
                    echo '<td>'. $noe++ . '</td>';
                    echo '<td>'. htmlspecialchars($row['nama_pegawai']) . '</td>';
                    echo '<td>'. htmlspecialchars($row['nis']) . '</td>'; // GANTI employee_id -> nis
                    echo '<td>'. $row['waktu_masuk'] . '</td>';
                    echo '<td>'. $row['waktu_keluar'] . '</td>';
                    echo '<td>'. $row['selisih_waktu'] . '</td>';
                    echo '<td>'. htmlspecialchars($row['kamera']) . '</td>';
                    echo '<td>'. htmlspecialchars($row['note']) . '</td>';
                    echo '<td>'. htmlspecialchars($row['kelas']) . '</td>'; // GANTI divisi -> kelas
                    
                    // GANTI FOLDER
                    $foto_masuk = "hasil_absensi_siswa/". $row['foto_masuk'];
                    echo '<td>';
                    if ($row['foto_masuk'] && file_exists($foto_masuk)) {
                      echo '<img style="width:80px; height:auto; cursor:pointer;" id="'.$no++.'" data-toggle="modal" data-target="#myModal" src="'.$foto_masuk.'" alt="'. htmlspecialchars($row['nama_pegawai']) . '" />';
                    } else { echo 'N/A'; }
                    echo '</td>';

                    // GANTI FOLDER
                    $foto_keluar = "hasil_absensi_siswa/". $row['foto_keluar'];
                    echo '<td>';
                    if ($row['foto_keluar'] && file_exists($foto_keluar)) {
                      echo '<img style="width:80px; height:auto; cursor:pointer;" id="'.$no++.'" data-toggle="modal" data-target="#myModal" src="'.$foto_keluar.'" alt="'. htmlspecialchars($row['nama_pegawai']) . '" />';
                    } else { echo 'N/A'; }
                    echo '</td>';
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