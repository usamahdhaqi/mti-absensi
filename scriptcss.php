<!-- Bootstrap 3.3.7 -->
<link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
<!-- Ionicons -->
<link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="dist/css/AdminLTE.min.css">
<!-- AdminLTE Skins. Choose a skin from the css/skins
     folder instead of downloading all of them to reduce the load. -->
<link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
<!-- Morris chart -->
<link rel="stylesheet" href="bower_components/morris.js/morris.css">
<!-- jvectormap -->
<link rel="stylesheet" href="bower_components/jvectormap/jquery-jvectormap.css">
<!-- Date Picker -->
<link rel="stylesheet" href="bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<!-- Daterange picker -->
<link rel="stylesheet" href="bower_components/bootstrap-daterangepicker/daterangepicker.css">
<!-- bootstrap wysihtml5 - text editor -->
<link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<!-- Google Font -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

<style>
/* * =================================================================
 * SKIN MODERN KUSTOM (JINGGA & HIJAU)
 * =================================================================
 */

/* --- Variabel Warna Utama --- */
:root {
  --color-primary-orange: #F57C00; /* Jingga yang kuat */
  --color-primary-orange-dark: #E65100; /* Jingga lebih gelap */
  --color-primary-orange-light: #FFF3E0; /* Jingga sangat muda */
  --color-secondary-green: #388E3C; /* Hijau yang teduh */
  --color-secondary-green-light: #E8F5E9; /* Hijau sangat muda */
  --color-text-dark: #333;
  --color-text-light: #f8f9fa;
  --color-bg-light: #f4f6f9; /* Latar belakang body */
  --color-bg-white: #ffffff;
  --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
  --shadow-md: 0 5px 15px rgba(0,0,0,0.08);
  --border-radius-md: 12px;
  --border-radius-sm: 8px;
}

/* --- 1. Reset Global & Layout Utama --- */
body.hold-transition {
  background-color: var(--color-bg-light) !important;
  font-family: 'Source Sans Pro', sans-serif;
}

/* Menghapus semua gaya skin-blue bawaan */
body,
body.skin-blue .wrapper, 
.wrapper, 
.content-wrapper {
  background-color: var(--color-bg-light) !important;
}

/* --- 2. Header (header.php) --- */
.main-header .navbar {
  background: linear-gradient(135deg, var(--color-primary-orange) 0%, var(--color-primary-orange-dark) 100%);
  box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}
.main-header .logo {
  background: var(--color-primary-orange-dark) !important;
  font-weight: 700 !important;
  color: var(--color-text-light) !important;
}
.main-header .sidebar-toggle,
.main-header .navbar-nav > li > a {
  color: var(--color-text-light) !important;
}
.main-header .sidebar-toggle:hover {
  background-color: rgba(0,0,0,0.1) !important;
}

/* --- 3. Sidebar (sidebar.php) --- */
.main-sidebar {
  background-color: var(--color-bg-white) !important;
  box-shadow: var(--shadow-md);
  border-right: none !important;
}
.sidebar-menu > li.header {
  background: var(--color-primary-orange-light) !important;
  color: var(--color-primary-orange-dark) !important;
  font-weight: 600;
  padding: 12px 15px;
}
.sidebar-menu > li > a {
  color: #555 !important;
  padding: 14px 15px;
  margin: 4px;
  margin-right: 7px;
  border-radius: var(--border-radius-sm);
  transition: all 0.2s ease;
}
.sidebar-menu > li > a:hover {
  background-color: var(--color-primary-orange-light) !important;
  color: var(--color-primary-orange-dark) !important;
  transform: translateX(3px);
}
.sidebar-menu > li.active > a {
  background: linear-gradient(135deg, var(--color-secondary-green) 0%, #4CAF50 100%) !important;
  color: var(--color-text-light) !important;
  font-weight: 600;
  box-shadow: 0 4px 10px rgba(56, 142, 60, 0.3);
  transform: translateX(3px);
}
.sidebar-menu > li.active > a:hover {
  color: var(--color-text-light) !important;
}
.sidebar-menu .fa {
  width: 20px; /* Merapikan ikon */
}

/* --- 4. Konten (Content Wrapper) --- */
.content-header > h1 {
  font-weight: 600;
  color: var(--color-text-dark);
}
/* Mengubah .box menjadi .card modern */
.box {
  background: var(--color-bg-white);
  border-radius: var(--border-radius-md);
  box-shadow: var(--shadow-md);
  border-top: none !important; /* Hapus border atas bawaan */
  margin-bottom: 25px;
}
.box-header {
  border-bottom: 1px solid #f4f4f4;
  padding: 15px;
}
.box-header .box-title {
  font-weight: 600;
  color: var(--color-primary-orange-dark);
}
.box-body {
  padding: 20px;
}
.box-footer {
  background-color: #fcfcfc;
  border-bottom-left-radius: var(--border-radius-md);
  border-bottom-right-radius: var(--border-radius-md);
}

/* --- 5. Footer (footer.php) --- */
.main-footer {
  background-color: var(--color-bg-white);
  border-top: 1px solid #eee;
  padding: 15px;
  color: #777;
}

/* --- 6. Halaman Login (index.php) --- */
/* Sembunyikan header dan sidebar di halaman login */
.login-page .main-header,
.login-page .main-sidebar {
  display: none;
}
/* Posisikan content-wrapper untuk login */
.login-page .content-wrapper {
  margin-left: 0 !important;
  padding-top: 60px;
  background: var(--color-bg-light) !important;
  min-height: 100vh;
}
/* Kartu Login Modern */
.login-card {
  background: var(--color-bg-white);
  border-radius: var(--border-radius-md);
  box-shadow: var(--shadow-md);
  overflow: hidden; /* Penting untuk border-radius */
}
.login-card-header {
  text-align: center;
  padding: 25px 20px;
}
.login-card-header img {
  max-width: 180px;
  margin-bottom: 15px;
  margin-left: auto;
  margin-right: auto;
}
.login-card-header .title {
  font-size: 24px;
  font-weight: 600;
  color: var(--color-text-dark);
}
.login-card-body {
  padding: 30px;
}
.login-card-body .form-control {
  border-radius: 25px;
  height: 45px;
  padding-left: 20px;
}
.login-card-body .btn-primary {
  border-radius: 25px;
  height: 45px;
  font-weight: 700;
  font-size: 16px;
  background: var(--color-secondary-green) !important;
  border: none;
  transition: all 0.2s ease;
}
.login-card-body .btn-primary:hover {
  background-color: #2E7D32 !important; /* Hijau lebih gelap saat hover */
  box-shadow: 0 4px 10px rgba(56, 142, 60, 0.3);
  transform: translateY(-2px);
}
.login-links {
  margin-top: 15px;
  display: flex;
  justify-content: space-between;
  font-size: 13px;
}

/* ... semua gaya kustom Anda yang ada ... */
.login-links {
  margin-top: 15px;
  display: flex;
  justify-content: space-between;
  font-size: 13px;
}

/* --- 7. Penyesuaian Responsif --- */
@media (max-width: 767px) {
  
  /* Sembunyikan teks pada tombol di header .box untuk layar kecil */
  .btn-text-mobile-hide {
    display: none;
  }

  /* Rapikan tata letak box-tools di mobile.
    Ini akan membuat judul (box-title) tampil dulu, 
    lalu tombol-tombol akan muncul di baris baru di bawahnya.
    Ini jauh lebih rapi di layar sempit.
  */
  .box-header .box-tools {
    float: none; /* Hapus float */
    text-align: left; /* Ratakan tombol ke kiri (atau 'right' jika suka) */
    position: relative;
    top: auto;
    right: auto;
    margin-top: 10px; /* Beri jarak dari judul */
  }

  /* Beri jarak antar tombol */
  .box-header .box-tools .btn {
    margin-bottom: 5px; /* Tambahkan margin bawah jika tombolnya jadi 2 baris */
  }

  /* Membuat modal (popup) bisa di-scroll di layar kecil 
    jika kontennya sangat panjang.
  */
  .modal-body {
     max-height: 60vh; /* Batasi tinggi modal body */
     overflow-y: auto; /* Tambahkan scroll jika perlu */
  }

}

</style>

<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="bower_components/raphael/raphael.min.js"></script>
<script src="bower_components/morris.js/morris.min.js"></script>
<!-- Sparkline -->
<script src="bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="bower_components/moment/min/moment.min.js"></script>
<script src="bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
