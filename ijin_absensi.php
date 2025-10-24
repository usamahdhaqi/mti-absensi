<?php
// Start the session
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
  <title>MTI Absensi | Ijin Absensi</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <?php
  include('scriptcss.php')
  ?>
  <style>
  /* Style ini spesifik untuk modal PDF, jadi biarkan saja */
  .iframe-container {
    padding-bottom: 60%;
    padding-top: 30px; height: 0; overflow: hidden;
    }
    .iframe-container iframe,
    .iframe-container object,
    .iframe-container embed{
    position: absolute;
    top: 0;
    left: 0;
    width: 100%; /* Ubah dari 200% ke 100% */
    height: 100%; /* Ubah dari 200% ke 100% */
    }
    .modal.in .modal-dialog {
      transform: none;
    }
    /* Tambahkan style untuk PDF modal agar lebih besar */
    #pdfModal .modal-dialog {
        width: 80%; /* Atur lebar modal */
        height: 90vh; /* Atur tinggi modal */
    }
    #pdfModal .modal-content {
        height: 100%;
    }
    #pdfModal .modal-body {
        height: calc(100% - 120px); /* Sesuaikan tinggi body */
    }
  </style>
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
        Data Ijin Absensi
        <small>Semua pengajuan ijin karyawan</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Ijin Absensi</li>
      </ol>
    </section>

    <section class="content">

      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-filter"></i> Filter Data Ijin</h3>
        </div>
        <div class="box-body">
          <form action="ijin_absensi.php" method="post">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label>Pilih Filter Divisi</label>
                  <select name="valuedivisi" class="form-control">
                      <option name="divisi" value="All">All</option>
                      <?php
                        $divisisql="SELECT DISTINCT divisi FROM employee WHERE 1 ORDER BY divisi";
                        $query2 = $con->query($divisisql);
                        while ($row = $query2->fetch_assoc()) {
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
          <h3 class="box-title"><i class="fa fa-table"></i> Data Pengajuan Ijin</h3>
          <div class="box-tools pull-right">
            <a target="_blank" href="export_ijinabsensi.php" class="btn btn-success btn-sm" style="background-color: var(--color-secondary-green); border: none;">
              <i class="fa fa-file-excel-o"></i> EXPORT KE EXCEL
            </a>
          </div>
        </div>
        <div class="box-body">
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Nama Pegawai</th>
                      <th>Ijin</th>
                      <th>Alasan</th>
                      <th>Tgl Ijin</th>
                      <th>Waktu Buat</th>
                      <th>Divisi</th>
                      <th>Atasan</th>
                      <th>Status</th>
                      <th>Alasan Status</th>
                      <th>Lampiran</th>
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
                   $base_sql = "FROM `ijin_absensi` JOIN employee ON ijin_absensi.nama_pegawai=employee.nama_pegawai WHERE 1 ";

                   if (isset($_SESSION['valuedivisi']) && strlen($_SESSION['valuedivisi'])>=1) {
                        if ($_SESSION['valuedivisi']!='All') {
                          $div = mysqli_real_escape_string($con, $_SESSION['valuedivisi']);
                          $base_sql .= "AND divisi='$div' ";
                        }
                    }

                    if (isset($_SESSION['from']) && strlen($_SESSION['from'])>5) {
                      $date = new DateTime($_SESSION['from']);
                      $dt1=$date->format('Y-m-d'); // To match MySQL date format
                      // CATATAN PENTING: Query Anda salah, menggunakan 'face_keamanan.waktu'. 
                      // Saya ganti ke 'ijin_absensi.tanggal_ijin'
                      $base_sql .= "AND DATE(ijin_absensi.tanggal_ijin)>= '$dt1' " ;
                    }

                    if (isset($_SESSION['to']) && strlen($_SESSION['to'])>5) {
                      $date = new DateTime($_SESSION['to']);
                      $dt2=$date->format('Y-m-d'); // To match MySQL date format
                      // Ganti ke 'ijin_absensi.tanggal_ijin'
                      $base_sql .= "AND DATE(ijin_absensi.tanggal_ijin)<= '$dt2' " ;
                    }

                   // Total pages query
                   $total_pages_sql = "SELECT COUNT(*) " . $base_sql;
                   $result = $con->query($total_pages_sql);
                   $total_rows = mysqli_fetch_array($result)[0];
                   $total_pages = ceil($total_rows / $no_of_records_per_page);

                   // Data query
                   $sqlemp = "SELECT *, employee.divisi " . $base_sql;
                   $sqlemp .= "ORDER BY ijin_absensi.waktu_buat_ijin DESC ";
                   $sqlemp .= "LIMIT $offset, $no_of_records_per_page";

                   $query = $con->query($sqlemp);
                   $no=1;
                   $noe=$offset+1;
                   while ($row = $query->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>'. $noe++ . '</td>';
                            echo '<td>'. htmlspecialchars($row['nama_pegawai']) . '</td>';
                            echo '<td>'. htmlspecialchars($row['ijin']) . '</td>';
                            echo '<td>'. htmlspecialchars($row['alasan_ijin']) . '</td>';
                            echo '<td>'. $row['tanggal_ijin'] . '</td>';
                            echo '<td>'. $row['waktu_buat_ijin'] . '</td>';
                            echo '<td>'. htmlspecialchars($row['divisi']) . '</td>';
                            echo '<td>'. htmlspecialchars($row['atasan']) . '</td>';
                            
                            // Pewarnaan Status
                            $status = $row['app'];
                            if ($status == 'Approve') {
                              echo '<td><span class="label label-success">Approve</span></td>';
                            } else if ($status == 'Disapprove') {
                              echo '<td><span class="label label-danger">Disapprove</span></td>';
                            } else {
                              echo '<td><span class="label label-warning">Pending</span></td>';
                            }

                            echo '<td>'. htmlspecialchars($row['alasan_app_dpp']) . '</td>';
                            
                            $lampiran_file = 'Lampiran/'. $row['lampiran'];
                            echo '<td>';
                            if ($row['lampiran'] && file_exists($lampiran_file)) {
                              if ($row['tipe_lampiran']=='Photo') {
                                echo '<img style="width:80px;height:auto; cursor:pointer;" id="'.$no++.'" data-toggle="modal" data-target="#myModal" src="'.$lampiran_file.'" alt="'. htmlspecialchars($row['nama_pegawai']) . '" />';
                              }
                              if ($row['tipe_lampiran']=='Document') {
                                echo '<a class="btn btn-info btn-xs view-pdf" href="'.$lampiran_file.'" alt="'. htmlspecialchars($row['nama_pegawai']) . '"><i class="fa fa-file-pdf-o"></i> View PDF</a> ';
                              }
                            } else {
                              echo 'N/A';
                            }
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
// Skrip Modal PDF (dari file asli Anda, sedikit dimodifikasi)
(function(a){a.createModal=function(b){defaults={title:"",message:"Your Message Goes Here!",closeButton:true,scrollable:false};var b=a.extend({},defaults,b);var c=(b.scrollable===true)?'style="max-height: 420px;overflow-y: auto;"':"";html='<div class="modal fade" id="pdfModal">';html+='<div class="modal-dialog">';html+='<div class="modal-content">';html+='<div class="modal-header">';html+='<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';if(b.title.length>0){html+='<h4 class="modal-title">'+b.title+"</h4>"}html+="</div>";html+='<div class="modal-body" '+c+">";html+=b.message;html+="</div>";html+='<div class="modal-footer">';if(b.closeButton===true){html+='<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>'}html+="</div>";html+="</div>";html+="</div>";html+="</div>";a("body").prepend(html);a("#pdfModal").modal().on("hidden.bs.modal",function(){a(this).remove()})}})(jQuery);

$(function(){
    $('.view-pdf').on('click',function(){
        var pdf_link = $(this).attr('href');
        // Gunakan <object> untuk kompatibilitas terbaik
        var iframe = '<div class="iframe-container"><object type="application/pdf" data="'+pdf_link+'" width="100%" height="100%">No Support</object></div>'
        $.createModal({
            title:'Lihat Dokumen PDF',
            message: iframe,
            closeButton:true,
            scrollable:true // Izinkan scroll
        });
        return false;
    });
})

// Skrip Modal FOTO
jQuery(document).ready(function () {
  jQuery('#myModal').on('show.bs.modal', function (e) {
      var image = jQuery(e.relatedTarget).attr('src');
      jQuery(".img-responsive").attr("src", image);
  });
});

// Skrip Datepicker (Perbaikan)
$( function() {
  $( "#datepicker" ).datepicker({
    dateFormat: 'yy-mm-dd'
  });
} );

$( function() {
  $( "#datepicker2" ).datepicker({
    dateFormat: 'yy-mm-dd'
  });
} );
</script>

</body>
</html>