<?php
include 'connection.php'; // Menghubungkan ke file koneksi database

// Check if form is submitted
if (isset($_POST['convert'])) {
    // Check if a file is uploaded
    if (isset($_FILES['file'])) {
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_name = $_FILES['file']['name'];

        // Target URL to upload the file
        $target_url = 'http://127.0.0.1:5000'; // Ganti dengan URL tujuan yang sesuai

        // Initialize cURL session
        $curl = curl_init();

        // Set cURL options
        curl_setopt($curl, CURLOPT_URL, $target_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, [
            'file' => new CURLFile($file_tmp, '', $file_name)
        ]);

        // Execute cURL request
        $response = curl_exec($curl);

        // Check for errors
        if (curl_errno($curl)) {
            echo '<p>Error: ' . curl_error($curl) . '</p>';
        } else {
            $data = json_decode($response, true);
            $extracted_text = isset($data['text']) ? $data['text'] : 'No text extracted.';

            // Mendapatkan alamat IP pengguna
            $ip_address = $_SERVER['REMOTE_ADDR'];

            // Simpan data ke database
            $sql = "INSERT INTO user_response (ip_address, file_name, extracted_text, created_at) VALUES ('$ip_address', '$file_name', '$extracted_text', NOW())";

            if ($conn->query($sql) === TRUE) {
                // Generate download link for extracted text
                // Simpan teks yang diekstraksi ke dalam session untuk diunduh nanti
                session_start();
                $_SESSION['extracted_text'] = $extracted_text;
            } else {
                echo '<p>Gagal menyimpan data: ' . $conn->error . '</p>';
            }
        }

        // Close cURL session
        curl_close($curl);
    } else {
        echo '<script>alert("No File Uploaded")</script>';
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image to Text Extractor</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
    <style>
        .file-drop-area {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            cursor: pointer;
        }

        .file-drop-area.dragover {
            border-color: #007bff;
        }

        /* CSS untuk loader */
        .loader {
            border-top-color: #3498db;
            -webkit-animation: spin 1s ease-in-out infinite;
            animation: spin 1s ease-in-out infinite;
        }

        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="bg-white">
    <div id="loading-spinner" class="hidden fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex justify-center items-center">
        <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12"></div>
    </div>

    <nav class="w-full h-16 bg-white shadow-lg flex justify-between items-center px-10">
        <div class="flex items-center">
            <img src="images/logo.png" class="h-10 mr-2">
            <h2 class="text-xl font-semibold text-blue-600">image2text.com</h2>
        </div>
        <div class="flex items-center">
            <a href="https://saweria.co/image2text" class="text-black font-semibold hidden sm:block bg-yellow-500 w-max px-4 py-1 rounded-lg transition duration-300 border-2 hover:bg-white hover:text-yellow-600 hover:border-yellow-500">Donate
                me in <span class="underline">Saweria</span></a>
            <a href="https://saweria.co/image2text" class="text-black font-semibold bg-yellow-500 sm:hidden w-max px-4 py-1 rounded-lg transition duration-300 border-2 hover:bg-white hover:text-yellow-600 hover:border-yellow-500">Donate</a>
        </div>
    </nav>

    <section class="w-full h-screen px-4">
        <h1 class="text-4xl font-bold text-center mt-10 mb-2">Image to Text Converter</h1>
        <p class="text-center text-gray-600 mb-10">An online image to text converter to extract text from images. Upload
            your photo, to get text file instantly.</p>
        <div id="file-drop-area" class="file-drop-area mb-4 mx-5 h-50 sm:h-96" onclick="triggerFileInput()">
            <div class="flex justify-center sm:mt-12">
                <img src="images/tool-upload-img.png" class="h-24 mr-2 sm:h-32">
            </div>
            <p class="text-xl">Drag and drop file here, or click to <span class="text-blue-600 underline">browse</span>
            </p>
            <p class="text-xl text-gray-400">File supported: PNG, JPG, JPEG, PDF </p>
            <input type="file" id="file-input" name="file" required accept=".png, .jpg, .jpeg, .pdf" style="display: none;">
        </div>
        <form id="upload-form" method="POST" enctype="multipart/form-data">
            <input type="file" id="real-file-input" name="file" required accept=".png, .jpg, .jpeg, .pdf" style="display: none;">
            <button name="convert" type="submit" class="bg-blue-500 mx-5 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Convert
                Image</button>
        </form>

        <?php if (isset($extracted_text) && !empty($extracted_text)) : ?>
            <div class="mt-4 mx-5">
                <h2 class="text-lg font-semibold mb-2">Extracted Text:</h2>
                <textarea class="w-full h-72 mb-5 border-2 cursor-text" id="extracted-text"><?php echo htmlspecialchars($extracted_text); ?></textarea>
                <button onclick="copyToClipboard()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 mb-5 rounded">Copy</button>
                <button onclick="downloadText()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 mb-5 rounded">Download Text</button>

            <?php else : ?>
            </div>
        <?php endif; ?>

        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        function triggerFileInput() {
            const fileInput = document.getElementById('file-input');
            fileInput.click();

            fileInput.addEventListener('change', (event) => {
                const selectedFile = event.target.files[0]; // Mengambil file yang dipilih
                if (selectedFile) {
                    displayFileName(selectedFile.name); // Menampilkan nama file di area drop
                    setFileInput(selectedFile); // Menambahkan file ke real-file-input
                }
            });
        }

        const fileDropArea = document.getElementById('file-drop-area');

        fileDropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileDropArea.classList.add('dragover');
        });

        fileDropArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            fileDropArea.classList.remove('dragover');
        });

        fileDropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileDropArea.classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                displayFileName(file.name);
                setFileInput(file);
            }
        });

        function displayFileName(fileName) {
            const fileDropArea = document.getElementById('file-drop-area');
            fileDropArea.innerHTML = `<p>File: ${fileName}</p>`;

            // Show the upload form after file is dropped
            const uploadForm = document.getElementById('upload-form');
            uploadForm.style.display = 'block';
        }

        function setFileInput(file) {
            const realFileInput = document.getElementById('real-file-input');
            const fileList = new DataTransfer();
            fileList.items.add(new File([file], file.name));
            realFileInput.files = fileList.files;
        }

        function copyToClipboard() {
            const textarea = document.getElementById('extracted-text');
            textarea.select();

            try {
                const successful = document.execCommand('copy');
                const message = successful ? 'Text copied to clipboard!' : 'Failed to copy text!';

                // Tampilkan pesan toaster menggunakan Toastify
                Toastify({
                    text: message,
                    duration: 3000, // Durasi tampilan pesan (dalam milidetik)
                    gravity: 'top', // Posisi pesan (top, bottom, left, right)
                    position: 'right' // Posisi pesan (center, left, right)
                }).showToast();
            } catch (err) {
                console.error('Error copying text:', err);

                // Tampilkan pesan toaster untuk error
                Toastify({
                    text: 'Error copying text! Please try again.',
                    duration: 3000,
                    gravity: 'top',
                    position: 'right'
                }).showToast();
            }
        }


        function showLoadingSpinner() {
            document.getElementById('loading-spinner').classList.remove('hidden');
        }

        function hideLoadingSpinner() {
            document.getElementById('loading-spinner').classList.add('hidden');
        }

        document.getElementById('upload-form').addEventListener('submit', function(event) {
            // Tampilkan loading spinner saat formulir dikirim
            showLoadingSpinner();

            // Set timeout untuk simulasi proses yang sedang berlangsung (contoh: 2 detik)
            setTimeout(function() {
                // Sembunyikan loading spinner setelah proses selesai
                hideLoadingSpinner();
            }, 2000); // Ganti 2000 dengan waktu yang sesuai dengan proses yang sebenarnya
        });

        function downloadText() {
                const extractedText = document.getElementById('extracted-text').value;
                const blob = new Blob([extractedText], { type: 'text/plain' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'extracted_text.txt';
                a.click();
                URL.revokeObjectURL(url);
            }
    </script>
</body>

</html>