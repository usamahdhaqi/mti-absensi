<?php
/**
 * Mengirim pesan WhatsApp menggunakan API Fonnte
 *
 * @param string $nomor_tujuan - Nomor HP target
 * @param string $pesan - Isi pesan
 * @return string - Respon dari server Fonnte
 */
function kirimPesanWA($nomor_tujuan, $pesan) {
    
    // =======================================================
    // === MASUKKAN TOKEN ANDA DI SINI ===
    // (Salin dari dashboard Fonnte Anda)
    // =======================================================
    $TOKEN = "4s7XHoSmVzaKn5QUDWKs"; 
    // =======================================================

    // Pastikan nomor HP formatnya 628... (bukan 08...)
    if (substr($nomor_tujuan, 0, 1) == '0') {
        $nomor_tujuan = '62' . substr($nomor_tujuan, 1);
    }
    
    // Ubah spasi menjadi %20 (beberapa server gateway memerlukannya)
    //$pesan = urlencode($pesan);

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.fonnte.com/send",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30, // 30 detik timeout
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => http_build_query(array(
          'target' => $nomor_tujuan,
          'message' => $pesan
      )),
      CURLOPT_HTTPHEADER => array(
        "Authorization: " . $TOKEN
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return "cURL Error #:" . $err;
    } else {
      return $response;
    }
}
?>