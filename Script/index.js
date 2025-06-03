let selectedJobIndex = null;

function formatSalaryJS(salaryMin, salaryMax, salaryText) {
  if (salaryText && salaryText.trim() !== '') {
    return salaryText;
  } else if (salaryMin && salaryMax) {
    return `Rp ${parseInt(salaryMin).toLocaleString('id-ID')} - Rp ${parseInt(salaryMax).toLocaleString('id-ID')} per bulan`;
  } else if (salaryMin) {
    return `Mulai dari Rp ${parseInt(salaryMin).toLocaleString('id-ID')}`;
  } else {
    return "Gaji dapat dinegosiasi";
  }
}

function formatJobTypeJS(jobType) {
  if (!jobType) return [];
  return jobType.split(',').map(type => type.trim());
}

function showJobDetail(index) {
  const job = jobsData[index];
  if (!job) return;

  selectedJobIndex = index;

  document.querySelectorAll('.job-item').forEach((item, i) => {
    if (i === index) {
      item.classList.add('active');
    } else {
      item.classList.remove('active');
    }
  });

  const jobTypes = formatJobTypeJS(job.job_type);

  const companyBanner = job.company_banner;
  const companyLogo = job.company_logo;

  const detailHTML = `
    <div class="content">
      <img class="bg" src="${companyBanner}" alt="${job.company_name} Banner" />
      <div class="company-info">
        <img src="${companyLogo}" alt="${job.company_name} Logo" />
        <div class="info">
          <h2>${job.title}</h2>
          <p>
            ${job.company_name}<br />
            ${job.location}
          </p>
        </div>
      </div>
      <div class="apply">
        <a href="detail.php?id=${job.id}">
          View More
          <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 24 24">
            <path d="M19,21H5c-1.1,0-2-0.9-2-2V5c0-1.1,0.9-2,2-2h7v2H5v14h14v-7h2v7C21,20.1,20.1,21,19,21z"></path>
            <path d="M21 10L19 10 19 5 14 5 14 3 21 3z"></path>
            <path d="M6.7 8.5H22.3V10.5H6.7z" transform="rotate(-45.001 14.5 9.5)"></path>
          </svg>
        </a>
      </div>
    </div>
    <div class="summary">
      <div class="details">
        <h1>Detail Pekerjaan</h1>
        <div class="columns">
          <div class="left-group">
            <div class="type">
              <h1>Kategori</h1>
              <div class="tag">
                <p>${job.category_name}</p>
              </div>
            </div>
            <div class="type">
              <h1>Gaji</h1>
              <div class="tag">
                <p>${formatSalaryJS(job.salary_min, job.salary_max, job.salary_text)}</p>
              </div>
            </div>
            ${job.experience_required ? `
            <div class="type">
              <h1>Pengalaman</h1>
              <div class="tag">
                <p>${job.experience_required}</p>
              </div>
            </div>
            ` : ''}
          </div>
          <div class="right-group">
            <div class="type">
              <h1>Tipe Pekerjaan</h1>
              <div class="tag">
                ${jobTypes.map(type => `<p>${type}</p>`).join('')}
              </div>
            </div>
            ${job.application_deadline ? `
            <div class="type">
              <h1>Batas Lamaran</h1>
              <div class="tag">
                <p>${new Date(job.application_deadline).toLocaleDateString('id-ID')}</p>
              </div>
            </div>
            ` : ''}
          </div>
        </div>
      </div>
      <div class="description">
        <h1>Deskripsi singkat pekerjaan</h1>
        <div class="description-content">
          <p>${job.description || 'Tidak ada deskripsi tersedia.'}</p>
        </div>
      </div>
    </div>
  `;

  const container = document.getElementById('job-detail-container');
  const emptyState = document.getElementById('empty-state');

  if (emptyState) {
    emptyState.style.display = 'none';
  }

  container.innerHTML = detailHTML;
}

document.addEventListener('DOMContentLoaded', function() {
  if (jobsData.length > 0) {
    showJobDetail(0);
  }

  const yearElement = document.getElementById('currentYear');
  if (yearElement) {
    yearElement.textContent = new Date().getFullYear();
  }
});