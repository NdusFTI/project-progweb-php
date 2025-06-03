<?php 
  require 'koneksi.php';
  session_start();

  if (!isset($_SESSION['user_id'])) {
    header("Location: Auth/login.php");
    exit();
  }

  $username = $_SESSION['username'];
  $user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SugoiJob - Dashboard</title>
    <link rel="stylesheet" href="Style/dashboard.css">
  </head>
  <body>
    <header>
      <nav>
        <div class="logo-section">
          <img src="Asset/logo.png" alt="SugoiJob Logo" width="32" height="32">
          <h2>SugoiJob</h2>
        </div>
        <ul class="links">
          <li><a href="index.php" class="active">Home</a></li>
        </ul>
        <div class="user">
          <h1><?php echo $username ?></h1>
        </div>
      </nav>
    </header>
  </body>
</html>