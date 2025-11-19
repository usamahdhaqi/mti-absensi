<link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
<link rel="stylesheet" href="dist/css/AdminLTE.min.css">
<link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
<link rel="stylesheet" href="bower_components/morris.js/morris.css">
<link rel="stylesheet" href="bower_components/jvectormap/jquery-jvectormap.css">
<link rel="stylesheet" href="bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="bower_components/bootstrap-daterangepicker/daterangepicker.css">
<link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

<style>
/* * =================================================================
 * SKIN MODERN KUSTOM (JINGGA & HIJAU) - REVISI HEADER FIX
 * =================================================================
 */

/* --- Variabel Warna Utama --- */
:root {
  --color-primary-orange: #F57C00;
  --color-primary-orange-dark: #E65100;
  --color-primary-orange-light: #FFF3E0;
  --color-secondary-green: #388E3C;
  --color-secondary-green-light: #E8F5E9;
  --color-text-dark: #333;
  --color-text-light: #f8f9fa;
  --color-bg-light: #f4f6f9;
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

body, body.skin-blue .wrapper, .wrapper, .content-wrapper {
  background-color: var(--color-bg-light) !important;
}

/* --- 2. Header (header.php) --- */
.main-header {
  max-height: 100px; /* Pastikan header punya tinggi */
}
.main-header .navbar {
  background: linear-gradient(135deg, var(--color-primary-orange) 0%, var(--color-primary-orange-dark) 100%);
  box-shadow: 0 3px 10px rgba(0,0,0,0.1);
  border: none;
}
/* Logo: Pastikan punya tinggi fix dan line-height agar pas */
.main-header .logo {
  background: var(--color-primary-orange-dark) !important;
  font-weight: 700 !important;
  color: var(--color-text-light) !important;
  border-right: none;
  height: 50px !important;
  line-height: 50px !important;
}
.main-header .sidebar-toggle,
.main-header .navbar-nav > li > a {
  color: var(--color-text-light) !important;
}
.main-header .sidebar-toggle:hover {
  background-color: rgba(0,0,0,0.1) !important;
}

/* --- 3. Sidebar (sidebar.php) - MODE NORMAL --- */
.main-sidebar {
  background-color: var(--color-bg-white) !important;
  box-shadow: var(--shadow-md);
  border-right: none !important;
  /* PERBAIKAN DI SINI: Kembalikan padding agar tidak tertutup header */
  padding-top: 50px !important; 
}

/* Panel User */
.user-panel {
  background-color: var(--color-primary-orange-light);
  margin-bottom: 10px;
  padding: 10px;
}
.user-panel > .info, .user-panel > .info > a {
  color: var(--color-primary-orange-dark) !important;
}

/* Menu Items (Normal) */
.sidebar-menu > li.header {
  background: transparent !important;
  color: #999 !important;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 11px;
  padding: 10px 15px;
  letter-spacing: 1px;
}

.sidebar-menu > li > a {
  color: #555 !important;
  padding: 12px 15px;
  margin: 2px 8px;
  border-radius: var(--border-radius-sm);
  transition: all 0.2s ease;
  border-left: none !important;
}

/* Efek Hover */
.sidebar-menu > li:hover > a,
.sidebar-menu > li.active > a {
  background-color: var(--color-primary-orange-light) !important;
  color: var(--color-primary-orange-dark) !important;
  transform: translateX(3px);
}

/* Efek Aktif (Terpilih) */
.sidebar-menu > li.active > a {
  background: linear-gradient(135deg, var(--color-secondary-green) 0%, #4CAF50 100%) !important;
  color: var(--color-text-light) !important;
  box-shadow: 0 4px 10px rgba(56, 142, 60, 0.3);
}

/* ===============================================
   SUB-MENU STYLING (KEREN & MODERN)
   =============================================== */

/* Container Sub-menu */
.treeview-menu {
  background: #f4f6f9 !important; /* Abu-abu sangat muda agar beda dengan menu utama */
  padding-left: 0; /* Reset padding */
  margin-bottom: 5px;
}

/* Link Sub-menu (Default) */
.treeview-menu > li > a {
  color: #6c757d !important; /* Abu-abu medium */
  padding: 10px 15px 10px 35px; /* Indentasi dalam */
  font-size: 14px;
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); /* Transisi mulus */
  border-left: 4px solid transparent; /* Border transparan untuk persiapan hover */
  position: relative;
  display: block;
}

/* Ikon di dalam Sub-menu */
.treeview-menu > li > a > i {
  font-size: 12px; /* Lebih kecil dari ikon utama */
  margin-right: 10px;
  transition: all 0.3s ease;
  color: #adb5bd;
}

/* --- EFEK SAAT DI-HOVER (Kursor Menempel) --- */
.treeview-menu > li > a:hover {
  color: var(--color-primary-orange-dark) !important;
  background-color: #fff; /* Putih bersih */
  border-left-color: var(--color-primary-orange); /* Garis aksen Jingga */
  padding-left: 42px; /* Efek SLIDE ke kanan */
  box-shadow: 0 2px 6px rgba(0,0,0,0.05); /* Bayangan halus */
}

/* Efek Ikon saat Hover */
.treeview-menu > li > a:hover > i {
  color: var(--color-primary-orange);
  transform: scale(1.2); /* Membesar sedikit */
}

/* --- EFEK SAAT AKTIF (Halaman Terpilih) --- */
.treeview-menu > li.active > a {
  color: var(--color-secondary-green) !important;
  background-color: #fff;
  border-left-color: var(--color-secondary-green); /* Garis aksen Hijau */
  font-weight: 600;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.treeview-menu > li.active > a > i {
  color: var(--color-secondary-green);
  transform: rotate(90deg); /* Ikon berputar jika aktif */
}

/* FIX KHUSUS UNTUK SIDEBAR MINI (SAAT DITUTUP)
   Agar submenu pop-out juga terlihat keren
*/
body.sidebar-collapse .sidebar-menu > li:hover > .treeview-menu {
    padding: 0;
    background-color: #fff !important;
}
body.sidebar-collapse .sidebar-menu > li:hover > .treeview-menu > li > a {
    padding-left: 20px; /* Reset indentasi untuk pop-out */
}
body.sidebar-collapse .sidebar-menu > li:hover > .treeview-menu > li > a:hover {
    padding-left: 25px; /* Slide kecil */
    background-color: #f4f6f9;
}

/* * =================================================================
 * FIX SIDEBAR MINI (SAAT DI-TOGGLE/COLLAPSED) 
 * =================================================================
 */

/* 1. Reset Margin & Radius saat collapsed agar ikon rata tengah */
body.sidebar-collapse .sidebar-menu > li > a {
    margin: 0 !important;
    border-radius: 0 !important;
    padding: 12px 5px !important;
    text-align: center;
}

/* 2. Sembunyikan teks span saat collapsed */
body.sidebar-collapse .sidebar-menu > li > a > span {
    display: none !important; 
}

/* 3. Perbaiki posisi ikon saat collapsed */
body.sidebar-collapse .sidebar-menu > li > a > .fa, 
body.sidebar-collapse .sidebar-menu > li > a > .glyphicon, 
body.sidebar-collapse .sidebar-menu > li > a > .ion {
    margin-right: 0 !important;
    font-size: 18px;
    width: auto;
}

/* 4. Perbaiki menu yang muncul saat di-hover (Pop-out menu) */
body.sidebar-collapse .sidebar-menu > li:hover > a {
    background-color: var(--color-primary-orange-dark) !important;
    color: #fff !important;
}

body.sidebar-collapse .sidebar-menu > li:hover > .treeview-menu {
    background-color: #fff !important;
    border-left: 1px solid #ddd;
    border-radius: 0 4px 4px 0;
    box-shadow: 3px 3px 10px rgba(0,0,0,0.1);
    left: 50px !important; 
    padding-top: 5px;
    padding-bottom: 5px;
    top: 44px; 
}

/* 5. Perbaiki judul menu pop-out */
body.sidebar-collapse .sidebar-menu > li:hover > a > span {
    display: block !important;
    position: absolute;
    top: 0;
    left: 45px; 
    padding: 12px 20px;
    background-color: var(--color-primary-orange-dark) !important;
    color: #fff !important;
    width: 220px;
    border-radius: 0 4px 0 0;
    z-index: 1000;
}

/* --- 4. Konten (Content Wrapper) --- */
.content-header > h1 {
  font-weight: 600;
  color: var(--color-text-dark);
}
.box {
  background: var(--color-bg-white);
  border-radius: var(--border-radius-md);
  box-shadow: var(--shadow-md);
  border-top: none !important;
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

/* --- 6. Halaman Login --- */
.login-page .main-header,
.login-page .main-sidebar { display: none; }
.login-page .content-wrapper { margin-left: 0 !important; padding-top: 60px; background: var(--color-bg-light) !important; min-height: 100vh; }
.login-card { background: var(--color-bg-white); border-radius: var(--border-radius-md); box-shadow: var(--shadow-md); overflow: hidden; }
.login-card-header { text-align: center; padding: 25px 20px; }
.login-card-header img { max-width: 180px; margin-bottom: 15px; margin: 0 auto 15px auto; }
.login-card-header .title { font-size: 24px; font-weight: 600; color: var(--color-text-dark); }
.login-card-body { padding: 30px; }
.login-card-body .form-control { border-radius: 25px; height: 45px; padding-left: 20px; }
.login-card-body .btn-primary { border-radius: 25px; height: 45px; font-weight: 700; font-size: 16px; background: var(--color-secondary-green) !important; border: none; transition: all 0.2s ease; }
.login-card-body .btn-primary:hover { background-color: #2E7D32 !important; box-shadow: 0 4px 10px rgba(56, 142, 60, 0.3); transform: translateY(-2px); }
.login-links { margin-top: 15px; display: flex; justify-content: space-between; font-size: 13px; }

/* --- 7. Penyesuaian Responsif --- */
@media (max-width: 767px) {
  .btn-text-mobile-hide { display: none; }
  .box-header .box-tools { float: none; text-align: left; position: relative; top: auto; right: auto; margin-top: 10px; }
  .box-header .box-tools .btn { margin-bottom: 5px; }
  .modal-body { max-height: 60vh; overflow-y: auto; }
  .sidebar-open .main-sidebar { box-shadow: 5px 0 15px rgba(0,0,0,0.2); }
  
  /* Fix Sidebar Mobile Padding */
  .main-sidebar {
      padding-top: 50px !important; /* Mobile kadang juga butuh ini */
  }
}

</style>

<script src="bower_components/jquery/dist/jquery.min.js"></script>
<script src="bower_components/jquery-ui/jquery-ui.min.js"></script>
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="bower_components/raphael/raphael.min.js"></script>
<script src="bower_components/morris.js/morris.min.js"></script>
<script src="bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<script src="bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
<script src="bower_components/moment/min/moment.min.js"></script>
<script src="bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<script src="dist/js/adminlte.min.js"></script>
<script src="dist/js/pages/dashboard.js"></script>
<script src="dist/js/demo.js"></script>