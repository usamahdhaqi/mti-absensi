<?php
// Mulai session
session_start();

// 1. Hubungkan ke database
include 'config/db.php'; 

// Pastikan variabel koneksi Anda adalah $con
if (!$con) {
    // Jika koneksi database itu sendiri gagal, kita tidak bisa melanjutkan
    die("Koneksi gagal: " . mysqli_connect_error());
}

// 2. Cek apakah data dikirim dari form (method POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Ambil data dari form
    $id_pegawai = mysqli_real_escape_string($con, $_POST['id_pegawai']);
    $nama_pegawai = mysqli_real_escape_string($con, $_POST['nama_pegawai']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $no_hp = mysqli_real_escape_string($con, $_POST['no_hp']);
    $divisi = mysqli_real_escape_string($con, $_POST['divisi']);
    $jabatan = mysqli_real_escape_string($con, $_POST['jabatan']);
    $id_telegram = mysqli_real_escape_string($con, $_POST['id_telegram']);

    // 4. Buat kueri SQL
    // GANTI 'employee' DENGAN NAMA TABEL ANDA JIKA BERBEDA
    $sql = "INSERT INTO employee (id_pegawai, nama_pegawai, email, no_hp, divisi, jabatan, id_telegram) 
            VALUES ('$id_pegawai', '$nama_pegawai', '$email', '$no_hp', '$divisi', '$jabatan', '$id_telegram')";

    // 5. Eksekusi kueri dan siapkan redirect
    if (mysqli_query($con, $sql)) {
        // --- SUKSES ---
        // Kirim status sukses kembali ke halaman employee
        header("Location: employee.php?status=sukses_tambah");
        exit();
    } else {
        // --- GAGAL ---
        // Ambil pesan error dari MySQL
        $error_message = mysqli_error($con);
        
        // Kirim status gagal DAN pesan error-nya kembali ke halaman employee
        // urlencode() penting agar pesan error aman dibawa di URL
        header("Location: employee.php?status=gagal_tambah&error=" . urlencode($error_message));
        exit();
    }

    // 6. Tutup koneksi (baris ini tidak akan pernah tercapai, tapi ini praktik yang baik)
    mysqli_close($con);

} else {
    // Jika file diakses langsung tanpa POST, tendang kembali
    header("Location: employee.php");
    exit();
}
?>