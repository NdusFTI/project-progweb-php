<?php
session_start();

// // Check if user is logged in
// if (!isset($_SESSION['user_id'])) {
//     header("Location: Auth/login.php");
//     exit();
// }

$username = $_SESSION['username'] ?? 'Employer';
$user_id = $_SESSION['user_id'];
$company_name = $_SESSION['company_name'] ?? 'Your Company';

$jobs = [
    // [
    //     'id' => 1,
    //     'title' => 'Senior Web Developer',
    //     'category_name' => 'Technology',
    //     'job_type' => 'full_time',
    //     'location' => 'Jakarta, Indonesia',
    //     'salary_min' => 8000000,
    //     'salary_max' => 12000000,
    //     'salary_text' => '',
    //     'applicant_count' => 15,
    //     'status' => 'active',
    //     'created_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
    //     'views' => 245
    // ],
    // [
    //     'id' => 2,
    //     'title' => 'Marketing Manager',
    //     'category_name' => 'Marketing',
    //     'job_type' => 'full_time',
    //     'location' => 'Surabaya, Indonesia',
    //     'salary_min' => 6000000,
    //     'salary_max' => 9000000,
    //     'salary_text' => '',
    //     'applicant_count' => 8,
    //     'status' => 'active',
    //     'created_at' => date('Y-m-d H:i:s', strtotime('-12 days')),
    //     'views' => 189
    // ],
    // [
    //     'id' => 3,
    //     'title' => 'Data Analyst',
    //     'category_name' => 'Technology',
    //     'job_type' => 'contract',
    //     'location' => 'Bandung, Indonesia',
    //     'salary_min' => 5000000,
    //     'salary_max' => 7000000,
    //     'salary_text' => '',
    //     'applicant_count' => 22,
    //     'status' => 'active',
    //     'created_at' => date('Y-m-d H:i:s', strtotime('-20 days')),
    //     'views' => 312
    // ],
    // [
    //     'id' => 4,
    //     'title' => 'UI/UX Designer',
    //     'category_name' => 'Design',
    //     'job_type' => 'part_time',
    //     'location' => 'Yogyakarta, Indonesia',
    //     'salary_min' => 4000000,
    //     'salary_max' => 6000000,
    //     'salary_text' => '',
    //     'applicant_count' => 5,
    //     'status' => 'inactive',
    //     'created_at' => date('Y-m-d H:i:s', strtotime('-35 days')),
    //     'views' => 98
    // ]
];

// Calculate statistics
$total_jobs = count($jobs);
$active_jobs = count(array_filter($jobs, function($job) {
    return $job['status'] === 'active';
}));
$total_applicants = array_sum(array_column($jobs, 'applicant_count'));
$total_views = array_sum(array_column($jobs, 'views'));
$recent_jobs = count(array_filter($jobs, function($job) {
    return strtotime($job['created_at']) > strtotime('-30 days');
}));

// Helper functions
function formatSalary($min, $max, $text) {
    if (!empty($text)) {
        return htmlspecialchars($text);
    }
    if ($min && $max) {
        return 'Rp ' . number_format($min, 0, ',', '.') . ' - Rp ' . number_format($max, 0, ',', '.');
    }
    if ($min) {
        return 'Rp ' . number_format($min, 0, ',', '.') . '+';
    }
    return 'Salary not specified';
}

function formatJobType($type) {
    $types = [
        'full_time' => 'Full Time',
        'part_time' => 'Part Time',
        'contract' => 'Contract',
        'freelance' => 'Freelance',
        'internship' => 'Internship'
    ];
    return $types[$type] ?? ucfirst($type);
}

function getApplicantCountClass($count) {
    if ($count >= 20) return 'high';
    if ($count >= 10) return 'medium';
    return '';
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'Just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    
    return date('M j, Y', strtotime($datetime));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SugoiJob - Employer Dashboard</title>
    <link rel="stylesheet" href="Style/dashboard_employer.css">
    <!-- Add Font Awesome for icons -->
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
                    <li><a href="employer-dashboard.php" class="navbar-link active">Dashboard</a></li>
                    <li><a href="manage-jobs.php" class="navbar-link">Manage Jobs</a></li>
                    <li><a href="applications.php" class="navbar-link">Applications</a></li>
                </ul>
            </div>
            <div class="navbar-user">
                <span class="navbar-welcome">Welcome,</span>
                <span class="navbar-username"><?php echo htmlspecialchars($username); ?></span>
                <a href="Auth/logout.php" class="navbar-signout">Sign Out</a>
            </div>
        </nav>
    </header>

    <div class="dashboard-header">
        <h1>Employer Dashboard</h1>
        <p>Manage your job postings and track applications for <?php echo htmlspecialchars($company_name); ?></p>
    </div>

    <main>
        <section class="breadcrumb">
            <p><a href="employer-dashboard.php" class="active">Dashboard</a></p>
        </section>

        <div class="dashboard-content">
            <!-- Statistics Cards -->
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

            <!-- Jobs List -->
            <div class="jobs-section">
                <div class="section-header">
                    <h2><i class="fas fa-list"></i> Your Job Postings</h2>
                    <a href="add-job.php" class="add-job-btn">
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
                                        <div class="job-company"><?php echo formatJobType($job['job_type']); ?> • <?php echo htmlspecialchars($job['location']); ?></div>
                                    </td>
                                    <td><?php echo htmlspecialchars($job['category_name']); ?></td>
                                    <td><?php echo formatSalary($job['salary_min'], $job['salary_max'], $job['salary_text']); ?></td>
                                    <td>
                                        <div class="applicant-count <?php echo getApplicantCountClass($job['applicant_count']); ?>">
                                            <i class="fas fa-user"></i>
                                            <?php echo $job['applicant_count']; ?> applicant<?php echo $job['applicant_count'] != 1 ? 's' : ''; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="job-status status-<?php echo $job['status']; ?>">
                                            <?php echo ucfirst($job['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="date-posted"><?php echo timeAgo($job['created_at']); ?></div>
                                    </td>
                                    <td>
                                        <div class="job-actions">
                                            <button class="action-btn view" title="View Applications" onclick="viewApplications(<?php echo $job['id']; ?>)">
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
    </main>

    <footer>
        <div class="logo">
            <img src="Asset/logo.png" alt="SugoiJob Logo" width="36" />
            <span>SugoiJob © <span id="currentYear"></span>. All rights reserved.</span>
        </div>
        <ul>
            <li>
                <img src="Asset/instagram.png" width="16" height="16" />
                <a href="https://www.instagram.com/kncrln_/" target="_blank">Kenzie</a>
            </li>
            <li>
                <img src="Asset/instagram.png" width="16" height="16" />
                <a href="https://www.instagram.com/r.van83/" target="_blank">Ivan</a>
            </li>
            <li>
                <img src="Asset/instagram.png" width="16" height="16" />
                <a href="https://www.instagram.com/ndusft/" target="_blank">Bernadus</a>
            </li>
        </ul>
    </footer>

    <script>
        // Set current year in footer
        document.getElementById('currentYear').textContent = new Date().getFullYear();

        // Action button functions
        function viewApplications(jobId) {
            alert('View applications for job ID: ' + jobId + '\n\nThis would redirect to applications page.');
            // window.location.href = 'view-applications.php?job_id=' + jobId;
        }

        function editJob(jobId) {
            alert('Edit job ID: ' + jobId + '\n\nThis would redirect to edit job page.');
            // window.location.href = 'edit-job.php?id=' + jobId;
        }

        function deleteJob(jobId) {
            if (confirm('Are you sure you want to delete this job posting?\n\nThis action cannot be undone.')) {
                alert('Delete job ID: ' + jobId + '\n\nThis would send delete request to server.');
                // Add AJAX call to delete job
                // location.reload(); // Refresh page after deletion
            }
        }
    </script>
</body>

</html>