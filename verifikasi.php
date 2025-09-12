<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Verifikasi</title>
    <link rel="stylesheet" href="verifikasi.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav class="main-nav">
        <ul>
            <li><a href="pengiriman.php"><i class="fas fa-truck-fast"></i> Pengiriman</a></li>
            <li><a href="mutasi.php"><i class="fas fa-exchange-alt"></i> Mutasi</a></li>
            <li><a href="verifikasi_security.php"><i class="fas fa-shield-alt"></i> Verifikasi Security</a></li>
            <li><a href="profile.php"><i class="fas fa-user-circle"></i> Profil</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
    <div class="dashboard-container">
        <h2>Verifikasi</h2>
        <div class="verification-menu">
            <a href="verifikasi_pengiriman.php" class="menu-button pengiriman">
                <i class="fas fa-clipboard-check"></i>
                <span>Verifikasi Pengiriman</span>
            </a>
            <a href="verifikasi_mutasi.php" class="menu-button mutasi">
                <i class="fas fa-clipboard-check"></i>
                <span>Verifikasi Mutasi</span>
            </a>
        </div>
    </div>
</body>
</html>
