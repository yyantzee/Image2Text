import os
import json
from flask import Flask, request, jsonify
import cv2
import pytesseract
import fitz  # PyMuPDF

app = Flask(__name__)

# Tentukan jalur Tesseract OCR secara eksplisit
pytesseract.pytesseract.tesseract_cmd = r"C:\Program Files\Tesseract-OCR\tesseract.exe"

# Fungsi praproses gambar sebelum ekstraksi teks
def preprocess_image(image_path):
    try:
        # Baca gambar menggunakan OpenCV
        image = cv2.imread(image_path)
        # Konversi ke skala abu-abu
        gray_image = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
        # Peningkatan kontras
        enhanced_image = cv2.convertScaleAbs(gray_image, alpha=2.0, beta=0)
        # Penerapan filter untuk menghilangkan noise
        denoised_image = cv2.medianBlur(enhanced_image, 1)
        
        return denoised_image
    except Exception as e:
        raise ValueError(f"Error preprocessing image: {str(e)}")

# Fungsi untuk mengekstrak teks dari gambar
def extract_text_from_image(image_path):
    try:
        # Praproses gambar
        preprocessed_image = preprocess_image(image_path)
        
        # Ekstraksi teks menggunakan pytesseract dari gambar yang sudah diproses
        text = pytesseract.image_to_string(preprocessed_image)

        return text
    except Exception as e:
        raise ValueError(f"Error extracting text from image: {str(e)}")

# Fungsi untuk mengekstrak teks dari file PDF
def extract_text_from_pdf(pdf_path):
    try:
        doc = fitz.open(pdf_path)
        text = ''
        for page in doc:
            text += page.get_text()
        doc.close()
        return text
    except Exception as e:
        raise ValueError(f"Error extracting text from PDF: {str(e)}")

@app.route('/', methods=['POST'])
def api_extract_text():
    if 'file' not in request.files:
        return jsonify({"error": "No file uploaded"}), 400

    file = request.files['file']

    if file.filename == '':
        return jsonify({"error": "No file selected"}), 400

    if file:
        try:
            # Simpan file sementara
            file_path = f"uploads/{file.filename}"
            file.save(file_path)

            # Mengekstrak teks dari gambar atau PDF
            if file_path.lower().endswith(('.png', '.jpg', '.jpeg')):
                # Extract text from image
                extracted_text = extract_text_from_image(file_path)
            elif file_path.lower().endswith('.pdf'):
                # Extract text from PDF
                extracted_text = extract_text_from_pdf(file_path)
            else:
                os.remove(file_path)
                return jsonify({"error": "Unsupported file format"}), 400

            # Hapus file yang sudah diunggah
            os.remove(file_path)

            # Kembalikan respons JSON dengan teks yang diekstrak
            return jsonify({"text": extracted_text}), 200

        except Exception as e:
            return jsonify({"error": str(e)}), 500

    return jsonify({"error": "Unexpected error occurred"}), 500

if __name__ == '__main__':
    app.run(debug=True)
