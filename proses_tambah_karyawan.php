<?php
// SELALU mulai session di baris paling atas
session_start();

// 1. Hubungkan ke database
include 'config/db.php'; 

// Pastikan variabel koneksi Anda adalah $con
if (!$con) {
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

    // Logika untuk ID Telegram (dari error Anda sebelumnya)
    $id_telegram_input = mysqli_real_escape_string($con, $_POST['id_telegram']);
    $id_telegram_sql = "NULL"; // Default-nya NULL
    if (!empty($id_telegram_input)) {
        // Jika tidak kosong, gunakan nilainya
        $id_telegram_sql = "'$id_telegram_input'"; 
    }

    // === Validasi 1: Cek Kosong ===
    if (empty($id_pegawai) || empty($nama_pegawai)) {
        $_SESSION['notif_status'] = 'error';
        $_SESSION['notif_message'] = 'Gagal: ID Pegawai dan Nama Pegawai tidak boleh kosong.';
        header("Location: employee.php");
        exit();
    }

    // =======================================================
    // === VALIDASI 2: CEK DUPLIKAT ID PEGAWAI (BARU) ===
    // =======================================================
    // Ganti 'employee' jika nama tabel Anda berbeda
    $cek_sql = "SELECT COUNT(*) as total FROM employee WHERE id_pegawai = '$id_pegawai'";
    $cek_hasil = mysqli_query($con, $cek_sql);
    $cek_data = mysqli_fetch_assoc($cek_hasil);

    if ($cek_data['total'] > 0) {
        // Jika ID sudah ada (total > 0), kirim notifikasi error
        $_SESSION['notif_status'] = 'error';
        $_SESSION['notif_message'] = "Gagal: ID Pegawai '$id_pegawai' sudah terdaftar. Gunakan ID lain.";
        header("Location: employee.php");
        exit(); // Hentikan eksekusi
    }
    // =======================================================
    // === AKHIR VALIDASI 2 ===
    // =======================================================


    // 4. Buat kueri SQL (Hanya akan berjalan jika lolos 2 validasi di atas)
    // Ganti 'employee' jika nama tabel Anda berbeda
    $sql = "INSERT INTO employee (id_pegawai, nama_pegawai, email, no_hp, divisi, jabatan, id_telegram) 
            VALUES ('$id_pegawai', '$nama_pegawai', '$email', '$no_hp', '$divisi', '$jabatan', $id_telegram_sql)";

    // 5. Eksekusi kueri
    if (mysqli_query($con, $sql)) {
        // JIKA SUKSES
        $_SESSION['notif_status'] = 'sukses';
        $_SESSION['notif_message'] = "Karyawan '$nama_pegawai' berhasil ditambahkan.";
        
    } else {
        // JIKA GAGAL (Meskipun sudah dicek, ini jaring pengaman kedua)
        $_SESSION['notif_status'] = 'error';
        if (mysqli_errno($con) == 1062) { // 1062 = Error 'Duplicate entry'
            $_SESSION['notif_message'] = "Gagal: ID Pegawai '$id_pegawai' sudah terdaftar.";
        } else {
            $_SESSION['notif_message'] = 'Error: ' . mysqli_error($con);
        }
    }

    // 6. Tutup koneksi dan redirect kembali
    mysqli_close($con);
    header("Location: employee.php");
    exit();

} else {
    // Jika file diakses langsung, tendang
    header("Location: employee.php");
    exit();
}
?>