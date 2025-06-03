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

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$job_type = isset($_GET['job_type']) ? trim($_GET['job_type']) : '';
$company = isset($_GET['company']) ? trim($_GET['company']) : '';
$date_posted = isset($_GET['date_posted']) ? trim($_GET['date_posted']) : '';
$salary_range = isset($_GET['salary_range']) ? trim($_GET['salary_range']) : '';

$salary_min = '';
$salary_max = '';
if (!empty($salary_range)) {
    $range_parts = explode('-', $salary_range);
    if (count($range_parts) == 2) {
        $salary_min = trim($range_parts[0]);
        $salary_max = trim($range_parts[1]);
        if ($salary_max === 'max') $salary_max = '';
    }
}

$jobs = getJobsWithFilters($koneksi, $keyword, $location, $job_type, $company, $date_posted, $salary_min, $salary_max);
$jobs_json = json_encode($jobs);

$listJobType = getAllJobTypes($koneksi);
$listJobLocation = getAllJobLocation($koneksi);
$listJobCompany = getAllCompanyName($koneksi);
$salaryRanges = getSalaryRanges();

$selectedSalaryLabel = '';
if (!empty($salary_range)) {
    foreach ($salaryRanges as $range) {
        if (isset($range['min']) && isset($range['max'])) {
            $rangeValue = $range['min'] . '-' . ($range['max'] ?: 'max');
            if ($rangeValue === $salary_range) {
                $selectedSalaryLabel = $range['label'];
                break;
            }
        }
    }
}

$searchStats = getSearchStats(count($jobs), $keyword, $location, $job_type, $company);
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
                <span class="navbar-username"><?php echo htmlspecialchars($username); ?></span>
                <a href="Auth/logout.php" class="navbar-signout">Sign Out</a>
            </div>
        </nav>
    </header>
    
    <main>
        <section class="search-filter">
            <div class="search-wrap">
                <form action="" method="get" id="search-form">
                    <div class="input-wrap">
                        <input type="text" 
                               name="keyword" 
                               id="job-title" 
                               placeholder="Judul pekerjaan, kata kunci, atau perusahaan" 
                               value="<?php echo htmlspecialchars($keyword); ?>" />
                        <input type="text" 
                               name="location" 
                               id="location" 
                               placeholder="Kota, negara bagian, kode pos, atau 'remote'" 
                               value="<?php echo htmlspecialchars($location); ?>" />
                        <button type="submit">Cari</button>
                    </div>
                </form>
            </div>
            
            <div class="filter-wrap">
                <form action="" method="get" id="filter-form">
                    <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">
                    <input type="hidden" name="location" value="<?php echo htmlspecialchars($location); ?>">
                    
                    <div class="filter-group">
                        <input type="date" 
                               id="date-posted" 
                               name="date_posted" 
                               value="<?php echo htmlspecialchars($date_posted); ?>"
                               onchange="applyFilters()">
                        
                        <select id="job-type" 
                                name="job_type" 
                                class="custom-select <?php echo !empty($job_type) ? 'filter-active' : ''; ?>"
                                onchange="applyFilters()">
                            <option value="">Job Type</option>
                            <?php foreach ($listJobType as $type): ?>
                                <option value="<?php echo htmlspecialchars($type['job_type']); ?>"
                                        <?php echo ($job_type === $type['job_type']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($type['job_type']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <select id="company" 
                                name="company" 
                                class="custom-select <?php echo !empty($company) ? 'filter-active' : ''; ?>"
                                onchange="applyFilters()">
                            <option value="">Company</option>
                            <?php foreach ($listJobCompany as $comp): ?>
                                <option value="<?php echo htmlspecialchars($comp['company_name']); ?>"
                                        <?php echo ($company === $comp['company_name']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($comp['company_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <select id="salary-range" 
                                name="salary_range" 
                                class="custom-select <?php echo !empty($salary_range) ? 'filter-active' : ''; ?>"
                                onchange="applyFilters()">
                            <option value="">Rentang Gaji</option>
                            <?php foreach ($salaryRanges as $range): ?>
                                <?php if (isset($range['min']) && isset($range['max'])): ?>
                                    <?php 
                                    $rangeValue = $range['min'] . '-' . ($range['max'] ?: 'max');
                                    ?>
                                    <option value="<?php echo $rangeValue; ?>"
                                            <?php echo ($salary_range === $rangeValue) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($range['label']); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        
                        <select id="location-filter" 
                                name="location_filter" 
                                class="custom-select"
                                onchange="applyLocationFilter()">
                            <option value="">Location</option>
                            <?php foreach ($listJobLocation as $loc): ?>
                                <option value="<?php echo htmlspecialchars($loc['location']); ?>">
                                    <?php echo htmlspecialchars($loc['location']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
                
                <?php if (!empty($keyword) || !empty($location) || !empty($job_type) || !empty($company) || !empty($date_posted)): ?>
                    <a href="index.php" class="clear-filters">Clear All Filters</a>
                <?php endif; ?>
            </div>
        </section>

        <section class="breadcrumb">
            <p><a href="index.php" class="active">Home</a></p>
        </section>

        <div class="search-stats">
            <?php echo $searchStats; ?>
        </div>

        <section class="job-list">
            <section class="job-wrap">
                <div class="scroll-content">
                    <?php if (!empty($jobs)): ?>
                        <p class="info">Yang tersedia saat ini.</p>
                        <?php foreach ($jobs as $index => $job): ?>
                            <div class="job-item" data-job-index="<?php echo $index; ?>" onclick="showJobDetail(<?php echo $index; ?>)">
                                <?php if (isPriority($job['created_at'])): ?>
                                    <div class="priority">
                                        <p>Dicari!</p>
                                    </div>
                                <?php endif; ?>

                                <div class="title">
                                    <h2><?php echo highlightSearchTerm($job['title'], $keyword); ?></h2>
                                </div>

                                <div class="company">
                                    <p>
                                        <?php echo highlightSearchTerm($job['company_name'], $keyword); ?> <br />
                                        <?php echo highlightSearchTerm($job['location'], $location); ?>
                                    </p>
                                </div>

                                <div class="info">
                                    <p><?php echo formatSalary($job['salary_min'], $job['salary_max'], $job['salary_text']); ?></p>
                                    <p><?php echo formatJobType($job['job_type']); ?></p>
                                    <p><?php echo highlightSearchTerm($job['category_name'], $keyword); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-results">
                            <h3>Tidak ada lowongan kerja yang ditemukan</h3>
                            <p>Coba ubah kata kunci pencarian atau filter Anda</p>
                            
                            <div class="search-suggestions">
                                <strong>Saran pencarian:</strong>
                                <ul>
                                    <li>• Periksa ejaan kata kunci</li>
                                    <li>• Gunakan kata kunci yang lebih umum</li>
                                    <li>• Coba hapus beberapa filter</li>
                                    <li>• Cari berdasarkan kategori pekerjaan</li>
                                </ul>
                            </div>
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

    <script id="jobs-data-json" type="application/json">
      <?php echo $jobs_json; ?>
    </script>
    <script src="Script/search.js"></script>
    <script src="Script/index.js"></script>
</body>
</html>