<?php
session_start();
session_unset(); // Menghapus semua variabel sesi
session_destroy(); // Menghapus sesi

// Redirect ke halaman login
header('Location: index.php');
exit;
?>