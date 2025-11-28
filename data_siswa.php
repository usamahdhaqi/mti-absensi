<?php
session_start();

$notif_status = null;
$notif_message = '';

// Cek apakah ada notifikasi dari session
if (isset($_SESSION['notif_status'])) {
    $notif_status = $_SESSION['notif_status'];
    $notif_message = $_SESSION['notif_message'];
    
    // HAPUS session agar notifikasi tidak muncul lagi saat refresh
    unset($_SESSION['notif_status']);
    unset($_SESSION['notif_message']);
}

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
  <title>MTI Absensi | Peserta Didik</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <?php
  include('scriptcss.php');
  ?>
  <?php
  include('config/db.php');
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
   $_SESSION['valuekelas'] = $_POST['valuekelas'];
   // Hapus session 'from' dan 'to' karena tidak ada di form ini
   unset($_SESSION['from']);
   unset($_SESSION['to']);
 }
  ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Data Siswa
        <small>Semua Siswa Terdaftar</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Siswa</li>
      </ol>
    </section>

    <section class="content">

      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-filter"></i> Filter Data Siswa</h3>
        </div>
        <div class="box-body">
          <form action="siswa.php" method="post">
            <div class="row">
              <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="form-group">
                  <label>Pilih Filter Kelas</label>
                  <select name="valuekelas" class="form-control">
                      <option name="kelas" value="All">All</option>
                      <?php
                        $kelassql="SELECT DISTINCT kelas FROM siswa WHERE 1 ORDER BY kelas";
                        $query2 = $con->query($kelassql);
                        while ($row = $query2->fetch_assoc()) {
                          $div=$row['kelas'];
                          $selected = (isset($_SESSION['valuekelas']) && $_SESSION['valuekelas'] == $div) ? 'selected' : '';
                          echo "<option name='kelas' value='". $div."' $selected>" . $div. "</option>\n";
                        }
                       ?>
                  </select>
                </div>
              </div>
              <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                  <label>&nbsp;</label> <input type="submit" name="btn-submit" value="Search" class="btn btn-primary btn-block" style="background-color: var(--color-secondary-green); border: none;">
                </div>
              </div>
              </div>
          </form>
        </div>
      </div> <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-table"></i> Data Peserta Didik</h3>
          <div class="box-tools pull-right" style="top: 10px";>
            <button type="button" class="btn btn-success btn-sm" style="background-color: var(--color-secondary-green); border: none; margin-right: 7px;" data-toggle="modal" data-target="#tambahsiswaModal">
              <i class="fa fa-plus"></i><span class="btn-text-mobile-hide"> Tambah Siswa</span>
            </button>
            <a target="_blank" href="export_siswa.php" class="btn btn-success btn-sm" style="background-color: var(--color-secondary-green); border: none;">
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
                      <th>Email</th>
                      <th>No HP</th>
                      <th>Reg/PKL</th>
                      <th>jurusan</th>
                      <th>Warning 1</th>
                      <th>Warning 2</th>
                      <th>Warning 3</th>
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
                   $base_sql = "FROM `siswa` WHERE 1 ";

                   if (isset($_SESSION['valuekelas']) && strlen($_SESSION['valuekelas'])>=1) {
                        if ($_SESSION['valuekelas']!='All') {
                          $div = mysqli_real_escape_string($con, $_SESSION['valuekelas']); // Keamanan
                          $base_sql .= "AND kelas='$div' ";
                        }
                    }
                   
                   // Total pages query
                   $total_pages_sql = "SELECT COUNT(*) " . $base_sql;
                   $result = $con->query($total_pages_sql);
                   $total_rows = mysqli_fetch_array($result)[0];
                   $total_pages = ceil($total_rows / $no_of_records_per_page);

                   // Data query
                   $sqlemp = "SELECT * " . $base_sql;
                   $sqlemp .= "ORDER BY kelas,nama_siswa LIMIT $offset, $no_of_records_per_page";

                   $query = $con->query($sqlemp);
                   $noe = $offset+1;
                   while ($row = $query->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>'. $noe++ . '</td>';
                        echo '<td>'. htmlspecialchars($row['nama_siswa']) . '</td>';
                        echo '<td>'. htmlspecialchars($row['nis']) . '</td>';
                        echo '<td>'. htmlspecialchars($row['email']) . '</td>';
                        echo '<td>'. htmlspecialchars($row['no_hp']) . '</td>';
                        echo '<td>'. htmlspecialchars($row['kelas']) . '</td>';
                        echo '<td>'. htmlspecialchars($row['jurusan']) . '</td>';
                        echo '<td>'. htmlspecialchars($row['warning1']) . '</td>';
                        echo '<td>'. htmlspecialchars($row['warning2']) . '</td>';
                        echo '<td>'. htmlspecialchars($row['warning3']) . '</td>';
                        
                        // --- BAGIAN PERBAIKAN TOMBOL EDIT ---
                        echo '<td>
                                <button type="button" class="btn btn-warning btn-sm btn-edit" 
                                        data-toggle="modal" 
                                        data-target="#editsiswaModal" 
                                        data-id="' . $row['id'] . '" 
                                        data-nis="' . htmlspecialchars($row['nis']) . '" 
                                        data-nama="' . htmlspecialchars($row['nama_siswa']) . '"
                                        data-fotoprofil="' . htmlspecialchars($row['foto_profil']) . '"
                                        data-w1="' . htmlspecialchars($row['warning1']) . '" 
                                        data-w2="' . htmlspecialchars($row['warning2']) . '" 
                                        data-w3="' . htmlspecialchars($row['warning3']) . '">
                                    <i class="fa fa-edit"></i> Edit / Upload Foto
                                </button>
                            </td>';
                        // ------------------------------------
                        
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
        </div> </div> </section>
    </div>
  <?php
  include('footer.php');
   ?>

</div>

<div class="modal fade" id="tambahsiswaModal" tabindex="-1" role="dialog" aria-labelledby="tambahsiswaModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
      <form action="proses_tambah_siswa.php" method="POST">
      
        <div class="modal-header">
          <h5 class="modal-title" id="tambahsiswaModalLabel">Tambah siswa Baru</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        
        <div class="modal-body">
          <div class="form-group">
            <label for="nis">ID Peserta Didik (NIS)</label>
            <input type="text" class="form-control" id="nis" name="nis" placeholder="Contoh: 1001" required>
          </div>
          <div class="form-group">
            <label for="nama_siswa">Nama Lengkap</label>
            <input type="text" class="form-control" id="nama_siswa" name="nama_siswa" placeholder="Contoh: Budi Santoso" required>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Contoh: budi@email.com">
          </div>
          <div class="form-group">
            <label for="no_hp">No. HP</label>
            <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="Contoh: 08123456...">
          </div>
          <div class="form-group">
            <label for="kelas">Reg/PKL</label>
            <input type="text" class="form-control" id="kelas" name="kelas" placeholder="Contoh: Reguler">
          </div>
          <div class="form-group">
            <label for="jurusan">jurusan</label>
            <input type="text" class="form-control" id="jurusan" name="jurusan" placeholder="Contoh: Administrasi Profesional">
          </div>
          <div class="form-group">
            <label for="id_telegram">ID Telegram (Optional)</label>
            <input type="text" class="form-control" id="id_telegram" name="id_telegram" placeholder="Contoh: 12345678">
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Siswa</button>
        </div>
        
      </form>
      </div>
  </div>
</div>

<div class="modal fade" id="editsiswaModal" tabindex="-1" role="dialog" aria-labelledby="editsiswaModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
      <form action="proses_edit_siswa.php" method="POST" enctype="multipart/form-data">
      
        <div class="modal-header">
          <h5 class="modal-title" id="editsiswaModalLabel">Edit siswa</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        
        <div class="modal-body">
          <input type="hidden" id="edit_id_siswa" name="id_siswa">
          
          <div class="form-group">
            <label>ID Peserta Didik (NIS)</label>
            <input type="text" class="form-control" id="edit_nis" name="nis" readonly>
          </div>
          <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" class="form-control" id="edit_nama_siswa" name="nama_siswa" required>
          </div>
          
          <hr>
          
          <div class="form-group">
            <label>Foto Profil (Foto Master)</label>
            <p>Foto saat ini:</p>
            <img src="" id="gambar_profil_sekarang" class="img-thumbnail" width="150px" alt="Foto Profil">
            <br><br>
            <label>Upload Foto Baru (Opsional)</label>
            <input type="file" class="form-control-file" name="foto_profil_baru">
            <small class="form-text text-muted">Hanya upload jika ingin mengganti foto master.</small>
          </div>
        </div>
        
        <hr>
        <h5 class="text-warning"><i class="fa fa-exclamation-triangle"></i> Status Peringatan (SP)</h5>
        <div class="form-group">
            <label>Pilih Tingkat Peringatan:</label>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="warning1" value="SP1"> <strong>Warning 1</strong>
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="warning2" value="SP2"> <strong>Warning 2</strong>
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="warning3" value="SP3"> <strong>Warning 3</strong>
                </label>
            </div>
            <small class="text-muted">*Centang untuk mengaktifkan SP. Hapus centang untuk mencabut SP.</small>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
        
      </form>
      </div>
  </div>
</div>

<div class="modal fade" id="notifSuksesModal" tabindex="-1" role="dialog" aria-labelledby="notifSuksesModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="notifSuksesModalLabel">Sukses!</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="text-center">
            <i class="fa fa-check-circle fa-5x text-success mb-3"></i>
            <h4 id="notifSuksesMessage"></h4>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="notifErrorModal" tabindex="-1" role="dialog" aria-labelledby="notifErrorModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="notifErrorModalLabel">Terjadi Masalah!</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <div class="text-center">
            <i class="fa fa-times-circle fa-5x text-danger mb-3"></i>
            <h5 id="notifErrorMessage" class="text-dark"></h5>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    
    // Ambil status notif dari variabel PHP
    var status = '<?php echo $notif_status; ?>';
    var message = '<?php echo addslashes($notif_message); ?>'; // addslashes untuk keamanan

    if (status === 'sukses') {
        // Masukkan pesan ke modal sukses
        $('#notifSuksesMessage').text(message);
        // Tampilkan modal sukses
        $('#notifSuksesModal').modal('show');
        
    } else if (status === 'error') {
        // Masukkan pesan ke modal error
        $('#notifErrorMessage').text(message);
        // Tampilkan modal error
        $('#notifErrorModal').modal('show');
    }
    
});
</script>

<script>
$(document).ready(function() {
    // JavaScript untuk memicu Modal EDIT
    $('.btn-edit').on('click', function() {
        // Ambil data dari atribut 'data-*' tombol
        var id = $(this).data('id');
        var nis = $(this).data('nis'); // Perhatikan di PHP saya ubah jadi data-nis (bukan data-idsiswa agar konsisten)
        var nama = $(this).data('nama');
        var foto = $(this).data('fotoprofil');
        
        // Ambil data warning yang sudah ditambahkan di PHP
        var w1 = $(this).data('w1'); 
        var w2 = $(this).data('w2');
        var w3 = $(this).data('w3');

        // Masukkan data ke dalam form di modal
        $('#edit_id_siswa').val(id); // ID (Primary Key) masuk ke hidden input
        $('#edit_nis').val(nis);     // NIS masuk ke input text readonly
        $('#edit_nama_siswa').val(nama);

        // Logic Checkbox: Jika data warning tidak kosong, maka centang
        $('input[name="warning1"]').prop('checked', (w1 != '' && w1 != null));
        $('input[name="warning2"]').prop('checked', (w2 != '' && w2 != null));
        $('input[name="warning3"]').prop('checked', (w3 != '' && w3 != null));

        // Atur gambar profil
        if(foto) {
            $('#gambar_profil_sekarang').attr('src', 'foto_profil_siswa/' + foto);
        } else {
            $('#gambar_profil_sekarang').attr('src', 'foto_profil_siswa/default.jpg'); 
        }
    });
});
</script>

</body>
</html>