// js/mc/mc_table_events.js
let selectedRowIndex = null;

$(document).ready(function () {
  const table = $('#mcTable').DataTable();

  // Reset highlight khi redraw
  table.on('draw', function () {
    selectedRowIndex = null;
    $('#mcTable tbody tr').removeClass('selected');
  });

  // --- Click chọn dòng ---
  $('#mcTable tbody').on('click', 'tr', function () {
    const index = table.row(this).index();
    const rowIndexes = table.rows({ search: 'applied' }).indexes().toArray();
    if (rowIndexes.includes(index)) {
      selectedRowIndex = index;
      highlightAndSend(table, index, false);
    }
  });

  // --- Double click: chọn + focus form ---
  $('#mcTable tbody').on('dblclick', 'tr', function () {
    const index = table.row(this).index();
    const rowIndexes = table.rows({ search: 'applied' }).indexes().toArray();
    if (rowIndexes.includes(index)) {
      selectedRowIndex = index;
      highlightAndSend(table, index, false);
      window.parent.postMessage({ type: 'focus-form' }, '*');
    }
  });

  // --- Arrow key + Enter ---
  $(document).on('keydown', function (e) {
    const rowIndexes = table.rows({ search: 'applied' }).indexes().toArray();
    if (!rowIndexes.length) return;

    if (e.key === 'ArrowDown') {
      e.preventDefault();
      if (selectedRowIndex === null) {
        selectedRowIndex = rowIndexes[0];
      } else {
        const pos = rowIndexes.indexOf(selectedRowIndex);
        selectedRowIndex = rowIndexes[(pos + 1) % rowIndexes.length];
      }
      highlightAndSend(table, selectedRowIndex, true);
    }

    if (e.key === 'ArrowUp') {
      e.preventDefault();
      if (selectedRowIndex === null) {
        selectedRowIndex = rowIndexes[rowIndexes.length - 1];
      } else {
        const pos = rowIndexes.indexOf(selectedRowIndex);
        selectedRowIndex = rowIndexes[(pos - 1 + rowIndexes.length) % rowIndexes.length];
      }
      highlightAndSend(table, selectedRowIndex, true);
    }

    if (e.key === 'Enter' && selectedRowIndex !== null) {
      e.preventDefault();
      highlightAndSend(table, selectedRowIndex, true);
      window.parent.postMessage({ type: 'focus-form' }, '*');
    }
  });
});

// --- Hàm chọn dòng + gửi dữ liệu về form ---
function highlightAndSend(table, rowIndex, scroll = false) {
  $('#mcTable tbody tr').removeClass('selected');
  $(table.row(rowIndex).node()).addClass('selected');

  const rowData = table.row(rowIndex).data();
  if (rowData) {
    window.parent.postMessage(
      {
        type: 'fill-form',
        data: rowData
      },
      '*'
    );
  }

  if (scroll) scrollToRow(table, rowIndex);
}

// --- Scroll tới dòng đã chọn ---
function scrollToRow(table, rowIndex) {
  const rowNode = $(table.row(rowIndex).node());
  if (rowNode.length) {
    $('html, body').animate(
      { scrollTop: rowNode.offset().top - 100 },
      200
    );
  }
}
