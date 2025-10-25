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
  document.querySelectorAll('#filterColumn, #filterValue, #filterNivel, #filterFechaDesde, #filterFechaHasta')
    .forEach(el => el.addEventListener('input', filterTable));

  clearBtn.addEventListener('click', clearFilters);
});

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
  const nivel = document.getElementById('filterNivel').value.toUpperCase();
  const fechaDesde = document.getElementById('filterFechaDesde').value;
  const fechaHasta = document.getElementById('filterFechaHasta').value;

  const rows = document.querySelectorAll('#logTableBody tr');

  rows.forEach(row => {
    const cells = row.getElementsByTagName('td');
    if (cells.length < 4) return;

    const origenText = cells[0].textContent.toLowerCase();
    const rowNivel = cells[1].textContent.trim().toUpperCase();
    const detallesText = cells[2].textContent.toLowerCase();
    const mensaje = cells[3].textContent.toLowerCase();

    const rowFechaStr = origenText.match(/\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}/)?.[0];
    const rowFecha = rowFechaStr ? parseDateTime(rowFechaStr) : null;

    let rowColumnValue = '';
    if (filterColumn === 'dominio' || filterColumn === 'modulo') rowColumnValue = origenText;
    else if (filterColumn === 'pidtid' || filterColumn === 'cliente' || filterColumn === 'codigo') rowColumnValue = detallesText;
    else if (filterColumn === 'mensaje') rowColumnValue = mensaje;

    const matchesColumn = filterValue === '' || rowColumnValue.includes(filterValue);
    const matchesNivel = nivel === '' || rowNivel === nivel;

    let matchesFecha = true;
    if (rowFecha && (fechaDesde || fechaHasta)) {
      const desde = fechaDesde ? new Date(fechaDesde) : null;
      const hasta = fechaHasta ? new Date(fechaHasta) : null;
      if (desde && hasta) matchesFecha = rowFecha >= desde && rowFecha <= hasta;
      else if (desde) matchesFecha = rowFecha >= desde;
      else if (hasta) matchesFecha = rowFecha <= hasta;
    }

    row.style.display = (matchesColumn && matchesNivel && matchesFecha) ? '' : 'none';
  });
}

function clearFilters() {
  document.getElementById('filterColumn').value = 'dominio';
  document.getElementById('filterValue').value = '';
  document.getElementById('filterNivel').value = '';
  document.getElementById('filterFechaDesde').value = '';
  document.getElementById('filterFechaHasta').value = '';
  filterTable();
}