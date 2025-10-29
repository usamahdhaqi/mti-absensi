<?php
// File: helper_pesan.php

/**
 * Menampilkan halaman notifikasi (Sukses, Error, Warning, Info)
 * yang modern dan penuh gaya.
 *
 * @param string $status        - 'sukses', 'error', 'warning', 'info'
 * @param string $judul         - Judul di header card
 * @param string $pesan         - Pesan utama
 * @param string $info_tambahan - Teks kecil di bawah pesan utama (opsional)
 */
function tampilkan_pesan($status, $judul, $pesan, $info_tambahan = "") {
    
    // Logika untuk menentukan warna dan ikon berdasarkan status
    $header_class = 'bg-info'; // Biru muda (default)
    $icon_class = 'fas fa-info-circle';
    $icon_color = 'text-info';

    if ($status == 'sukses') {
        $header_class = 'bg-success'; // Hijau
        $icon_class = 'fas fa-check-circle';
        $icon_color = 'text-success';
    } else if ($status == 'error') {
        $header_class = 'bg-danger'; // Merah
        $icon_class = 'fas fa-times-circle';
        $icon_color = 'text-danger';
    } else if ($status == 'warning') {
        $header_class = 'bg-warning'; // Jingga (dari Bootstrap)
        $icon_class = 'fas fa-exclamation-triangle';
        $icon_color = 'text-warning';
    } else if ($status == 'info') {
        $header_class = 'bg-primary'; // Biru (untuk absen pulang)
        $icon_class = 'fas fa-sign-out-alt';
        $icon_color = 'text-primary';
    }

    // Output seluruh halaman HTML
    // (Menggunakan HEREDOC agar rapi)
    echo <<<HTML
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Status Pengiriman</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <style>
            /* Style kustom untuk tampilan modern */
            body { 
                background-color: #f4f6f9; 
                padding-top: 40px; 
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; 
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
        </style>
    </head>
    <body class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card text-center">
                    <div class="card-header $header_class text-white">
                        <h4 class="mb-0">$judul</h4>
                    </div>
                    <div class="card-body" style="padding: 30px;">
                        <i class="$icon_class fa-4x $icon_color mb-3"></i>
                        <p style="font-size: 1.1rem;">$pesan</p>
                        <p class="text-muted">$info_tambahan</p>
                        
                        <a href="javascript:history.back()" class="btn btn-secondary mt-3">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
HTML;
} // Akhir dari function tampilkan_pesan

?>