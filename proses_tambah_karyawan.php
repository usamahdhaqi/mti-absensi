<?php
// Mulai session
session_start();

// 1. Hubungkan ke database
include 'config/db.php'; 

// Pastikan variabel koneksi Anda adalah $con
if (!$con) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// 2. Cek apakah data dikirim dari form (method POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Ambil data dari form dan bersihkan (basic security)
    // Sesuaikan dengan name="" di form modal Anda
    $id_pegawai = mysqli_real_escape_string($con, $_POST['id_pegawai']);
    $nama_pegawai = mysqli_real_escape_string($con, $_POST['nama_pegawai']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $no_hp = mysqli_real_escape_string($con, $_POST['no_hp']);
    $divisi = mysqli_real_escape_string($con, $_POST['divisi']);
    $jabatan = mysqli_real_escape_string($con, $_POST['jabatan']);
    $id_telegram = mysqli_real_escape_string($con, $_POST['id_telegram']);

    // Kolom 'id' (auto-increment) dan 'warning' (default 0) tidak perlu dimasukkan di sini

    // 4. Buat kueri SQL untuk INSERT data
    // PERINGATAN: Ganti 'nama_tabel_karyawan' dengan nama tabel Anda yang sebenarnya!
    // Kemungkinan nama tabelnya adalah 'employee' atau 'karyawan'
    $sql = "INSERT INTO employee (id_pegawai, nama_pegawai, email, no_hp, divisi, jabatan, id_telegram) 
            VALUES ('$id_pegawai', '$nama_pegawai', '$email', '$no_hp', '$divisi', '$jabatan', '$id_telegram')";

    // 5. Eksekusi kueri
    if (mysqli_query($con, $sql)) {
        // Jika berhasil, kembalikan ke halaman employee
        header("Location: employee.php?status=sukses_tambah");
        exit();
    } else {
        // Jika gagal
        echo "Error: " . $sql . "<br>"."<br>". mysqli_error($con);
        echo "<br><br><a href='employee.php'>Kembali ke Halaman Karyawan</a>";
    }

    // 6. Tutup koneksi
    mysqli_close($con);

} else {
    // Jika file diakses langsung tanpa POST, tendang kembali
    header("Location: employee.php");
    exit();
}
?>