<?php
require '../koneksi.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username-job'] ?? $_POST['username-company'];
  $password = $_POST['password-job'] ?? $_POST['password-company'];
  $email = $_POST['email-job'] ?? $_POST['email-company'];
  $role = $_POST['role-job'] ?? $_POST['role-company'];

  if (empty($username) || empty($password) || empty($email) || empty($role)) {
    echo "<script>alert('Semua field harus diisi!');</scrip>";
  } else {
    $stmt = $koneksi->prepare("SELECT * FROM users WHERE name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      echo "<script>alert('Username sudah terdaftar!');</script>";
    } else {
      $stmt = $koneksi->prepare("INSERT INTO users (name, password, email, role) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("ssss", $username, $password, $email, $role);

      if ($stmt->execute()) {
        echo "<script>alert('Akun berhasil dibuat!'); window.location.href = 'login.php';</script>";
      } else {
        echo "<script>alert('Terjadi kesalahan saat membuat akun. Silakan coba lagi.');</script>";
      }
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
            <input type="text" id="email-job" name="email-job" required>
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
            <input type="text" id="email-company" name="email-company" required>
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