<?php
// Mulai session untuk mengambil nama admin
session_start();

// Hubungkan ke DB dan Helper WA baru
include 'config/db.php'; 
include 'helper_kirim_wa.php'; // <-- MEMUAT FUNGSI KIRIM WA

// Cek jika admin tidak login atau data tidak dikirim
if (!isset($_SESSION['nama_log']) || !isset($_POST['ijin_id']) || !isset($_POST['action'])) {
    die("Aksi tidak sah.");
}

// Ambil data dari form
$ijin_id = mysqli_real_escape_string($con, $_POST['ijin_id']);
$action = mysqli_real_escape_string($con, $_POST['action']); // "Approve" atau "Reject"
$admin_name = mysqli_real_escape_string($con, $_SESSION['nama_log']);
$alasan_approval = mysqli_real_escape_string($con, $_POST['alasan_reject']);

// Tentukan status baru
$new_status = 'Pending';
if ($action == 'Approve') {
    $new_status = 'Approved';
    if (empty($alasan_approval)) {
        $alasan_approval = 'Disetujui'; // Beri alasan default jika kosong
    }
} else if ($action == 'Reject') {
    $new_status = 'Rejected';
    if (empty($alasan_approval)) {
        $alasan_approval = 'Ditolak tanpa keterangan';
    }
}

// 1. UPDATE status di tabel 'ijin_absensi'
$sql_update = "UPDATE ijin_absensi 
               SET app = '$new_status', 
                   app_by = '$admin_name',
                   alasan_app_dpp = '$alasan_approval' 
               WHERE id = '$ijin_id'";

if (mysqli_query($con, $sql_update)) {
    
    // 2. AMBIL DATA LENGKAP KARYAWAN UNTUK NOTIFIKASI
    // (Kita JOIN dengan 'employee' untuk mengambil 'no_hp')
    $sql_get_data = "SELECT 
                        i.nama_pegawai, 
                        i.tanggal_ijin, 
                        i.ijin, 
                        e.no_hp,
                        e.id_pegawai 
                     FROM ijin_absensi i
                     LEFT JOIN employee e ON i.nama_pegawai = e.nama_pegawai
                     WHERE i.id = '$ijin_id' LIMIT 1";
                     
    $result_data = mysqli_query($con, $sql_get_data);
    
    if (mysqli_num_rows($result_data) > 0) {
        $data_karyawan = mysqli_fetch_assoc($result_data);
        
        $nama_pegawai = $data_karyawan['nama_pegawai'];
        $no_hp = $data_karyawan['no_hp'];
        $id_pegawai = $data_karyawan['id_pegawai'];
        $tanggal_ijin_db = $data_karyawan['tanggal_ijin'];
        $tanggal_ijin_formatted = date('d M Y', strtotime($tanggal_ijin_db));
        $jenis_ijin = $data_karyawan['ijin'];
        
        // 3. SIAPKAN PESAN WA
        $pesan_wa = "";
        
        if ($action == 'Approve') {
            $pesan_wa = "Halo $nama_pegawai,\n\nPengajuan *($jenis_ijin)* Anda untuk tanggal *$tanggal_ijin_formatted* telah disetujui (Approved) oleh $admin_name.\n\nCatatan: $alasan_approval\n\nTerima kasih.\n(Pesan Otomatis - Absensi MTI)";
            
            // 4A. JIKA DISETUJUI, masukkan ke tabel 'not_absensi'
            $sql_insert_not = "INSERT INTO not_absensi (employee_id, nama_pegawai, tanggal_absen) 
                               VALUES ('$id_pegawai', '$nama_pegawai', '$tanggal_ijin_db')";
            mysqli_query($con, $sql_insert_not);

        } else if ($action == 'Reject') {
            $pesan_wa = "Halo $nama_pegawai,\n\nMohon maaf, pengajuan *($jenis_ijin)* Anda untuk tanggal *$tanggal_ijin_formatted* telah ditolak (Rejected) oleh $admin_name.\n\nAlasan: $alasan_approval\n\nSilakan hubungi HRD atau ajukan ulang jika diperlukan.\n(Pesan Otomatis - Absensi MTI)";
        }
        
        // 5. KIRIM WA (jika ada nomor HP)
        if (!empty($pesan_wa) && !empty($no_hp)) {
            // Memanggil fungsi dari 'helper_kirim_wa.php'
            kirimPesanWA($no_hp, $pesan_wa);
        }
    }
    
    // 6. Kembalikan Admin ke halaman manajemen izin
    header("Location: ijin_absensi.php");
    exit();
    
} else {
    // Jika gagal update
    echo "Error: " . mysqli_error($con);
}

mysqli_close($con);
?>