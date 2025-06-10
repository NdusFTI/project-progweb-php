<?php
require "../koneksi.php";

$job_id = $_GET['id'] ?? null;
$applicant_count = 0;
$has_applicants = false;

if (!empty($job_id)) {
  $check_sql = "SELECT COUNT(*) as count FROM job_applications WHERE job_id = ?";
  $check_stmt = mysqli_prepare($koneksi, $check_sql);
  mysqli_stmt_bind_param($check_stmt, "i", $job_id);
  mysqli_stmt_execute($check_stmt);
  $check_result = mysqli_stmt_get_result($check_stmt);
  $row = mysqli_fetch_assoc($check_result);
  $applicant_count = $row['count'] ?? 0;
  $has_applicants = $applicant_count > 0;

  if (!$has_applicants) {
    $sql = "DELETE FROM job_postings WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "i", $job_id);
    $result = mysqli_stmt_execute($stmt);
  } else {
    echo "<script>
      alert('Tidak dapat menghapus! Masih ada pelamar pada pekerjaan ini ($applicant_count pelamar).');
      window.location.href = '/';
    </script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SugoiJob - Delete Jobs</title>
  <link rel="stylesheet" href="../style/jobs.css">
</head>

<body>
  <?php if (empty($job_id)): ?>
    <script>
      alert('Error! ID pekerjaan tidak valid!');
      window.location.href = '/';
    </script>
  <?php elseif ($has_applicants): ?>
    <script>
      alert('Tidak dapat menghapus! Masih ada pelamar pada pekerjaan ini (<?php echo $applicant_count; ?> pelamar).');
      window.location.href = '/';
    </script>
  <?php else: ?>
    <script>
      <?php if (isset($result) && $result): ?>
        alert('Berhasil! Pekerjaan berhasil dihapus!');
        window.location.href = '/';
      <?php else: ?>
        alert('Gagal! Gagal menghapus pekerjaan: <?php echo mysqli_error($koneksi); ?>');
        window.location.href = '/';
      <?php endif; ?>
    </script>
  <?php endif; ?>
</body>

</html>