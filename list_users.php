<?php
include 'config.php';

// Jika ada aksi hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM users WHERE id = $id");
    header("Location: list_users.php");
    exit();
}

// Ambil semua user
$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar User Terdaftar</title>
    <style>
        table {
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #333;
            padding: 8px;
        }
        a {
            text-decoration: none;
            color: red;
        }
    </style>
</head>
<body>
    <h2>Daftar User Terdaftar</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Password</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['password']; ?></td>
            <td>
                <a href="list_users.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Yakin mau hapus?')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
