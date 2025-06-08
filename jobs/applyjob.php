<?php
require '../koneksi.php';
session_start();

$username = $_SESSION["username"];
$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"];

$firstName = explode(" ", $username)[0];
?>