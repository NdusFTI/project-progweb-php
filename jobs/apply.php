<?php
require "../koneksi.php";
require "../utils.php";
session_start();

if (!isset($_SESSION["user_id"])) {
  header("Location: auth/login.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $job_id = $_POST["job_id"];
  $namaLengkap = $_POST["nama-lengkap"];
  $tanggalLahir = $_POST["tanggal-lahir"];
  $email = $_POST["email"];
  $nomorHp = $_POST["nomor-hp"];
  $cv = $_FILES["cv"];
  $portofolio = $_FILES["portofolio"] ?? null;
  $suratLamaran = $_FILES["surat-lamaran"] ?? null;
  $user_id = $_SESSION["user_id"];

  $files_to_validate = [
    'CV' => $cv,
    'Portofolio' => $portofolio,
    'Surat Lamaran' => $suratLamaran
  ];

  $validationErrors = [];

  $cvValidation = validateFile($cv, 'CV', true);
  if ($cvValidation !== true) {
    $validationErrors[] = $cvValidation;
  }

  $portofolioValidation = validateFile($portofolio, 'Portofolio', false);
  if ($portofolioValidation !== true) {
    $validationErrors[] = $portofolioValidation;
  }

  $suratLamaranValidation = validateFile($suratLamaran, 'Surat Lamaran', false);
  if ($suratLamaranValidation !== true) {
    $validationErrors[] = $suratLamaranValidation;
  }

  if (!empty($validationErrors)) {
    $errorMessage = implode('\\n', $validationErrors);
    echo "<script>alert('$errorMessage'); window.location.href = 'apply.php?id=$job_id';</script>";
    exit();
  }

  $uploadDir = 'uploads/applications/';
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
  }

  $timestamp = time();

  $cvNewName = null;
  $portofolioNewName = null;
  $suratLamaranNewName = null;

  $uploadMessages = [];

  // Upload CV (required)
  if ($cv && $cv['error'] === UPLOAD_ERR_OK) {
    $cvNewName = $user_id . '_' . $job_id . '_cv_' . $timestamp . '.' . pathinfo($cv['name'], PATHINFO_EXTENSION);
    $cvPath = $uploadDir . $cvNewName;
    if (move_uploaded_file($cv['tmp_name'], $cvPath)) {
      $uploadMessages[] = 'CV berhasil diunggah';
    } else {
      echo "<script>alert('Gagal mengunggah CV.'); window.location.href = 'apply.php?id=$job_id';</script>";
      exit();
    }
  } else {
    echo "<script>alert('CV wajib diunggah.'); window.location.href = 'apply.php?id=$job_id';</script>";
    exit();
  }

  // Upload Portfolio (optional)
  if ($portofolio && $portofolio['error'] === UPLOAD_ERR_OK) {
    $portofolioNewName = $user_id . '_' . $job_id . '_portofolio_' . $timestamp . '.' . pathinfo($portofolio['name'], PATHINFO_EXTENSION);
    $portofolioPath = $uploadDir . $portofolioNewName;
    if (move_uploaded_file($portofolio['tmp_name'], $portofolioPath)) {
      $uploadMessages[] = 'Portofolio berhasil diunggah';
    }
  }

  // Upload Cover Letter (optional)
  if ($suratLamaran && $suratLamaran['error'] === UPLOAD_ERR_OK) {
    $suratLamaranNewName = $user_id . '_' . $job_id . '_surat_' . $timestamp . '.' . pathinfo($suratLamaran['name'], PATHINFO_EXTENSION);
    $suratLamaranPath = $uploadDir . $suratLamaranNewName;
    if (move_uploaded_file($suratLamaran['tmp_name'], $suratLamaranPath)) {
      $uploadMessages[] = 'Surat Lamaran berhasil diunggah';
    }
  }

  // Insert application data
  $insertQuery = "INSERT INTO job_applications (
    job_id, 
    applicant_id, 
    full_name, 
    birth_date, 
    email, 
    phone, 
    cv_file, 
    portfolio_file, 
    cover_letter_file, 
    status, 
    applied_at
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";

  $insertStmt = mysqli_prepare($koneksi, $insertQuery);

  mysqli_stmt_bind_param(
    $insertStmt,
    "iisssssss",
    $job_id,
    $user_id,
    $namaLengkap,
    $tanggalLahir,
    $email,
    $nomorHp,
    $cvNewName,
    $portofolioNewName,
    $suratLamaranNewName
  );

  if (mysqli_stmt_execute($insertStmt)) {
    if (mysqli_stmt_affected_rows($insertStmt) > 0) {
      $allMessages = implode(', ', $uploadMessages);
      $successMessage = $allMessages . '. Lamaran berhasil dikirim!';
      echo "<script>alert('$successMessage'); window.location.href = 'detail.php?id=$job_id';</script>";
    } else {
      echo "<script>alert('Gagal mengirim lamaran. Silakan coba lagi.'); window.location.href = 'apply.php?id=$job_id';</script>";
    }
  } else {
    echo "<script>alert('Terjadi kesalahan database. Silakan coba lagi.'); window.location.href = 'apply.php?id=$job_id';</script>";
  }

  mysqli_stmt_close($insertStmt);
  exit();
}

$username = $_SESSION["username"];
$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"];

$firstName = explode(" ", $username)[0];

if (!isset($_GET["id"])) {
  header("Location: ../");
  exit();
}

$id = $_GET["id"];
$result = getDetailJobs($koneksi, $id);

$checkQuery = "SELECT id FROM job_applications WHERE job_id = ? AND applicant_id = ?";
$checkStmt = mysqli_prepare($koneksi, $checkQuery);
mysqli_stmt_bind_param($checkStmt, "ii", $id, $user_id);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);
if (mysqli_num_rows($checkResult) > 0) {
  echo "<script>alert('Anda sudah mengajukan lamaran untuk pekerjaan ini.'); window.location.href = 'detail.php?id=$id';</script>";
  exit();
}

if (mysqli_num_rows($result) > 0) {
  $job = mysqli_fetch_assoc($result);

  $updateQuery = "UPDATE job_postings SET views_count = views_count + 1 WHERE id = ?";
  $updateStmt = mysqli_prepare($koneksi, $updateQuery);
  mysqli_stmt_bind_param($updateStmt, "i", $id);
  mysqli_stmt_execute($updateStmt);
} else {
  header("Location: ../");
  exit();
}

$userQuery = "SELECT email FROM users WHERE id = ?";
$userStmt = mysqli_prepare($koneksi, $userQuery);
mysqli_stmt_bind_param($userStmt, "i", $user_id);
mysqli_stmt_execute($userStmt);
$userResult = mysqli_stmt_get_result($userStmt);
$userData = mysqli_fetch_assoc($userResult);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SugoiJob - Application Form</title>
  <link rel="stylesheet" href="../style/apply.css">
</head>

<body>
  <header>
    <nav class="navbar">
      <div class="navbar-left">
        <div class="navbar-logo">
          <img src="../asset/logo.png" alt="SugoiJob Logo" width="32" height="32">
          <h2 class="navbar-brand">SugoiJob</h2>
        </div>
        <ul class="navbar-links">
          <li><a href="index.php" class="navbar-link">Home</a></li>
        </ul>
      </div>
      <div class="navbar-user">
        <span class="navbar-welcome">Welcome,</span>
        <span class="navbar-username"><?php echo htmlspecialchars(
          $username
        ); ?></span>
        <a href="/auth/logout.php" class="navbar-btns">Sign Out</a>
      </div>
    </nav>
  </header>

  <section class="breadcrumb">
    <p>
      <a href="/" class="">Home</a> /
      <a href="detail.php?id=<?php echo $_GET["id"]; ?>">
        <?php echo $job["title"] . " - " . $job["company_name"]; ?>
      </a> /
      <span class="active">Apply</span>
    </p>
  </section>

  <section class="apply-form">
    <div class="content">
      <img class="bg" src=<?php echo $job["company_banner"]; ?> alt="" />
      <div class="company-info">
        <img src=<?php echo $job["company_logo"]; ?> alt="" />
        <div class="info">
          <h2><?php echo $job["title"]; ?></h2>
          <p>
            <?php echo $job["company_name"]; ?> <br />
            <?php echo $job["city"]; ?>
          </p>
        </div>
      </div>
    </div>

    <div class="form-container">
      <h1>Formulir Pengajuan Lamaran</h1>
      <form action="apply.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="job_id" value="<?php echo $id; ?>">
        <div class="columns">
          <!-- Kolom Kiri -->
          <div class="column-left">
            <div class="form-group">
              <label for="nama-lengkap">Nama Lengkap</label>
              <input type="text" id="nama-lengkap" name="nama-lengkap" placeholder="Nama Lengkap" required />
            </div>
            <div class="form-group">
              <label for="tanggal-lahir">Tanggal Lahir</label>
              <input type="date" id="tanggal-lahir" name="tanggal-lahir" required />
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" value="<?php echo htmlspecialchars(
                $userData["email"]
              ); ?>" placeholder="yourmail@gmail.com" readonly style="background-color: #f5f5f5;" required />
              <small class="form-info">Email otomatis diisi dari akun Anda</small>
            </div>
            <div class="form-group">
              <label for="nomor-hp">Nomor HP</label>
              <input type="tel" id="nomor-hp" name="nomor-hp" placeholder="08119923012" required />
            </div>
          </div>

          <!-- Kolom Kanan -->
          <div class="column-right">
            <div class="form-group">
              <label for="cv">CV (PDF)</label>
              <input type="file" id="cv" name="cv" accept=".pdf" required />
              <small class="form-info"">Format: PDF, Maksimal 5MB</small>
            </div>
            <div class=" form-group">
                <label for="portofolio">Portofolio (PDF, Opsional)</label>
                <input type="file" id="portofolio" name="portofolio" accept=".pdf" />
                <small class="form-info">Format: PDF, Maksimal 5MB</small>
            </div>
            <div class="form-group">
              <label for="surat-lamaran">Surat Lamaran (Opsional)</label>
              <input type="file" id="surat-lamaran" name="surat-lamaran" accept=".pdf" />
              <small class="form-info">Format: PDF, Maksimal 5MB</small>
            </div>
            <button type="submit" class="submit-btn">Kirim Lamaran</button>
          </div>
        </div>
      </form>
    </div>
  </section>

  <footer>
    <div class="logo">
      <img src="../asset/logo.png" alt="SugoiJob Logo" width="36" />
      <span>SugoiJob © <span id="currentYear"></span>. All rights reserved.</span>
    </div>
    <ul>
      <li>
        <img
          src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Instagram_icon.png/640px-Instagram_icon.png" />
        <a href="https://www.instagram.com/kncrln_/" target="_blank">Kenzie</a>
      </li>
      <li>
        <img
          src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Instagram_icon.png/640px-Instagram_icon.png" />
        <a href="https://www.instagram.com/r.van83/" target="_blank">Ivan</a>
      </li>
      <li>
        <img
          src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Instagram_icon.png/640px-Instagram_icon.png" />
        <a href="https://www.instagram.com/ndusft/" target="_blank">Bernadus</a>
      </li>
    </ul>
  </footer>
</body>

</html>