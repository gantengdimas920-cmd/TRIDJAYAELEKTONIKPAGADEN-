<?php
session_start();
include 'config.php';

// Asumsi: Anda sudah menginstal pustaka PhpSpreadsheet melalui Composer
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$message = "";
$error = "";

// --- Fungsionalitas Tambah/Edit Barang (Manual) ---
if (isset($_POST['tambah_barang'])) {
    $nama_barang = htmlspecialchars($_POST['nama_barang']);
    $jenis = htmlspecialchars($_POST['jenis']);
    $jumlah = htmlspecialchars($_POST['jumlah']);

    $stmt = $conn->prepare("INSERT INTO barang (nama_barang, jenis, jumlah) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $nama_barang, $jenis, $jumlah);

    if ($stmt->execute()) {
        $message = "Barang berhasil ditambahkan!";
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
}

if (isset($_POST['edit_barang'])) {
    $id_barang = htmlspecialchars($_POST['id_barang']);
    $nama_barang = htmlspecialchars($_POST['nama_barang']);
    $jenis = htmlspecialchars($_POST['jenis']);
    $jumlah = htmlspecialchars($_POST['jumlah']);

    $stmt = $conn->prepare("UPDATE barang SET nama_barang = ?, jenis = ?, jumlah = ? WHERE id_barang = ?");
    $stmt->bind_param("ssii", $nama_barang, $jenis, $jumlah, $id_barang);

    if ($stmt->execute()) {
        $message = "Barang berhasil diperbarui!";
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
}

if (isset($_GET['hapus'])) {
    $id_barang = htmlspecialchars($_GET['hapus']);
    $stmt = $conn->prepare("DELETE FROM barang WHERE id_barang = ?");
    $stmt->bind_param("i", $id_barang);

    if ($stmt->execute()) {
        $message = "Barang berhasil dihapus!";
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
    header("Location: menu_barang.php");
    exit();
}

// --- Fungsionalitas Unggah Excel ---
if (isset($_POST['unggah_excel'])) {
    if (isset($_FILES['file_excel']['name']) && !empty($_FILES['file_excel']['name'])) {
        $inputFileName = $_FILES['file_excel']['tmp_name'];

        try {
            $spreadsheet = IOFactory::load($inputFileName);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            
            $total_updated = 0;
            $total_added = 0;

            for ($row = 2; $row <= $highestRow; $row++) { // Mulai dari baris 2 untuk melewati header
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                                NULL,
                                                TRUE,
                                                FALSE);

                if (!empty($rowData[0][0])) {
                    $nama_barang = $rowData[0][0];
                    $jenis = $rowData[0][1];
                    $jumlah = $rowData[0][2];
                    
                    // Cek apakah barang sudah ada di database
                    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM barang WHERE nama_barang = ?");
                    $check_stmt->bind_param("s", $nama_barang);
                    $check_stmt->execute();
                    $check_stmt->bind_result($count);
                    $check_stmt->fetch();
                    $check_stmt->close();
                    
                    if ($count > 0) {
                        // Jika ada, lakukan UPDATE
                        $update_stmt = $conn->prepare("UPDATE barang SET jenis = ?, jumlah = ? WHERE nama_barang = ?");
                        $update_stmt->bind_param("sis", $jenis, $jumlah, $nama_barang);
                        $update_stmt->execute();
                        $update_stmt->close();
                        $total_updated++;
                    } else {
                        // Jika tidak ada, lakukan INSERT
                        $insert_stmt = $conn->prepare("INSERT INTO barang (nama_barang, jenis, jumlah) VALUES (?, ?, ?)");
                        $insert_stmt->bind_param("ssi", $nama_barang, $jenis, $jumlah);
                        $insert_stmt->execute();
                        $insert_stmt->close();
                        $total_added++;
                    }
                }
            }
            $message = "Data berhasil diunggah! Berhasil menambahkan $total_added item dan memperbarui $total_updated item.";
        } catch (Exception $e) {
            $error = "Error mengunggah file Excel: " . $e->getMessage();
        }
    } else {
        $error = "Silakan pilih file Excel untuk diunggah.";
    }
}

// Ambil data barang untuk ditampilkan
$result = $conn->query("SELECT * FROM barang ORDER BY id_barang DESC");

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Barang</title>
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
        <h2>Menu Barang</h2>
        <?php if (!empty($message)) echo "<p class='success-message'>$message</p>"; ?>
        <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>

        <div class="form-pengiriman">
            <h3>Tambah/Edit Barang Manual</h3>
            <form method="post" action="menu_barang.php">
                <input type="hidden" name="id_barang" id="id_barang">
                <div class="input-group">
                    <label for="nama_barang">Nama Barang</label>
                    <input type="text" id="nama_barang" name="nama_barang" required>
                </div>
                <div class="input-group">
                    <label for="jenis">Jenis</label>
                    <input type="text" id="jenis" name="jenis" required>
                </div>
                <div class="input-group">
                    <label for="jumlah">Jumlah</label>
                    <input type="number" id="jumlah" name="jumlah" required>
                </div>
                <button type="submit" name="tambah_barang">Simpan Barang</button>
            </form>
        </div>

        <hr>

        <div class="form-pengiriman">
            <h3>Unggah Data Barang (Excel)</h3>
            <p>Unggah file Excel (.xlsx) dengan kolom: Nama Barang, Jenis, Jumlah. Data akan diperbarui atau ditambahkan secara otomatis.</p>
            <form method="post" action="menu_barang.php" enctype="multipart/form-data">
                <div class="input-group">
                    <label for="file_excel">Pilih File Excel</label>
                    <input type="file" name="file_excel" id="file_excel" required accept=".xlsx, .xls">
                </div>
                <button type="submit" name="unggah_excel">Unggah dan Perbarui</button>
            </form>
        </div>

        <hr>

        <div class="table-container">
            <h3>Daftar Barang</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                            <td><?php echo htmlspecialchars($row['jenis']); ?></td>
                            <td><?php echo htmlspecialchars($row['jumlah']); ?></td>
                            <td>
                                <a href="#" class="action-btn" onclick="editBarang('<?php echo $row['id_barang']; ?>', '<?php echo htmlspecialchars($row['nama_barang']); ?>', '<?php echo htmlspecialchars($row['jenis']); ?>', '<?php echo htmlspecialchars($row['jumlah']); ?>')">Edit</a>
                                <a href="menu_barang.php?hapus=<?php echo $row['id_barang']; ?>" class="action-btn delete-btn" onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?');">Hapus</a>
                            </td>
                        </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center;'>Tidak ada data barang.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function editBarang(id, nama, jenis, jumlah) {
            document.getElementById('id_barang').value = id;
            document.getElementById('nama_barang').value = nama;
            document.getElementById('jenis').value = jenis;
            document.getElementById('jumlah').value = jumlah;
            document.querySelector('button[name="tambah_barang"]').name = 'edit_barang';
            document.querySelector('button[name="edit_barang"]').innerText = 'Perbarui Barang';
        }
    </script>
</body>
</html>
```eof