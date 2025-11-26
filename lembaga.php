<?php
session_start();
include 'config/db.php';
if (!isset($_SESSION['nama_log'])){ header("location: index.php"); }

// Ambil data lembaga (ID 1)
$query = mysqli_query($con, "SELECT * FROM profil_lembaga WHERE id = 1");
$data = mysqli_fetch_assoc($query);

// Default data jika kosong
$nama = !empty($data['nama_lembaga']) ? $data['nama_lembaga'] : '-';
$alamat = !empty($data['alamat']) ? $data['alamat'] : '-';
$telepon = !empty($data['telepon']) ? $data['telepon'] : '-';
$email = !empty($data['email']) ? $data['email'] : '-';
$website = !empty($data['website']) ? $data['website'] : '-';
$logo_path = !empty($data['logo']) ? $data['logo'] : 'dist/img/logomti.jpg';
?>
<!DOCTYPE html>
<html>
<head>
  <title>MTI Absensi | Profil Lembaga</title>
  <?php include('scriptcss.php'); ?>
  <style>
      /* --- MODERN CSS OVERRIDES (THEME: ORANGE-GREEN) --- */
      .content-wrapper {
          background-color: #f4f6f9;
      }
      
      .modern-card {
          background: #fff;
          border-radius: 15px;
          box-shadow: 0 10px 25px rgba(0,0,0,0.05);
          border: none;
          margin-bottom: 30px;
          overflow: hidden;
          transition: all 0.3s ease;
      }
      
      .modern-card:hover {
          transform: translateY(-5px);
          box-shadow: 0 15px 35px rgba(0,0,0,0.1);
      }

      /* --- GRADASI JINGGA KE HIJAU --- */
      .profile-header-bg {
          height: 130px;
          /* Gradient from Orange to Green */
          background: linear-gradient(135deg, #F57C00 0%, #43A047 100%); 
          position: relative;
      }

      .profile-img-container {
          position: relative;
          margin-top: -75px;
          text-align: center;
      }

      .profile-img-custom {
          width: 160px;
          height: 160px;
          object-fit: contain;
          border: 5px solid #fff;
          border-radius: 25px;
          box-shadow: 0 10px 20px rgba(0,0,0,0.15);
          background: #fff;
          padding: 5px;
      }

      .profile-name-large {
          font-size: 22px;
          font-weight: 700;
          color: #333;
          margin-top: 15px;
          margin-bottom: 5px;
      }

      /* Badge nuansa Jingga */
      .profile-badge {
          background: #FFF3E0; /* Orange muda */
          color: #E65100;      /* Orange tua */
          padding: 5px 15px;
          border-radius: 20px;
          font-size: 12px;
          font-weight: 600;
          display: inline-block;
          margin-bottom: 20px;
          border: 1px solid #FFE0B2;
      }

      /* Tombol Custom Jingga */
      .btn-orange-gradient {
          background: linear-gradient(to right, #FF9800, #F57C00);
          color: white;
          border: none;
          box-shadow: 0 4px 10px rgba(245, 124, 0, 0.3);
          transition: all 0.2s;
      }
      .btn-orange-gradient:hover {
          background: linear-gradient(to right, #F57C00, #EF6C00);
          color: white;
          box-shadow: 0 6px 15px rgba(245, 124, 0, 0.4);
      }

      .info-list-item {
          display: flex;
          align-items: center;
          padding: 20px;
          border-bottom: 1px solid #f0f0f0;
      }
      .info-list-item:last-child {
          border-bottom: none;
      }

      .icon-box {
          width: 50px;
          height: 50px;
          border-radius: 12px;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 20px;
          margin-right: 20px;
          flex-shrink: 0;
      }

      /* Update warna icon agar match dengan tema alam/segar */
      .bg-icon-primary { background: #E8F5E9; color: #2E7D32; } /* Hijau untuk nama */
      .bg-icon-danger { background: #FFEBEE; color: #C62828; }   /* Merah tetap untuk lokasi */
      .bg-icon-success { background: #E0F2F1; color: #00695C; }  /* Teal untuk telepon */
      .bg-icon-warning { background: #FFF3E0; color: #EF6C00; }  /* Orange untuk email */
      .bg-icon-info { background: #E3F2FD; color: #1565C0; }     /* Biru untuk web */

      .info-content h5 {
          margin: 0 0 5px 0;
          color: #888;
          font-size: 12px;
          text-transform: uppercase;
          letter-spacing: 1px;
      }

      .info-content p {
          margin: 0;
          font-size: 16px;
          font-weight: 600;
          color: #333;
      }

      .info-content a {
          color: #333;
          text-decoration: none;
      }
      .info-content a:hover {
          color: #F57C00; /* Hover jadi orange */
      }

      /* Callout Nuansa Hijau */
      .modern-callout {
          background: linear-gradient(to right, #E8F5E9, #fff);
          border-left: 5px solid #43A047;
          padding: 20px;
          border-radius: 8px;
          box-shadow: 0 5px 15px rgba(0,0,0,0.03);
      }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include('header.php'); include('sidebar.php'); ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Informasi Lembaga
        <small>Profil Instansi</small>
      </h1>
    </section>

    <section class="content">
      
      <div class="row">
        
        <div class="col-md-4">
          <div class="modern-card">
            <div class="profile-header-bg"></div>
            
            <div class="profile-img-container">
                <img class="profile-img-custom" src="<?php echo $logo_path; ?>" alt="Logo Lembaga">
            </div>

            <div class="text-center" style="padding: 0 20px 30px 20px;">
              <h3 class="profile-name-large"><?php echo htmlspecialchars($nama); ?></h3>
              <span class="profile-badge"><i class="fa fa-check-circle"></i> Akun Resmi Terverifikasi</span>
              
              <p class="text-muted" style="font-size: 14px; line-height: 1.6;">
                Profil ini digunakan sebagai identitas resmi pada seluruh output dokumen dan absensi sistem.
              </p>
              
              <br>
              <a href="edit_lembaga.php" class="btn btn-orange-gradient btn-block btn-flat">
                  <i class="fa fa-cog"></i> &nbsp; Pengaturan Profil
              </a>
            </div>
          </div>
        </div>

        <div class="col-md-8">
          <div class="modern-card">
            <div class="box-header with-border" style="padding: 20px;">
              <h3 class="box-title" style="font-weight: 600; color: #2E7D32;"><i class="fa fa-building-o"></i> &nbsp;Detail Lengkap</h3>
            </div>
            <div class="box-body" style="padding: 0;">
                
                <div class="info-list-item">
                    <div class="icon-box bg-icon-primary">
                        <i class="fa fa-id-card-o"></i>
                    </div>
                    <div class="info-content">
                        <h5>Nama Lembaga</h5>
                        <p><?php echo htmlspecialchars($nama); ?></p>
                    </div>
                </div>

                <div class="info-list-item">
                    <div class="icon-box bg-icon-danger">
                        <i class="fa fa-map-marker"></i>
                    </div>
                    <div class="info-content">
                        <h5>Alamat Lengkap</h5>
                        <p><?php echo nl2br(htmlspecialchars($alamat)); ?></p>
                    </div>
                </div>

                <div class="info-list-item">
                    <div class="icon-box bg-icon-success">
                        <i class="fa fa-phone"></i>
                    </div>
                    <div class="info-content">
                        <h5>Telepon / Fax</h5>
                        <p><?php echo htmlspecialchars($telepon); ?></p>
                    </div>
                </div>

                <div class="info-list-item">
                    <div class="icon-box bg-icon-warning">
                        <i class="fa fa-envelope-o"></i>
                    </div>
                    <div class="info-content">
                        <h5>Email Resmi</h5>
                        <p><?php echo htmlspecialchars($email); ?></p>
                    </div>
                </div>

                <div class="info-list-item">
                    <div class="icon-box bg-icon-info">
                        <i class="fa fa-globe"></i>
                    </div>
                    <div class="info-content">
                        <h5>Website</h5>
                        <p>
                            <a href="<?php echo htmlspecialchars($website); ?>" target="_blank">
                                <?php echo htmlspecialchars($website); ?> <i class="fa fa-external-link" style="font-size: 12px; margin-left: 5px;"></i>
                            </a>
                        </p>
                    </div>
                </div>

            </div>
          </div>
          
          <div class="modern-callout">
            <h4 style="margin-top: 0; color: #2E7D32; font-size: 18px;"><i class="icon fa fa-bullhorn"></i> Informasi Penting</h4>
            <p style="color: #555;">Data di atas disinkronisasi secara otomatis ke dalam <b>Kop Surat</b> dan <b>Formulir Absensi</b>. Pastikan data selalu diperbarui.</p>
          </div>

        </div>
      </div>

    </section>
  </div>
  <?php include('footer.php'); ?>
</div>

</body>
</html>