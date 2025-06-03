<?php
require 'koneksi.php';
include 'utils.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: Auth/login.php");
    exit();
}

$keyword = mysqli_real_escape_string($koneksi, $_GET['keyword'] ?? '');
$location = mysqli_real_escape_string($koneksi, $_GET['location'] ?? '');
$date_posted = $_GET['date_posted'] ?? '';
$job_type = mysqli_real_escape_string($koneksi, $_GET['job_type'] ?? '');
$company = mysqli_real_escape_string($koneksi, $_GET['company'] ?? '');
$location_filter = mysqli_real_escape_string($koneksi, $_GET['location_filter'] ?? '');
$gaji_min = (int)($_GET['gaji_min'] ?? 0);
$gaji_max = (int)($_GET['gaji_max'] ?? 100000000);

$sql = "SELECT 
    jp.id,
    jp.title,
    jp.salary_min,
    jp.salary_max,
    jp.salary_text,
    jp.location,
    jp.job_type,
    jp.description,
    jp.requirements,
    jp.experience_required,
    jp.application_deadline,
    jp.created_at,
    c.company_name,
    c.company_logo,
    c.company_banner,
    c.city,
    jcat.name as category_name
FROM job_postings jp
JOIN companies c ON jp.company_id = c.id
JOIN job_categories jcat ON jp.category_id = jcat.id
WHERE jp.is_active = 1";

$conditions = [];
$params = [];

if (!empty($keyword)) {
    $conditions[] = "(jp.title LIKE ? OR c.company_name LIKE ? OR jcat.name LIKE ? OR jp.location LIKE ? OR jp.job_type LIKE ?)";
    $keyword_param = "%$keyword%";
    $params = array_merge($params, [$keyword_param, $keyword_param, $keyword_param, $keyword_param, $keyword_param]);
}

if (!empty($location)) {
    $conditions[] = "jp.location LIKE ?";
    $params[] = "%$location%";
}

if (!empty($job_type)) {
    $conditions[] = "jp.job_type LIKE ?";
    $params[] = "%$job_type%";
}

if (!empty($company)) {
    $conditions[] = "c.company_name = ?";
    $params[] = $company;
}

if (!empty($location_filter)) {
    $conditions[] = "jp.location = ?";
    $params[] = $location_filter;
}

if (!empty($date_posted)) {
    $conditions[] = "DATE(jp.created_at) >= ?";
    $params[] = $date_posted;
}

if ($gaji_min > 0) {
    $conditions[] = "(jp.salary_max >= ? OR jp.salary_min >= ?)";
    $params[] = $gaji_min;
    $params[] = $gaji_min;
}

if ($gaji_max < 100000000) {
    $conditions[] = "(jp.salary_min <= ? OR jp.salary_max <= ?)";
    $params[] = $gaji_max;
    $params[] = $gaji_max;
}

if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY jp.created_at DESC";

$stmt = $koneksi->prepare($sql);

if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$jobs = $result->fetch_all(MYSQLI_ASSOC);

$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

$listJobType = getAllJobTypes($koneksi);
$listJobLocation = getAllJobLocation($koneksi);
$listJobCompany = getAllCompanyName($koneksi);

$jobs_json = json_encode($jobs);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SugoiJob - Search Results</title>
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
                    <li><a href="index.php" class="navbar-link">Home</a></li>
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
                <form action="search.php" method="get">
                    <div class="input-wrap">
                        <input type="text" name="keyword" id="job-title" placeholder="Judul pekerjaan, kata kunci, atau perusahaan" value="<?php echo htmlspecialchars($keyword); ?>" />
                        <input type="text" name="location" id="location" placeholder="Kota, negara bagian, kode pos, atau 'remote'" value="<?php echo htmlspecialchars($location); ?>" />
                        <button type="submit">Cari</button>
                    </div>
                </form>
            </div>
            <div class="filter-wrap">
                <div class="filter-group">
                    <input type="date" id="date-posted" name="date_posted" value="<?php echo htmlspecialchars($date_posted); ?>">
                    <select id="job-type" class="custom-select" name="job_type">
                        <option value="">Job Type</option>
                        <?php foreach ($listJobType as $type): ?>
                            <option value="<?php echo htmlspecialchars($type['job_type']); ?>" <?php echo ($job_type == $type['job_type']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($type['job_type']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select id="company" class="custom-select" name="company">
                        <option value="">Company</option>
                        <?php foreach ($listJobCompany as $comp): ?>
                            <option value="<?php echo htmlspecialchars($comp['company_name']); ?>" <?php echo ($company == $comp['company_name']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($comp['company_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select id="location-filter" class="custom-select" name="location_filter">
                        <option value="">Location</option>
                        <?php foreach ($listJobLocation as $loc): ?>
                            <option value="<?php echo htmlspecialchars($loc['location']); ?>" <?php echo ($location_filter == $loc['location']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($loc['location']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="gaji_min" placeholder="Gaji Min" value="<?php echo $gaji_min > 0 ? $gaji_min : ''; ?>">
                    <input type="number" name="gaji_max" placeholder="Gaji Max" value="<?php echo $gaji_max < 100000000 ? $gaji_max : ''; ?>">
                    <button type="submit" form="search-form">Filter</button>
                </div>
            </div>
        </section>

        <section class="breadcrumb">
            <p><a href="index.php">Home</a> > <span class="active">Search Results</span></p>
        </section>

        <section class="job-list">
            <section class="job-wrap">
                <div class="scroll-content">
                    <?php if (!empty($keyword) || !empty($location)): ?>
                        <p class="info">
                            Hasil pencarian untuk: 
                            <?php if (!empty($keyword)): ?>
                                "<?php echo htmlspecialchars($keyword); ?>"
                            <?php endif; ?>
                            <?php if (!empty($location)): ?>
                                <?php echo !empty($keyword) ? ' di ' : ''; ?>"<?php echo htmlspecialchars($location); ?>"
                            <?php endif; ?>
                            (<?php echo count($jobs); ?> hasil ditemukan)
                        </p>
                    <?php else: ?>
                        <p class="info">Semua lowongan kerja (<?php echo count($jobs); ?> hasil ditemukan)</p>
                    <?php endif; ?>
                    
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
                            <div class="no-results">
                                <h3>Tidak ada hasil ditemukan</h3>
                                <p>Coba ubah kata kunci pencarian atau filter yang Anda gunakan.</p>
                                <a href="index.php" class="btn-back-home">Kembali ke Beranda</a>
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

    <script>
        const jobsData = <?php echo $jobs_json; ?>;
        
        document.getElementById('date-posted').setAttribute('form', 'search-form');
        document.getElementById('job-type').setAttribute('form', 'search-form');
        document.getElementById('company').setAttribute('form', 'search-form');
        document.getElementById('location-filter').setAttribute('form', 'search-form');
        document.querySelector('input[name="gaji_min"]').setAttribute('form', 'search-form');
        document.querySelector('input[name="gaji_max"]').setAttribute('form', 'search-form');
        document.querySelector('form[action="search.php"]').setAttribute('id', 'search-form');
        
        document.getElementById('currentYear').textContent = new Date().getFullYear();
    </script>
    <script src="Script/index.js"></script>
</body>

</html>