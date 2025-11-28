<?php
session_start();
include 'config/db.php';
// Pastikan file helper ini ada, jika tidak, ganti tampilkan_pesan dengan header location manual
include 'helper_pesan.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Ambil ID Primary Key (Hidden Input) untuk WHERE clause
    $id_pk = mysqli_real_escape_string($con, $_POST['id_siswa']);
    
    // 2. Ambil NIS (Readonly Input) untuk keperluan rename file foto
    $nis = mysqli_real_escape_string($con, $_POST['nis']); 
    
    $nama_siswa = mysqli_real_escape_string($con, $_POST['nama_siswa']);

    // Logika Warning (SP)
    // Jika dicentang, update tanggal hari ini. Jika tidak dicentang, kosongkan (NULL/Empty).
    $w1 = isset($_POST['warning1']) ? date('Y-m-d') : ''; 
    $w2 = isset($_POST['warning2']) ? date('Y-m-d') : '';
    $w3 = isset($_POST['warning3']) ? date('Y-m-d') : '';

    // Siapkan Query Dasar
    $sql_update = "UPDATE siswa SET 
                    nama_siswa = '$nama_siswa',
                    warning1 = '$w1',
                    warning2 = '$w2',
                    warning3 = '$w3' ";

    // Logika Upload Foto
    if (isset($_FILES['foto_profil_baru']) && $_FILES['foto_profil_baru']['error'] == 0) {
        $foto = $_FILES['foto_profil_baru'];
        $folder_tujuan = "foto_profil_siswa/"; 
        
        // Validasi ekstensi (keamanan dasar)
        $allowed_ext = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));

        if(in_array($ext, $allowed_ext)){
            // Gunakan NIS dari variabel $nis yang sudah diambil di atas
            $nama_bersih = preg_replace("/[^A-Za-z0-9]/", '', $nama_siswa);
            
            // Format nama file: NIS_Nama.ext
            $nama_file_baru = $nis . "_" . $nama_bersih . "." . $ext;
            $path_tujuan = $folder_tujuan . $nama_file_baru;

            if (move_uploaded_file($foto['tmp_name'], $path_tujuan)) {
                // Tambahkan update kolom foto_profil ke query
                $sql_update .= ", foto_profil = '$nama_file_baru' ";
            } else {
                 tampilkan_pesan('error', 'Gagal', 'Gagal mengupload file ke folder tujuan.');
                 exit();
            }
        } else {
            tampilkan_pesan('error', 'Gagal', 'Format file harus JPG, JPEG, atau PNG.');
            exit();
        }
    }

    // Eksekusi Query dengan WHERE berdasarkan Primary Key (id)
    $sql_update .= " WHERE id = '$id_pk'"; 
    
    if (mysqli_query($con, $sql_update)) {
        tampilkan_pesan('sukses', 'Berhasil', 'Data siswa berhasil diperbarui.');
    } else {
        tampilkan_pesan('error', 'Gagal', 'Gagal memperbarui data: ' . mysqli_error($con));
    }
    
    // Redirect dilakukan oleh helper_pesan atau manual jika helper tidak melakukan redirect
    // header("Location: data_siswa.php"); // Uncomment jika helper_pesan hanya set session

    mysqli_close($con);
} else {
    header("Location: data_siswa.php");
}
?>