<?php
// Hubungkan ke database
include 'config/db.php'; // Pastikan $con ada dari file ini

if (isset($_POST['submit'])) {
    
    $id_pegawai_input = mysqli_real_escape_string($con, $_POST['id_pegawai']);
    $waktu_sekarang_db = date('Y-m-d H:i:s'); // Waktu saat ini
    $tanggal_hari_ini = date('Y-m-d');      // Tanggal hari ini
    
    // =================================================================
    // LANGKAH 1: VALIDASI ID KARYAWAN
    // =================================================================
    $sql_cek_karyawan = "SELECT nama_pegawai, id_pegawai FROM employee WHERE id_pegawai = '$id_pegawai_input'";
    $hasil_cek_karyawan = mysqli_query($con, $sql_cek_karyawan);
    
    if (mysqli_num_rows($hasil_cek_karyawan) > 0) {
        $data_pegawai = mysqli_fetch_assoc($hasil_cek_karyawan);
        $nama_pegawai = $data_pegawai['nama_pegawai'];
        $db_id_pegawai = $data_pegawai['id_pegawai'];

        // =================================================================
        // LANGKAH 2: CEK ABSENSI HARI INI
        // =================================================================
        $sql_cek_absen = "SELECT waktu_masuk, waktu_keluar FROM face_absensi 
                          WHERE employee_id = '$db_id_pegawai' AND DATE(waktu_masuk) = '$tanggal_hari_ini' 
                          LIMIT 1";
        $hasil_cek_absen = mysqli_query($con, $sql_cek_absen);
        $record_hari_ini = mysqli_fetch_assoc($hasil_cek_absen);

        // =================================================================
        // LANGKAH 3: PROSES FOTO (Dilakukan untuk Masuk atau Pulang)
        // =================================================================
        $foto = $_FILES['fotoAbsen'];
        $nama_file_asli = $foto['name'];
        $tmp_file = $foto['tmp_name'];
        $error_file = $foto['error'];

        if ($error_file !== 0) {
            echo "Error: Terjadi masalah saat mengupload file.";
            exit();
        }

        // Siapkan nama file baru
        $folder_tujuan = "hasil_absensi/";
        $waktu_sekarang_file = date('Y-m-d_H-i-s');
        $ext = pathinfo($nama_file_asli, PATHINFO_EXTENSION);
        $nama_bersih = preg_replace("/[^A-Za-z0-9]/", '', $nama_pegawai);
        $nama_file_baru = $nama_bersih . "_" . $db_id_pegawai . "_" . $waktu_sekarang_file . "." . $ext;
        $path_tujuan = $folder_tujuan . $nama_file_baru;
        
        // =================================================================
        // LANGKAH 4: LOGIKA INSERT (MASUK) vs UPDATE (PULANG)
        // =================================================================

        // JIKA TIDAK ADA RECORD HARI INI -> INI ADALAH ABSEN MASUK
        if (!$record_hari_ini) {

            // === HITUNG KETERLAMBATAN (TELAT) ===
            $selisih_waktu_db = '00:00:00'; // Default jika tidak telat (format TIME)
            $target_masuk_string = $tanggal_hari_ini . " 08:00:00"; // Target jam 8 pagi
            
            $waktu_masuk_obj = new DateTime($waktu_sekarang_db);
            $target_masuk_obj = new DateTime($target_masuk_string);

            if ($waktu_masuk_obj > $target_masuk_obj) {
                $interval = $target_masuk_obj->diff($waktu_masuk_obj);
                $selisih_waktu_db = $interval->format('%H:%I:%S'); // Format HH:MM:SS
            }
            
            // Pindahkan file dulu
            if (move_uploaded_file($tmp_file, $path_tujuan)) {
                
                // Ganti 'selisih_waktu' jika nama kolom beda
                $sql_insert = "INSERT INTO face_absensi 
                                (nama_pegawai, employee_id, waktu_masuk, kamera, note, foto_masuk, state, aktif_notif, foto_keluar, selisih_waktu) 
                               VALUES 
                                ('$nama_pegawai', '$db_id_pegawai', '$waktu_sekarang_db', 'HP Karyawan', 'Hadir', '$nama_file_baru', 'Hadir', 0, NULL, '$selisih_waktu_db')";

                if (mysqli_query($con, $sql_insert)) {
                    echo "<div style='padding: 20px; font-family: Arial; text-align: center; color: green;'>
                            <h2>Absen Masuk Berhasil!</h2>
                            <p>Terima kasih, $nama_pegawai. Absen MASUK Anda telah dicatat pukul " . date('H:i:s') . ".</p>
                            <p>Keterlambatan: $selisih_waktu_db</p>
                            <a href='upload_absen.php'>Kembali</a>
                          </div>";
                } else {
                    echo "Error: Gagal mencatat ke database: " . mysqli_error($con);
                }
            } else {
                echo "Error: Gagal memindahkan file.";
            }

        // JIKA ADA RECORD, TAPI 'waktu_keluar' KOSONG -> INI ADALAH ABSEN PULANG
        } else if ($record_hari_ini && empty($record_hari_ini['waktu_keluar'])) {

            // =======================================================
            // === TAMBAHAN BARU: CEK WAKTU PULANG (JAM 16:00) ===
            // =======================================================
            $target_pulang_string = $tanggal_hari_ini . " 16:00:00"; // Target jam 4 sore
            
            $waktu_pulang_obj = new DateTime($waktu_sekarang_db);
            $target_pulang_obj = new DateTime($target_pulang_string);

            // Cek apakah waktu pulang LEBIH AWAL DARI target
            if ($waktu_pulang_obj < $target_pulang_obj) {
                // Jika ya, kirim pesan error dan hentikan
                echo "<div style='padding: 20px; font-family: Arial; text-align: center; color: red;'>
                        <h2>Belum Waktunya Pulang!</h2>
                        <p>Absen pulang hanya bisa dilakukan setelah <strong>pukul 16:00 (Jam 4 Sore)</strong>.</p>
                        <p>Waktu Anda saat ini: " . date('H:i:s') . "</p>
                        <a href='upload_absen.php'>Kembali</a>
                      </div>";
                
                // Hentikan eksekusi, jangan proses file
                mysqli_close($con);
                exit(); 
            }
            // =======================================================
            // === AKHIR CEK WAKTU PULANG ===
            // =======================================================
            
            // Pindahkan file dulu (HANYA JIKA LOLOS CEK DI ATAS)
            if (move_uploaded_file($tmp_file, $path_tujuan)) {
                
                // Lakukan UPDATE
                $sql_update = "UPDATE face_absensi 
                               SET waktu_keluar = '$waktu_sekarang_db', 
                                   foto_keluar = '$nama_file_baru' 
                               WHERE employee_id = '$db_id_pegawai' AND DATE(waktu_masuk) = '$tanggal_hari_ini'";

                if (mysqli_query($con, $sql_update)) {
                    echo "<div style='padding: 20px; font-family: Arial; text-align: center; color: blue;'>
                            <h2>Absen Pulang Berhasil!</h2>
                            <p>Terima kasih, $nama_pegawai. Absen PULANG Anda telah dicatat pukul " . date('H:i:s') . ".</p>
                            <a href='upload_absen.php'>Kembali</a>
                          </div>";
                } else {
                    echo "Error: Gagal update data pulang: " . mysqli_error($con);
                }
            } else {
                echo "Error: Gagal memindahkan file.";
            }

        // JIKA ADA RECORD DAN 'waktu_keluar' SUDAH TERISI
        } else {
            echo "<div style='padding: 20px; font-family: Arial; text-align: center; color: orange;'>
                    <h2>Sudah Selesai!</h2>
                    <p>Halo $nama_pegawai, Anda sudah melakukan absen MASUK dan PULANG hari ini.</p>
                    <a href='upload_absen.php'>Kembali</a>
                  </div>";
        }
        
    } else {
        // Jika ID Pegawai tidak ditemukan
        echo "<div style='padding: 20px; font-family: Arial; text-align: center; color: red;'>
                <h2>Gagal!</h2>
                <p>ID Pegawai '$id_pegawai_input' tidak ditemukan.</p>
                <a href='upload_absen.php'>Coba Lagi</a>
              </div>";
    }
    
    mysqli_close($con);
    
} else {
    header("Location: upload_absen.php");
}
?>