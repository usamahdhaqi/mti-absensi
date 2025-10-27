<?php
date_default_timezone_set('Asia/Jakarta');
// Hubungkan ke database
include 'config/db.php'; // Pastikan $con ada dari file ini

// Fungsi Helper untuk menampilkan pesan (JANGAN UBAH INI)
function tampilkan_pesan($status, $judul, $pesan, $info_tambahan = "") {
    $header_class = 'bg-info'; // Default
    $icon_class = 'fas fa-info-circle'; // Default
    $icon_color = 'text-info'; // Default

    if ($status == 'sukses') {
        $header_class = 'bg-success'; // Hijau
        $icon_class = 'fas fa-check-circle';
        $icon_color = 'text-success';
    } else if ($status == 'error') {
        $header_class = 'bg-danger'; // Merah
        $icon_class = 'fas fa-times-circle';
        $icon_color = 'text-danger';
    } else if ($status == 'warning') {
        $header_class = 'bg-warning'; // Jingga
        $icon_class = 'fas fa-exclamation-triangle';
        $icon_color = 'text-warning';
    } else if ($status == 'info') {
        $header_class = 'bg-primary'; // Biru (untuk pulang)
        $icon_class = 'fas fa-sign-out-alt';
        $icon_color = 'text-primary';
    }

    echo <<<HTML
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Status Absensi</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <style>
            body { background-color: #f4f6f9; padding-top: 40px; }
            .card { border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border: none; }
            .card-header { border-top-left-radius: 10px; border-top-right-radius: 10px; }
        </style>
    </head>
    <body class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card text-center">
                    <div class="card-header $header_class text-white">
                        <h4 class="mb-0">$judul</h4>
                    </div>
                    <div class="card-body" style="padding: 30px;">
                        <i class="$icon_class fa-4x $icon_color mb-3"></i>
                        <p style="font-size: 1.1rem;">$pesan</p>
                        <p class="text-muted">$info_tambahan</p>
                        <a href='upload_absen.php' class="btn btn-secondary mt-3">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
HTML;
}
// AKHIR FUNGSI HELPER


// =================================================================
// PROSES UTAMA (Logika Anda sebelumnya)
// =================================================================

if (isset($_POST['submit'])) {
    
    $id_pegawai_input = mysqli_real_escape_string($con, $_POST['id_pegawai']);
    $waktu_sekarang_db = date('Y-m-d H:i:s'); 
    $tanggal_hari_ini = date('Y-m-d');      
    
    // LANGKAH 1: VALIDASI ID KARYAWAN
    $sql_cek_karyawan = "SELECT nama_pegawai, id_pegawai FROM employee WHERE id_pegawai = '$id_pegawai_input'";
    $hasil_cek_karyawan = mysqli_query($con, $sql_cek_karyawan);
    
    if (mysqli_num_rows($hasil_cek_karyawan) > 0) {
        $data_pegawai = mysqli_fetch_assoc($hasil_cek_karyawan);
        $nama_pegawai = $data_pegawai['nama_pegawai'];
        $db_id_pegawai = $data_pegawai['id_pegawai'];

        // LANGKAH 2: CEK ABSENSI HARI INI
        $sql_cek_absen = "SELECT waktu_masuk, waktu_keluar FROM face_absensi 
                          WHERE employee_id = '$db_id_pegawai' AND DATE(waktu_masuk) = '$tanggal_hari_ini' 
                          LIMIT 1";
        $hasil_cek_absen = mysqli_query($con, $sql_cek_absen);
        $record_hari_ini = mysqli_fetch_assoc($hasil_cek_absen);

        // LANGKAH 3: PROSES FOTO
        $foto = $_FILES['fotoAbsen'];
        $nama_file_asli = $foto['name'];
        $tmp_file = $foto['tmp_name'];
        $error_file = $foto['error'];

        if ($error_file !== 0) {
            tampilkan_pesan('error', 'Upload Gagal', 'Terjadi masalah saat mengupload file. Silakan coba lagi.');
            mysqli_close($con);
            exit();
        }

        $folder_tujuan = "hasil_absensi/";
        $waktu_sekarang_file = date('Y-m-d_H-i-s');
        $ext = pathinfo($nama_file_asli, PATHINFO_EXTENSION);
        $nama_bersih = preg_replace("/[^A-Za-z0-9]/", '', $nama_pegawai);
        $nama_file_baru = $nama_bersih . "_" . $db_id_pegawai . "_" . $waktu_sekarang_file . "." . $ext;
        $path_tujuan = $folder_tujuan . $nama_file_baru;
        
        // LANGKAH 4: LOGIKA INSERT (MASUK) vs UPDATE (PULANG)

        // JIKA TIDAK ADA RECORD HARI INI -> INI ADALAH ABSEN MASUK
        if (!$record_hari_ini) {

            $selisih_waktu_db = '00:00:00'; 
            $target_masuk_string = $tanggal_hari_ini . " 08:00:00";
            $waktu_masuk_obj = new DateTime($waktu_sekarang_db);
            $target_masuk_obj = new DateTime($target_masuk_string);

            if ($waktu_masuk_obj > $target_masuk_obj) {
                $interval = $target_masuk_obj->diff($waktu_masuk_obj);
                $selisih_waktu_db = $interval->format('%H:%I:%S'); 
            }
            
            if (move_uploaded_file($tmp_file, $path_tujuan)) {
                
                $sql_insert = "INSERT INTO face_absensi (nama_pegawai, employee_id, waktu_masuk, kamera, note, foto_masuk, state, aktif_notif, foto_keluar, selisih_waktu) 
                               VALUES ('$nama_pegawai', '$db_id_pegawai', '$waktu_sekarang_db', 'HP Karyawan', 'Hadir', '$nama_file_baru', 'Hadir', 0, NULL, '$selisih_waktu_db')";

                if (mysqli_query($con, $sql_insert)) {
                    tampilkan_pesan(
                        'sukses', // (Hijau)
                        'Absen Masuk Berhasil!',
                        "Terima kasih, $nama_pegawai. Absen MASUK Anda telah dicatat.",
                        "Waktu: " . date('H:i:s') . " | Keterlambatan: $selisih_waktu_db"
                    );
                } else {
                    tampilkan_pesan('error', 'Database Error', 'File berhasil diupload tapi gagal dicatat ke database.', mysqli_error($con));
                }
            } else {
                tampilkan_pesan('error', 'Upload Gagal', 'Gagal memindahkan file yang diupload.');
            }

        // JIKA ADA RECORD, TAPI 'waktu_keluar' KOSONG -> INI ADALAH ABSEN PULANG
        } else if ($record_hari_ini && empty($record_hari_ini['waktu_keluar'])) {

            $target_pulang_string = $tanggal_hari_ini . " 16:00:00"; // Target jam 4 sore
            $waktu_pulang_obj = new DateTime($waktu_sekarang_db);
            $target_pulang_obj = new DateTime($target_pulang_string);

            if ($waktu_pulang_obj < $target_pulang_obj) {
                tampilkan_pesan(
                    'error', // (Merah)
                    'Belum Waktunya Pulang!',
                    'Absen pulang hanya bisa dilakukan setelah <strong>pukul 16:00 (Jam 4 Sore)</strong>.',
                    'Waktu Anda saat ini: ' . date('H:i:s')
                );
                mysqli_close($con);
                exit(); 
            }
            
            if (move_uploaded_file($tmp_file, $path_tujuan)) {
                
                $sql_update = "UPDATE face_absensi SET waktu_keluar = '$waktu_sekarang_db', foto_keluar = '$nama_file_baru' 
                               WHERE employee_id = '$db_id_pegawai' AND DATE(waktu_masuk) = '$tanggal_hari_ini'";

                if (mysqli_query($con, $sql_update)) {
                    tampilkan_pesan(
                        'info', // (Biru)
                        'Absen Pulang Berhasil!',
                        "Terima kasih, $nama_pegawai. Absen PULANG Anda telah dicatat.",
                        "Waktu: " . date('H:i:s')
                    );
                } else {
                    tampilkan_pesan('error', 'Database Error', 'Gagal update data pulang.', mysqli_error($con));
                }
            } else {
                tampilkan_pesan('error', 'Upload Gagal', 'Gagal memindahkan file yang diupload.');
            }

        // JIKA ADA RECORD DAN 'waktu_keluar' SUDAH TERISI
        } else {
            tampilkan_pesan(
                'warning', // (Jingga)
                'Sudah Selesai!',
                "Halo $nama_pegawai, Anda sudah melakukan absen MASUK dan PULANG hari ini.",
                'Tidak ada tindakan lebih lanjut diperlukan.'
            );
        }
        
    } else {
        // Jika ID Pegawai tidak ditemukan
        tampilkan_pesan(
            'error', // (Merah)
            'ID Tidak Ditemukan',
            "ID Pegawai '$id_pegawai_input' tidak ditemukan atau tidak terdaftar.",
            'Pastikan Anda memasukkan ID yang benar.'
        );
    }
    
    mysqli_close($con);
    
} else {
    header("Location: upload_absen.php");
}
?>