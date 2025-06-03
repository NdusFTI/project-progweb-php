<?php 
  require '../koneksi.php';
  session_start();


?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SugoiJob - Registration</title>
    <link rel="stylesheet" href="../Style/register.css">
  </head>
  <body>
    <div class="container">
      <div class="logo-section">
        <img src="../Asset/logo.png" alt="SugoiJob Logo" width="42" height="42">
        <h1>SugoiJob</h1>
      </div>
      <div class="register-section">
        <h2>Daftar Akun</h2>
        <p>Pilih jenis akun yang ingin Anda buat untuk memulai.</p>
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
      </div>
    </div>
  </body>
</html>