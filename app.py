from flask import Flask, render_template, request, redirect
from PIL import Image
import pytesseract
import fitz  # PyMuPDF

app = Flask(__name__)

# Tentukan jalur Tesseract OCR secara eksplisit
pytesseract.pytesseract.tesseract_cmd = r"C:\Program Files\Tesseract-OCR\tesseract.exe"

# Fungsi untuk mengekstrak teks dari gambar
def extract_text_from_image(image_path):
    try:
        image = Image.open(image_path)
        text = pytesseract.image_to_string(image)
        return text
    except Exception as e:
        return f"Error extracting text from image: {str(e)}"

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
        return f"Error extracting text from PDF: {str(e)}"

@app.route('/', methods=['GET', 'POST'])
def index():
    if request.method == 'POST':
        # Cek jika file sudah diunggah
        if 'file' not in request.files:
            return redirect(request.url)

        file = request.files['file']

        if file.filename == '':
            return redirect(request.url)

        if file:
            # Simpan file sementara
            file_path = f"uploads/{file.filename}"
            file.save(file_path)

            # Mengekstrak teks dari gambar atau PDF
            if file_path.lower().endswith(('.png', '.jpg', '.jpeg')):
                # Extract text from image using pytesseract
                extracted_text = extract_text_from_image(file_path)
            elif file_path.lower().endswith('.pdf'):
                # Extract text from PDF using PyMuPDF (fitz)
                extracted_text = extract_text_from_pdf(file_path)
            else:
                return "Format file tidak didukung."

            return render_template('index.html', text=extracted_text)

    return render_template('index.html', text=None)

if __name__ == '__main__':
    app.run(debug=True)
