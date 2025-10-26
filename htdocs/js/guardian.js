document.addEventListener('DOMContentLoaded', () => {
  const topScrollbar = document.getElementById('topScrollbar');
  const tableContainer = document.getElementById('tableContainer');
  const clearBtn = document.getElementById('clearFilters');

  // Sincronizar scroll superior
  if (topScrollbar && tableContainer) {
    topScrollbar.addEventListener('scroll', () => {
      tableContainer.scrollLeft = topScrollbar.scrollLeft;
    });
    tableContainer.addEventListener('scroll', () => {
      topScrollbar.scrollLeft = tableContainer.scrollLeft;
    });
  }

  // Filtros dinámicos
  document.querySelectorAll('#filterColumn, #filterValue, #filterLevel, #filterDateFrom, #filterDateTo')
    .forEach(el => el.addEventListener('input', filterTable));

  clearBtn.addEventListener('click', clearFilters);
});

// Show/Hide Scroll to Top Button
window.addEventListener('scroll', function () {
  const scrollToTopBtn = document.getElementById('scrollToTopBtn');
  if (window.pageYOffset > 300) {
    scrollToTopBtn.style.display = 'block';
  } else {
    scrollToTopBtn.style.display = 'none';
  }
});

// Scroll to Top Function
function scrollToTop() {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  });
}

// Refresh Logs Function
function refreshLogs() {
}

function parseDateTime(dateTimeStr) {
  const parts = dateTimeStr.split(' ');
  const dateParts = parts[0].split('-');
  const timeParts = (parts[1] || '').split(':');
  return new Date(
    dateParts[0],
    dateParts[1] - 1,
    dateParts[2],
    timeParts[0] || 0,
    timeParts[1] || 0,
    timeParts[2] || 0
  );
}

function filterTable() {
  const filterColumn = document.getElementById('filterColumn').value;
  const filterValue = document.getElementById('filterValue').value.toLowerCase();
  const level = document.getElementById('filterLevel').value.toUpperCase();
  const dateFrom = document.getElementById('filterDateFrom').value;
  const dateTo = document.getElementById('filterDateTo').value;

  const rows = document.querySelectorAll('#logTableBody tr');

  rows.forEach(row => {
    const cells = row.getElementsByTagName('td');
    if (cells.length < 4) return;

    const originText = cells[0].textContent.toLowerCase();
    const rowLevel = cells[1].textContent.trim().toUpperCase();
    const detallesText = cells[2].textContent.toLowerCase();
    const mensaje = cells[3].textContent.toLowerCase();

    const rowFechaStr = originText.match(/\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}/)?.[0];
    const rowFecha = rowFechaStr ? parseDateTime(rowFechaStr) : null;

    let rowColumnValue = '';
    if (filterColumn === 'domain' || filterColumn === 'module') rowColumnValue = originText;
    else if (filterColumn === 'pidtid' || filterColumn === 'client' || filterColumn === 'code') rowColumnValue = detallesText;
    else if (filterColumn === 'message') rowColumnValue = mensaje;

    const matchesColumn = filterValue === '' || rowColumnValue.includes(filterValue);
    const matchesLevel = level === '' || rowLevel === level;

    let matchesDate = true;
    if (rowFecha && (dateFrom || dateTo)) {
      const desde = dateFrom ? new Date(dateFrom) : null;
      const hasta = dateTo ? new Date(dateTo) : null;
      if (desde && hasta) matchesDate = rowFecha >= desde && rowFecha <= hasta;
      else if (desde) matchesDate = rowFecha >= desde;
      else if (hasta) matchesDate = rowFecha <= hasta;
    }

    row.style.display = (matchesColumn && matchesLevel && matchesDate) ? '' : 'none';
  });
}

function clearFilters() {
  document.getElementById('filterColumn').value = 'domain';
  document.getElementById('filterValue').value = '';
  document.getElementById('filterLevel').value = '';
  document.getElementById('filterDateFrom').value = '';
  document.getElementById('filterDateTo').value = '';
  filterTable();
}