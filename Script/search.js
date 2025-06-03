const jobsData = JSON.parse(document.getElementById('jobs-data-json').textContent);

function applyFilters() {
  document.getElementById('filter-form').submit();
}

function applyLocationFilter() {
  const locationFilter = document.getElementById('location-filter').value;
  const locationInput = document.getElementById('location');
  locationInput.value = locationFilter;
  document.getElementById('search-form').submit();
}