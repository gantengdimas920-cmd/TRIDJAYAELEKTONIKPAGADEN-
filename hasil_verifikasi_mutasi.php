<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$error = "";
$result = null;

try {
    // Ambil data mutasi yang sudah diverifikasi (status_verifikasi = 1)
    // Asumsi: tabel 'mutasi' memiliki kolom 'status_verifikasi'
    $sql = "SELECT * FROM mutasi WHERE status_verifikasi = 1 ORDER BY id_mutasi DESC";
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
    <title>Hasil Verifikasi Mutasi</title>
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
        <h2>Hasil Verifikasi Mutasi</h2>
        <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Cabang</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Jenis</th>
                        <th>Driver</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['nama_cabang']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                            <td><?php echo htmlspecialchars($row['jumlah']); ?></td>
                            <td><?php echo htmlspecialchars($row['jenis']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_driver']); ?></td>
                            <td>Sudah Diverifikasi</td>
                        </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center;'>Tidak ada data mutasi yang sudah diverifikasi.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
