<?php

function timeAgo($datetime)
{
  $time = time() - strtotime($datetime);

  if ($time < 60)
    return 'Just now';
  if ($time < 3600)
    return floor($time / 60) . ' minutes ago';
  if ($time < 86400)
    return floor($time / 3600) . ' hours ago';
  if ($time < 2592000)
    return floor($time / 86400) . ' days ago';

  return date('M j, Y', strtotime($datetime));
}

function getApplicantCountClass($count)
{
  if ($count >= 20)
    return 'high';
  if ($count >= 10)
    return 'medium';
  return '';
}

// Detail
function getDetailJobs($koneksi, $id)
{
  $query = "SELECT 
    jp.*,
    c.*,
    jc.name as category_name,
    (SELECT COUNT(*) FROM job_applications ja WHERE ja.job_id = jp.id) as applicant_count
  FROM job_postings jp
  INNER JOIN companies c ON jp.company_id = c.id 
  INNER JOIN job_categories jc ON jp.category_id = jc.id 
  WHERE jp.id = ?;";

  $stmt = mysqli_prepare($koneksi, $query);
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  return $result;
}

// User
function getCompanyByUserId($koneksi, $user_id)
{
  $sql = "SELECT * FROM companies WHERE user_id = ?";
  $stmt = $koneksi->prepare($sql);
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  return $result->fetch_assoc();
}

function getJobsWithFilters($koneksi, $keyword = '', $location = '', $job_type = '', $company = '', $date_posted = '', $salary_min = '', $salary_max = '')
{
  $sql = "SELECT 
      jp.id,
      jp.title,
      jp.salary_min,
      jp.salary_max,
      jp.salary_text,
      jp.location,
      jp.job_type,
      jp.description,
      jp.experience_required,
      jp.application_deadline,
      jp.views_count,
      c.company_name,
      c.company_logo,
      c.company_banner,
      c.city,
      c.company_description,
      jcat.name as category_name,
      jp.is_active,
      jp.created_at
    FROM job_postings jp
    JOIN companies c ON jp.company_id = c.id
    JOIN job_categories jcat ON jp.category_id = jcat.id
    WHERE jp.is_active = 1";

  $params = [];
  $types = '';

  // Search berdasarkan kata koentji
  if (!empty($keyword)) {
    $sql .= " AND (jp.title LIKE ? OR c.company_name LIKE ? OR jcat.name LIKE ? OR jp.description LIKE ?)";
    $keyword_param = '%' . $keyword . '%';
    $params = array_merge($params, [$keyword_param, $keyword_param, $keyword_param, $keyword_param]);
    $types .= 'ssss';
  }

  // Filter berdasarkan lokasi
  if (!empty($location)) {
    $sql .= " AND jp.location LIKE ?";
    $params[] = '%' . $location . '%';
    $types .= 's';
  }

  // Filter berdasarkan tipe pekerjaan
  if (!empty($job_type)) {
    $sql .= " AND jp.job_type LIKE ?";
    $params[] = '%' . $job_type . '%';
    $types .= 's';
  }

  // Filter berdasarkan nama perusahaan
  if (!empty($company)) {
    $sql .= " AND c.company_name LIKE ?";
    $params[] = '%' . $company . '%';
    $types .= 's';
  }

  // Filter berdasarkan tanggal
  if (!empty($date_posted)) {
    $sql .= " AND DATE(jp.created_at) >= ?";
    $params[] = $date_posted;
    $types .= 's';
  }

  // Filter berdasarkan gaji minimum
  if (!empty($salary_min)) {
    $sql .= " AND (jp.salary_min >= ? OR jp.salary_max >= ?)";
    $params[] = intval($salary_min);
    $params[] = intval($salary_min);
    $types .= 'ii';
  }

  // Filter berdasarkan gaji maksimum
  if (!empty($salary_max)) {
    $sql .= " AND (jp.salary_min <= ? OR jp.salary_max <= ?)";
    $params[] = intval($salary_max);
    $params[] = intval($salary_max);
    $types .= 'ii';
  }

  $sql .= " ORDER BY jp.created_at DESC";

  $stmt = $koneksi->prepare($sql);

  if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
  }

  $stmt->execute();
  $result = $stmt->get_result();
  return $result->fetch_all(MYSQLI_ASSOC);
}

function formatSalary($salary_min, $salary_max, $salary_text)
{
  if (!empty($salary_text)) {
    return $salary_text;
  } elseif ($salary_min && $salary_max) {
    return "Rp " . number_format($salary_min, 0, ',', '.') . " - Rp " . number_format($salary_max, 0, ',', '.') . " per bulan";
  } elseif ($salary_min) {
    return "Mulai dari Rp " . number_format($salary_min, 0, ',', '.');
  } else {
    return "Gaji dapat dinegosiasi";
  }
}

function formatJobType($job_type)
{
  $types = explode(',', $job_type);
  $formatted_types = array_map('trim', $types);

  if (count($formatted_types) > 1) {
    $first_type = $formatted_types[0];
    $other_count = count($formatted_types) - 1;
    return $first_type . ' <span class="other">+' . $other_count . '</span>';
  } else {
    return $formatted_types[0];
  }
}

function isPriority($created_at)
{
  $created_date = new DateTime($created_at);
  $now = new DateTime();
  $diff = $now->diff($created_date);
  return $diff->days <= 3;
}

function getAllCompanyName($koneksi)
{
  $sql = "SELECT DISTINCT company_name FROM companies 
    ORDER BY company_name ASC";
  $stmt = $koneksi->prepare($sql);
  $stmt->execute();
  $result = $stmt->get_result();
  return $result->fetch_all(MYSQLI_ASSOC);
}

function getAllJobLocation($koneksi)
{
  $sql = "SELECT DISTINCT location FROM job_postings 
    WHERE location IS NOT NULL AND location != '' 
    ORDER BY location ASC";
  $stmt = $koneksi->prepare($sql);
  $stmt->execute();
  $result = $stmt->get_result();
  return $result->fetch_all(MYSQLI_ASSOC);
}

function getAllJobTypes($koneksi)
{
  $sql = "SELECT DISTINCT job_type FROM job_postings 
    WHERE job_type IS NOT NULL AND job_type != '' 
    ORDER BY job_type ASC";
  $stmt = $koneksi->prepare($sql);
  $stmt->execute();
  $result = $stmt->get_result();
  $job_types = $result->fetch_all(MYSQLI_ASSOC);
  return $job_types;
}

function getSalaryRanges()
{
  return [
    ['value' => '', 'label' => 'Semua Gaji'],
    ['min' => 0, 'max' => 5000000, 'label' => 'Di bawah 5 Juta'],
    ['min' => 5000000, 'max' => 10000000, 'label' => '5 - 10 Juta'],
    ['min' => 10000000, 'max' => 15000000, 'label' => '10 - 15 Juta'],
    ['min' => 15000000, 'max' => 20000000, 'label' => '15 - 20 Juta'],
    ['min' => 20000000, 'max' => '', 'label' => 'Di atas 20 Juta']
  ];
}

// Company Functions

function getJobsByCompanyId($koneksi, $company_id)
{
  $sql = 'SELECT 
      jp.id,
      jp.title,
      jp.salary_min,
      jp.salary_max,
      jp.salary_text,
      jp.location,
      jp.job_type,
      jp.description,
      jp.experience_required,
      jp.application_deadline,
      jp.views_count,
      c.company_name,
      c.company_logo,
      c.company_banner,
      c.city,
      c.company_description,
      jcat.name as category_name,
      jp.is_active,
      jp.created_at,
      "active" as status,
      (SELECT COUNT(*) FROM job_applications ja WHERE ja.job_id = jp.id) as applicant_count
    FROM job_postings jp
    JOIN companies c ON jp.company_id = c.id
    JOIN job_categories jcat ON jp.category_id = jcat.id
    WHERE jp.company_id = ?
    ORDER BY jp.created_at DESC';

  $stmt = $koneksi->prepare($sql);
  $stmt->bind_param("i", $company_id);
  $stmt->execute();
  $result = $stmt->get_result();

  return $result->fetch_all(MYSQLI_ASSOC);
}

function getTotalJobsByCompanyId($koneksi, $company_id)
{
  $sql = "SELECT COUNT(*) as total FROM job_postings 
    WHERE company_id = ?";
  $stmt = $koneksi->prepare($sql);
  $stmt->bind_param("i", $company_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  return $row['total'];
}

function getRecentJobsCountById($koneksi, $company_id, $days = 7)
{
  $sql = "SELECT COUNT(*) as total FROM job_postings 
    WHERE company_id = ? AND created_at >= NOW() - INTERVAL ? DAY";
  $stmt = $koneksi->prepare($sql);
  $stmt->bind_param("ii", $company_id, $days);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  return $row['total'];
}

function getActiveJobsCountById($koneksi, $company_id)
{
  $sql = 'SELECT COUNT(*) as total FROM job_postings WHERE company_id = ? AND is_active = 1';
  $stmt = $koneksi->prepare($sql);
  $stmt->bind_param("i", $company_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  return $row['total'];
}

function getTotalApplicantsByCompanyId($koneksi, $company_id)
{
  $sql = "SELECT COUNT(DISTINCT ja.applicant_id) as total FROM job_applications ja
    JOIN job_postings jp ON ja.job_id = jp.id
    WHERE jp.company_id = ? AND jp.is_active = 1";
  $stmt = $koneksi->prepare($sql);
  $stmt->bind_param("i", $company_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  return $row['total'];
}

function getTotalViewsByCompanyId($koneksi, $company_id)
{
  $sql = "SELECT SUM(views_count) as total FROM job_postings 
    WHERE company_id = ? AND is_active = 1";
  $stmt = $koneksi->prepare($sql);
  $stmt->bind_param("i", $company_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  return $row['total'];
}

// Validation
function validateFile($file, $fileName, $isRequired = true)
{
  if (!$isRequired && (!isset($file) || $file === null || $file["error"] === UPLOAD_ERR_NO_FILE)) {
    return true;
  }

  if ($isRequired && (!isset($file) || $file === null || $file["error"] === UPLOAD_ERR_NO_FILE)) {
    return "File $fileName wajib diunggah.";
  }

  if ($file["error"] !== UPLOAD_ERR_OK) {
    return "Gagal mengunggah $fileName. Silakan coba lagi.";
  }

  if ($file["size"] > 5 * 1024 * 1024) {
    return "Ukuran $fileName terlalu besar. Maksimal 5MB.";
  }

  $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
  if (!in_array($file["type"], $allowedTypes)) {
    return "$fileName harus berformat PDF atau Word.";
  }

  return true;
}
?>