// js/tf/tf_table_events.js
let selectedRowIndex = null;

$(document).ready(function () {
  const table = $('#tfTable').DataTable();

  // Reset highlight khi redraw
  table.on('draw', function () {
    selectedRowIndex = null;
    $('#tfTable tbody tr').removeClass('selected');
  });

  // --- Click chọn dòng ---
  $('#tfTable tbody').on('click', 'tr', function () {
    const index = table.row(this).index();
    const rowIndexes = table.rows({ search: 'applied' }).indexes().toArray();
    if (rowIndexes.includes(index)) {
      selectedRowIndex = index;
      highlightAndSendTF(table, index, false);
    }
  });

  // --- Double click: chọn + focus form ---
  $('#tfTable tbody').on('dblclick', 'tr', function () {
    const index = table.row(this).index();
    const rowIndexes = table.rows({ search: 'applied' }).indexes().toArray();
    if (rowIndexes.includes(index)) {
      selectedRowIndex = index;
      highlightAndSendTF(table, index, false);
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
      highlightAndSendTF(table, selectedRowIndex, true);
    }

    if (e.key === 'ArrowUp') {
      e.preventDefault();
      if (selectedRowIndex === null) {
        selectedRowIndex = rowIndexes[rowIndexes.length - 1];
      } else {
        const pos = rowIndexes.indexOf(selectedRowIndex);
        selectedRowIndex = rowIndexes[(pos - 1 + rowIndexes.length) % rowIndexes.length];
      }
      highlightAndSendTF(table, selectedRowIndex, true);
    }

    if (e.key === 'Enter' && selectedRowIndex !== null) {
      e.preventDefault();
      highlightAndSendTF(table, selectedRowIndex, true);
      window.parent.postMessage({ type: 'focus-form' }, '*');
    }
  });
});

// --- Hàm chọn dòng + gửi dữ liệu về form ---
function highlightAndSendTF(table, rowIndex, scroll = false) {
  $('#tfTable tbody tr').removeClass('selected');
  $(table.row(rowIndex).node()).addClass('selected');

  const rowData = table.row(rowIndex).data();
  if (rowData) {
    window.parent.postMessage(
      {
        type: 'fill-form',
        data: {
          tf_id: rowData.tf_id || '',
          tf_topic: rowData.tf_topic || '',
          tf_question: rowData.tf_question || '',
          tf_statement1: rowData.tf_statement1 || '',
          tf_statement2: rowData.tf_statement2 || '',
          tf_statement3: rowData.tf_statement3 || '',
          tf_statement4: rowData.tf_statement4 || '',
          tf_correct_answer1: rowData.tf_correct_answer1 || '',
          tf_correct_answer2: rowData.tf_correct_answer2 || '',
          tf_correct_answer3: rowData.tf_correct_answer3 || '',
          tf_correct_answer4: rowData.tf_correct_answer4 || '',
          tf_image_url: rowData.tf_image_url || '',
          tf_created_at: rowData.tf_created_at || ''
        }
      },
      '*'
    );
  }

  if (scroll) scrollToRowTF(table, rowIndex);
}

// --- Scroll tới dòng đã chọn ---
function scrollToRowTF(table, rowIndex) {
  const rowNode = $(table.row(rowIndex).node());
  if (rowNode.length) {
    $('html, body').animate(
      { scrollTop: rowNode.offset().top - 100 },
      200
    );
  }
}
