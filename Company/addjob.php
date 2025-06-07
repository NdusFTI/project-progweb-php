<?php
require "../koneksi.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SugoiJob - Add Jobs</title>
  <link rel="stylesheet" href="../style/jobs.css" />
</head>

<body>
  <div class="form-container">
    <div class="form-header">
      <h1><i class="fas fa-plus-circle"></i> Post New Job</h1>
      <p>Buka peluang karier menarik, tarik talenta terbaik</p>
    </div>

    <div class="form-content">
      <form action="process-job.php" method="POST" id="job-form">
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
              <input type="text" id="job_type" name="job_type" required placeholder="Enter your job type here" />
            </div>
          </div>

          <!-- Ini Location -->
          <div class="form-group">
            <label for="location">Location <span class="required">*</span></label>
            <input type="text" id="location" name="location" required placeholder="Enter your job location here" />
          </div>

          <!-- Ini Salary Information -->
          <div class="form-group">
            <label>Salary Information <span class="required">*</span></label>
            <div class="salary-inputs">
              <div>
                <input type="number" id="salary_min" name="salary_min" required placeholder="Min Salary" min="0" />
              </div>
              <div class="salary-separator">to</div>
              <div>
                <input type="number" id="salary_max" name="salary_max" required placeholder="Max Salary" min="0" />
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

          <!-- Ini Benefits -->
          <div class="form-group">
            <label for="benefits">Benefits & Perks</label>
            <textarea id="benefits" name="benefits"
              placeholder="Benefit yang bisa didapatkan jika mengambil pekerjaan ini..."></textarea>
          </div>

          <!-- Ini Application Instructions -->
          <div class="form-group">
            <label for="application_instructions">Application Instructions</label>
            <textarea id="application_instructions" name="application_instructions"
              placeholder="Instruksi khusus untuk pelamar (persyaratan portofolio, dll)..."></textarea>
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
                <i>Set a deadline for applications</i>
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

  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>

</html>