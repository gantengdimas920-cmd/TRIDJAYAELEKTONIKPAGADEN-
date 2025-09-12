<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$message = "";
$error = "";
$edit_mode = false;
$edit_data = array();

// --- Logika Hapus ---
if (isset($_GET['hapus'])) {
    $id_to_delete = htmlspecialchars($_GET['hapus']);
    $stmt = $conn->prepare("DELETE FROM mutasi WHERE id_mutasi = ?");
    $stmt->bind_param("i", $id_to_delete);
    if ($stmt->execute()) {
        $message = "Data berhasil dihapus.";
    } else {
        $error = "Gagal menghapus data: " . $stmt->error;
    }
    $stmt->close();
    header("Location: mutasi.php");
    exit();
}

// --- Logika Tambah & Edit ---
if (isset($_POST['tambah_mutasi']) || isset($_POST['edit_mutasi'])) {
    $id_mutasi = isset($_POST['id_mutasi']) ? htmlspecialchars($_POST['id_mutasi']) : null;
    $cabang = htmlspecialchars($_POST['cabang']);
    $nama_barang = htmlspecialchars($_POST['nama_barang']);
    $jumlah = htmlspecialchars($_POST['jumlah']);
    $jenis = htmlspecialchars($_POST['jenis']);
    $nama_driver = htmlspecialchars($_POST['nama_driver']);

    if (isset($_POST['tambah_mutasi'])) {
        $stmt = $conn->prepare("INSERT INTO mutasi (nama_cabang, nama_barang, jumlah, jenis, nama_driver) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $cabang, $nama_barang, $jumlah, $jenis, $nama_driver);
        if ($stmt->execute()) {
            $message = "Mutasi berhasil ditambahkan!";
        } else {
            $error = "Error: " . $stmt->error;
        }
    } else if (isset($_POST['edit_mutasi'])) {
        $stmt = $conn->prepare("UPDATE mutasi SET nama_cabang = ?, nama_barang = ?, jumlah = ?, jenis = ?, nama_driver = ? WHERE id_mutasi = ?");
        $stmt->bind_param("ssissi", $cabang, $nama_barang, $jumlah, $jenis, $nama_driver, $id_mutasi);
        if ($stmt->execute()) {
            $message = "Data mutasi berhasil diperbarui!";
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
    $stmt->close();
}

// --- Logika Edit Data (mengisi form) ---
if (isset($_GET['edit'])) {
    $id_to_edit = htmlspecialchars($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM mutasi WHERE id_mutasi = ?");
    $stmt->bind_param("i", $id_to_edit);
    $stmt->execute();
    $result_edit = $stmt->get_result();
    if ($result_edit->num_rows > 0) {
        $edit_data = $result_edit->fetch_assoc();
        $edit_mode = true;
    }
    $stmt->close();
}

// --- Ambil Data Mutasi untuk Tabel ---
$sql = "SELECT * FROM mutasi ORDER BY id_mutasi DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mutasi</title>
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
        <h2>Input Mutasi</h2>
        <?php if (!empty($message)) echo "<p class='success-message'>$message</p>"; ?>
        <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>

        <form class="form-mutasi" action="mutasi.php" method="post">
            <input type="hidden" name="id_mutasi" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['id_mutasi']) : ''; ?>">
            
            <div class="input-row-group">
                <div class="input-group">
                    <label for="cabang">Cabang</label>
                    <input type="text" id="cabang" name="cabang" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['nama_cabang']) : ''; ?>" required>
                </div>
                <div class="input-group">
                    <label for="nama_barang">Nama Barang</label>
                    <input type="text" id="nama_barang" name="nama_barang" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['nama_barang']) : ''; ?>" required>
                </div>
            </div>
            
            <div class="input-row-group">
                <div class="input-group">
                    <label for="jumlah">Jumlah</label>
                    <input type="number" id="jumlah" name="jumlah" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['jumlah']) : ''; ?>" required>
                </div>
                <div class="input-group">
                    <label for="jenis">Jenis</label>
                    <input type="text" id="jenis" name="jenis" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['jenis']) : ''; ?>" required>
                </div>
            </div>
            
            <div class="input-group">
                <label for="nama_driver">Nama Driver</label>
                <input type="text" id="nama_driver" name="nama_driver" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['nama_driver']) : ''; ?>" required>
            </div>
            
            <button type="submit" name="<?php echo $edit_mode ? 'edit_mutasi' : 'tambah_mutasi'; ?>">
                <?php echo $edit_mode ? 'Perbarui Mutasi' : 'Tambahkan Mutasi'; ?>
            </button>
        </form>

        <hr>

        <div class="table-container">
            <h3>Hasil Input Mutasi</h3>
            <table>
                <thead>
                    <tr>
                        <th>Cabang</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Jenis</th>
                        <th>Driver</th>
                        <th>Aksi</th>
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
                            <td>
                                <a href="mutasi.php?edit=<?php echo $row['id_mutasi']; ?>" class="action-btn">Edit</a>
                                <a href="mutasi.php?hapus=<?php echo $row['id_mutasi']; ?>" class="action-btn delete-btn" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">Hapus</a>
                            </td>
                        </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center;'>Tidak ada data mutasi.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
