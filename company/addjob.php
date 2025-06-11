<?php
require "../koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $job_title = trim($_POST['job_title']);
  $category = trim($_POST['category']);
  $job_type = $_POST['job_type'];
  $location = trim($_POST['location']);
  $full_address = trim($_POST['full_address']);
  $salary_min = $_POST['salary_min'];
  $salary_max = $_POST['salary_max'];
  $description = trim($_POST['description']);
  $requirements = trim($_POST['requirements']);
  $experience_required = trim($_POST['experience_required']);
  $status = $_POST['status'];
  $application_deadline = $_POST['application_deadline'];

  $errors = [];

  // Validasi
  if (empty($job_title))
    $errors[] = "Job title required";

  if (empty($category)) {
    $errors[] = "Category required";
  } else {
    $check_cat = $koneksi->prepare("SELECT id FROM job_categories WHERE name = ?");
    $check_cat->bind_param("s", $category);
    $check_cat->execute();
    $result = $check_cat->get_result();

    if ($result->num_rows > 0) {
      $cat_data = $result->fetch_assoc();
      $category_id = $cat_data['id'];
    } else {
      $insert_cat = $koneksi->prepare("INSERT INTO job_categories (name) VALUES (?)");
      $insert_cat->bind_param("s", $category);
      $insert_cat->execute();
      $category_id = $koneksi->insert_id;
    }
  }

  if (empty($job_type))
    $errors[] = "Job type required";
  elseif (!in_array($job_type, ['Full-Time', 'Part-Time', 'Kontrak', 'Internship']))
    $errors[] = "Invalid job type selected";

  if (empty($description))
    $errors[] = "Description required";
  if (empty($requirements))
    $errors[] = "Requirements required";
  if (empty($location))
    $errors[] = "Location required";
  if (empty($full_address))
    $errors[] = "Full address required";

  if (!is_numeric($salary_min) || $salary_min < 0)
    $errors[] = "Minimum salary must be a non-negative number";
  if (!is_numeric($salary_max) || $salary_max < 0)
    $errors[] = "Maximum salary must be a non-negative number";
  if ($salary_min > $salary_max)
    $errors[] = "Minimum salary cannot be greater than maximum salary";

  if (empty($application_deadline))
    $errors[] = "Application deadline required";
  if (!DateTime::createFromFormat('Y-m-d', $application_deadline))
    $errors[] = "Invalid application deadline format";
  if (strtotime($application_deadline) < strtotime(date('Y-m-d')))
    $errors[] = "Application deadline cannot be in the past";

  if (!in_array($status, ['active', 'draft', 'paused']))
    $errors[] = "Invalid status";

  if (empty($errors)) {
    $sql = "INSERT INTO job_postings (company_id, category_id, title, description, requirements, salary_min, salary_max, salary_text, location, full_address, job_type, experience_required, application_deadline, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $koneksi->prepare($sql);
    $company_id = $_SESSION['company_id'] ?? 1;

    $status_value = ($status == 'active') ? 1 : (($status == 'draft') ? 2 : 0);
    $salary_text = "Rp " . number_format($salary_min, 0, ',', '.') . " - Rp " . number_format($salary_max, 0, ',', '.');
    $experience_required = empty($experience_required) ? null : $experience_required;

    $stmt->bind_param(
      "iissssissssssi",
      $company_id,
      $category_id,
      $job_title,
      $description,
      $requirements,
      $salary_min,
      $salary_max,
      $salary_text,
      $location,
      $full_address,
      $job_type,
      $experience_required,
      $application_deadline,
      $status_value
    );

    if ($stmt->execute()) {
      echo "<script>
        alert('Success! Job posting created successfully!');
        window.location.href = '../';
      </script>";
    } else {
      echo "<script>
        alert('Error! Failed to create job posting. Please try again.');
      </script>";
    }
  } else {
    $error_text = implode('\\n', $errors);
    echo "<script>
      alert('Validation Error!\\n$error_text');
    </script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SugoiJob - Add Jobs</title>
  <link rel="stylesheet" href="../style/jobs.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
  <div class="form-container">
    <div class="form-header">
      <h1><i class="fas fa-plus-circle"></i> Post New Job</h1>
      <p>Buka peluang karier menarik, tarik talenta terbaik</p>
    </div>

    <div class="form-content">
      <form action="addjob.php" method="POST" id="job-form">
        <div class="form-grid">

          <!-- Ini Job Title -->
          <div class="form-group">
            <label for="job_title">Job Title <span class="required">*</span></label>
            <input type="text" id="job_title" name="job_title" required placeholder="Enter your job title here" />
            <div class="help-text">
              <i>Disarankan untuk spesifik dan deskriptif demi menarik kandidat yang berkualitas</i>
            </div>
          </div>

          <!-- Ini Job Category & Type -->
          <div class="form-row">
            <div class="form-group">
              <label for="category">Job Category <span class="required">*</span></label>
              <input type="text" id="category" name="category" required placeholder="Enter your job category here" />
            </div>

            <div class="form-group">
              <label for="job_type">Job Type <span class="required">*</span></label>
              <select id="job_type" name="job_type" required>
                <option value="">Select Job Type</option>
                <option value="Full-Time">Full-Time</option>
                <option value="Part-Time">Part-Time</option>
                <option value="Kontrak">Kontrak</option>
                <option value="Internship">Internship</option>
              </select>
            </div>
          </div>

          <!-- Ini Location -->
          <div class="form-row">
            <div class="form-group">
              <label for="location">Location <span class="required">*</span></label>
              <input type="text" id="location" name="location" required placeholder="Enter your job location here" />
            </div>

            <div class="form-group">
              <label for="full_address">Full Address <span class="required">*</span></label>
              <input type="text" id="full_address" name="full_address" required
                placeholder="Enter your full address here" />
            </div>
          </div>

          <!-- Ini Salary Information -->
          <div class="form-group">
            <label>Salary Information <span class="required">*</span></label>
            <div class="salary-inputs">
              <div>
                <input type="number" id="salary_min" name="salary_min" required placeholder="Min Salary" min="0" step="100000" />
              </div>
              <div class="salary-separator">to</div>
              <div>
                <input type="number" id="salary_max" name="salary_max" required placeholder="Max Salary" min="0" step="100000" />
              </div>
            </div>
          </div>

          <!-- Ini Job Description -->
          <div class="form-group">
            <label for="description">Job Description <span class="required">*</span></label>
            <textarea id="description" name="description" required
              placeholder="Jelaskan peran, tanggung jawab, dan apa yang membuat pekerjaan ini menarik..."></textarea>
          </div>

          <!-- Ini Requirements -->
          <div class="form-group">
            <label for="requirements">Requirements <span class="required">*</span></label>
            <textarea id="requirements" name="requirements" required
              placeholder="Daftar keterampilan, pengalaman, dan kualifikasi penting yang dibutuhkan untuk pekerjaan ini..."></textarea>
          </div>

          <!-- Ini Experience Required -->
          <div class="form-group">
            <label for="experience_required">Experience Required</label>
            <input type="text" id="experience_required" name="experience_required"
              placeholder="Experience yang dibutuhkan..." />
          </div>

          <!-- Ini Job Status & Deadline -->
          <div class="form-row">
            <div class="form-group">
              <label for="status">Job Status <span class="required">*</span></label>
              <select id="status" name="status">
                <option value="active">Active (Accept Applications)</option>
                <option value="draft">Draft (Save for Later)</option>
                <option value="paused">Paused (Temporarily Closed)</option>
              </select>
            </div>

            <div class="form-group">
              <label for="application_deadline">Application Deadline <span class="required">*</span></label>
              <input type="date" id="application_deadline" name="application_deadline" required />
              <div class="help-text">
                <i>Tetapkan tenggat waktu untuk aplikasi</i>
              </div>
            </div>
          </div>
        </div>

        <!-- Ini Action Buttons (Cancel/Submit) -->
        <div class="form-actions">
          <button type="button" class="btn btn-secondary" onclick="history.back()">
            <i class="fas fa-arrow-left"></i> Cancel
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane"></i> Post Job
          </button>
        </div>
      </form>
    </div>
  </div>
</body>

</html>