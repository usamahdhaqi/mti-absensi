<?php
// Hubungkan ke database dan helper
include 'config/db.php'; 
include 'helper_pesan.php'; // Memuat fungsi 'tampilkan_pesan'

// Atur Zona Waktu (PENTING)
date_default_timezone_set('Asia/Jakarta');

if (isset($_POST['submit'])) {
    
    // Ambil data dari form
    $nis_input = mysqli_real_escape_string($con, $_POST['nis']);
    $tanggal_ijin = mysqli_real_escape_string($con, $_POST['tanggal_ijin']);
    
    // Penyesuaian Logika Mapping
    $db_ijin_tipe = mysqli_real_escape_string($con, $_POST['alasan_ijin']); // (Sakit, Izin, Cuti)
    $db_ijin_alasan = mysqli_real_escape_string($con, $_POST['keterangan']); // (Keterangan teks)
    
    // Siapkan data otomatis
    $db_waktu_buat = date('Y-m-d H:i:s');

    // LANGKAH 1: VALIDASI ID KARYAWAN
    $sql_cek_karyawan = "SELECT nama_pegawai FROM siswa WHERE nis = '$nis_input'";
    $hasil_cek_karyawan = mysqli_query($con, $sql_cek_karyawan);
    
    if (mysqli_num_rows($hasil_cek_karyawan) > 0) {
        $data_pegawai = mysqli_fetch_assoc($hasil_cek_karyawan);
        $nama_pegawai = $data_pegawai['nama_pegawai'];

        // LANGKAH 2: PROSES FILE LAMPIRAN
        $lampiran = $_FILES['fileLampiran'];
        if ($lampiran['error'] !== 0) {
            tampilkan_pesan('error', 'Upload Gagal', 'Terjadi masalah saat mengupload file lampiran.');
            mysqli_close($con); exit();
        }

        // Siapkan nama file baru
        $folder_tujuan = "Lampiran_siswa/";
        
        // Cek jika folder tidak ada, buat folder
        if (!is_dir($folder_tujuan)) {
            mkdir($folder_tujuan, 0755, true);
        }

        $nama_file_asli = $lampiran['name'];
        $db_tipe_lampiran = strtolower(pathinfo($nama_file_asli, PATHINFO_EXTENSION)); // pdf, jpg
        $timestamp = date('Ymd_His'); // Format YYYYMMDD_HHMMSS
        $nama_bersih = preg_replace("/[^A-Za-z0-9]/", '', $nama_pegawai);
        
        $nama_file_baru = "IJIN_" . $nama_bersih . "_" . $timestamp . "." . $db_tipe_lampiran;
        $path_tujuan = $folder_tujuan . $nama_file_baru;
        
        // Pindahkan file
        if (move_uploaded_file($lampiran['tmp_name'], $path_tujuan)) {
            
            // LANGKAH 3: MASUKKAN KE DATABASE 'ijin_siswa'
            $sql_insert = "INSERT INTO ijin_siswa (
                                nama_pegawai, ijin, alasan_ijin, tanggal_ijin, waktu_buat_ijin, 
                                atasan, lampiran, tipe_lampiran, 
                                aktif_notif_manager, aktif_appdpp_manager, app, 
                                app_by, alasan_app_dpp, aktif_notif_karyawan
                           ) 
                           VALUES (
                                '$nama_pegawai', '$db_ijin_tipe', '$db_ijin_alasan', '$tanggal_ijin', '$db_waktu_buat', 
                                '', '$nama_file_baru', '$db_tipe_lampiran',  -- <<<<<< PERBAIKAN DI SINI (NULL diubah menjadi '')
                                1, 0, 'Pending', 
                                NULL, NULL, 0
                           )";

            if (mysqli_query($con, $sql_insert)) {
                tampilkan_pesan(
                    'sukses', // (Hijau)
                    'Pengajuan Terkirim!',
                    "Terima kasih, $nama_pegawai. Pengajuan izin Anda telah terkirim.",
                    "Silakan tunggu persetujuan dari Admin/HRD."
                );
            } else {
                tampilkan_pesan('error', 'Database Error', 'File berhasil diupload tapi gagal dicatat ke database.', mysqli_error($con));
            }
        } else {
            tampilkan_pesan('error', 'Upload Gagal', 'Gagal memindahkan file lampiran. Pastikan folder "Lampiran" memiliki izin tulis (write permission).');
        }
        
    } else {
        // Jika ID Pegawai tidak ditemukan
        tampilkan_pesan(
            'error', // (Merah)
            'ID Tidak Ditemukan',
            "ID Pegawai '$nis_input' tidak ditemukan atau tidak terdaftar.",
            'Pastikan Anda memasukkan ID yang benar.'
        );
    }
    
    mysqli_close($con);
    
} else {
    header("Location: form_ijin_siswa.php");
}
?>