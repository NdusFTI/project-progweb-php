<?php
require '../koneksi.php';

if (!isset($_GET['id'])) {
  header('Location: ../');
  exit();
}

$id = $_GET['id'];
echo $id;
?>