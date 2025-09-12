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
    // Ambil data pengiriman
    $sql = "SELECT * FROM pengiriman ORDER BY id DESC";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Error saat mengambil data: " . $conn->error);
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

if (isset($_POST['verifikasi_pengiriman'])) {
    $ids = $_POST['id_pengiriman'];

    // Folder upload
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    foreach ($ids as $id_to_verify) {
        $status = isset($_POST['verifikasi'][$id_to_verify]) ? 1 : 0;

        $foto_path = null;
        if (isset($_FILES['foto']['name'][$id_to_verify]) && !empty($_FILES['foto']['name'][$id_to_verify])) {
            $file_name = $_FILES['foto']['name'][$id_to_verify];
            $temp_name = $_FILES['foto']['tmp_name'][$id_to_verify];
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_file_name = $id_to_verify . '_' . uniqid() . '.' . $file_extension;
            $foto_path = $target_dir . $new_file_name;

            if (!move_uploaded_file($temp_name, $foto_path)) {
                $error .= "Gagal mengunggah foto untuk ID: " . $id_to_verify . "<br>";
                $foto_path = null;
            }
        }

        // Update database
        $stmt = $conn->prepare("UPDATE pengiriman SET status_verifikasi = ?, foto_verifikasi = ? WHERE id = ?");
        $stmt->bind_param("isi", $status, $foto_path, $id_to_verify);
        $stmt->execute();
        $stmt->close();
    }
    $message = "Verifikasi berhasil disimpan.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pengiriman</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav class="main-nav">
        <ul>
            <li><a href="pengiriman.php"><i class="fas fa-truck-fast"></i> PENGIRIMAN</a></li>
            <li><a href="mutasi.php"><i class="fas fa-exchange-alt"></i> MUTASI</a></li>
            <li><a href="verifikasi_pengiriman.php"><i class="fas fa-shield-alt"></i> VERIFIKASI</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> LOGOUT</a></li>
        </ul>
    </nav>
    <div class="dashboard-container">
        <h2>Verifikasi Pengiriman</h2>
        <?php if (!empty($message)) echo "<p class='success-message'>$message</p>"; ?>
        <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>

        <form method="post" action="verifikasi_pengiriman.php" enctype="multipart/form-data">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Konsumen</th>
                            <th>Barang</th>
                            <th>Driver</th>
                            <th>Foto</th>
                            <th>Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $is_verified = $row['status_verifikasi'] ?? 0;
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['nama_konsumen']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_driver']); ?></td>
                                <td>
                                    <input type="file" name="foto[<?php echo $row['id']; ?>]" accept="image/*">
                                    <?php if (!empty($row['foto_verifikasi'])) { ?>
                                        <a href="<?php echo htmlspecialchars($row['foto_verifikasi']); ?>" target="_blank" style="margin-left: 5px;">Lihat Foto</a>
                                    <?php } ?>
                                </td>
                                <td>
                                    <input type="checkbox" name="verifikasi[<?php echo $row['id']; ?>]" value="1" <?php echo $is_verified ? 'checked' : ''; ?>>
                                </td>
                                <input type="hidden" name="id_pengiriman[]" value="<?php echo $row['id']; ?>">
                            </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center;'>Tidak ada data pengiriman.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <button type="submit" name="verifikasi_pengiriman" style="margin-top: 20px;">Simpan Verifikasi</button>
        </form>
    </div>
</body>
</html>
