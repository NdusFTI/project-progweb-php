<?php
require "koneksi.php";
include "utils.php";
session_start();

if (!isset($_SESSION["user_id"])) {
  header("Location: auth/login.php");
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

if ($_SESSION['role'] == 'company') {
  $companyData = getCompanyByUserId($koneksi, $user_id);

  $jobs = getJobsByCompanyId($koneksi, $companyData['id']);
  $total_jobs = getTotalJobsByCompanyId($koneksi, $companyData['id']);
  $recent_jobs = getRecentJobsCountById($koneksi, $companyData['id']);
  $active_jobs = getActiveJobsCountById($koneksi, $companyData['id']);
  $total_applicants = getTotalApplicantsByCompanyId($koneksi, $companyData['id']);
  $total_views = getTotalViewsByCompanyId($koneksi, $companyData['id']);
} else {
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
  <link rel="stylesheet" href="style/index.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
  <header>
    <nav class="navbar">
      <div class="navbar-left">
        <div class="navbar-logo">
          <img src="asset/logo.png" alt="SugoiJob Logo" width="32" height="32">
          <h2 class="navbar-brand">SugoiJob</h2>
        </div>
        <ul class="navbar-links">
          <?php if ($_SESSION['role'] == 'job_seeker'): ?>
            <li><a href="/" class="navbar-link active">Home</a></li>
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
          <span class="navbar-username"><?php echo htmlspecialchars($companyData['company_name']); ?></span>
        <?php endif; ?>
        <a href="auth/logout.php" class="navbar-signout">Sign Out</a>
      </div>
    </nav>
  </header>
  <main>
    <?php if ($_SESSION['role'] == 'job_seeker'): ?>
      <section class="search-filter">
        <div class="search-wrap">
          <form action="/" method="GET" id="search-form">
            <div class="input-wrap">
              <input type="text" name="keyword" id="job-title" placeholder="Judul pekerjaan, kata kunci, atau perusahaan"
                value="<?php echo htmlspecialchars($keyword); ?>" />
              <input type="text" name="location" id="location" placeholder="Kota, negara bagian, kode pos, atau 'remote'"
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
            <form action="/" method="GET" id="filter-form"> <input type="hidden" name="keyword" value="<?php echo htmlspecialchars(
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
              <a href="/" class="clear-filters">Clear All Filters</a>
            <?php endif; ?>
          </div>
        </div>
      </section>

      <section class="breadcrumb">
        <p><a href="/" class="active">Home</a></p>
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
    <?php endif; ?>
    <?php if ($_SESSION['role'] == 'company'): ?>
      <div class="dashboard-header">
        <h1>Employer Dashboard</h1>
        <p>Kelola lowongan pekerjaan Anda dan lacak lamaran untuk
          <?php echo htmlspecialchars($companyData['company_name']); ?>
        </p>
      </div>

      <section class="breadcrumb">
        <p><a href="/" class="active">Dashboard</a></p>
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

          <div class="stat-card">
            <div class="stat-card-header">
              <div class="stat-icon active">
                <i class="fas fa-play-circle"></i>
              </div>
              <div>
                <h3>Active Jobs</h3>
                <div class="stat-number"><?php echo $active_jobs; ?></div>
                <div class="stat-change neutral">
                  <i class="fas fa-info-circle"></i>
                  Currently accepting applications
                </div>
              </div>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-card-header">
              <div class="stat-icon applicants">
                <i class="fas fa-users"></i>
              </div>
              <div>
                <h3>Total Applicants</h3>
                <div class="stat-number"><?php echo $total_applicants; ?></div>
                <div class="stat-change <?php echo $total_applicants > 0 ? 'positive' : 'neutral'; ?>">
                  <i class="fas fa-user-plus"></i>
                  Across all job postings
                </div>
              </div>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-card-header">
              <div class="stat-icon views">
                <i class="fas fa-eye"></i>
              </div>
              <div>
                <h3>Total Views</h3>
                <div class="stat-number"><?php echo number_format($total_views); ?></div>
                <div class="stat-change neutral">
                  <i class="fas fa-chart-line"></i>
                  Job listing impressions
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="jobs-section">
          <div class="section-header">
            <h2><i class="fas fa-list"></i> Your Job Postings</h2>
            <a href="company/addjob.php" class="add-job-btn">
              <i class="fas fa-plus"></i>
              Post New Job
            </a>
          </div>

          <?php if (!empty($jobs)): ?>
            <table class="jobs-table">
              <thead>
                <tr>
                  <th>Job Title</th>
                  <th>Category</th>
                  <th>Salary</th>
                  <th>Applicants</th>
                  <th>Status</th>
                  <th>Posted</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($jobs as $job): ?>
                  <tr>
                    <td>
                      <div class="job-title"><?php echo htmlspecialchars($job['title']); ?></div>
                      <div class="job-company"><?php echo formatJobType($job['job_type']); ?> •
                        <?php echo htmlspecialchars($job['location']); ?>
                      </div>
                    </td>
                    <td><?php echo htmlspecialchars($job['category_name']); ?></td>
                    <td class="job-salary">
                      <?php echo formatSalary($job['salary_min'], $job['salary_max'], $job['salary_text']); ?>
                    </td>
                    <td>
                      <div class="applicant-count <?php echo getApplicantCountClass($job['applicant_count']); ?>">
                        <i class="fas fa-user"></i>
                        <?php echo $job['applicant_count']; ?> applicant<?php echo $job['applicant_count'] != 1 ? 's' : ''; ?>
                      </div>
                    </td>
                    <td>
                      <span
                        class="job-status status-<?php echo $job['is_active'] == 1 ? 'active' : ($job['is_active'] == 2 ? 'draft' : 'inactive'); ?>">
                        <?php echo $job['is_active'] == 1 ? 'Active' : ($job['is_active'] == 2 ? 'Draft' : 'Inactive'); ?>
                      </span>
                    </td>
                    <td>
                      <div class="date-posted"><?php echo timeAgo($job['created_at']); ?></div>
                    </td>
                    <td>
                      <div class="job-actions">
                        <button class="action-btn view" title="View Applications"
                          onclick="viewApplications(<?php echo $job['id']; ?>)">
                          <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn edit" title="Edit Job" onclick="editJob(<?php echo $job['id']; ?>)">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete" title="Delete Job" onclick="deleteJob(<?php echo $job['id']; ?>)">
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <div class="empty-state">
              <div style="font-size: 4rem; opacity: 0.3; margin-bottom: 1rem;">
                <i class="fas fa-briefcase"></i>
              </div>
              <h3>No Job Postings Yet</h3>
              <p>Start by posting your first job to attract talented candidates.</p>
              <a href="add-job.php" class="add-job-btn">
                <i class="fas fa-plus"></i>
                Post Your First Job
              </a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </main>
  <footer>
    <div class="logo">
      <img src="asset/logo.png" alt="SugoiJob Logo" width="36" />
      <span>SugoiJob © <span id="currentYear"></span>. All rights reserved.</span>
    </div>
    <ul>
      <li>
        <img src="asset/instagram.png" />
        <a href="https://www.instagram.com/kncrln_/" target="_blank">Kenzie</a>
      </li>
      <li>
        <img src="asset/instagram.png" />
        <a href="https://www.instagram.com/r.van83/" target="_blank">Ivan</a>
      </li>
      <li>
        <img src="asset/instagram.png" />
        <a href="https://www.instagram.com/ndusft/" target="_blank">Bernadus</a>
      </li>
    </ul>
  </footer>

  <?php if ($_SESSION['role'] == 'job_seeker'): ?>
    <script id="jobs-data-json" type="application/json">
      <?php echo $jobs_json; ?>
    </script>
    <script src="script/search.js"></script>
  <?php endif; ?>
  <script src="script/index.js"></script>
</body>

</html>