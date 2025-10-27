<?php
// Hubungkan ke database
include 'config/db.php'; // Pastikan $con ada dari file ini

if (isset($_POST['submit'])) {
    
    $id_pegawai_input = mysqli_real_escape_string($con, $_POST['id_pegawai']);
    
    // 1. Validasi ID Pegawai ke Database
    // Ganti 'employee' jika nama tabel Anda berbeda
    // Kita ambil juga NAMA dan ID PEGAWAI (untuk disimpan ke tabel face_absensi)
    $sql_cek = "SELECT nama_pegawai, id_pegawai FROM employee WHERE id_pegawai = '$id_pegawai_input'";
    $hasil_cek = mysqli_query($con, $sql_cek);
    
    if (mysqli_num_rows($hasil_cek) > 0) {
        $data_pegawai = mysqli_fetch_assoc($hasil_cek);
        $nama_pegawai = $data_pegawai['nama_pegawai'];
        $db_id_pegawai = $data_pegawai['id_pegawai']; // ID Pegawai dari DB
        
        // 2. Proses File Gambar
        $foto = $_FILES['fotoAbsen'];
        $nama_file_asli = $foto['name'];
        $tmp_file = $foto['tmp_name'];
        $error_file = $foto['error'];

        if ($error_file === 0) {
            
            // Siapkan data untuk DB dan Nama File
            $folder_tujuan = "hasil_absensi/";
            $waktu_sekarang_db = date('Y-m-d H:i:s'); // Format untuk database (misal: 2025-10-27 12:30:01)
            $waktu_sekarang_file = date('Y-m-d_H-i-s'); // Format untuk nama file (misal: 2025-10-27_12-30-01)
            
            $ext = pathinfo($nama_file_asli, PATHINFO_EXTENSION);
            $nama_bersih = preg_replace("/[^A-Za-z0-9]/", '', $nama_pegawai); // Hapus spasi/simbol
            
            // INI NAMA FILE FINAL KITA
            $nama_file_baru = $nama_bersih . "_" . $db_id_pegawai . "_" . $waktu_sekarang_file . "." . $ext;
            $path_tujuan = $folder_tujuan . $nama_file_baru;
            
            // 3. Pindahkan file
            if (move_uploaded_file($tmp_file, $path_tujuan)) {
                
                // === LANGKAH KRUSIAL: MASUKKAN KE DATABASE ===
                // Ganti 'face_absensi' jika nama tabel Anda berbeda
                $sql_insert = "INSERT INTO face_absensi 
                                (nama_pegawai, employee_id, waktu_masuk, kamera, note, foto_masuk) 
                               VALUES 
                                ('$nama_pegawai', '$db_id_pegawai', '$waktu_sekarang_db', 'HP Karyawan', 'Hadir', '$nama_file_baru')";

                if (mysqli_query($con, $sql_insert)) {
                    // SUKSES ULTIMATE
                    echo "<div style='padding: 20px; font-family: Arial; text-align: center; color: green;'>
                            <h2>Absen Berhasil!</h2>
                            <p>Terima kasih, $nama_pegawai. Absen Anda telah dicatat.</p>
                            <a href='upload_absen.php'>Kembali</a>
                          </div>";
                } else {
                    // File tersimpan, tapi DB gagal
                    echo "Error: File berhasil diupload tapi gagal dicatat ke database: " . mysqli_error($con);
                }
                
            } else {
                echo "Error: Gagal memindahkan file.";
            }
        } else {
            echo "Error: Terjadi masalah saat mengupload file.";
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