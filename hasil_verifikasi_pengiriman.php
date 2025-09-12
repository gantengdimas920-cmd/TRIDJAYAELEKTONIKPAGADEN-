<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$message = "";
$error = "";
$result = null;

try {
    // Ambil data pengiriman yang sudah diverifikasi
    $sql = "SELECT * FROM pengiriman WHERE status_verifikasi = 1 ORDER BY id_pengiriman DESC";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Error saat mengambil data: " . $conn->error);
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Verifikasi Pengiriman</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav class="main-nav">
        <ul>
            <li><a href="pengiriman.php"><i class="fas fa-truck-fast"></i> PENGIRIMAN</a></li>
            <li><a href="mutasi.php"><i class="fas fa-exchange-alt"></i> MUTASI</a></li>
            <li><a href="verifikasi.php"><i class="fas fa-shield-alt"></i> VERIFIKASI SECURITY</a></li>
            <li><a href="profile.php"><i class="fas fa-user-circle"></i> PROFIL</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> LOGOUT</a></li>
        </ul>
    </nav>
    <div class="dashboard-container">
        <h2>Hasil Verifikasi Pengiriman</h2>
        <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Konsumen</th>
                        <th>Barang</th>
                        <th>Driver</th>
                        <th>Foto</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['nama_konsumen']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_driver']); ?></td>
                            <td>
                                <?php if (!empty($row['foto_verifikasi'])) { ?>
                                    <a href="<?php echo htmlspecialchars($row['foto_verifikasi']); ?>" target="_blank">Lihat Foto</a>
                                <?php } else { ?>
                                    -
                                <?php } ?>
                            </td>
                            <td>Sudah Diverifikasi</td>
                        </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center;'>Tidak ada data pengiriman yang sudah diverifikasi.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
