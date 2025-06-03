<?php 
  require 'koneksi.php';
  session_start();

  if (!isset($_SESSION['user_id'])) {
    header("Location: Auth/login.php");
    exit();
  }
?>