<?php
session_start();
include 'config/db.php'; 
include 'helper_pesan.php'; // (Kita asumsikan helper_pesan.php ada)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ganti nama variabel
    $nis = mysqli_real_escape_string($con, $_POST['nis']);
    $nama_pegawai = mysqli_real_escape_string($con, $_POST['nama_pegawai']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $no_hp = mysqli_real_escape_string($con, $_POST['no_hp']);
    $kelas = mysqli_real_escape_string($con, $_POST['kelas']);
    $jurusan = mysqli_real_escape_string($con, $_POST['jurusan']);
    $id_telegram = mysqli_real_escape_string($con, $_POST['id_telegram']);

    if (empty($nis) || empty($nama_pegawai)) {
        tampilkan_pesan('error', 'Gagal', 'NIS dan Nama Siswa tidak boleh kosong.');
        exit();
    }
    
    // Ganti Kueri
    $sql = "INSERT INTO siswa (nis, nama_pegawai, email, no_hp, kelas, jurusan, id_telegram) 
            VALUES ('$nis', '$nama_pegawai', '$email', '$no_hp', '$kelas', '$jurusan', '$id_telegram')";

    if (mysqli_query($con, $sql)) {
        tampilkan_pesan('sukses', 'Berhasil', "Siswa '$nama_pegawai' berhasil ditambahkan.");
    } else {
        if (mysqli_errno($con) == 1062) {
             tampilkan_pesan('error', 'Gagal', "NIS '$nis' sudah terdaftar.");
        } else {
             tampilkan_pesan('error', 'Gagal', 'Error: ' . mysqli_error($con));
        }
    }
    mysqli_close($con);
} else {
    header("Location: data_siswa.php");
}
?>