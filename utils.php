<?php
function getJobsWithFilters($koneksi, $keyword = '', $location = '', $job_type = '', $company = '', $date_posted = '') {
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
    $sql = "SELECT DISTINCT company_name FROM companies ORDER BY company_name ASC";
    $stmt = $koneksi->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAllJobLocation($koneksi)
{
    $sql = "SELECT DISTINCT location FROM job_postings WHERE location IS NOT NULL AND location != '' ORDER BY location ASC";
    $stmt = $koneksi->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAllJobTypes($koneksi)
{
    $sql = "SELECT DISTINCT job_type FROM job_postings WHERE job_type IS NOT NULL AND job_type != '' ORDER BY job_type ASC";
    $stmt = $koneksi->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $job_types = $result->fetch_all(MYSQLI_ASSOC);
    return $job_types;
}

function getJobCategories($koneksi)
{
    $sql = "SELECT id, name FROM job_categories ORDER BY name ASC";
    $stmt = $koneksi->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function highlightSearchTerm($text, $searchTerm) {
    if (empty($searchTerm)) {
        return htmlspecialchars($text);
    }
    
    $highlighted = preg_replace('/(' . preg_quote($searchTerm, '/') . ')/i', '<mark>$1</mark>', htmlspecialchars($text));
    return $highlighted;
}

function getSearchStats($totalJobs, $keyword = '', $location = '', $job_type = '', $company = '') {
    $filters = [];
    
    if (!empty($keyword)) $filters[] = "kata kunci: \"$keyword\"";
    if (!empty($location)) $filters[] = "lokasi: \"$location\"";
    if (!empty($job_type)) $filters[] = "tipe: \"$job_type\"";
    if (!empty($company)) $filters[] = "perusahaan: \"$company\"";
    
    $filterText = !empty($filters) ? ' dengan filter ' . implode(', ', $filters) : '';
    
    return "Ditemukan $totalJobs lowongan kerja$filterText";
}
?>