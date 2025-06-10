const jobsData = JSON.parse(document.getElementById('jobs-data-json').textContent);

function applyFilters() {
  const keyword = document.getElementById('job-title').value;
  let location = document.getElementById('location-filter').value;
  
  if (location) {
    document.getElementById('location').value = location;
  } else {
    location = document.getElementById('location').value;
  }
  
  const jobType = document.getElementById('job-type').value;
  const company = document.getElementById('company').value;
  const datePosted = document.getElementById('date-posted').value;
  const salaryRange = document.getElementById('salary-range').value;

  const url = new URL(window.location.href.split('?')[0]);
  
  if (keyword) url.searchParams.set('keyword', keyword);
  if (location) url.searchParams.set('location', location);
  if (jobType) url.searchParams.set('job_type', jobType);
  if (company) url.searchParams.set('company', company);
  if (datePosted) url.searchParams.set('date_posted', datePosted);
  if (salaryRange) url.searchParams.set('salary_range', salaryRange);

  window.location.href = url.toString();
}

document.addEventListener('DOMContentLoaded', function() {
  const locationInput = document.getElementById('location');
  const locationFilter = document.getElementById('location-filter');
  
  if (locationInput.value) {
    locationFilter.value = locationInput.value;
  }
});