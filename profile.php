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
    <title>Profil Pengguna</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="icon.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav class="main-nav">
        <ul>
            <li><a href="pengiriman.php"><i class="fas fa-truck-fast"></i> PENGIRIMAN</a></li>
            <li><a href="mutasi.php"><i class="fas fa-exchange-alt"></i> MUTASI</a></li>
            <li><a href="verifikasi_security.php"><i class="fas fa-shield-alt"></i> VERIFIKASI SECURITY</a></li>
            <li><a href="profile.php"><i class="fas fa-user-circle"></i> PROFIL</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> LOGOUT</a></li>
        </ul>
    </nav>
    <div class="dashboard-container">
        <h2 style="text-align:center;">Halaman Profil</h2>
        <div class="profile-card">
            <div class="profile-info">
                <i class="fas fa-user-circle profile-icon"></i>
                <div class="profile-details">
                    <h3>Halo, <?php echo $username; ?>!</h3>
                    <p>Selamat datang kembali di sistem pengelola barang.</p>
                </div>
            </div>
            <div class="profile-actions menu-column">
                <a href="hasil_verifikasi_pengiriman.php">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Hasil Verifikasi Pengiriman</span>
                </a>
                <a href="hasil_verifikasi_mutasi.php">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Hasil Verifikasi Mutasi</span>
                </a>
                <a href="menu_barang.php">
                    <i class="fas fa-box-open"></i>
                    <span>Menu Barang</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
