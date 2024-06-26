<?php
include 'connection.php'; // Menghubungkan ke file koneksi database

session_start(); // Mulai session

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query untuk mendapatkan data pengguna berdasarkan email
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Memeriksa kecocokan password menggunakan password_verify
        if (password_verify($password, $user['password'])) {
            // Set session untuk menandai bahwa pengguna telah login
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['email'] = $user['email'];
            echo '<script>alert("Login berhasil!")</script>';
            // Redirect atau lakukan tindakan lain setelah berhasil login
        } else {
            echo '<script>alert("Email atau password salah.")</script>';
        }
    } else {
        echo '<script>alert("Akun tidak ditemukan.")</script>';
    }
}

if (isset($_POST['register'])) {
    $email = $_POST['register-email'];
    $password = $_POST['register-password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Enkripsi password menggunakan bcrypt

    // Query untuk menyimpan data pengguna ke dalam database
    $sql = "INSERT INTO users (email, password, created_at) VALUES ('$email', '$hashedPassword', NOW())";

    if ($conn->query($sql) === TRUE) {
        echo '<script>alert("Registrasi berhasil! Silakan login untuk melanjutkan.")</script>';
    } else {
        echo '<script>alert("Gagal melakukan registrasi. Silakan coba lagi.")</script>';
    }
}

if (isset($_POST['logout'])) {
    session_start();
    session_unset(); // Hapus semua variabel session
    session_destroy(); // Hapus session
    header('Location: index.php'); // Redirect ke halaman login setelah logout
    exit;
}

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

            if (!isset($_SESSION['user_id'])) {
                $sql = "INSERT INTO user_response (id_user, ip_address, file_name, extracted_text, created_at) VALUES ('2','$ip_address', '$file_name', '$extracted_text', NOW())";

                if ($conn->query($sql) === TRUE) {
                    // Generate download link for extracted text
                    // Simpan teks yang diekstraksi ke dalam session untuk diunduh nanti
                    $_SESSION['extracted_text'] = $extracted_text;
                } else {
                    echo '<p>Gagal menyimpan data: ' . $conn->error . '</p>';
                }
            } else {
                $sql = "INSERT INTO user_response (id_user, ip_address, file_name, extracted_text, created_at) VALUES ('$_SESSION[user_id]','$ip_address', '$file_name', '$extracted_text', NOW())";

                if ($conn->query($sql) === TRUE) {
                    // Generate download link for extracted text
                    // Simpan teks yang diekstraksi ke dalam session untuk diunduh nanti
                    $_SESSION['extracted_text'] = $extracted_text;
                } else {
                    echo '<p>Gagal menyimpan data: ' . $conn->error . '</p>';
                }
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

        @media (max-width: 640px) {
            .ml-4 {
                margin-left: 0.5rem;
                margin-top: 0.5rem;
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
            <?php if (isset($_SESSION['user_id'])) : ?>
                <!-- Tombol Logout -->
                <form method="POST">
                    <button type="submit" name="logout" class="text-white font-semibold bg-red-500 w-max px-4 py-1 rounded-lg transition duration-300 border-2 hover:bg-white hover:text-red-500 hover:border-red-500">Logout</button>
                </form>
            <?php else : ?>
                <!-- Tombol Login -->
                <a href="#" onclick="showLoginModal()" class="text-white font-semibold bg-blue-500 w-max px-4 py-1 rounded-lg transition duration-300 border-2 hover:bg-white hover:text-blue-500 hover:border-blue-500">Login</a>
            <?php endif; ?>
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
                <?php if (isset($_SESSION['user_id'])) : ?>
                    <button onclick="downloadText()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 mb-5 rounded">Download Text</button>
                <?php else : ?>
                    <button onclick="showLoginModal()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 mb-5 rounded">Download Text</button>
                <?php endif; ?>

            <?php else : ?>
            </div>
        <?php endif; ?>

        </div>
    </section>

    <!-- Login Modal -->
    <div id="login-modal" class="hidden fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-full mx-4 sm:w-1/2 md:w-1/3 lg:w-1/4">
            <h2 class="text-2xl font-bold mb-4">Login</h2>
            <form id="login-form" method="post">
                <div class="mb-4">
                    <label for="email" class="block text-sm font-semibold mb-1">Email</label>
                    <input type="email" id="email" name="email" class="w-full border border-gray-300 rounded-md p-2" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-semibold mb-1">Password</label>
                    <input type="password" id="password" name="password" class="w-full border border-gray-300 rounded-md p-2" required>
                </div>
                <button type="submit" name="login" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Login</button>
                <button type="button" onclick="closeLoginModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded ml-4">Close</button>
                <p class="mt-4 text-sm">Don't have an account? <button type="button" onclick="showRegisterModal()" class="text-blue-500">Register here</button></p>
            </form>
        </div>
    </div>


    <!-- Register Modal -->
    <div id="register-modal" class="hidden fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-full mx-4 sm:w-1/2 md:w-1/3 lg:w-1/4">
            <h2 class="text-2xl font-bold mb-4">Register</h2>
            <form id="register-form" method="post">
                <div class="mb-4">
                    <label for="register-email" class="block text-sm font-semibold mb-1">Email</label>
                    <input type="email" id="register-email" name="register-email" class="w-full border border-gray-300 rounded-md p-2" required>
                </div>
                <div class="mb-4">
                    <label for="register-password" class="block text-sm font-semibold mb-1">Password</label>
                    <input type="password" id="register-password" name="register-password" class="w-full border border-gray-300 rounded-md p-2" required>
                </div>
                <button type="submit" name="register" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Register</button>
                <button type="button" onclick="closeRegisterModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded ml-4">Close</button>
            </form>
        </div>
    </div>


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
            const blob = new Blob([extractedText], {
                type: 'text/plain'
            });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'extracted_text.txt';
            a.click();
            URL.revokeObjectURL(url);
        }

        function showLoginModal() {
            const loginModal = document.getElementById('login-modal');
            loginModal.classList.remove('hidden');
        }

        function closeLoginModal() {
            const loginModal = document.getElementById('login-modal');
            loginModal.classList.add('hidden');
        }

        function showRegisterModal() {
            const loginModal = document.getElementById('login-modal');
            const registerModal = document.getElementById('register-modal');
            registerModal.classList.remove('hidden');
            loginModal.classList.add('hidden');
        }

        function closeRegisterModal() {
            const registerModal = document.getElementById('register-modal');
            registerModal.classList.add('hidden');
        }
    </script>
</body>

</html>