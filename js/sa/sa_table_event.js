// js/sa/sa_table_events.js
let selectedRowIndex = null;

$(document).ready(function () {
  const table = $('#saTable').DataTable();

  // Reset highlight khi redraw
  table.on('draw', function () {
    selectedRowIndex = null;
    $('#saTable tbody tr').removeClass('selected');
  });

  // --- Click chọn dòng ---
  $('#saTable tbody').on('click', 'tr', function () {
    const index = table.row(this).index();
    const rowIndexes = table.rows({ search: 'applied' }).indexes().toArray();
    if (rowIndexes.includes(index)) {
      selectedRowIndex = index;
      highlightAndSendSA(table, index, false);
    }
  });

  // --- Double click: chọn + focus form ---
  $('#saTable tbody').on('dblclick', 'tr', function () {
    const index = table.row(this).index();
    const rowIndexes = table.rows({ search: 'applied' }).indexes().toArray();
    if (rowIndexes.includes(index)) {
      selectedRowIndex = index;
      highlightAndSendSA(table, index, false);
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
      highlightAndSendSA(table, selectedRowIndex, true);
    }

    if (e.key === 'ArrowUp') {
      e.preventDefault();
      if (selectedRowIndex === null) {
        selectedRowIndex = rowIndexes[rowIndexes.length - 1];
      } else {
        const pos = rowIndexes.indexOf(selectedRowIndex);
        selectedRowIndex = rowIndexes[(pos - 1 + rowIndexes.length) % rowIndexes.length];
      }
      highlightAndSendSA(table, selectedRowIndex, true);
    }

    if (e.key === 'Enter' && selectedRowIndex !== null) {
      e.preventDefault();
      highlightAndSendSA(table, selectedRowIndex, true);
      window.parent.postMessage({ type: 'focus-form' }, '*');
    }
  });
});

// --- Hàm chọn dòng + gửi dữ liệu về form ---
function highlightAndSendSA(table, rowIndex, scroll = false) {
  $('#saTable tbody tr').removeClass('selected');
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

  if (scroll) scrollToRowSA(table, rowIndex);
}

// --- Scroll tới dòng đã chọn ---
function scrollToRowSA(table, rowIndex) {
  const rowNode = $(table.row(rowIndex).node());
  if (rowNode.length) {
    $('html, body').animate(
      { scrollTop: rowNode.offset().top - 100 },
      200
    );
  }
}
