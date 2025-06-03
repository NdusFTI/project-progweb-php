<?php
$server = "localhost";
$username = "root";
$password = "";
$database = "sugoijob";

$koneksi = mysqli_connect($server, $username, $password, $database) or die("Koneksi gagal: " . mysqli_connect_error());
?>