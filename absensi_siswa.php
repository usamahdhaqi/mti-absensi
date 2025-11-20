<?php
session_start();

// Cek Login Apakah Sudah Login atau Belum
if (!isset($_SESSION['nama_log'])){
// Jika Tidak Arahkan Kembali ke Halaman Login
  header("location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MTI Absensi | Data Absensi Siswa</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <?php
  include('scriptcss.php')
  ?>
  <?php
  include('config/db.php')
  ?>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
<?php
include('header.php');
 ?>

<?php
include('sidebar.php');
 ?>

<?php
if(isset($_POST['btn-submit'])){
  $_SESSION['valuedivisi'] = $_POST['valuedivisi'];
  $_SESSION['from'] = $_POST['from'];
  $_SESSION['to'] = $_POST['to'];
}
 ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Data Absensi
        <small>Histori Absensi Siswa</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Absensi</li>
      </ol>
    </section>

    <section class="content">

      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-filter"></i> Filter Data Absensi</h3>
        </div>
        <div class="box-body">
          <form action="absensi_siswa.php" method="post">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label>Pilih Filter Divisi</label>
                  <select name="valuedivisi" class="form-control">
                      <option name="divisi" value="All">All</option>
                      <?php
                        $divisisql="SELECT DISTINCT kelas FROM siswa WHERE 1 ORDER BY kelas";
                        $query2 = $con->query($divisisql);
                        while ($row = $query2->fetch_assoc()) {
                          $div=$row['kelas'];
                          $selected = (isset($_SESSION['valuedivisi']) && $_SESSION['valuedivisi'] == $div) ? 'selected' : '';
                          echo "<option value='". $div."' $selected>". $div. "</option>\n";
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
                  <label>&nbsp;</label> <input type="submit" name="btn-submit" value="Search" class="btn btn-primary btn-block" style="background-color: var(--color-secondary-green); border: none;">
                </div>
              </div>
            </div>
          </form>
        </div>
      </div> <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-table"></i> Data Absensi Peserta Didik</h3>
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
                      <th>Nama Peserta Didik</th>
                      <th>NIS</th>
                      <th>Waktu Masuk</th>
                      <th>Waktu Keluar</th>
                      <th>Telat</th>
                      <th>Kamera</th>
                      <th>Status</th>
                      <th>Divisi</th>
                      <th>Foto Kedatangan</th>
                      <th>Foto Kepulangan</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php

                   if (isset($_GET['pageno'])) {
                        $pageno = $_GET['pageno'];
                    } else {
                        $pageno = 1;
                    }
                   $no_of_records_per_page = 25;
                   $offset = ($pageno-1) * $no_of_records_per_page;
                   
                   // Base query
                   $base_sql = "FROM `absensi_siswa` fa JOIN `siswa` e ON fa.nama_pegawai=e.nama_siswa WHERE 1 ";
                    if (isset($_SESSION['valuedivisi']) && strlen($_SESSION['valuedivisi'])>=1) {
                        if ($_SESSION['valuedivisi']!='All') {
                        $div = mysqli_real_escape_string($con, $_SESSION['valuedivisi']);
                        $base_sql .= "AND e.kelas='$div' "; 
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
                    $sqlemp = "SELECT fa.*, e.kelas, e.nis " . $base_sql; 
                    $sqlemp .= "ORDER BY fa.waktu_masuk DESC LIMIT $offset, $no_of_records_per_page";
                    
                    $query = mysqli_query($con, $sqlemp);
                    $no = 1; $noe = $offset+1;
                    while ($row = mysqli_fetch_assoc($query)) {
                        echo '<tr>';
                        echo '<td>'. $noe++ . '</td>';
                        echo '<td>'. htmlspecialchars($row['nama_siswa']) . '</td>';
                        echo '<td>'. htmlspecialchars($row['nis']) . '</td>'; 
                        echo '<td>'. $row['waktu_masuk'] . '</td>';
                        echo '<td>'. $row['waktu_keluar'] . '</td>';
                        echo '<td>'. $row['selisih_waktu'] . '</td>';
                        echo '<td>'. htmlspecialchars($row['kamera']) . '</td>';
                        echo '<td>'. htmlspecialchars($row['note']) . '</td>';
                        echo '<td>'. htmlspecialchars($row['kelas']) . '</td>';
                        
                        // GANTI FOLDER
                        $foto_masuk = "hasil_absensi_siswa/". $row['foto_masuk'];
                        echo '<td>';
                        if ($row['foto_masuk'] && file_exists($foto_masuk)) {
                        echo '<img style="width:80px; height:auto; cursor:pointer;" id="'.$no++.'" data-toggle="modal" data-target="#myModal" src="'.$foto_masuk.'" alt="'. htmlspecialchars($row['nama_siswa']) . '" />';
                        } else { echo 'N/A'; }
                        echo '</td>';

                        // FOLDER
                        $foto_keluar = "hasil_absensi_siswa/". $row['foto_keluar'];
                        echo '<td>';
                        if ($row['foto_keluar'] && file_exists($foto_keluar)) {
                        echo '<img style="width:80px; height:auto; cursor:pointer;" id="'.$no++.'" data-toggle="modal" data-target="#myModal" src="'.$foto_keluar.'" alt="'. htmlspecialchars($row['nama_siswa']) . '" />';
                        } else { echo 'N/A'; }
                        echo '</td>';
                        echo '</tr>';
                    }
                ?>
                  </tbody>
            </table>
          </div> </div> <div class="box-footer clearfix">
          <ul class="pagination pagination-sm no-margin pull-right">
              <li><a href="?pageno=1">First</a></li>
              <li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>">
                  <a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pageno=".($pageno - 1); } ?>">Prev</a>
              </li>
              <?php
              // Sederhanakan loop pagination
              $max_pages_to_show = 10;
              $start_page = max(1, $pageno - floor($max_pages_to_show / 2));
              $end_page = min($total_pages, $start_page + $max_pages_to_show - 1);
              if ($end_page - $start_page < $max_pages_to_show - 1) {
                  $start_page = max(1, $end_page - $max_pages_to_show + 1);
              }

              if ($start_page > 1) echo '<li><a>...</a></li>';
              for($i = $start_page; $i<=$end_page; $i++) {
                  $active = ($i == $pageno) ? 'class="active"' : '';
                  echo "<li $active><a href='?pageno=$i'>$i</a></li>";
              }
              if ($end_page < $total_pages) echo '<li><a>...</a></li>';
              ?>
              <li class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
                  <a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?pageno=".($pageno + 1); } ?>">Next</a>
              </li>
              <li><a href="?pageno=<?php echo $total_pages; ?>">Last</a></li>
          </ul>
        </div> </div> <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <img class="img-responsive" src="" style="width:100%;" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
      </div>

    </section>
    </div>
  <?php
  include('footer.php');
   ?>

</div>
<script>
// Skrip untuk Modal Gambar
jQuery(document).ready(function () {
  jQuery('#myModal').on('show.bs.modal', function (e) {
      var image = jQuery(e.relatedTarget).attr('src');
      jQuery(".img-responsive").attr("src", image);
  });
});

// Skrip Datepicker (Perbaikan)
$( function() {
  $( "#datepicker" ).datepicker({
    dateFormat: 'yy-mm-dd' // format dipindahkan ke dalam object
  });
} );

$( function() {
  $( "#datepicker2" ).datepicker({
    dateFormat: 'yy-mm-dd' // format dipindahkan ke dalam object
  });
} );
</script>

</body>
</html>