<?php
// Start the session
session_start();
include 'config/db.php'; // Hubungkan ke DB
if (!isset($_SESSION['nama_log'])){ header("location: index.php"); }
$admin_name = $_SESSION['nama_log'];
?>
<!DOCTYPE html>
<html>
<head>
  <title>MTI Absensi | Persetujuan Izin Siswa</title>
  <?php include('scriptcss.php'); ?>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include('header.php'); include('sidebar.php'); ?>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Manajemen Izin Peserta Didik</h1>
    </section>
    <section class="content">
      <div class="box">
        <div class="box-body">
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
              <thead>
                <tr>
                  <th>No</th> <th>Nama Siswa</th> <th>Jenis Izin</th> <th>Tanggal Izin</th> <th>Keterangan</th> <th>Lampiran</th> <th>Tgl. Pengajuan</th> <th>Status</th> <th style="width: 100px;">Aksi</th>
                </tr>
              </thead>
              <tbody>
              <?php
                // GANTI TABEL
                $sql = "SELECT * FROM ijin_siswa ORDER BY waktu_buat_ijin DESC";
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
                    // GANTI FOLDER
                    echo '<td><a href="Lampiran_siswa/'. htmlspecialchars($row['lampiran']) .'" target="_blank" class="btn btn-info btn-xs"><i class="fa fa-eye"></i> Lihat</a></td>';
                    echo '<td>'. date('d M Y H:i', strtotime($row['waktu_buat_ijin'])) . '</td>';
                    
                    $status = $row['app'];
                    $badge_class = 'bg-yellow';
                    if ($status == 'Approved') { $badge_class = 'bg-green'; } 
                    else if ($status == 'Rejected') { $badge_class = 'bg-red'; }
                    echo '<td><span class="label '. $badge_class .'">'. $status .'</span></td>';
                    
                    echo '<td>';
                    if ($row['app'] == 'Pending') {
                        // Tombol pemicu modal (data-target SAMA, JS akan membedakan)
                        echo '<button type_button" class="btn btn-primary btn-xs btn-aksi" 
                                  data-toggle="modal" 
                                  data-target="#approvalModalSiswa"
                                  data-id_ijin="'. $row['id'] .'"
                                  data-nama="'. htmlspecialchars($row['nama_pegawai']) .'"
                                  data-tanggal="'. $tanggal_ijin_formatted .'"
                                  data-alasan="'. htmlspecialchars($row['alasan_ijin']) .'">
                                <i class="fa fa-gavel"></i> Tindakan
                              </button>';
                    } else {
                        echo 'by: '. htmlspecialchars($row['app_by']);
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

<div class="modal fade" id="approvalModalSiswa" tabindex="-1" role="dialog" aria-labelledby="approvalModalLabelSiswa" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="proses_approval_siswa.php" method="POST">
        <div class="modal-header">
          <h4 class="modal-title" id="approvalModalLabelSiswa">Konfirmasi Tindakan (Siswa)</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
            <textarea class="form-control" name="alasan_reject" id="alasan_reject" rows="3" placeholder="Contoh: Lampiran surat sakit tidak valid."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="action" value="Reject" class="btn btn-danger"><i class="fa fa-times"></i> Tolak (Reject)</button>
          <button type="submit" name="action" value="Approve" class="btn btn-success"><i class="fa fa-check"></i> Setujui (Approve)</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include('scriptjs.php'); ?>
<script>
$(document).ready(function() {
    $('.btn-aksi').on('click', function() {
        var id_ijin = $(this).data('id_ijin');
        var nama = $(this).data('nama');
        var tanggal = $(this).data('tanggal');
        var alasan = $(this).data('alasan');
        // JS ini akan mengisi modal manapun yang aktif (termasuk #approvalModalSiswa)
        $('#approvalModalSiswa .modal-body #modal_ijin_id').val(id_ijin);
        $('#approvalModalSiswa .modal-body #modal_nama_pegawai').text(nama);
        $('#approvalModalSiswa .modal-body #modal_tanggal_ijin').text(tanggal);
        $('#approvalModalSiswa .modal-body #modal_alasan_ijin').text(alasan);
        $('#approvalModalSiswa .modal-body #alasan_reject').val('');
    });
});
</script>
</body>
</html>