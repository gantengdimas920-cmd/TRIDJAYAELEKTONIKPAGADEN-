<?php
session_start();
include 'config.php';

$error = '';

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi!";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Cek password tanpa hashing
            if ($password === $user['password']) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $user['role'];

                // Arahkan ke halaman yang berbeda berdasarkan peran
                if ($user['role'] === 'admin') {
                    header("Location: ads.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login Aplikasi Kasir</title>
  <link rel="stylesheet" href="st.css">
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <h2>LOGIN</h2>
      <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
      <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <div class="button-group">
          <button type="submit" name="login">Login</button>
          <a href="register.php" class="register-button">Register</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
