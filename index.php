<?php
require 'koneksi.php';
include 'utils.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: Auth/login.php");
  exit();
}

$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

$jobs_json = json_encode($jobs);

$listJobType = getAllJobTypes($koneksi);
$listJobLocation = getAllJobLocation($koneksi);
$listJobCompany = getAllCompanyName($koneksi);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SugoiJob - Dashboard</title>
  <link rel="stylesheet" href="Style/index.css">
</head>

<body>
  <header>
    <nav class="navbar">
      <div class="navbar-left">
        <div class="navbar-logo">
          <img src="Asset/logo.png" alt="SugoiJob Logo" width="32" height="32">
          <h2 class="navbar-brand">SugoiJob</h2>
        </div>
        <ul class="navbar-links">
          <li><a href="index.php" class="navbar-link active">Home</a></li>
        </ul>
      </div>
      <div class="navbar-user">
        <span class="navbar-welcome">Welcome,</span>
        <span class="navbar-username"><?php echo $username ?></span>
        <a href="Auth/logout.php" class="navbar-signout">Sign Out</a>
      </div>
    </nav>
  </header>
  <main>
    <section class="search-filter">
      <div class="search-wrap">
        <form action="search.php" method="get">
          <div class="input-wrap">
            <input type="text" name="keyword" id="job-title" placeholder="Judul pekerjaan, kata kunci, atau perusahaan" />
            <input type="text" name="location" id="location" placeholder="Kota, negara bagian, kode pos, atau 'remote'" />
            <button type="submit">Cari</button>
          </div>
        </form>
      </div>
      <div class="filter-wrap">
        <div class="filter-group">
          <input type="date" id="date-posted" name="date_posted">
          <select id="job-type" class="custom-select">
            <option value="">Job Type</option>
            <?php foreach ($listJobType as $type): ?>
              <option value="<?php echo htmlspecialchars($type['job_type']); ?>">
                <?php echo htmlspecialchars($type['job_type']); ?>
              </option>
            <?php endforeach; ?>
          </select>
          <select id="company" class="custom-select">
            <option value="">Company</option>
            <?php foreach ($listJobCompany as $company): ?>
              <option value="<?php echo htmlspecialchars($company['company_name']); ?>">
                <?php echo htmlspecialchars($company['company_name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
          <select id="location-filter" class="custom-select">
            <option value="">Location</option>
            <?php foreach ($listJobLocation as $location): ?>
              <option value="<?php echo htmlspecialchars($location['location']); ?>">
                <?php echo htmlspecialchars($location['location']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </section>

    <section class="breadcrumb">
      <p><a href="index.php" class="active">Home</a></p>
    </section>

    <section class="job-list">
      <section class="job-wrap">
        <div class="scroll-content">
          <p class="info">Yang tersedia saat ini.</p>
          <?php foreach ($jobs as $index => $job): ?>
            <div class="job-item" data-job-index="<?php echo $index; ?>" onclick="showJobDetail(<?php echo $index; ?>)">
              <?php if (isPriority($job['created_at'])): ?>
                <div class="priority">
                  <p>Dicari!</p>
                </div>
              <?php endif; ?>

              <div class="title">
                <h2><?php echo htmlspecialchars($job['title']); ?></h2>
              </div>

              <div class="company">
                <p>
                  <?php echo htmlspecialchars($job['company_name']); ?> <br />
                  <?php echo htmlspecialchars($job['location']); ?>
                </p>
              </div>

              <div class="info">
                <p><?php echo formatSalary($job['salary_min'], $job['salary_max'], $job['salary_text']); ?></p>
                <p><?php echo formatJobType($job['job_type']); ?></p>
                <p><?php echo htmlspecialchars($job['category_name']); ?></p>
              </div>
            </div>
          <?php endforeach; ?>

          <?php if (empty($jobs)): ?>
            <div class="job-item">
              <p>Belum ada lowongan kerja yang tersedia.</p>
            </div>
          <?php endif; ?>
        </div>
        <div class="static-info">
          <div class="job-detail" id="job-detail-container">
            <div class="empty-state" id="empty-state">
              <p>Pilih lowongan kerja untuk melihat detail</p>
            </div>
          </div>
        </div>
      </section>
    </section>
  </main>
  <footer>
    <div class="logo">
      <img src="Asset/logo.png" alt="SugoiJob Logo" width="36" />
      <span>SugoiJob © <span id="currentYear"></span>. All rights reserved.</span>
    </div>
    <ul>
      <li>
        <img src="Asset/instagram.png" />
        <a href="https://www.instagram.com/kncrln_/" target="_blank">Kenzie</a>
      </li>
      <li>
        <img src="Asset/instagram.png" />
        <a href="https://www.instagram.com/r.van83/" target="_blank">Ivan</a>
      </li>
      <li>
        <img src="Asset/instagram.png" />
        <a href="https://www.instagram.com/ndusft/" target="_blank">Bernadus</a>
      </li>
    </ul>
  </footer>

  <script>
    const jobsData = <?php echo $jobs_json; ?>;
  </script>
  <script src="Script/index.js"></script>
</body>

</html>