<?php
// 1. Ambil nama file halaman saat ini
$current_page = basename($_SERVER['PHP_SELF']);

// 2. Daftar Halaman Karyawan
$karyawan_pages = [
    'employee.php',
    'face_absensi.php',
    'notabsence.php',
    'belumabsence.php',
    'face_keamanan.php',
    'ijin_absensi.php'
];

// 3. Daftar Halaman Peserta Didik
$siswa_pages = [
    'data_siswa.php',
    'absensi_siswa.php',
    'tidak_absensi_siswa.php',
    'belum_absensi_siswa.php',
    'keamanan_siswa.php',
    'ijin_absensi_siswa.php'
];

// 4. Logika Cek Aktif (True/False)
$is_karyawan_active = in_array($current_page, $karyawan_pages);
$is_siswa_active = in_array($current_page, $siswa_pages);
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

      <li class="<?php echo ($current_page == 'lembaga.php') ? 'active' : ''; ?>">
        <a href="lembaga.php">
          <i class="fa fa-university"></i> <span>Profil Lembaga</span>
        </a>
      </li>
      
      <li class="treeview <?php echo ($is_karyawan_active) ? 'active menu-open' : ''; ?>">
        <a href="#">
          <i class="fa fa-users"></i> <span>Karyawan</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu" style="<?php echo ($is_karyawan_active) ? 'display: block;' : ''; ?>">
          <li class="<?php echo ($current_page == 'employee.php') ? 'active' : ''; ?>">
            <a href="employee.php"><i class="fa fa-circle-o"></i> Data Karyawan</a>
          </li>
          <li class="<?php echo ($current_page == 'face_absensi.php') ? 'active' : ''; ?>">
            <a href="face_absensi.php"><i class="fa fa-circle-o"></i> Data Absensi</a>
          </li>
          <li class="<?php echo ($current_page == 'notabsence.php') ? 'active' : ''; ?>">
            <a href="notabsence.php"><i class="fa fa-circle-o"></i> Data Tidak Absensi</a>
          </li>
          <li class="<?php echo ($current_page == 'belumabsence.php') ? 'active' : ''; ?>">
            <a href="belumabsence.php"><i class="fa fa-circle-o"></i> Data Belum Absensi</a>
          </li>
          <li class="<?php echo ($current_page == 'face_keamanan.php') ? 'active' : ''; ?>">
            <a href="face_keamanan.php"><i class="fa fa-circle-o"></i> Data Keamanan</a>
          </li>
          <li class="<?php echo ($current_page == 'ijin_absensi.php') ? 'active' : ''; ?>">
            <a href="ijin_absensi.php"><i class="fa fa-circle-o"></i> Izin Absensi</a>
          </li>
        </ul>
      </li>

      <li class="treeview <?php echo ($is_siswa_active) ? 'active menu-open' : ''; ?>">
        <a href="#">
          <i class="fa fa-graduation-cap"></i> <span>Peserta Didik</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu" style="<?php echo ($is_siswa_active) ? 'display: block;' : ''; ?>">
          <li class="<?php echo ($current_page == 'data_siswa.php') ? 'active' : ''; ?>">
            <a href="data_siswa.php"><i class="fa fa-circle-o"></i> Data Peserta Didik</a>
          </li>
          <li class="<?php echo ($current_page == 'absensi_siswa.php') ? 'active' : ''; ?>">
            <a href="absensi_siswa.php"><i class="fa fa-circle-o"></i> Data Absensi</a>
          </li>
          <li class="<?php echo ($current_page == 'tidak_absensi_siswa.php') ? 'active' : ''; ?>">
            <a href="tidak_absensi_siswa.php"><i class="fa fa-circle-o"></i> Data Tidak Absensi</a>
          </li>
          <li class="<?php echo ($current_page == 'belum_absensi_siswa.php') ? 'active' : ''; ?>">
            <a href="belum_absensi_siswa.php"><i class="fa fa-circle-o"></i> Data Belum Absensi</a>
          </li>
          <li class="<?php echo ($current_page == 'keamanan_siswa.php') ? 'active' : ''; ?>">
            <a href="keamanan_siswa.php"><i class="fa fa-circle-o"></i> Data Keamanan</a>
          </li>
          <li class="<?php echo ($current_page == 'ijin_absensi_siswa.php') ? 'active' : ''; ?>">
            <a href="ijin_absensi_siswa.php"><i class="fa fa-circle-o"></i> Izin Absensi</a>
          </li>
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