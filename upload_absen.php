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
            /* Cegah overflow agar overlay rapi */
            overflow: hidden; 
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

        /* WARNA KUSTOM */
        .text-dark-green { color: #1E7E34; }
        .text-custom-orange { color: #E67E22; }
        .btn-custom-orange { background-color: #E67E22; border-color: #E67E22; color: #fff; }
        .btn-custom-orange:hover { background-color: #F39C12; border-color: #F39C12; color: #fff; }
        .link-ijin { font-weight: 600; color: #1E7E34; }
        .link-ijin:hover { color: #155724; }

        /* ================================== */
        /* === CSS BARU UNTUK GEO-LOCK === */
        /* ================================== */
        #main-content {
            /* Sembunyikan form utama sampai lokasi diverifikasi */
            display: none;
        }
        #location-overlay {
            /* Overlay ini akan tampil pertama kali */
            display: block;
            padding: 40px;
            text-align: center;
        }
    </style>
</head>
<body class="container">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-body" style="padding: 35px;">

                    <div id="location-overlay">
                        <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                        <h4 id="location-status-text" class="text-dark">Memverifikasi Lokasi Anda...</h4>
                        <p id="location-status-subtext" class="text-muted">Harap izinkan akses lokasi (Allow Location) saat diminta oleh browser Anda.</p>
                        <div class="spinner-border text-primary mt-3" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>

                    <div id="main-content">
                        <div id="form-container">
                            <div class="text-center mb-4">
                                <img src="dist/img/logomti.jpg" alt="Logo Perusahaan" style="max-height: 100px;">
                                <h5 class="mt-3 text-dark-green">Form Absensi Mandiri</h5>
                            </div>

                            <form id="form-absen" action="proses_upload.php" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="id_pegawai"><b>ID Pegawai (NIK) Anda:</b></label>
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
                                
                                <button type="submit" id="submit-button" class="btn btn-custom-orange btn-block btn-lg mt-4" name="submit">
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
                    </div> </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <script>
    // ==================================
    // === 1. KONFIGURASI LOKASI ===
    // ==================================
    // KOORDINAT KANTOR (dari Google Maps)
    const OFFICE_LATITUDE = -5.11867877830864;  //
    const OFFICE_LONGITUDE = 105.30968088572726; //

    // Jarak Toleransi (dalam meter).
    const MAX_DISTANCE_METERS = 14;
    // ==================================


    /**
     * Fungsi Rumit (Haversine) untuk menghitung jarak antara 2 titik GPS di Bumi.
     * Anda tidak perlu mengedit ini.
     */
    function getDistance(lat1, lon1, lat2, lon2) {
        function toRad(value) {
            return value * Math.PI / 180;
        }
        var R = 6371; // Radius Bumi dalam km
        var dLat = toRad(lat2 - lat1);
        var dLon = toRad(lon2 - lon1);
        var lat1 = toRad(lat1);
        var lat2 = toRad(lat2);

        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.sin(dLon / 2) * Math.sin(dLon / 2) * Math.cos(lat1) * Math.cos(lat2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        var d = R * c;
        return d * 1000; // Kembalikan dalam meter
    }

    /**
     * Fungsi untuk memperbarui UI (Tampilan)
     */
    function updateLocationStatus(icon, colorClass, title, message, showForm, disableButton = false) {
        $('#location-overlay .fa-map-marker-alt').attr('class', 'fas ' + icon + ' fa-3x mb-3 ' + colorClass); // Ubah ikon
        $('#location-overlay .spinner-border').hide(); // Sembunyikan spinner
        $('#location-status-text').text(title);
        $('#location-status-subtext').html(message); // Pakai html() agar <br> berfungsi

        if (showForm) {
            $('#location-overlay').hide(); // Sembunyikan overlay
            $('#main-content').show(); // Tampilkan konten (form)
        }
        if (disableButton) {
            $('#submit-button').prop('disabled', true).text('Di Luar Jangkauan');
            // Nonaktifkan juga area upload foto
            $('#fotoAbsen').prop('disabled', true);
            $('.file-upload-container').css('cursor', 'not-allowed').css('background-color', '#eee');
        }
    }


    $(document).ready(function() {

        // --- 1. SCRIPT GEOLOKASI (Dijalankan saat halaman dimuat) ---
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                // A. JIKA BERHASIL DAPAT LOKASI
                function(position) {
                    var userLat = position.coords.latitude;
                    var userLon = position.coords.longitude;

                    // Hitung Jarak
                    var distance = getDistance(OFFICE_LATITUDE, OFFICE_LONGITUDE, userLat, userLon);

                    // Periksa Jarak
                    if (distance <= MAX_DISTANCE_METERS) {
                        // KASUS 1: LOKASI VALID (DALAM JANGKAUAN)
                        updateLocationStatus(
                            'fa-check-circle', 
                            'text-success', 
                            'Lokasi Terverifikasi!', 
                            'Anda berada dalam jangkauan kantor (' + Math.round(distance) + ' meter). Silakan absen.',
                            true // Tampilkan form
                        );
                    } else {
                        // KASUS 2: LOKASI TIDAK VALID (TERLALU JAUH)
                        updateLocationStatus(
                            'fa-exclamation-triangle', 
                            'text-danger', 
                            'Anda Berada di Luar Jangkauan', 
                            'Jarak Anda: ' + Math.round(distance) + ' meter. <br>Harap mendekat ke lokasi kantor untuk absen.',
                            true, // Tampilkan form (tapi kita nonaktifkan tombolnya)
                            true  // Nonaktifkan tombol
                        );
                    }
                },
                // B. JIKA GAGAL DAPAT LOKASI
                function(error) {
                    var message = 'Terjadi error tidak diketahui.';
                    if (error.code == error.PERMISSION_DENIED) {
                        message = 'Anda menolak izin lokasi. Absensi tidak dapat dilanjutkan.<br>Harap aktifkan izin lokasi untuk situs ini di pengaturan browser Anda.';
                    } else if (error.code == error.POSITION_UNAVAILABLE) {
                        message = 'Gagal mendapatkan lokasi. Pastikan GPS di HP Anda aktif.';
                    } else if (error.code == error.TIMEOUT) {
                        message = 'Waktu mencari lokasi habis. Coba refresh halaman.';
                    }
                    updateLocationStatus('fa-times-circle', 'text-danger', 'Akses Lokasi Gagal', message, false);
                },
                // Opsi tambahan: Akurasi tinggi
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        } else {
            // Browser terlalu jadul, tidak support Geolocation
            updateLocationStatus('fa-times-circle', 'text-danger', 'Browser Tidak Support', 'Browser Anda tidak mendukung Geolocation. Gunakan browser modern.', false);
        }


        // --- 2. SCRIPT LOADING FORM ---
        $('#form-absen').on('submit', function() {
            if ($('#fotoAbsen').get(0).files.length === 0) {
                alert("Harap ambil foto selfie Anda terlebih dahulu.");
                return false; 
            }
            $('#form-container').hide();
            $('#loading-container').show();
        });

        // --- 3. SCRIPT PRATINJAU FOTO ---
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