<?php
session_start();
include 'config/db.php';
include 'helper_pesan.php'; // (Kita asumsikan helper_pesan.php ada)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_siswa = mysqli_real_escape_string($con, $_POST['id_karyawan']); // Nama 'id_karyawan' dari form
    $nama_pegawai = mysqli_real_escape_string($con, $_POST['nama_pegawai']);

    // Ganti Kueri
    $sql_update = "UPDATE siswa SET nama_pegawai = '$nama_pegawai' ";

    if (isset($_FILES['foto_profil_baru']) && $_FILES['foto_profil_baru']['error'] == 0) {
        $foto = $_FILES['foto_profil_baru'];
        $folder_tujuan = "foto_profil_siswa/"; // GANTI FOLDER
        $id_pegawai_unik = $_POST['id_pegawai']; // Ini adalah NIS
        $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $nama_bersih = preg_replace("/[^A-Za-z0-9]/", '', $nama_pegawai);
        $nama_file_baru = $id_pegawai_unik . "_" . $nama_bersih . "." . $ext;
        $path_tujuan = $folder_tujuan . $nama_file_baru;

        if (move_uploaded_file($foto['tmp_name'], $path_tujuan)) {
            $sql_update .= ", foto_profil = '$nama_file_baru' ";
        } else {
             tampilkan_pesan('error', 'Gagal', 'Gagal mengupload foto profil.');
             exit();
        }
    }
    $sql_update .= " WHERE id = '$id_siswa'"; // ID unik (primary key)
    
    if (mysqli_query($con, $sql_update)) {
        tampilkan_pesan('sukses', 'Berhasil', 'Data siswa berhasil diperbarui.');
    } else {
        tampilkan_pesan('error', 'Gagal', 'Gagal memperbarui data: ' . mysqli_error($con));
    }
    mysqli_close($con);
} else {
    header("Location: data_siswa.php");
}
?>