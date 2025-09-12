<?php
session_start();
include 'config.php';

// Hanya admin boleh akses
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
  header("Location: index.php");
  exit();
}

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// --- SIMPAN MANUAL ---
if (isset($_POST['simpan_manual'])) {
  $kode = trim($_POST['kode_barang']);
  $nama = trim($_POST['nama_barang']);
  $stok = intval($_POST['stok']);
  $toko = trim($_POST['toko']);

  $cek = $conn->query("SELECT * FROM barang WHERE kode_barang='$kode'");
  if ($cek->num_rows > 0) {
    $conn->query("UPDATE barang SET nama_barang='$nama', stok='$stok', toko='$toko' WHERE kode_barang='$kode'");
  } else {
    $conn->query("INSERT INTO barang (kode_barang, nama_barang, stok, toko) VALUES ('$kode', '$nama', '$stok', '$toko')");
  }
  $success_manual = "✅ Input manual berhasil!";
}

// --- UPLOAD EXCEL ---
if (isset($_POST['upload_excel'])) {
  $file = $_FILES['file_excel']['tmp_name'];
  if ($file) {
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    foreach ($rows as $index => $row) {
      if ($index === 0) continue; // header
      $kode = trim($row[0]);
      $nama = trim($row[1]);
      $stok = intval($row[2]);
      $toko = trim($row[3]);

      $cek = $conn->query("SELECT * FROM barang WHERE kode_barang='$kode'");
      if ($cek->num_rows > 0) {
        $conn->query("UPDATE barang SET nama_barang='$nama', stok='$stok', toko='$toko' WHERE kode_barang='$kode'");
      } else {
        $conn->query("INSERT INTO barang (kode_barang, nama_barang, stok, toko) VALUES ('$kode', '$nama', '$stok', '$toko')");
      }
    }
    $success_excel = "✅ Upload Excel berhasil!";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Input Barang Admin</title>
  <link rel="stylesheet" href="style2.css" />
</head>
<body>

  <!-- ✅ EFEK BUBBLE -->
  <div class="bubbles">
    <?php for ($i = 0; $i < 30; $i++): ?>
      <span></span>
    <?php endfor; ?>
  </div>

  <!-- ✅ FORM -->
  <div class="container">
    <h2>📦 INPUT BARANG</h2>
    <?php if (isset($success_manual)) echo "<p style='color:lime;'>$success_manual</p>"; ?>
    <form method="post">
      <label>Kode Barang:</label>
      <input type="text" name="kode_barang" required>
      <label>Nama Barang:</label>
      <input type="text" name="nama_barang" required>
      <label>Jumlah Stok:</label>
      <input type="number" name="stok" required>
      <label>Toko:</label>
      <input type="text" name="toko" required>
      <button type="submit" name="simpan_manual">💾 Simpan Manual</button>
    </form>

    <h2>📑 UPLOAD EXCEL</h2>
    <?php if (isset($success_excel)) echo "<p style='color:lime;'>$success_excel</p>"; ?>
    <form method="post" enctype="multipart/form-data">
      <input type="file" name="file_excel" accept=".xlsx" required>
      <button type="submit" name="upload_excel">⬆ Upload Excel</button>
    </form>
  </div>

</body>
</html>
