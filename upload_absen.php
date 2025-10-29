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
            background-color: #f4f6f9;
            padding-top: 40px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border: none;
        }
        .card-header {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        #loading-container {
            display: none;
            padding: 60px 20px;
            text-align: center;
        }

        /* CSS UNTUK UPLOAD FOTO */
        #fotoAbsen { display: none; }
        .file-upload-container {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 30px 20px;
            text-align: center;
            cursor: pointer;
            display: block;
            background-color: #fafafa;
            transition: background-color 0.3s;
        }
        .file-upload-container:hover { background-color: #f0f0f0; }
        #image-preview {
            display: none;
            max-width: 100%;
            max-height: 250px;
            border-radius: 8px;
            margin: 0 auto;
        }

        /* CSS BARU UNTUK WARNA GELAP */
        .text-dark-green {
            color: #1E7E34; /* Hijau Tua */
        }
        .text-custom-orange {
            color: #E67E22; /* Jingga Kustom */
        }
        .btn-custom-orange {
            background-color: #E67E22; 
            border-color: #E67E22;
            color: #fff; /* Teks putih */
        }
        .btn-custom-orange:hover {
            background-color: #F39C12; 
            border-color: #F39C12;
            color: #fff;
        }
        
        /* CSS untuk link izin */
        .link-ijin {
            font-weight: 600; /* Sedikit tebal */
            color: #1E7E34; /* Samakan dengan warna hijau */
        }
        .link-ijin:hover {
            color: #155724; /* Sedikit lebih gelap saat hover */
        }

    </style>
</head>
<body class="container">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-body" style="padding: 35px;">

                    <div id="form-container">
                        <div class="text-center mb-4">
                            <img src="dist/img/logomti.jpg" alt="Logo Perusahaan" style="max-height: 100px;">
                            
                            <h4 class="mt-3 text-dark-green">Form Absensi Mandiri</h4>
                        </div>

                        <form id="form-absen" action="proses_upload.php" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="id_pegawai"><b>ID Pegawai (NIP) Anda:</b></label>
                                <input type="text" class="form-control form-control-lg" name="id_pegawai" id="id_pegawai" placeholder="Masukkan ID Pegawai Anda" required>
                            </div>
                            
                            <div class="form-group">
                                <label><b>Ambil Foto (Selfie):</b></label>
                                <label for="fotoAbsen" class="file-upload-container">
                                    <div id="upload-prompt">
                                        <i class="fas fa-camera fa-2x text-muted"></i>
                                        <p class="text-muted mb-0 mt-2">Ketuk di sini untuk membuka kamera</p>
                                    </div>
                                    <img id="image-preview" src="#" alt="Preview Foto" />
                                </label>
                                <input type="file" name="fotoAbsen" id="fotoAbsen" accept="image/*" capture="camera" required>
                                <small class="form-text text-muted">Pastikan wajah terlihat jelas.</small>
                            </div>
                            
                            <button type="submit" class="btn btn-custom-orange btn-block btn-lg mt-4" name="submit">
                                <i class="fas fa-paper-plane"></i> Kirim Absen
                            </button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                Atau, ingin mengajukan Izin/Sakit? 
                                <a href="form_ijin.php" class="link-ijin">Klik di sini</a>
                            </small>
                        </div>
                        
                    </div>

                    <div id="loading-container">
                        <i class="fas fa-spinner fa-spin fa-3x text-custom-orange"></i>
                        <h4 class="mt-3 text-dark">Mencocokkan Wajah...</h4>
                        <p class="text-muted">Proses ini mungkin memakan waktu 5-10 detik.<br>Harap jangan tutup halaman ini.</p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <script>
    $(document).ready(function() {
        
        $('#form-absen').on('submit', function() {
            if ($('#fotoAbsen').get(0).files.length === 0) {
                alert("Harap ambil foto selfie Anda terlebih dahulu.");
                return false; 
            }
            $('#form-container').hide();
            $('#loading-container').show();
        });

        $('#fotoAbsen').on('change', function(event) {
            var file = event.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#image-preview').attr('src', e.target.result);
                    $('#image-preview').show();
                    $('#upload-prompt').hide();
                }
                reader.readAsDataURL(file);
            }
        });
    });
    </script>
</body>
</html>