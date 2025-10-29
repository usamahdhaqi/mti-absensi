<?php
// Mulai session untuk mengambil nama admin
session_start();
include 'config/db.php'; // Hubungkan ke DB

// Cek jika admin tidak login atau data tidak dikirim
if (!isset($_SESSION['nama_log']) || !isset($_POST['ijin_id']) || !isset($_POST['action'])) {
    die("Aksi tidak sah.");
}

// Ambil data dari form
$ijin_id = mysqli_real_escape_string($con, $_POST['ijin_id']);
$action = mysqli_real_escape_string($con, $_POST['action']); // "Approve" atau "Reject"
$admin_name = mysqli_real_escape_string($con, $_SESSION['nama_log']);

// === PERUBAHAN: Ambil data textarea 'alasan_reject' ===
$alasan_approval = mysqli_real_escape_string($con, $_POST['alasan_reject']);

// Tentukan status baru
$new_status = 'Pending';
if ($action == 'Approve') {
    $new_status = 'Approved';
    // Jika diapprove, kita tidak perlu simpan alasannya
    $alasan_approval = NULL; 
} else if ($action == 'Reject') {
    $new_status = 'Rejected';
    // Jika ditolak tapi alasannya kosong, beri pesan default
    if (empty($alasan_approval)) {
        $alasan_approval = 'Ditolak tanpa keterangan';
    }
}

// 1. UPDATE status di tabel 'ijin_absensi'
// === PERUBAHAN: Tambahkan kolom 'alasan_app_dpp' ===
$sql_update = "UPDATE ijin_absensi 
               SET app = '$new_status', 
                   app_by = '$admin_name',
                   alasan_app_dpp = '$alasan_approval' 
               WHERE id = '$ijin_id'";

if (mysqli_query($con, $sql_update)) {
    
    // 2. JIKA DISETUJUI (Approve), masukkan ke tabel 'not_absensi'
    if ($action == 'Approve') {
        
        // Ambil data dari pengajuan yang baru disetujui
        $sql_get = "SELECT nama_pegawai, tanggal_ijin FROM ijin_absensi WHERE id = '$ijin_id' LIMIT 1";
        $result_get = mysqli_query($con, $sql_get);
        $row_ijin = mysqli_fetch_assoc($result_get);
        
        $nama_pegawai = $row_ijin['nama_pegawai'];
        $tanggal_absen = $row_ijin['tanggal_ijin'];
        
        // Ambil ID Pegawai dari tabel 'employee' (berdasarkan nama)
        $sql_get_id = "SELECT id_pegawai FROM employee WHERE nama_pegawai = '$nama_pegawai' LIMIT 1";
        $result_id = mysqli_query($con, $sql_get_id);
        $employee_id = 'N/A'; // Default jika tidak ketemu
        
        if (mysqli_num_rows($result_id) > 0) {
            $row_emp = mysqli_fetch_assoc($result_id);
            $employee_id = $row_emp['id_pegawai'];
        }
        
        // Masukkan ke tabel 'not_absensi'
        $sql_insert_not = "INSERT INTO not_absensi (employee_id, nama_pegawai, tanggal_absen) 
                           VALUES ('$employee_id', '$nama_pegawai', '$tanggal_absen')";
        
        mysqli_query($con, $sql_insert_not);
    }
    
    // 3. Kembalikan Admin ke halaman manajemen izin
    header("Location: ijin_absensi.php");
    exit();
    
} else {
    // Jika gagal update
    echo "Error: " . mysqli_error($con);
}

mysqli_close($con);
?>