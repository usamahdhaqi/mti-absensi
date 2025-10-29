<?php
// Start the session
session_start();
include 'config/db.php'; // Hubungkan ke DB

// Cek Login (ambil dari file Anda yang lain)
if (!isset($_SESSION['nama_log'])){
  header("location: index.php");
}
// Ambil nama admin yang sedang login
$admin_name = $_SESSION['nama_log'];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MTI Absensi | Persetujuan Izin</title>
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
        Manajemen Perizinan
        <small>Persetujuan Izin, Sakit, dan Cuti</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Perizinan</li>
      </ol>
    </section>

    <section class="content">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-table"></i> Data Pengajuan Izin</h3>
          <div class="box-tools pull-right" style="top: 10px";>
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
                  <th>Jenis Izin</th>
                  <th>Tanggal Izin</th>
                  <th>Keterangan</th>
                  <th>Lampiran</th>
                  <th>Tgl. Pengajuan</th>
                  <th>Status</th>
                  <th style="width: 100px;">Aksi</th>
                </tr>
              </thead>
              <tbody>
              <?php
                // Query untuk mengambil semua data izin, diurutkan dari yang terbaru
                $sql = "SELECT * FROM ijin_absensi ORDER BY waktu_buat_ijin DESC";
                $query = mysqli_query($con, $sql);
                
                if (mysqli_num_rows($query) == 0) {
                    echo '<tr><td colspan="9" class="text-center">Belum ada data pengajuan izin.</td></tr>';
                }
                
                $no = 1;
                while ($row = mysqli_fetch_assoc($query)) {
                    $tanggal_ijin_formatted = date('d M Y', strtotime($row['tanggal_ijin']));
                    
                    echo '<tr>';
                    echo '<td>'. $no++ . '</td>';
                    echo '<td>'. htmlspecialchars($row['nama_pegawai']) . '</td>';
                    echo '<td>'. htmlspecialchars($row['ijin']) . '</td>';
                    echo '<td>'. $tanggal_ijin_formatted . '</td>';
                    echo '<td>'. htmlspecialchars($row['alasan_ijin']) . '</td>';
                    echo '<td><a href="Lampiran/'. htmlspecialchars($row['lampiran']) .'" target="_blank" class="btn btn-info btn-xs"><i class="fa fa-eye"></i> Lihat</a></td>';
                    echo '<td>'. date('d M Y H:i', strtotime($row['waktu_buat_ijin'])) . '</td>';
                    
                    $status = $row['app'];
                    $badge_class = 'bg-yellow'; // Default (Pending)
                    if ($status == 'Approved') { $badge_class = 'bg-green'; } 
                    else if ($status == 'Rejected') { $badge_class = 'bg-red'; }
                    echo '<td><span class="label '. $badge_class .'">'. $status .'</span></td>';
                    
                    // =============================================
                    // === PERUBAHAN KOLOM AKSI DI SINI ===
                    // =============================================
                    echo '<td>';
                    if ($row['app'] == 'Pending') {
                        // Tombol Biru tunggal untuk memicu modal
                        echo '<button type_button" class="btn btn-primary btn-xs btn-aksi" 
                                  data-toggle="modal" 
                                  data-target="#approvalModal"
                                  data-id_ijin="'. $row['id'] .'"
                                  data-nama="'. htmlspecialchars($row['nama_pegawai']) .'"
                                  data-tanggal="'. $tanggal_ijin_formatted .'"
                                  data-alasan="'. htmlspecialchars($row['alasan_ijin']) .'">
                                <i class="fa fa-gavel"></i> Tindakan
                              </button>';
                    } else {
                        // Jika sudah diapprove/reject, tampilkan nama approver-nya
                        echo 'by: '. htmlspecialchars($row['app_by']);
                        // Tampilkan alasan jika ditolak
                        if ($status == 'Rejected' && !empty($row['alasan_app_dpp'])) {
                           echo '<br><small><i>('. htmlspecialchars($row['alasan_app_dpp']) .')</i></small>';
                        }
                    }
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

<div class="modal fade" id="approvalModal" tabindex="-1" role="dialog" aria-labelledby="approvalModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="proses_approval.php" method="POST">
        <div class="modal-header">
          <h4 class="modal-title" id="approvalModalLabel">Konfirmasi Tindakan</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Anda akan mengambil tindakan untuk:</p>
          <ul class="list-group">
              <li class="list-group-item"><strong>Nama:</strong> <span id="modal_nama_pegawai"></span></li>
              <li class="list-group-item"><strong>Tanggal:</strong> <span id="modal_tanggal_ijin"></span></li>
              <li class="list-group-item"><strong>Alasan:</strong> <span id="modal_alasan_ijin"></span></li>
          </ul>
          <hr>
          
          <input type="hidden" name="ijin_id" id="modal_ijin_id">
          
          <div class="form-group">
            <label for="alasan_reject">Alasan (Wajib diisi jika menolak):</label>
            <textarea class="form-control" name="alasan_reject" id="alasan_reject" rows="3" placeholder="Contoh: Lampiran surat sakit tidak valid / tidak jelas."></textarea>
          </div>
          <small class="text-muted">Biarkan kosong jika Anda menyetujui (Approve).</small>

        </div>
        <div class="modal-footer">
          <button type="submit" name="action" value="Reject" class="btn btn-danger">
            <i class="fa fa-times"></i> Tolak (Reject)
          </button>
          <button type="submit" name="action" value="Approve" class="btn btn-success">
            <i class="fa fa-check"></i> Setujui (Approve)
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include('scriptjs.php'); ?>

<script>
$(document).ready(function() {
    // Saat tombol .btn-aksi (tombol 'Tindakan') diklik
    $('.btn-aksi').on('click', function() {
        // 1. Ambil data dari atribut 'data-*' tombol
        var id_ijin = $(this).data('id_ijin');
        var nama = $(this).data('nama');
        var tanggal = $(this).data('tanggal');
        var alasan = $(this).data('alasan');

        // 2. Masukkan data tersebut ke dalam elemen-elemen di Modal
        $('#modal_ijin_id').val(id_ijin);
        $('#modal_nama_pegawai').text(nama);
        $('#modal_tanggal_ijin').text(tanggal);
        $('#modal_alasan_ijin').text(alasan);
        
        // 3. Bersihkan textarea alasan
        $('#alasan_reject').val('');
    });
});
</script>
</body>
</html>