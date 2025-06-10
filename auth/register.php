<?php
require '../koneksi.php';
session_start();

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username-job'] ?? $_POST['username-company']);
  $password = $_POST['password-job'] ?? $_POST['password-company'];
  $email = trim($_POST['email-job'] ?? $_POST['email-company']);
  $role = $_POST['role-job'] ?? $_POST['role-company'];

  if (empty($username) || empty($password) || empty($email) || empty($role)) {
    $error_message = 'Semua field harus diisi!';
  } else {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $error_message = 'Format email tidak valid!';
    } else {
      $stmt = $koneksi->prepare("SELECT * FROM users WHERE name = ?");
      $stmt->bind_param("s", $username);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) {
        $error_message = 'Username sudah terdaftar! Silakan gunakan username lain.';
      } else {
        $stmt2 = $koneksi->prepare("SELECT * FROM users WHERE email = ?");
        $stmt2->bind_param("s", $email);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        if ($result2->num_rows > 0) {
          $error_message = 'Email sudah terdaftar! Silakan gunakan email lain atau <a href="login.php" style="color: #007bff;">login di sini</a>.';
        } else {
          $stmt3 = $koneksi->prepare("INSERT INTO users (name, password, email, role) VALUES (?, ?, ?, ?)");
          $stmt3->bind_param("ssss", $username, $password, $email, $role);

          if ($stmt3->execute()) {
            $success_message = 'Akun berhasil dibuat! Anda akan diarahkan ke halaman login dalam 3 detik...';
            echo "<script>
              setTimeout(function() {
                window.location.href = 'login.php';
              }, 3000);
            </script>";
          } else {
            $error_message = 'Terjadi kesalahan saat membuat akun. Silakan coba lagi.';
          }
          $stmt3->close();
        }
        $stmt2->close();
      }
      $stmt->close();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SugoiJob - Registration</title>
  <link rel="stylesheet" href="../style/register.css">
</head>

<body>
  <div class="container">
    <div class="logo-section">
      <img src="../asset/logo.png" alt="SugoiJob Logo" width="42" height="42">
      <h1>SugoiJob</h1>
    </div>
    <div class="register-section">
      <h2>Daftar Akun</h2>
      <p id="text-register">Pilih jenis akun yang ingin Anda buat untuk memulai.</p>
      <?php if (!empty($error_message)): ?>
        <div class="error-message">
          <?php echo $error_message; ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($success_message)): ?>
        <div class="success-message">
          <?php echo $success_message; ?>
        </div>
      <?php endif; ?>
      <div id="role">
        <div class="role-selection">
          <div class="role-card" data-role="jobseeker">
            <h3>Pencari Kerja</h3>
            <p>Saya ingin mencari pekerjaan dan melamar ke berbagai perusahaan</p>
          </div>
          <div class="role-card" data-role="company">
            <h3>Perusahaan</h3>
            <p>Saya ingin merekrut karyawan dan memposting lowongan pekerjaan</p>
          </div>
        </div>
        <button type="button" class="continue-btn">
          Lanjutkan Pendaftaran
        </button>
      </div>
      <div id="jobseeker">
        <form action="register.php" method="POST">
          <div class="input-group">
            <label for="username-job">Username</label>
            <input type="text" id="username-job" name="username-job" required>
          </div>
          <div class="input-group">
            <label for="password-job">Password</label>
            <input type="password" id="password-job" name="password-job" required>
          </div>
          <div class="input-group">
            <label for="email-job">Email</label>
            <input type="email" id="email-job" name="email-job" required>
          </div>
          <input type="text" value="job_seeker" id="role-job" name="role-job" hidden>
          <button type="submit">Buat Akun</button>
        </form>
      </div>
      <div id="company">
        <form action="register.php" method="POST">
          <div class="input-group">
            <label for="username-company">Username</label>
            <input type="text" id="username-company" name="username-company" required>
          </div>
          <div class="input-group">
            <label for="password-company">Password</label>
            <input type="password" id="password-company" name="password-company" required>
          </div>
          <div class="input-group">
            <label for="email-company">Email</label>
            <input type="email" id="email-company" name="email-company" required>
          </div>
          <input type="text" value="company" id="role-company" name="role-company" hidden>
          <button type="submit">Buat Akun</button>
        </form>
      </div>
    </div>
  </div>
  <script src="../script/register.js"></script>
</body>

</html>