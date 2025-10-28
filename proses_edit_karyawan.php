<?php
session_start();
include 'config/db.php'; // Hubungkan ke DB

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data dari form
    $id_karyawan = mysqli_real_escape_string($con, $_POST['id_karyawan']); // ID unik (primary key)
    $nama_pegawai = mysqli_real_escape_string($con, $_POST['nama_pegawai']);
    // (Anda bisa tambahkan field lain di sini jika ingin mengeditnya juga, misal divisi, jabatan)

    $sql_update = "UPDATE employee SET nama_pegawai = '$nama_pegawai' ";

    // === PROSES UPLOAD FOTO PROFIL (JIKA ADA) ===
    if (isset($_FILES['foto_profil_baru']) && $_FILES['foto_profil_baru']['error'] == 0) {
        
        $foto = $_FILES['foto_profil_baru'];
        $folder_tujuan = "foto_profil_karyawan/";
        $id_pegawai_unik = $_POST['id_pegawai']; // Ambil NIK untuk nama file

        // Buat nama file baru yang bersih (misal: 0001_Budi.jpg)
        $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $nama_bersih = preg_replace("/[^A-Za-z0-9]/", '', $nama_pegawai);
        $nama_file_baru = $id_pegawai_unik . "_" . $nama_bersih . "." . $ext;
        
        $path_tujuan = $folder_tujuan . $nama_file_baru;

        // Pindahkan file
        if (move_uploaded_file($foto['tmp_name'], $path_tujuan)) {
            // Jika upload berhasil, tambahkan nama file ke kueri SQL
            $sql_update .= ", foto_profil = '$nama_file_baru' ";
        } else {
            // Gagal upload foto
            $_SESSION['notif_status'] = 'error';
            $_SESSION['notif_message'] = 'Gagal mengupload foto profil.';
            header("Location: employee.php");
            exit();
        }
    }
    // === AKHIR PROSES UPLOAD FOTO ===

    // Tambahkan klausa WHERE di akhir
    $sql_update .= " WHERE id = '$id_karyawan'";

    // Eksekusi kueri UPDATE
    if (mysqli_query($con, $sql_update)) {
        $_SESSION['notif_status'] = 'sukses';
        $_SESSION['notif_message'] = 'Data karyawan berhasil diperbarui.';
    } else {
        $_SESSION['notif_status'] = 'error';
        $_SESSION['notif_message'] = 'Gagal memperbarui data: ' . mysqli_error($con);
    }

    mysqli_close($con);
    header("Location: employee.php");
    exit();

} else {
    header("Location: employee.php");
    exit();
}
?>