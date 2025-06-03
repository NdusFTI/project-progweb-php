<?php 
  require '../koneksi.php';
  session_start();

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {}
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
        <form action="login.php">
          <div class="input-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
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