<?php
$sql = "SELECT 
  jp.id,
  jp.title,
  jp.salary_min,
  jp.salary_max,
  jp.salary_text,
  jp.location,
  jp.job_type,
  jp.description,
  c.company_name,
  c.company_logo,
  c.company_banner,
  jcat.name as category_name,
  jp.is_active,
  jp.created_at
  FROM job_postings jp
  JOIN companies c ON jp.company_id = c.id
  JOIN job_categories jcat ON jp.category_id = jcat.id
  WHERE jp.is_active = 1
  ORDER BY jp.created_at DESC";

$stmt = $koneksi->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$jobs = $result->fetch_all(MYSQLI_ASSOC);

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
?>