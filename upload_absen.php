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
</head>
<body class="container" style="padding-top: 20px;">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center">Form Absensi Mandiri</h3>
                </div>
                <div class="card-body">
                    
                    <form action="proses_upload.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="id_pegawai">ID Pegawai (NIP) Anda:</label>
                            <input type="text" class="form-control" name="id_pegawai" id="id_pegawai" placeholder="Masukkan ID Pegawai Anda" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="fotoAbsen">Ambil Foto (Selfie):</label>
                            <input type="file" class="form-control-file" name="fotoAbsen" id="fotoAbsen" accept="image/*" capture="camera" required>
                            <small class="form-text text-muted">Pastikan wajah terlihat jelas.</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block" name="submit">Kirim Absen</button>
                    </form>
                    </div>
            </div>
        </div>
    </div>
</body>
</html>