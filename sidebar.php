<?php
// Ambil nama file PHP yang sedang dibuka (misal: "employee.php")
$current_page = basename($_SERVER['PHP_SELF']);

// Tentukan halaman apa saja yang masuk menu "Karyawan"
$karyawan_pages = [
    'employee.php',
    'face_absensi.php',
    'notabsence.php',
    'belumabsence.php',
    'face_keamanan.php',
    'ijin_absensi.php'
];

// Tentukan halaman apa saja yang masuk menu "Peserta Didik"
$siswa_pages = [
    'data_siswa.php',           // (Ini file placeholder, Anda harus membuatnya nanti)
    'absensi_siswa.php',        // (Ini file placeholder)
    'tidak_absensi_siswa.php', // (Ini file placeholder)
    'belum_absensi_siswa.php',  // (Ini file placeholder)
    'keamanan_siswa.php',       // (Ini file placeholder)
    'ijin_absensi_siswa.php'  // (Ini file placeholder)
];

// Cek apakah halaman saat ini ada di dalam array
$is_karyawan_page = in_array($current_page, $karyawan_pages);
$is_siswa_page = in_array($current_page, $siswa_pages);
?>

<aside class="main-sidebar">
  <section class="sidebar">
    
    <div class="user-panel">
      <div class="pull-left image">
        <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info">
        <p><?php echo isset($_SESSION['nama_log']) ? htmlspecialchars($_SESSION['nama_log']) : 'Admin'; ?></p>
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>

    <ul class="sidebar-menu" data-widget="tree">
      <li class="header">NAVIGASI UTAMA</li>
      
      <li class="treeview <?php if ($is_karyawan_page) echo 'active'; ?>">
        <a href="#">
          <i class="fa fa-users"></i> <span>Karyawan</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="<?php if ($current_page == 'employee.php') echo 'active'; ?>"><a href="employee.php"><i class="fa fa-circle-o"></i> Data Karyawan</a></li>
          <li class="<?php if ($current_page == 'face_absensi.php') echo 'active'; ?>"><a href="face_absensi.php"><i class="fa fa-circle-o"></i> Data Absensi</a></li>
          <li class="<?php if ($current_page == 'notabsence.php') echo 'active'; ?>"><a href="notabsence.php"><i class="fa fa-circle-o"></i> Data Tidak Absensi</a></li>
          <li class="<?php if ($current_page == 'belumabsence.php') echo 'active'; ?>"><a href="belumabsence.php"><i class="fa fa-circle-o"></i> Data Belum Absensi</a></li>
          <li class="<?php if ($current_page == 'face_keamanan.php') echo 'active'; ?>"><a href="face_keamanan.php"><i class="fa fa-circle-o"></i> Data Keamanan</a></li>
          <li class="<?php if ($current_page == 'ijin_absensi.php') echo 'active'; ?>"><a href="ijin_absensi.php"><i class="fa fa-circle-o"></i> Izin Absensi</a></li>
        </ul>
      </li>

      <li class="treeview <?php if ($is_siswa_page) echo 'active'; ?>">
        <a href="#">
          <i class="fa fa-graduation-cap"></i> <span>Peserta Didik</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li class="<?php if ($current_page == 'data_siswa.php') echo 'active'; ?>"><a href="data_siswa.php"><i class="fa fa-circle-o"></i> Data Peserta Didik</a></li>
          <li class="<?php if ($current_page == 'absensi_siswa.php') echo 'active'; ?>"><a href="absensi_siswa.php"><i class="fa fa-circle-o"></i> Data Absensi</a></li>
          <li class="<?php if ($current_page == 'tidak_absensi_siswa.php') echo 'active'; ?>"><a href="tidak_absensi_siswa.php"><i class="fa fa-circle-o"></i> Data Tidak Absensi</a></li>
          <li class="<?php if ($current_page == 'belum_absensi_siswa.php') echo 'active'; ?>"><a href="belum_absensi_siswa.php"><i class="fa fa-circle-o"></i> Data Belum Absensi</a></li>
          <li class="<?php if ($current_page == 'keamanan_siswa.php') echo 'active'; ?>"><a href="keamanan_siswa.php"><i class="fa fa-circle-o"></i> Data Keamanan</a></li>
          <li class="<?php if ($current_page == 'ijin_absensi_siswa.php') echo 'active'; ?>"><a href="ijin_absensi_siswa.php"><i class="fa fa-circle-o"></i> Izin Absensi</a></li>
        </ul>
      </li>

      <li class="header">AKUN</li>

      <li>
        <a href="logout.php">
          <i class="fa fa-sign-out"></i> <span>Logout</span>
        </a>
      </li>
      
    </ul>
  </section>
  </aside>