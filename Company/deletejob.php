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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <?php if (empty($job_id)): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: 'ID pekerjaan tidak valid!',
        confirmButtonColor: '#d33'
      }).then(() => {
        window.location.href = '/';
      });
    </script>
  <?php else: ?>
    <script>
      <?php if ($result): ?>
        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: 'Pekerjaan berhasil dihapus!',
          confirmButtonColor: '#28a745'
        }).then(() => {
          window.location.href = '/';
        });
      <?php else: ?>
        Swal.fire({
          icon: 'error',
          title: 'Gagal!',
          text: 'Gagal menghapus pekerjaan: <?php echo mysqli_error($koneksi); ?>',
          confirmButtonColor: '#d33'
        }).then(() => {
          window.location.href = '/';
        });
      <?php endif; ?>
    </script>
  <?php endif; ?>
</body>

</html>