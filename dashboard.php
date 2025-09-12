<?php
session_start();
include 'config.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Pastikan hanya pengguna dengan role 'pegawai' yang bisa mengakses halaman ini
if ($_SESSION['role'] !== 'pegawai') {
    header("Location: ads.php");
    exit();
}

// Tampilkan nama pengguna dari sesi
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pegawai</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav class="main-nav">
        <ul>
            <li><a href="pengiriman.php"><i class="fas fa-truck-fast"></i> PENGIRIMAN</a></li>
            <li><a href="mutasi.php"><i class="fas fa-exchange-alt"></i> MUTASI</a></li>
            <li><a href="verifikasi.php"><i class="fas fa-shield-alt"></i> VERIFIKASI SECURITY</a></li>
        </ul>
    </nav>
    <div class="dashboard-container">
    </div>
</body>
</html>
