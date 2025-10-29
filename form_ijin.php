<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Perizinan</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    
    <style>
        /* (Salin semua CSS kustom dari upload_absen.php ke sini) */
        body { background-color: #f4f6f9; padding-top: 40px; }
        .card { border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border: none; }
        #loading-container { display: none; padding: 60px 20px; text-align: center; }
        #fileLampiran { display: none; }
        .file-upload-container { border: 2px dashed #ddd; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; background-color: #fafafa; }
        .file-upload-container:hover { background-color: #f0f0f0; }
        #upload-prompt p, #upload-prompt i { margin-bottom: 0; }
        .text-dark-green { color: #1E7E34; }
        .text-custom-orange { color: #E67E22; }
        .btn-custom-orange { background-color: #E67E22; border-color: #E67E22; color: #fff; }
        .btn-custom-orange:hover { background-color: #F39C12; border-color: #F39C12; color: #fff; }
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
                            <h4 class="mt-3 text-dark-green">Form Pengajuan Izin/Sakit</h4>
                        </div>

                        <form id="form-ijin" action="proses_ijin.php" method="post" enctype="multipart/form-data">
                            
                            <div class="form-group">
                                <label for="id_pegawai"><b>ID Pegawai (NIP) Anda:</b></label>
                                <input type="text" class="form-control form-control-lg" name="id_pegawai" id="id_pegawai" placeholder="Masukkan ID Pegawai Anda" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="alasan_ijin"><b>Jenis Izin:</b></label>
                                <select class="form-control form-control-lg" name="alasan_ijin" id="alasan_ijin" required>
                                    <option value="">-- Pilih Alasan --</option>
                                    <option value="Sakit">Sakit</option>
                                    <option value="Izin">Izin (Keperluan Pribadi)</option>
                                    <option value="Cuti">Cuti</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="tanggal_ijin"><b>Tanggal:</b></label>
                                <input type="text" class="form-control form-control-lg" name="tanggal_ijin" id="tanggal_ijin" placeholder="Pilih Tanggal Izin" required autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label for="keterangan"><b>Keterangan Singkat:</b></label>
                                <textarea class="form-control" name="keterangan" id="keterangan" rows="3" placeholder="Contoh: Sakit demam, perlu istirahat 1 hari." required></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label><b>Lampiran (Surat Sakit/Izin):</b></label>
                                <label for="fileLampiran" class="file-upload-container">
                                    <div id="upload-prompt">
                                        <i class="fas fa-file-upload fa-2x text-muted"></i>
                                        <p class="text-muted mb-0 mt-2">Ketuk untuk upload lampiran (JPG, PNG, PDF)</p>
                                        <p id="file-name-preview" class="text-success font-weight-bold mt-2"></p>
                                    </div>
                                </label>
                                <input type="file" name="fileLampiran" id="fileLampiran" accept="image/*,application/pdf" required>
                            </div>
                            
                            <button type="submit" class="btn btn-custom-orange btn-block btn-lg mt-4" name="submit">
                                <i class="fas fa-paper-plane"></i> Kirim Pengajuan
                            </button>
                        </form>
                    </div>

                    <div id="loading-container">
                        <i class="fas fa-spinner fa-spin fa-3x text-custom-orange"></i>
                        <h4 class="mt-3 text-dark">Mengirim Pengajuan...</h4>
                        <p class="text-muted">Harap tunggu.</p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script> <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <script>
    $(document).ready(function() {
        
        // Inisialisasi Datepicker
        $("#tanggal_ijin").datepicker({
            dateFormat: 'yy-mm-dd' // Format tanggal YYYY-MM-DD
        });

        // Script Loading
        $('#form-ijin').on('submit', function() {
            // Validasi
            if ($('#fileLampiran').get(0).files.length === 0) {
                alert("Harap sertakan file lampiran (Surat Sakit/Izin).");
                return false; // Hentikan submit
            }
            $('#form-container').hide();
            $('#loading-container').show();
        });

        // Script Pratinjau Nama File
        $('#fileLampiran').on('change', function(event) {
            var file = event.target.files[0];
            if (file) {
                // Tampilkan nama file di bawah ikon
                $('#file-name-preview').text(file.name);
            }
        });
    });
    </script>
</body>
</html>