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
  <title>MTI Absensi | Karyawan</title>
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
   $_SESSION['valuedivisi'] = $_POST['valuedivisi'];
   // Hapus session 'from' dan 'to' karena tidak ada di form ini
   unset($_SESSION['from']);
   unset($_SESSION['to']);
 }
  ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Data Karyawan
        <small>Semua Karyawan Terdaftar</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Karyawan</li>
      </ol>
    </section>

    <section class="content">

      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-filter"></i> Filter Data Karyawan</h3>
        </div>
        <div class="box-body">
          <form action="employee.php" method="post">
            <div class="row">
              <div class="col-md-4 col-sm-6 col-xs-12">
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
          <h3 class="box-title"><i class="fa fa-table"></i> Data Karyawan</h3>
          <div class="box-tools pull-right" style="top: 10px";>
            <button type="button" class="btn btn-success btn-sm" style="background-color: var(--color-secondary-green); border: none; margin-right: 7px;" data-toggle="modal" data-target="#tambahKaryawanModal">
              <i class="fa fa-plus"></i><span class="btn-text-mobile-hide"> Tambah Karyawan</span>
            </button>
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
                      <th>Nama Pegawai</th>
                      <th>NIP</th>
                      <th>Email</th>
                      <th>No HP</th>
                      <th>Divisi</th>
                      <th>Jabatan</th>
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
                   $base_sql = "FROM `employee` WHERE 1 ";

                   if (isset($_SESSION['valuedivisi']) && strlen($_SESSION['valuedivisi'])>=1) {
                        if ($_SESSION['valuedivisi']!='All') {
                          $div = mysqli_real_escape_string($con, $_SESSION['valuedivisi']); // Keamanan
                          $base_sql .= "AND divisi='$div' ";
                        }
                    }
                   
                   // Total pages query
                   $total_pages_sql = "SELECT COUNT(*) " . $base_sql;
                   $result = $con->query($total_pages_sql);
                   $total_rows = mysqli_fetch_array($result)[0];
                   $total_pages = ceil($total_rows / $no_of_records_per_page);

                   // Data query
                   $sqlemp = "SELECT * " . $base_sql;
                   $sqlemp .= "ORDER BY divisi,nama_pegawai LIMIT $offset, $no_of_records_per_page";

                   $query = $con->query($sqlemp);
                   $noe = $offset+1;
                   while ($row = $query->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>'. $noe++ . '</td>';
                            echo '<td>'. htmlspecialchars($row['nama_pegawai']) . '</td>';
                            echo '<td>'. htmlspecialchars($row['id_pegawai']) . '</td>';
                            echo '<td>'. htmlspecialchars($row['email']) . '</td>';
                            echo '<td>'. htmlspecialchars($row['no_hp']) . '</td>';
                            echo '<td>'. htmlspecialchars($row['divisi']) . '</td>';
                            echo '<td>'. htmlspecialchars($row['jabatan']) . '</td>';
                            echo '<td>'. htmlspecialchars($row['warning1']) . '</td>';
                            echo '<td>'. htmlspecialchars($row['warning2']) . '</td>';
                            echo '<td>'. htmlspecialchars($row['warning3']) . '</td>';
                            echo '<td>
                              <button type="button" class="btn btn-warning btn-sm btn-edit" 
                                      data-toggle="modal" 
                                      data-target="#editKaryawanModal" 
                                      data-id="' . $row['id'] . '" 
                                      data-idpegawai="' . htmlspecialchars($row['id_pegawai']) . '" 
                                      data-nama="' . htmlspecialchars($row['nama_pegawai']) . '"
                                      data-fotoprofil="' . htmlspecialchars($row['foto_profil']) . '"
                                      data-w1="' . htmlspecialchars($row['warning1']) . '"
                                      data-w2="' . htmlspecialchars($row['warning2']) . '"
                                      data-w3="' . htmlspecialchars($row['warning3']) . '">
                                  <i class="fa fa-edit"></i> Edit / Upload Foto
                              </button>
                            </td>';
                            echo '</tr>';
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

<div class="modal fade" id="tambahKaryawanModal" tabindex="-1" role="dialog" aria-labelledby="tambahKaryawanModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
      <form action="proses_tambah_karyawan.php" method="POST">
      
        <div class="modal-header">
          <h5 class="modal-title" id="tambahKaryawanModalLabel">Tambah Karyawan Baru</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        
        <div class="modal-body">
          <div class="form-group">
            <label for="id_pegawai">ID Pegawai (NIP)</label>
            <input type="text" class="form-control" id="id_pegawai" name="id_pegawai" placeholder="Contoh: 1001" required>
          </div>
          <div class="form-group">
            <label for="nama_pegawai">Nama Lengkap</label>
            <input type="text" class="form-control" id="nama_pegawai" name="nama_pegawai" placeholder="Contoh: Budi Santoso" required>
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
            <label for="divisi">Divisi</label>
            <input type="text" class="form-control" id="divisi" name="divisi" placeholder="Contoh: IT">
          </div>
          <div class="form-group">
            <label for="jabatan">Jabatan</label>
            <input type="text" class="form-control" id="jabatan" name="jabatan" placeholder="Contoh: Staff">
          </div>
          <div class="form-group">
            <label for="id_telegram">ID Telegram (Optional)</label>
            <input type="text" class="form-control" id="id_telegram" name="id_telegram" placeholder="Contoh: 12345678">
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Karyawan</button>
        </div>
        
      </form>
      </div>
  </div>
</div>

<div class="modal fade" id="editKaryawanModal" tabindex="-1" role="dialog" aria-labelledby="editKaryawanModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
      <form action="proses_edit_karyawan.php" method="POST" enctype="multipart/form-data">
      
        <div class="modal-header">
          <h5 class="modal-title" id="editKaryawanModalLabel">Edit Karyawan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        
        <div class="modal-body">
          <input type="hidden" id="edit_id_karyawan" name="id_karyawan">
          
          <div class="form-group">
            <label>ID Pegawai (NIP)</label>
            <input type="text" class="form-control" id="edit_id_pegawai" name="id_pegawai" readonly>
          </div>
          <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" class="form-control" id="edit_nama_pegawai" name="nama_pegawai" required>
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
        var id_pegawai = $(this).data('idpegawai');
        var nama = $(this).data('nama');
        var foto = $(this).data('fotoprofil');
        var w1 = $(this).data('w1'); // Pastikan tombol edit di tabel punya atribut data-w1="..."
        var w2 = $(this).data('w2');
        var w3 = $(this).data('w3');

        // Masukkan data ke dalam form di modal
        $('#edit_id_karyawan').val(id); // ID unik
        $('#edit_id_pegawai').val(id_pegawai);
        $('#edit_nama_pegawai').val(nama);
        $('input[name="warning1"]').prop('checked', (w1 != '' && w1 != null));
        $('input[name="warning2"]').prop('checked', (w2 != '' && w2 != null));
        $('input[name="warning3"]').prop('checked', (w3 != '' && w3 != null));
        
        // Atur gambar profil yang ada
        if(foto) {
            $('#gambar_profil_sekarang').attr('src', 'foto_profil_karyawan/' + foto);
        } else {
            // Tampilkan gambar default jika tidak ada
            $('#gambar_profil_sekarang').attr('src', 'foto_profil_karyawan/default.jpg'); 
        }
    });
});
</script>

</body>
</html>