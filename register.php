<?php
session_start();
include 'config.php';

$success = '';
$error = '';

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = 'kasir'; // default role

    if (empty($username) || empty($password)) {
        $error = "Semua kolom harus diisi!";
    } else {
        // Cek apakah username sudah digunakan
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Username sudah digunakan!";
        } else {
            // Simpan data ke database tanpa hashing password
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $password, $role);

            if ($stmt->execute()) {
                $success = "Registrasi berhasil! Silakan login.";
            } else {
                $error = "Gagal menyimpan data!";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Akun</title>
    <link rel="stylesheet" href="st.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>REGISTER</h2>
            <?php 
                if (!empty($error)) echo "<p class='error'>$error</p>";
                if (!empty($success)) echo "<p class='success'>$success</p>";
            ?>
            <form method="post">
                <input type="text" name="username" required>
                <input type="password" name="password" required>

                <div class="button-group">
                    <button type="submit" name="register">Daftar</button>
                    <a href="index.php" class="register-button">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
