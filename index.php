<?php
require "koneksi.php";
include "utils.php";
session_start();

if (!isset($_SESSION["user_id"])) {
  header("Location: Auth/login.php");
  exit();
}

$username = $_SESSION["username"];
$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"];

$firstName = explode(" ", $username)[0];

$keyword = isset($_GET["keyword"]) ? trim($_GET["keyword"]) : "";
$location = isset($_GET["location"]) ? trim($_GET["location"]) : "";
$job_type = isset($_GET["job_type"]) ? trim($_GET["job_type"]) : "";
$company = isset($_GET["company"]) ? trim($_GET["company"]) : "";
$date_posted = isset($_GET["date_posted"]) ? trim($_GET["date_posted"]) : "";
$salary_range = isset($_GET["salary_range"]) ? trim($_GET["salary_range"]) : "";

$salary_min = '';
$salary_max = '';
if (!empty($salary_range)) {
  $range_parts = explode('-', $salary_range);
  if (count($range_parts) == 2) {
    $salary_min = trim($range_parts[0]);
    $salary_max = trim($range_parts[1]);
    if ($salary_max === 'max')
      $salary_max = '';
  }
}

$jobs = getJobsWithFilters(
  $koneksi,
  $keyword,
  $location,
  $job_type,
  $company,
  $date_posted,
  $salary_min,
  $salary_max
);

if ($_SESSION['role'] == 'company') {
  $companyData = getCompanyByUserId($koneksi, $user_id);

  $total_jobs = getTotalJobsByCompanyId($koneksi, $companyData['id']);
  $recent_jobs = getRecentJobsCountById($koneksi, $companyData['id']);
} else {
  $jobs_json = json_encode($jobs);

  $listJobType = getAllJobTypes($koneksi);
  $listJobLocation = getAllJobLocation($koneksi);
  $listJobCompany = getAllCompanyName($koneksi);
  $salaryRanges = getSalaryRanges();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SugoiJob - Dashboard</title>
  <link rel="stylesheet" href="Style/index.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
          <?php if ($_SESSION['role'] == 'job_seeker'): ?>
            <li><a href="index.php" class="navbar-link active">Home</a></li>
            <li><a href="Jobseeker/profile.php" class="navbar-link">Profile</a></li>
          <?php endif; ?>
          <?php if ($_SESSION['role'] == 'company'): ?>
            <li><a href="index.php" class="navbar-link active">Dashboard</a></li>
            <li><a href="Company/manage-jobs.php" class="navbar-link">Manage Jobs</a></li>
            <li><a href="Company/application.php" class="navbar-link">Applications</a></li>
          <?php endif; ?>
        </ul>
      </div>
      <div class="navbar-user">
        <span class="navbar-welcome">Welcome,</span>
        <?php if ($_SESSION['role'] == 'job_seeker'): ?>
          <span class="navbar-username"><?php echo $firstName; ?></span>
        <?php endif; ?>

        <?php if ($_SESSION['role'] == 'company'): ?>
          <span class="navbar-username"><?php echo htmlspecialchars($companyData['company_name']); ?></span>
        <?php endif; ?>
        <a href="Auth/logout.php" class="navbar-signout">Sign Out</a>
      </div>
    </nav>
  </header>
  <main>
    <?php if ($_SESSION['role'] == 'job_seeker'): ?>
      <div class="jobseeker">
        <section class="search-filter">
          <div class="search-wrap">
            <form action="index.php" method="GET" id="search-form">
              <div class="input-wrap">
                <input type="text" name="keyword" id="job-title"
                  placeholder="Judul pekerjaan, kata kunci, atau perusahaan"
                  value="<?php echo htmlspecialchars($keyword); ?>" />
                <input type="text" name="location" id="location"
                  placeholder="Kota, negara bagian, kode pos, atau 'remote'"
                  value="<?php echo htmlspecialchars($location); ?>" />

                <input type="hidden" name="job_type" value="<?php echo htmlspecialchars($job_type); ?>">
                <input type="hidden" name="company" value="<?php echo htmlspecialchars($company); ?>">
                <input type="hidden" name="date_posted" value="<?php echo htmlspecialchars($date_posted); ?>">
                <input type="hidden" name="salary_range" value="<?php echo htmlspecialchars($salary_range); ?>">

                <button type="submit">Cari</button>
              </div>
            </form>
          </div>
          <div class="filter-wrap">
            <div class="filter-group">
              <form action="index.php" method="GET" id="filter-form"> <input type="hidden" name="keyword" value="<?php echo htmlspecialchars(
                $keyword
              ); ?>">

                <input type="date" id="date-posted" name="date_posted" value="<?php echo htmlspecialchars(
                  $date_posted
                ); ?>" onchange="applyFilters()">

                <select id="job-type" name="job_type" class="custom-select <?php echo !empty(
                  $job_type
                )
                  ? "filter-active"
                  : ""; ?>" onchange="applyFilters()">
                  <option value="">Job Type</option>
                  <?php foreach ($listJobType as $type): ?>
                    <option value="<?php echo htmlspecialchars(
                      $type["job_type"]
                    ); ?>" <?php echo $job_type ===
                       $type["job_type"]
                       ? "selected"
                       : ""; ?>>
                      <?php echo htmlspecialchars(
                        $type["job_type"]
                      ); ?>
                    </option>
                  <?php endforeach; ?>
                </select>

                <select id="company" name="company" class="custom-select <?php echo !empty($company)
                  ? "filter-active"
                  : ""; ?>" onchange="applyFilters()">
                  <option value="">Company</option>
                  <?php foreach ($listJobCompany as $comp): ?>
                    <option value="<?php echo htmlspecialchars(
                      $comp["company_name"]
                    ); ?>" <?php echo $company ===
                       $comp["company_name"]
                       ? "selected"
                       : ""; ?>>
                      <?php echo htmlspecialchars(
                        $comp["company_name"]
                      ); ?>
                    </option>
                  <?php endforeach; ?>
                </select>

                <select id="salary-range" name="salary_range" class="custom-select <?php echo !empty(
                  $salary_range
                )
                  ? "filter-active"
                  : ""; ?>" onchange="applyFilters()">
                  <option value="">Salary</option>
                  <?php foreach ($salaryRanges as $range): ?>
                    <?php if (
                      isset($range["min"]) &&
                      isset($range["max"])
                    ): ?>
                      <?php $rangeValue =
                        $range["min"] .
                        "-" .
                        ($range["max"] ?: "max"); ?>
                      <option value="<?php echo $rangeValue; ?>" <?php echo $salary_range ===
                           $rangeValue
                           ? "selected"
                           : ""; ?>>
                        <?php echo htmlspecialchars(
                          $range["label"]
                        ); ?>
                      </option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>

                <select id="location-filter" name="location" class="custom-select <?php echo !empty($location)
                  ? "filter-active"
                  : ""; ?>" onchange="applyFilters()">
                  <option value="">Location</option>
                  <?php foreach ($listJobLocation as $loc): ?>
                    <option value="<?php echo htmlspecialchars(
                      $loc["location"]
                    ); ?>" <?php echo $location ===
                       $loc["location"]
                       ? "selected"
                       : ""; ?>>
                      <?php echo htmlspecialchars(
                        $loc["location"]
                      ); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </form>
              <?php if (!empty($keyword) || !empty($location) || !empty($job_type) || !empty($company) || !empty($date_posted) || !empty($salary_range)): ?>
                <a href="index.php" class="clear-filters">Clear All Filters</a>
              <?php endif; ?>
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
                  <?php if (isPriority($job["created_at"])): ?>
                    <div class="priority">
                      <p>Dicari!</p>
                    </div>
                  <?php endif; ?>

                  <div class="title">
                    <h2><?php echo htmlspecialchars($job["title"]); ?></h2>
                  </div>

                  <div class="company">
                    <p>
                      <?php echo htmlspecialchars($job["company_name"]); ?> <br />
                      <?php echo htmlspecialchars($job["location"]); ?>
                    </p>
                  </div>

                  <div class="info">
                    <p><?php echo formatSalary(
                      $job["salary_min"],
                      $job["salary_max"],
                      $job["salary_text"]
                    ); ?>
                    </p>
                    <p><?php echo formatJobType($job["job_type"]); ?></p>
                    <p><?php echo htmlspecialchars($job["category_name"]); ?></p>
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
      </div>
    <?php endif; ?>
    <?php if ($_SESSION['role'] == 'company'): ?>
      <div class="company">
        <div class="dashboard-header">
          <h1>Employer Dashboard</h1>
          <p>Kelola lowongan pekerjaan Anda dan lacak lamaran untuk
            <?php echo htmlspecialchars($companyData['company_name']); ?>
          </p>
        </div>

        <section class="breadcrumb">
          <p><a href="index.php" class="active">Dashboard</a></p>
        </section>

        <div class="dashboard-content">
          <div class="stats-grid">
            <div class="stat-card">
              <div class="stat-card-header">
                <div class="stat-icon jobs">
                  <i class="fas fa-briefcase"></i>
                </div>
                <div>
                  <h3>Total Jobs Posted</h3>
                  <div class="stat-number"><?php echo $total_jobs; ?></div>
                  <div class="stat-change <?php echo $recent_jobs > 0 ? 'positive' : 'neutral'; ?>">
                    <?php if ($recent_jobs > 0): ?>
                      <i class="fas fa-arrow-up"></i>
                      <?php echo $recent_jobs; ?> this month
                    <?php else: ?>
                      <i class="fas fa-minus"></i>
                      No new jobs this month
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>
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

  <script id="jobs-data-json" type="application/json">
    <?php echo $jobs_json; ?>
  </script>
  <script src="Script/search.js"></script>
  <script src="Script/index.js"></script>
</body>

</html>