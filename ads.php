<?php
session_start();
include 'config.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Mengambil nama pengguna dari sesi
$username = $_SESSION['username'];
$user_role = 'admin'; // Ini adalah contoh, seharusnya diambil dari database

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav class="main-nav">
        <ul>
            <li><a href="pengiriman.php"><i class="fas fa-truck-fast"></i> Pengiriman</a></li>
            <li><a href="mutasi.php"><i class="fas fa-exchange-alt"></i> Mutasi</a></li>
            <li><a href="verifikasi.php"><i class="fas fa-shield-alt"></i> Verifikasi Security</a></li>
            <li><a href="dashboard.php"><i class="fas fa-user-circle"></i> Profil</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
    <div class="dashboard-container">
        <h2>Dashboard Admin</h2>
        <div class="profile-info">
            <p><strong>Selamat datang, <?php echo htmlspecialchars($username); ?>!</strong></p>
            <p>Anda masuk sebagai: <?php echo htmlspecialchars($user_role); ?></p>
        </div>
        <div class="dashboard-content">
            <h3>Ringkasan Aktivitas</h3>
            <p>Di sini Anda bisa melihat ringkasan data penting, seperti jumlah pengiriman hari ini, mutasi yang masuk, dan status verifikasi.</p>
            <ul>
                <li>Jumlah Pengiriman Hari Ini: <strong>15</strong></li>
                <li>Jumlah Mutasi yang Masuk: <strong>8</strong></li>
                <li>Verifikasi yang Tertunda: <strong>2</strong></li>
            </ul>
        </div>
    </div>
</body>
</html>
