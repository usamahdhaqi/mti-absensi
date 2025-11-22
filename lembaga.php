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
      /* Sedikit styling tambahan untuk tampilan info */
      .profile-info-name {
          font-weight: 600;
          color: #555;
          width: 140px;
      }
      .profile-info-value {
          color: #333;
      }
      .box-info-row {
          padding: 15px 0;
          border-bottom: 1px solid #f4f4f4;
      }
      .box-info-row:last-child {
          border-bottom: none;
      }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include('header.php'); include('sidebar.php'); ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Informasi Lembaga</h1>
    </section>

    <section class="content">
      
      <div class="row">
        
        <div class="col-md-4">
          <div class="box box-primary">
            <div class="box-body box-profile">
              <div class="text-center">
                  <img class="profile-user-img img-responsive img-circle" src="<?php echo $logo_path; ?>" alt="Logo Lembaga" style="width: 150px; height: 150px; border: 3px solid #eee; margin-bottom: 20px;">
              </div>

              <h3 class="profile-username text-center"><?php echo htmlspecialchars($nama); ?></h3>
              <p class="text-muted text-center">Profil Resmi</p>

              <a href="#" class="btn btn-primary btn-block">
                  <i class="fa fa-check-circle"></i> Terverifikasi
              </a>
            </div>
          </div>
        </div>

        <div class="col-md-8">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-info-circle"></i> Detail Lengkap</h3>
            </div>
            <div class="box-body">
                
                <div class="box-info-row">
                    <div class="row">
                        <div class="col-sm-3 profile-info-name">Nama Lembaga</div>
                        <div class="col-sm-9 profile-info-value">
                            <strong class="text-primary" style="font-size: 16px;"><?php echo htmlspecialchars($nama); ?></strong>
                        </div>
                    </div>
                </div>

                <div class="box-info-row">
                    <div class="row">
                        <div class="col-sm-3 profile-info-name">Alamat</div>
                        <div class="col-sm-9 profile-info-value">
                            <i class="fa fa-map-marker text-danger"></i> &nbsp; 
                            <?php echo nl2br(htmlspecialchars($alamat)); ?>
                        </div>
                    </div>
                </div>

                <div class="box-info-row">
                    <div class="row">
                        <div class="col-sm-3 profile-info-name">Telepon / Fax</div>
                        <div class="col-sm-9 profile-info-value">
                            <i class="fa fa-phone text-success"></i> &nbsp; 
                            <?php echo htmlspecialchars($telepon); ?>
                        </div>
                    </div>
                </div>

                <div class="box-info-row">
                    <div class="row">
                        <div class="col-sm-3 profile-info-name">Email Resmi</div>
                        <div class="col-sm-9 profile-info-value">
                            <i class="fa fa-envelope text-warning"></i> &nbsp; 
                            <?php echo htmlspecialchars($email); ?>
                        </div>
                    </div>
                </div>

                <div class="box-info-row">
                    <div class="row">
                        <div class="col-sm-3 profile-info-name">Website</div>
                        <div class="col-sm-9 profile-info-value">
                            <i class="fa fa-globe text-info"></i> &nbsp; 
                            <a href="<?php echo htmlspecialchars($website); ?>" target="_blank"><?php echo htmlspecialchars($website); ?></a>
                        </div>
                    </div>
                </div>

            </div>
          </div>
          
          <div class="callout callout-info" style="background-color: #E8F5E9 !important; border-color: #388E3C !important; color: #2E7D32 !important;">
            <h4><i class="icon fa fa-bullhorn"></i> Informasi!</h4>
            <p>Data ini ditampilkan pada form absensi dan surat perizinan resmi. Jika terdapat kesalahan data, harap hubungi Administrator Database.</p>
          </div>

        </div>
      </div>

    </section>
  </div>
  <?php include('footer.php'); ?>
</div>

</body>
</html>