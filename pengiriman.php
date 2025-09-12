<?php
session_start();
include 'config.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Peran pengguna (seharusnya diambil dari database, ini hanya contoh)
$user_role = 'admin';

$success = '';
$error = '';

// --- Aksi HAPUS data ---
if ($user_role == 'admin' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM pengiriman WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $success = "Data pengiriman berhasil dihapus!";
    } else {
        $error = "Gagal menghapus data: " . $stmt->error;
    }
    $stmt->close();
}

// --- Aksi UPDATE data ---
// Ambil data untuk form edit jika ada parameter edit_id di URL
$edit_data = null;
if ($user_role == 'admin' && isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $conn->prepare("SELECT * FROM pengiriman WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $edit_data = $result->fetch_assoc();
    }
    $stmt->close();
}

// Proses update data jika form edit disubmit
if ($user_role == 'admin' && isset($_POST['update_data'])) {
    $id = $_POST['id_pengiriman'];
    $nama_konsumen = trim($_POST['nama_konsumen']);
    $nama_barang = trim($_POST['nama_barang']);
    $jenis = trim($_POST['jenis']);
    $nama_driver = trim($_POST['nama_driver']);

    if (empty($nama_konsumen) || empty($nama_barang) || empty($jenis) || empty($nama_driver)) {
        $error = "Semua kolom harus diisi!";
    } else {
        $stmt = $conn->prepare("UPDATE pengiriman SET nama_konsumen=?, nama_barang=?, jenis=?, nama_driver=? WHERE id=?");
        $stmt->bind_param("ssssi", $nama_konsumen, $nama_barang, $jenis, $nama_driver, $id);
        if ($stmt->execute()) {
            $success = "Data pengiriman berhasil diperbarui!";
        } else {
            $error = "Gagal memperbarui data: " . $stmt->error;
        }
        $stmt->close();
    }
}

// --- Aksi TAMBAH data (kode yang sudah ada) ---
if (isset($_POST['simpan'])) {
    $nama_konsumen = trim($_POST['nama_konsumen']);
    $nama_barang = trim($_POST['nama_barang']);
    $jenis = trim($_POST['jenis']);
    $nama_driver = trim($_POST['nama_driver']);

    if (empty($nama_konsumen) || empty($nama_barang) || empty($jenis) || empty($nama_driver)) {
        $error = "Semua kolom harus diisi!";
    } else {
        $sql = "INSERT INTO pengiriman (nama_konsumen, nama_barang, jenis, nama_driver) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nama_konsumen, $nama_barang, $jenis, $nama_driver);

        if ($stmt->execute()) {
            $success = "Data pengiriman berhasil disimpan!";
        } else {
            $error = "Gagal menyimpan data: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Mengambil data dari database untuk ditampilkan di tabel
$pengiriman_data = [];
$sql_select = "SELECT * FROM pengiriman ORDER BY tanggal_input DESC";
$result = $conn->query($sql_select);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pengiriman_data[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengiriman</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav class="main-nav">
        <ul>
            <li><a href="pengiriman.php"><i class="fas fa-truck-fast"></i> Pengiriman</a></li>
            <li><a href="mutasi.php"><i class="fas fa-exchange-alt"></i> Mutasi</a></li>
            <li><a href="verifikasi.php"><i class="fas fa-shield-alt"></i> Verifikasi Security</a></li>
            <li><a href="profile.php"><i class="fas fa-user-circle"></i> Profil</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
    <div class="dashboard-container">
        <?php
        if (!empty($success)) {
            echo "<p class='success-message'>$success</p>";
        }
        if (!empty($error)) {
            echo "<p class='error-message'>$error</p>";
        }
        ?>

        <!-- Formulir Tambah Pengiriman -->
        <div class="form-pengiriman">
            <h2>Form Pengiriman</h2>
            <form action="#" method="post">
                <div class="input-group">
                    <label for="nama_konsumen">NAMA KONSUMEN</label>
                    <input type="text" id="nama_konsumen" name="nama_konsumen" required>
                </div>
                <div class="input-group">
                    <label for="nama_barang">NAMA BARANG</label>
                    <input type="text" id="nama_barang" name="nama_barang" required>
                </div>
                <div class="input-group">
                    <label for="jenis">JENIS</label>
                    <input type="text" id="jenis" name="jenis" required>
                </div>
                <div class="input-group">
                    <label for="nama_driver">NAMA DRIVER</label>
                    <input type="text" id="nama_driver" name="nama_driver" required>
                </div>
                <button type="submit" name="simpan">SIMPAN</button>
            </form>
        </div>

        <!-- Formulir Edit Pengiriman (hanya muncul jika ada data yang akan diedit) -->
        <?php if ($edit_data && $user_role == 'admin'): ?>
        <div class="form-pengiriman">
            <h2>Edit Pengiriman</h2>
            <form action="#" method="post">
                <input type="hidden" name="id_pengiriman" value="<?php echo htmlspecialchars($edit_data['id']); ?>">
                <div class="input-group">
                    <label for="edit_nama_konsumen">NAMA KONSUMEN</label>
                    <input type="text" id="edit_nama_konsumen" name="nama_konsumen" value="<?php echo htmlspecialchars($edit_data['nama_konsumen']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="edit_nama_barang">NAMA BARANG</label>
                    <input type="text" id="edit_nama_barang" name="nama_barang" value="<?php echo htmlspecialchars($edit_data['nama_barang']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="edit_jenis">JENIS</label>
                    <input type="text" id="edit_jenis" name="jenis" value="<?php echo htmlspecialchars($edit_data['jenis']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="edit_nama_driver">NAMA DRIVER</label>
                    <input type="text" id="edit_nama_driver" name="nama_driver" value="<?php echo htmlspecialchars($edit_data['nama_driver']); ?>" required>
                </div>
                <button type="submit" name="update_data">UPDATE DATA</button>
            </form>
        </div>
        <?php endif; ?>

        <!-- Tabel Data Pengiriman -->
        <div class="table-container">
            <h3>Hasil Inputan Pengiriman</h3>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Konsumen</th>
                        <th>Barang</th>
                        <th>Jenis</th>
                        <th>Driver</th>
                        <th>Tanggal</th>
                        <?php if ($user_role == 'admin'): ?>
                            <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($pengiriman_data) > 0): ?>
                        <?php foreach ($pengiriman_data as $index => $row): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($row['nama_konsumen']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                                <td><?php echo htmlspecialchars($row['jenis']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama_driver']); ?></td>
                                <td><?php echo htmlspecialchars($row['tanggal_input']); ?></td>
                                <?php if ($user_role == 'admin'): ?>
                                    <td>
                                        <a href="?edit_id=<?php echo $row['id']; ?>" class="action-btn">Edit</a>
                                        <form method="post" action="#" style="display:inline-block;">
                                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="action-btn delete-btn" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">Hapus</button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?php echo ($user_role == 'admin') ? '7' : '6'; ?>">Tidak ada data pengiriman.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
