<?php
session_start();
include 'config/db.php';
if (!isset($_SESSION['nama_log'])){ header("location: index.php"); }

// Proses Update Data
if (isset($_POST['simpan'])) {
    $nama    = mysqli_real_escape_string($con, $_POST['nama']);
    $alamat  = mysqli_real_escape_string($con, $_POST['alamat']);
    $telepon = mysqli_real_escape_string($con, $_POST['telepon']);
    $email   = mysqli_real_escape_string($con, $_POST['email']);
    $website = mysqli_real_escape_string($con, $_POST['website']);

    // Cek apakah ada upload gambar logo baru
    if ($_FILES['logo']['name'] != '') {
        $allowed_ext = array('png', 'jpg', 'jpeg');
        $filename = $_FILES['logo']['name'];
        $x = explode('.', $filename);
        $ekstensi = strtolower(end($x));
        $file_tmp = $_FILES['logo']['tmp_name'];
        
        if (in_array($ekstensi, $allowed_ext) === true) {
            // Nama file baru (timestamp agar unik)
            $new_filename = 'logo_' . time() . '.' . $ekstensi;
            $path = 'dist/img/' . $new_filename;
            
            // Upload file
            move_uploaded_file($file_tmp, $path);
            
            // Update database dengan logo
            $update = mysqli_query($con, "UPDATE profil_lembaga SET 
                nama_lembaga='$nama', 
                alamat='$alamat', 
                telepon='$telepon', 
                email='$email', 
                website='$website',
                logo='$path'
                WHERE id=1");
        } else {
            echo "<script>alert('Ekstensi gambar tidak diperbolehkan! Gunakan JPG atau PNG.');</script>";
        }
    } else {
        // Update database TANPA ganti logo
        $update = mysqli_query($con, "UPDATE profil_lembaga SET 
            nama_lembaga='$nama', 
            alamat='$alamat', 
            telepon='$telepon', 
            email='$email', 
            website='$website'
            WHERE id=1");
    }

    if ($update) {
        echo "<script>alert('Data profil berhasil diperbarui!'); window.location='lembaga.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data!');</script>";
    }
}

// Ambil data lama untuk ditampilkan di form
$query = mysqli_query($con, "SELECT * FROM profil_lembaga WHERE id = 1");
$data = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html>
<head>
  <title>MTI Absensi | Edit Profil</title>
  <?php include('scriptcss.php'); ?>
  <style>
      /* --- MODERN STYLING (SAME THEME) --- */
      .content-wrapper { background-color: #f4f6f9; }
      
      .modern-card {
          background: #fff;
          border-radius: 15px;
          box-shadow: 0 10px 25px rgba(0,0,0,0.05);
          border: none;
          margin-bottom: 30px;
          padding: 30px;
      }

      /* Header Gradient */
      .header-title {
          font-weight: 700;
          color: #2E7D32; /* Hijau */
          margin-bottom: 20px;
          padding-bottom: 10px;
          border-bottom: 2px solid #E8F5E9;
      }

      /* Form Styling */
      .form-group label {
          color: #555;
          font-weight: 600;
      }
      
      .form-control {
          border-radius: 8px;
          border: 1px solid #ddd;
          padding: 10px;
          height: auto;
      }
      
      .form-control:focus {
          border-color: #F57C00; /* Fokus Orange */
          box-shadow: 0 0 5px rgba(245, 124, 0, 0.2);
      }

      /* Tombol */
      .btn-save {
          background: linear-gradient(to right, #43A047, #2E7D32);
          color: white;
          border: none;
          border-radius: 8px;
          padding: 12px 30px;
          font-weight: bold;
          transition: 0.3s;
      }
      
      .btn-save:hover {
          box-shadow: 0 5px 15px rgba(46, 125, 50, 0.4);
          color: white;
      }

      .btn-cancel {
          background: #f1f1f1;
          color: #555;
          border: none;
          border-radius: 8px;
          padding: 12px 20px;
          margin-right: 10px;
          font-weight: 600;
      }
      .btn-cancel:hover { background: #e0e0e0; }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include('header.php'); include('sidebar.php'); ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Edit Profil
        <small>Perbarui Informasi Lembaga</small>
      </h1>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          
          <form method="POST" enctype="multipart/form-data">
            <div class="modern-card">
              <h3 class="header-title"><i class="fa fa-pencil-square-o"></i> Form Perubahan Data</h3>
              
              <div class="form-group">
                  <label>Nama Lembaga</label>
                  <input type="text" name="nama" class="form-control" value="<?php echo $data['nama_lembaga']; ?>" required>
              </div>

              <div class="form-group">
                  <label>Alamat Lengkap</label>
                  <textarea name="alamat" class="form-control" rows="3" required><?php echo $data['alamat']; ?></textarea>
              </div>

              <div class="row">
                  <div class="col-md-6">
                      <div class="form-group">
                          <label>Nomor Telepon</label>
                          <input type="text" name="telepon" class="form-control" value="<?php echo $data['telepon']; ?>">
                      </div>
                  </div>
                  <div class="col-md-6">
                      <div class="form-group">
                          <label>Email Resmi</label>
                          <input type="email" name="email" class="form-control" value="<?php echo $data['email']; ?>">
                      </div>
                  </div>
              </div>

              <div class="form-group">
                  <label>Website</label>
                  <input type="text" name="website" class="form-control" value="<?php echo $data['website']; ?>" placeholder="https://...">
              </div>

              <div class="form-group" style="background: #FFF3E0; padding: 15px; border-radius: 8px; border: 1px dashed #F57C00;">
                  <label style="color: #E65100;">Ganti Logo (Opsional)</label>
                  <br>
                  <img src="<?php echo $data['logo']; ?>" style="height: 60px; margin-bottom: 10px; border-radius: 5px;">
                  <input type="file" name="logo" class="form-control" style="border: none; background: transparent; padding: 0;">
                  <small class="text-muted">Format: JPG, JPEG, PNG. Kosongkan jika tidak ingin mengganti logo.</small>
              </div>

              <hr>

              <div class="text-right">
                  <a href="lembaga.php" class="btn btn-cancel">Batal</a>
                  <button type="submit" name="simpan" class="btn btn-save">
                      <i class="fa fa-save"></i> &nbsp; Simpan Perubahan
                  </button>
              </div>

            </div>
          </form>

        </div>
      </div>
    </section>
  </div>
  <?php include('footer.php'); ?>
</div>
</body>
</html>