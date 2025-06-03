<?php
require '../koneksi.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  if (empty($email) || empty($password)) {
    echo "<script>alert('Username dan password harus diisi!');</script>";
  } else {
    $stmt = $koneksi->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $_SESSION['user_id'] = $row['id'];
      $_SESSION['username'] = $row['name'];
      header("Location: ../index.php");
      exit();
    } else {
      echo "<script>alert('Email atau password salah!');</script>";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SugoiJob - Authentikasi</title>
  <link rel="stylesheet" href="../Style/login.css">
</head>

<body>
  <div class="container">
    <div class="logo-section">
      <img src="../Asset/logo.png" alt="SugoiJob Logo" width="42" height="42">
      <h1>SugoiJob</h1>
    </div>
    <div class="login-section">
      <h2>Login</h2>
      <p><i>Login terlebih dahulu untuk mengakses fitur.</i></p>
      <form action="login.php" method="POST">
        <div class="input-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required>
        </div>
        <div class="input-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Login</button>
        <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
      </form>
    </div>
  </div>
</body>

</html>