<?php
// Hubungkan ke database
include 'config/db.php'; // Pastikan $con ada dari file ini

// =================================================================
// === FUNGSI PENCOCOKAN WAJAH (VIA SERVER AI/FLASK - VERSI BASE64) ===
// =================================================================
/**
 * Memanggil server AI (Flask) via cURL untuk memverifikasi dua wajah.
 * Mengirim data gambar sebagai Base64.
 */
function pencocokanWajahOpenSource($path_foto_master, $path_foto_absen) {
    if (!file_exists($path_foto_master) || filesize($path_foto_master) == 0) {
        return [false, "Admin belum mengupload foto profil (foto master) untuk Anda atau file foto rusak."];
    }
    $master_data = file_get_contents($path_foto_master);
    $master_base64 = base64_encode($master_data);
    $master_base64_uri = "data:image/jpeg;base64," . $master_base64;

    if (!file_exists($path_foto_absen) || filesize($path_foto_absen) == 0) {
        return [false, "File foto absen yang diupload tidak ditemukan atau rusak."];
    }
    $absen_data = file_get_contents($path_foto_absen);
    $absen_base64 = base64_encode($absen_data);
    $absen_base64_uri = "data:image/jpeg;base64," . $absen_base64;

    $data = json_encode([
        'img_master' => $master_base64_uri,
        'img_absen'  => $absen_base64_uri
    ]);

    // GANTI DENGAN URL HUGGING FACE ANDA
    $ch = curl_init('https://usamahdhaqi-absensi-ai-server.hf.space/verify'); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60); 

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return [false, "Gagal terhubung ke Server AI. Error: " . $error_msg];
    }
    curl_close($ch);

    $json_response = json_decode($response, true);
    if ($http_code == 200 && isset($json_response['verified'])) {
        if ($json_response['verified'] == true) {
            return [true, "OK"]; 
        } else {
            if (isset($json_response['error']) && $json_response['error'] == 'NO_FACE_FOUND') {
                return [false, "Wajah tidak terdeteksi. Pastikan foto Anda (atau foto profil Anda) jelas dan tidak terpotong."];
            }
            return [false, "Foto absen Anda tidak cocok dengan foto profil yang terdaftar (mismatch)."];
        }
    } else {
        return [false, "Terjadi error internal pada Server AI: " . ($json_response['error'] ?? $response)];
    }
}
// =================================================================
// === AKHIR FUNGSI AI ===
// =================================================================


// Fungsi Helper untuk menampilkan pesan (JANGAN UBAH INI)
function tampilkan_pesan($status, $judul, $pesan, $info_tambahan = "") {
    $header_class = 'bg-info'; $icon_class = 'fas fa-info-circle'; $icon_color = 'text-info';
    if ($status == 'sukses') { $header_class = 'bg-success'; $icon_class = 'fas fa-check-circle'; $icon_color = 'text-success'; } 
    else if ($status == 'error') { $header_class = 'bg-danger'; $icon_class = 'fas fa-times-circle'; $icon_color = 'text-danger'; } 
    else if ($status == 'warning') { $header_class = 'bg-warning'; $icon_class = 'fas fa-exclamation-triangle'; $icon_color = 'text-warning'; } 
    else if ($status == 'info') { $header_class = 'bg-primary'; $icon_class = 'fas fa-sign-out-alt'; $icon_color = 'text-primary'; }
    echo <<<HTML
    <!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Absensi</title><link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>body { background-color: #f4f6f9; padding-top: 40px; } .card { border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border: none; } .card-header { border-top-left-radius: 10px; border-top-right-radius: 10px; }</style>
    </head><body class="container"><div class="row"><div class="col-md-6 offset-md-3"><div class="card text-center">
    <div class="card-header $header_class text-white"><h4 class="mb-0">$judul</h4></div>
    <div class="card-body" style="padding: 30px;"><i class="$icon_class fa-4x $icon_color mb-3"></i>
    <p style="font-size: 1.1rem;">$pesan</p><p class="text-muted">$info_tambahan</p>
    <a href='upload_absen.php' class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div></div></div></div></body></html>
HTML;
}
// AKHIR FUNGSI HELPER


// =================================================================
// PROSES UTAMA
// =================================================================

if (isset($_POST['submit'])) {
    
    $id_pegawai_input = mysqli_real_escape_string($con, $_POST['id_pegawai']);

    // =================================================================
    // === LOGIKA WAKTU (PERBAIKAN TOTAL) ===
    // =================================================================
    // 1. Tentukan Timezone kita (WIB)
    $timezone = new DateTimeZone('Asia/Jakarta');
    
    // 2. Buat objek waktu SAAT INI (WIB)
    $waktu_sekarang_obj = new DateTime('now', $timezone);
    
    // 3. Buat objek waktu TARGET MASUK (Jam 8 pagi WIB)
    $target_masuk_obj = new DateTime('now', $timezone);
    $target_masuk_obj->setTime(8, 0, 0); // Jam 8, Menit 0, Detik 0

    // 4. Buat objek waktu TARGET PULANG (Jam 4 sore WIB)
    $target_pulang_obj = new DateTime('now', $timezone);
    $target_pulang_obj->setTime(16, 0, 0); // Jam 16, Menit 0, Detik 0
    
    // 5. Siapkan string untuk database (Format: Y-m-d H:i:s)
    $waktu_sekarang_db = $waktu_sekarang_obj->format('Y-m-d H:i:s');
    $tanggal_hari_ini_db = $waktu_sekarang_obj->format('Y-m-d');
    // =================================================================
    // === AKHIR LOGIKA WAKTU ===
    // =================================================================
    
    
    // LANGKAH 1: VALIDASI ID KARYAWAN
    $sql_cek_karyawan = "SELECT nama_pegawai, id_pegawai, foto_profil FROM employee WHERE id_pegawai = '$id_pegawai_input'";
    $hasil_cek_karyawan = mysqli_query($con, $sql_cek_karyawan);
    
    if (mysqli_num_rows($hasil_cek_karyawan) > 0) {
        $data_pegawai = mysqli_fetch_assoc($hasil_cek_karyawan);
        $nama_pegawai = $data_pegawai['nama_pegawai'];
        $db_id_pegawai = $data_pegawai['id_pegawai'];
        $foto_profil_db = $data_pegawai['foto_profil'];

        // LANGKAH 2: CEK ABSENSI HARI INI
        // Gunakan $tanggal_hari_ini_db yang sudah pasti WIB
        $sql_cek_absen = "SELECT waktu_masuk, waktu_keluar FROM face_absensi 
                          WHERE employee_id = '$db_id_pegawai' AND DATE(waktu_masuk) = '$tanggal_hari_ini_db' 
                          LIMIT 1";
        $hasil_cek_absen = mysqli_query($con, $sql_cek_absen);
        $record_hari_ini = mysqli_fetch_assoc($hasil_cek_absen);

        // LANGKAH 3: PROSES FOTO
        $foto = $_FILES['fotoAbsen'];
        $tmp_file = $foto['tmp_name'];
        $error_file = $foto['error'];

        if ($error_file !== 0) {
            tampilkan_pesan('error', 'Upload Gagal', 'Terjadi masalah saat mengupload file. Silakan coba lagi.');
            mysqli_close($con); exit();
        }

        // LANGKAH X: PENCOCOKAN WAJAH
        $path_foto_master = "foto_profil_karyawan/" . $foto_profil_db;
        list($apakah_wajah_cocok, $pesan_error) = pencocokanWajahOpenSource($path_foto_master, $tmp_file);

        if (!$apakah_wajah_cocok) {
            tampilkan_pesan('error', 'Wajah Tidak Dikenali!', $pesan_error, 'Silakan coba lagi dengan pencahayaan lebih baik.');
            mysqli_close($con); exit();
        }
        
        // LANJUT JIKA WAJAH COCOK...
        $folder_tujuan = "hasil_absensi/";
        $waktu_sekarang_file = $waktu_sekarang_obj->format('Y-m-d_H-i-s'); // Nama file berdasarkan waktu WIB
        $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $nama_bersih = preg_replace("/[^A-Za-z0-9]/", '', $nama_pegawai);
        $nama_file_baru = $nama_bersih . "_" . $db_id_pegawai . "_" . $waktu_sekarang_file . "." . $ext;
        $path_tujuan = $folder_tujuan . $nama_file_baru;
        
        // LANGKAH 4: LOGIKA INSERT (MASUK) vs UPDATE (PULANG)

        // JIKA TIDAK ADA RECORD HARI INI -> INI ADALAH ABSEN MASUK
        if (!$record_hari_ini) {

            // === HITUNG KETERLAMBATAN (TELAT) ===
            $selisih_waktu_db = '00:00:00'; 
            // Bandingkan objek $waktu_sekarang_obj vs $target_masuk_obj (keduanya WIB)
            if ($waktu_sekarang_obj > $target_masuk_obj) {
                $interval = $target_masuk_obj->diff($waktu_sekarang_obj);
                $selisih_waktu_db = $interval->format('%H:%I:%S'); 
            }
            
            if (move_uploaded_file($tmp_file, $path_tujuan)) { 
                $sql_insert = "INSERT INTO face_absensi (nama_pegawai, employee_id, waktu_masuk, kamera, note, foto_masuk, state, aktif_notif, foto_keluar, selisih_waktu) 
                               VALUES ('$nama_pegawai', '$db_id_pegawai', '$waktu_sekarang_db', 'HP Karyawan', 'Hadir', '$nama_file_baru', 'Hadir', 0, NULL, '$selisih_waktu_db')";
                if (mysqli_query($con, $sql_insert)) {
                    tampilkan_pesan('sukses', 'Absen Masuk Berhasil!', "Terima kasih, $nama_pegawai. Wajah terverifikasi.", "Waktu: " . $waktu_sekarang_obj->format('H:i:s') . " | Keterlambatan: $selisih_waktu_db");
                } else {
                    tampilkan_pesan('error', 'Database Error', 'File berhasil diupload tapi gagal dicatat ke database.', mysqli_error($con));
                }
            } else {
                tampilkan_pesan('error', 'Upload Gagal', 'Gagal memindahkan file yang diupload.');
            }
        
        // JIKA ADA RECORD, TAPI 'waktu_keluar' KOSONG -> INI ADALAH ABSEN PULANG
        } else if ($record_hari_ini && empty($record_hari_ini['waktu_keluar'])) {

            // Cek apakah waktu pulang LEBIH AWAL DARI target
            // Bandingkan $waktu_sekarang_obj vs $target_pulang_obj (keduanya WIB)
            if ($waktu_sekarang_obj < $target_pulang_obj) {
                tampilkan_pesan('error', 'Belum Waktunya Pulang!', 'Absen pulang hanya bisa dilakukan setelah <strong>pukul 16:00 (Jam 4 Sore)</strong>.', 'Waktu Anda saat ini: ' . $waktu_sekarang_obj->format('H:i:s'));
                mysqli_close($con); exit(); 
            }
            
            if (move_uploaded_file($tmp_file, $path_tujuan)) { 
                $sql_update = "UPDATE face_absensi SET waktu_keluar = '$waktu_sekarang_db', foto_keluar = '$nama_file_baru' 
                               WHERE employee_id = '$db_id_pegawai' AND DATE(waktu_masuk) = '$tanggal_hari_ini_db'";
                if (mysqli_query($con, $sql_update)) {
                    tampilkan_pesan('info', 'Absen Pulang Berhasil!', "Terima kasih, $nama_pegawai. Wajah terverifikasi.", "Waktu: " . $waktu_sekarang_obj->format('H:i:s'));
                } else {
                    tampilkan_pesan('error', 'Database Error', 'Gagal update data pulang.', mysqli_error($con));
                }
            } else {
                tampilkan_pesan('error', 'Upload Gagal', 'Gagal memindahkan file yang diupload.');
            }

        // JIKA SUDAH SELESAI
        } else {
            tampilkan_pesan('warning', 'Sudah Selesai!', "Halo $nama_pegawai, Anda sudah melakukan absen MASUK dan PULANG hari ini.", 'Tidak ada tindakan lebih lanjut diperlukan.');
        }
        
    } else {
        // Jika ID Pegawai tidak ditemukan
        tampilkan_pesan('error', 'ID Tidak Ditemukan', "ID Pegawai '$id_pegawai_input' tidak ditemukan atau tidak terdaftar.", 'Pastikan Anda memasukkan ID yang benar.');
    }
    
    mysqli_close($con);
    
} else {
    header("Location: upload_absen.php");
}
?>