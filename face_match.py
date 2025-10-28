import sys
from deepface import DeepFace
import warnings
import os

# Sembunyikan pesan 'Warning' dari TensorFlow, agar output bersih
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'
warnings.filterwarnings('ignore')

# Ambil path gambar dari argumen PHP
# sys.argv[0] adalah nama script (face_match.py)
# sys.argv[1] adalah path foto master
# sys.argv[2] adalah path foto absen
img1_path = sys.argv[1]
img2_path = sys.argv[2]

try:
    # Ini adalah inti dari pencocokan wajah.
    # 'VGG-Face' adalah model AI yang populer dan akurat
    # 'enforce_detection=False' akan mencoba mencocokkan meski wajah agak miring
    result = DeepFace.verify(
        img1_path = img1_path, 
        img2_path = img2_path, 
        model_name = "VGG-Face",
        enforce_detection = False
    )

    # Kirim hasil ke PHP
    if result['verified'] == True:
        print("MATCH")
    else:
        print("NO_MATCH")

except Exception as e:
    # Jika tidak ada wajah terdeteksi di salah satu gambar
    print("NO_FACE_FOUND")