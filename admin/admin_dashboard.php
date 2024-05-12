<?php

include '../connection.php';
session_start();

// Cek apakah pengguna belum login, redirect ke halaman login
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Include DataTables CSS and JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <!-- Navigation bar -->
    <nav class="bg-white shadow-lg p-6">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-semibold text-blue-600">Admin Dashboard</h1>
            <a href="logout.php" class="text-red-600 hover:text-red-800 font-semibold">Logout</a>
        </div>
    </nav>

    <!-- Visitor Chart -->
    <div class="container mx-auto my-8 px-4">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold mb-4">Monthly Visitor Count</h2>
            <canvas id="visitorChart" width="400" height="300"></canvas>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="container mx-auto my-8 px-4">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold mb-4">Visitor Details</h2>
            <table id="visitorTable" class="stripe hover" style="width:100%;">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Email</th>
                        <th>IP Address</th>
                        <th>File Name</th>
                        <th>Extracted Text</th>
                        <th>Date Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    // Query to fetch visitor details
                    $sql = "SELECT user_response.ip_address, user_response.file_name, user_response.extracted_text, user_response.created_at, users.email FROM user_response INNER JOIN users ON user_response.id_user = users.id_user;";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        $no = 0;
                        while ($row = $result->fetch_assoc()) {
                            $no++;
                            echo '<tr>';
                            echo '<td>' . $no . '</td>';
                            echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['ip_address']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['file_name']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['extracted_text']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['created_at']) . '</td>';
                            echo '</tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="container mx-auto my-8 px-4">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold mb-4">User Details</h2>
            <table id="userTable" class="stripe hover" style="width:100%;">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Email</th>
                        <th>Date Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    // Query to fetch visitor details
                    $sql = "SELECT email, created_at FROM users";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        $no = 0;
                        while ($row = $result->fetch_assoc()) {
                            $no++;
                            echo '<tr>';
                            echo '<td>' . $no . '</td>';
                            echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['created_at']) . '</td>';
                            echo '</tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Labels bulan
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Menghitung total pengunjung per bulan menggunakan PHP
        var visitorCounts = <?php
                            // Inisialisasi array dengan 12 bulan dan nilai awal 0
                            $visitorCounts = array_fill(1, 12, 0);

                            // Query untuk menghitung jumlah pengunjung per bulan
                            $sql = "SELECT MONTH(created_at) AS month, COUNT(*) AS count FROM user_response GROUP BY MONTH(created_at)";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $month = intval($row['month']);
                                    $count = intval($row['count']);
                                    $visitorCounts[$month - 0] = $count; // Menggunakan index bulan (0-11) untuk mengisi array
                                }
                            }

                            echo json_encode(array_values($visitorCounts)); // Mengonversi array ke JSON
                            ?>;

        // Menggambar chart menggunakan Chart.js
        var ctx = document.getElementById('visitorChart').getContext('2d');
        var visitorChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Visitor Count',
                    data: visitorCounts,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: 100 // Atur nilai maksimum sumbu Y
                    }
                }
            }
        });

        // Initialize DataTable
        $(document).ready(function() {
            $('#visitorTable').DataTable({
                "pagingType": "full_numbers",
                "lengthMenu": [10, 25, 50, 75, 100],
                "pageLength": 10,
                "ordering": true,
                "searching": true,
                "info": true,
                "responsive": true
            });
        });

        $(document).ready(function() {
            $('#userTable').DataTable({
                "pagingType": "full_numbers",
                "lengthMenu": [10, 25, 50, 75, 100],
                "pageLength": 10,
                "ordering": true,
                "searching": true,
                "info": true,
                "responsive": true
            });
        });
    </script>

</body>

</html>