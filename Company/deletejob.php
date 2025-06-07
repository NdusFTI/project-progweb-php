<?php
require "../koneksi.php";

$job_id = $_GET['id'] ?? null;

if (!empty($job_id)) {
  $sql = "DELETE FROM job_postings WHERE id = ?";
  $stmt = mysqli_prepare($koneksi, $sql);
  mysqli_stmt_bind_param($stmt, "i", $job_id);
  $result = mysqli_stmt_execute($stmt);
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
  <?php else: ?>
    <script>
      <?php if ($result): ?>
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