<?php
// SELALU mulai session di baris paling atas
session_start();

// 1. Hubungkan ke database
include 'config/db.php'; 

// Pastikan variabel koneksi Anda adalah $con
if (!$con) {
    // Jika koneksi database-nya sendiri yang gagal
    $_SESSION['notif_status'] = 'error';
    $_SESSION['notif_message'] = 'Koneksi ke database gagal: ' . mysqli_connect_error();
    header("Location: employee.php");
    exit();
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

    // Validasi Sederhana (contoh: pastikan ID dan Nama tidak kosong)
    if (empty($id_pegawai) || empty($nama_pegawai)) {
        $_SESSION['notif_status'] = 'error';
        $_SESSION['notif_message'] = 'Gagal: ID Pegawai dan Nama Pegawai tidak boleh kosong.';
        header("Location: employee.php");
        exit();
    }

    // 4. Buat kueri SQL
    // GANTI 'employee' jika nama tabel Anda berbeda
    $sql = "INSERT INTO employee (id_pegawai, nama_pegawai, email, no_hp, divisi, jabatan, id_telegram) 
            VALUES ('$id_pegawai', '$nama_pegawai', '$email', '$no_hp', '$divisi', '$jabatan', '$id_telegram')";

    // 5. Eksekusi kueri
    if (mysqli_query($con, $sql)) {
        // JIKA SUKSES
        $_SESSION['notif_status'] = 'sukses';
        $_SESSION['notif_message'] = "Karyawan '$nama_pegawai' berhasil ditambahkan.";
        
    } else {
        // JIKA GAGAL
        $_SESSION['notif_status'] = 'error';
        
        // Cek error spesifik (contoh: NIK/ID sudah ada)
        if (mysqli_errno($con) == 1062) { // 1062 = Error 'Duplicate entry'
            $_SESSION['notif_message'] = "Gagal: ID Pegawai '$id_pegawai' sudah terdaftar. Gunakan ID lain.";
        } else {
            // Error umum lainnya
            $_SESSION['notif_message'] = 'Error: ' . mysqli_error($con);
        }
    }

    // 6. Tutup koneksi dan redirect kembali APAPUN HASILNYA
    mysqli_close($con);
    header("Location: employee.php");
    exit();

} else {
    // Jika file diakses langsung, tendang
    header("Location: employee.php");
    exit();
}
?>