<?php
// (Anda bisa tambahkan header.php jika ingin tampilannya cantik)
// include 'header.php';
// include 'sidebar.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Absensi</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        body {
            /* Warna latar belakang abu-abu muda */
            background-color: #f4f6f9;
            padding-top: 40px;
        }
        .card {
            /* Sudut tumpul, bayangan, dan hapus border */
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border: none;
        }
        .card-header {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        /* Kelas kustom untuk tombol jingga */
        .btn-warning {
            color: #fff; /* Teks putih agar kontras */
        }
    </style>
</head>
<body class="container">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h3 class="text-center mb-0"><i class="fas fa-camera"></i> Form Absensi Mandiri</h3>
                </div>
                <div class="card-body" style="padding: 25px;">
                    
                    <form action="proses_upload.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="id_pegawai"><b>ID Pegawai (NIK) Anda:</b></label>
                            <input type="text" class="form-control form-control-lg" name="id_pegawai" id="id_pegawai" placeholder="Masukkan ID Pegawai Anda" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="fotoAbsen"><b>Ambil Foto (Selfie):</b></label>
                            <input type="file" class="form-control-file" name="fotoAbsen" id="fotoAbsen" accept="image/*" capture="camera" required>
                            <small class="form-text text-muted">Pastikan wajah terlihat jelas.</small>
                        </div>
                        
                        <button type="submit" class="btn btn-warning btn-block btn-lg mt-4" name="submit">
                            <i class="fas fa-paper-plane"></i> Kirim Absen
                        </button>
                    </form>
                    </div>
            </div>
        </div>
    </div>
</body>
</html>