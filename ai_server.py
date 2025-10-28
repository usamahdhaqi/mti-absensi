import warnings
import os
from flask import Flask, request, jsonify
from deepface import DeepFace

# Sembunyikan pesan 'Warning' TensorFlow
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'
warnings.filterwarnings('ignore')

# 1. Inisialisasi server Flask
app = Flask(__name__)

# 2. Muat Model AI (HANYA SATU KALI SAAT SERVER START)
# Kita "panaskan" mesinnya di sini.
print(" * Loading AI Model (VGG-Face)...")
try:
    # Membangun model (memaksa DeepFace memuat model ke memori)
    DeepFace.build_model("VGG-Face")
    print(" * AI Model Loaded. Server is Ready.")
except Exception as e:
    print(f" * Error loading model: {e}")

# 3. Buat Endpoint '/verify'
@app.route("/verify", methods=["POST"])
def verify_face():
    try:
        # 4. Ambil data JSON yang dikirim PHP
        data = request.get_json()
        img1_path = data.get("img_master")
        img2_path = data.get("img_absen")

        if not img1_path or not img2_path:
            return jsonify({"error": "Missing image paths"}), 400

        # 5. Jalankan verifikasi (ini akan cepat karena model sudah di memori)
        result = DeepFace.verify(
            img1_path = img1_path, 
            img2_path = img2_path, 
            model_name = "VGG-Face",
            enforce_detection = False
        )

        # 6. Kembalikan hasil sebagai JSON
        return jsonify({
            "verified": bool(result['verified']), # Kirim true/false
            "distance": result['distance']
        })

    except ValueError as ve:
        # Error jika wajah tidak terdeteksi
        return jsonify({"verified": False, "error": "NO_FACE_FOUND"}), 200
    except Exception as e:
        # Error lainnya
        return jsonify({"error": str(e)}), 500

# 7. Jalankan server di http://127.0.0.1:5000
if __name__ == "__main__":
    app.run(host="127.0.0.1", port=5000, debug=False)