<?php
require '../koneksi.php';
session_start();

$username = $_SESSION["username"];
$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"];

$firstName = explode(" ", $username)[0];

if (!isset($_GET['id'])) {
  header('Location: ../');
  exit();
}

$id = $_GET['id'];

$query = "SELECT jp.*, c.*, jc.name as category_name 
FROM job_postings jp 
INNER JOIN companies c ON jp.company_id = c.id 
INNER JOIN job_categories jc ON jp.category_id = jc.id 
WHERE jp.id = ?";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

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
          <?php if ($_SESSION['role'] == 'job_seeker'): ?>
            <li><a href="/" class="navbar-link">Home</a></li>
            <li><a href="Jobseeker/profile.php" class="navbar-link">Profile</a></li>
          <?php endif; ?>
          <?php if ($_SESSION['role'] == 'company'): ?>
            <li><a href="/" class="navbar-link active">Dashboard</a></li>
            <li><a href="company/profile.php" class="navbar-link">Profile</a></li>
          <?php endif; ?>
        </ul>
      </div>
      <div class="navbar-user">
        <span class="navbar-welcome">Welcome,</span>
        <?php if ($_SESSION['role'] == 'job_seeker'): ?>
          <span class="navbar-username"><?php echo $firstName; ?></span>
        <?php endif; ?>

        <?php if ($_SESSION['role'] == 'company'): ?>
          <span class="navbar-username"><?php echo htmlspecialchars($job['company_name']); ?></span>
        <?php endif; ?>
        <a href="/auth/logout.php" class="navbar-signout">Sign Out</a>
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
          </div>
          <?php if ($_SESSION['role'] == 'job_seeker'): ?>
            <div class="apply">
              <a href="/lamaran.html">Apply Sekarang</a>
            </div>
          <?php endif; ?>

          <?php if ($_SESSION['role'] == 'company'): ?>
            <div class="count">
              <p>View Count: <?php echo $job["views_count"]; ?></p>
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
        $cleaned_requirements = str_replace(["\r", "\n"], '', $job["requirements"]);
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