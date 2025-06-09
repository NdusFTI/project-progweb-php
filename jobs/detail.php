<?php
require '../koneksi.php';
include '../utils.php';
session_start();

$isLoggedIn = isset($_SESSION["user_id"]);
$username = $isLoggedIn ? $_SESSION["username"] : "Guest";
$user_id = $isLoggedIn ? $_SESSION["user_id"] : null;
$role = $isLoggedIn ? $_SESSION["role"] : "job_seeker";

$firstName = $isLoggedIn ? explode(" ", $username)[0] : "Guest";

if (!isset($_GET['id'])) {
  header('Location: ../');
  exit();
}

$id = $_GET['id'];
$result = getDetailJobs($koneksi, $id);

if (mysqli_num_rows($result) > 0) {
  $job = mysqli_fetch_assoc($result);

  $updateQuery = "UPDATE job_postings SET views_count = views_count + 1 WHERE id = ?";
  $updateStmt = mysqli_prepare($koneksi, $updateQuery);
  mysqli_stmt_bind_param($updateStmt, "i", $id);
  mysqli_stmt_execute($updateStmt);
} else {
  header('Location: ../');
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SugoiJob - <?php echo $job["title"] . " - " . $job["company_name"]; ?></title>
  <link rel="stylesheet" href="../style/detail.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
  <header>
    <nav class="navbar">
      <div class="navbar-left">
        <div class="navbar-logo">
          <img src="../asset/logo.png" alt="SugoiJob Logo" width="32" height="32">
          <h2 class="navbar-brand">SugoiJob</h2>
        </div>
        <ul class="navbar-links">
          <?php if (!$isLoggedIn || $role == 'job_seeker'): ?>
            <li><a href="/" class="navbar-link">Home</a></li>
          <?php endif; ?>
          <?php if ($isLoggedIn && $role == 'company'): ?>
            <li><a href="/" class="navbar-link active">Dashboard</a></li>
          <?php endif; ?>
        </ul>
      </div>
      <div class="navbar-user">
        <?php if ($isLoggedIn): ?>
          <span class="navbar-welcome">Welcome,</span>
          <?php if ($role == 'job_seeker'): ?>
            <span class="navbar-username"><?php echo $firstName; ?></span>
          <?php endif; ?>

          <?php if ($role == 'company'): ?>
            <span class="navbar-username"><?php echo htmlspecialchars($job['company_name']); ?></span>
          <?php endif; ?>
          <a href="/auth/logout.php" class="navbar-btns">Sign Out</a>
        <?php else: ?>
          <span class="navbar-welcome">Welcome,</span>
          <span class="navbar-username"><?php echo $firstName; ?></span>
          <a href="/auth/login.php" class="navbar-btns">Login</a>
        <?php endif; ?>
      </div>
    </nav>
  </header>

  <section class="breadcrumb">
    <p>
      <a href="/" class="">Home</a> /
      <a href="detail.php?id=<?php echo $_GET['id']; ?>" class="active">
        <?php echo $job["title"] . " - " . $job["company_name"]; ?>
      </a>
    </p>
  </section>

  <main>
    <section class="columns">
      <div class="column_1">
        <div class="content">
          <img class="bg" src=<?php echo $job["company_banner"] ?> />
          <div class="company-info">
            <img src=<?php echo $job["company_logo"] ?> />
            <div class="info">
              <h2>
                <?php echo $job["title"] . " - " . $job["company_name"]; ?>
              </h2>
              <p>
                <?php echo $job["company_name"] ?> <br />
                <?php echo $job["city"] ?>
              </p>
            </div>
          </div> <?php if ($isLoggedIn && $role == 'job_seeker'): ?>
            <div class="apply">
              <a href="apply.php?id=<?php echo $id ?>">Apply Sekarang</a>
            </div>
          <?php elseif (!$isLoggedIn): ?>
            <div class="apply">
              <a href="../auth/login.php">Login to Apply</a>
            </div>
          <?php endif; ?>

          <?php if ($isLoggedIn && $role == 'company'): ?>
            <div class="applicant">
              <div class="count">
                <i class="fas fa-user"></i>
                0 applicant
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <div class="column_2">
        <div class="details">
          <h1>Detail</h1>
          <div class="column_detail">
            <div class="left-group">
              <div class="type">
                <h1>Kategori</h1>
                <p><?php echo $job["category_name"]; ?></p>
              </div>
              <div class="type">
                <h1>Gaji</h1>
                <p><?php echo $job["salary_text"]; ?> per bulan</p>
              </div>
            </div>
            <div class="right-group">
              <div class="type">
                <h1>Tipe pekerjaan</h1>
                <div class="tag">
                  <p>
                    <?php echo $job["job_type"]; ?>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="location">
          <h1>Lokasi</h1>
          <p>
            <?php echo $job["full_address"]; ?>
          </p>
        </div>
      </div>
    </section>
    <section class="description">
      <h1>Deskripsi</h1>
      <div class="description-content">
        <p>
          <?php echo htmlspecialchars($job["description"]); ?>
        </p>
        <p>Persyaratan:</p>
        <?php
        $cleaned_requirements = str_replace(["\\r\\n", "\\r", "\\n", "\r\n", "\r", "\n"], '', $job["requirements"]);
        $items = array_filter(explode('• ', $cleaned_requirements));
        ?>

        <ul>
          <?php foreach ($items as $item): ?>
            <?php if (empty(trim($item)))
              continue; ?>
            <li><?= htmlspecialchars(trim($item)) ?></li>
          <?php endforeach; ?>
        </ul>
        <p>Job Types: <?php echo $job["job_type"]; ?></p>
        <p>Experience:</p>
        <ul>
          <li><?php echo $job["experience_required"]; ?> (Required)</li>
        </ul>
        <?php
        $tanggal = DateTime::createFromFormat('Y-m-d', $job["application_deadline"]);
        $formatted = $tanggal->format('d F Y');
        ?>
        <p>Batas Lamaran: <?php echo $formatted; ?></p>
      </div>
    </section>
  </main>
</body>

</html>