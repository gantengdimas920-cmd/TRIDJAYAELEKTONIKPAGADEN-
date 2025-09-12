<?php
$servername = "hostlocal";
$username = "root";
$password = "";
$database = "kasir_toko";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
