<?php
session_start();
include 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data input
    $nama = mysqli_real_escape_string($con, $_POST['nama_lembaga']);
    $alamat = mysqli_real_escape_string($con, $_POST['alamat']);
    $telepon = mysqli_real_escape_string($con, $_POST['telepon']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $website = mysqli_real_escape_string($con, $_POST['website']);

    // Query Dasar Update
    $sql = "UPDATE profil_lembaga SET 
            nama_lembaga = '$nama', 
            alamat = '$alamat', 
            telepon = '$telepon', 
            email = '$email',
            website = '$website'
            WHERE id = 1";

    // Proses Upload Logo Baru (Jika ada)
    if (isset($_FILES['logo_baru']) && $_FILES['logo_baru']['error'] == 0) {
        $foto = $_FILES['logo_baru'];
        $target_dir = "dist/img/"; // Simpan di folder assets template
        $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
        
        // Nama file logo tetap (misal: logo_sekolah.jpg) agar menimpa yang lama atau buat unik
        // Di sini saya pakai timestamp agar browser me-refresh cache gambar
        $nama_file_baru = "logo_" . time() . "." . $ext;
        $target_file = $target_dir . $nama_file_baru;

        if (move_uploaded_file($foto['tmp_name'], $target_file)) {
            // Update database kolom logo jika upload sukses
            // Jalankan query update terpisah untuk logo agar query utama tidak ribet
            $sql_logo = "UPDATE profil_lembaga SET logo = '$target_file' WHERE id = 1";
            mysqli_query($con, $sql_logo);
        }
    }

    // Jalankan Query Utama
    if (mysqli_query($con, $sql)) {
        header("Location: lembaga.php?status=sukses");
    } else {
        echo "Error updating record: " . mysqli_error($con);
    }
    
    mysqli_close($con);
} else {
    header("Location: lembaga.php");
}
?>