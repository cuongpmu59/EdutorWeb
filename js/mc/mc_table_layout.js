// ==========================
// mc_table_layout.js
// ==========================
$(document).ready(function () {
    // ===== Khởi tạo DataTable =====
    const table = $('#mcTable').DataTable({
      ajax: {
        url: '../../includes/mc/mc_fetch_data.php',
        dataSrc: ''
      },
      columns: [
        { data: 'mc_id' },
        { data: 'mc_topic' },
        { data: 'mc_question' },
        { data: 'mc_answer_a' },
        { data: 'mc_answer_b' },
        { data: 'mc_answer_c' },
        { data: 'mc_answer_d' },
        { data: 'mc_correct' },
        {
          data: 'mc_image_url',
          render: function (data) {
            return data
              ? `<img src="${data}" class="thumb" alt="Ảnh" loading="lazy">`
              : '';
          }
        }
      ],
      responsive: true,
      scrollX: true,
      pageLength: 10,
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
      },
      initComplete: function () {
        // Render công thức toán khi load xong
        if (window.MathJax) MathJax.typesetPromise();
      }
    });
  
    // ===== Render MathJax mỗi lần vẽ lại =====
    table.on('draw', function () {
      if (window.MathJax) MathJax.typesetPromise();
    });
  
    // ===== Nút Xuất Excel =====
    $('#btnExportExcel').on('click', function () {
      table.button('.buttons-excel').trigger();
    });
  
    // ===== Nút In =====
    $('#btnPrint').on('click', function () {
      table.button('.buttons-print').trigger();
    });
  
    // ===== Thêm Buttons (Excel, Print) =====
    new $.fn.dataTable.Buttons(table, {
      buttons: [
        { extend: 'excelHtml5', text: '📤 Xuất Excel', className: 'd-none' },
        { extend: 'print', text: '🖨️ In bảng', className: 'd-none' }
      ]
    });
    table.buttons().container().appendTo($('body')); // ẩn nhưng vẫn trigger được
  
    // ===== Load danh sách chủ đề vào combobox =====
    $.getJSON('../../includes/mc/mc_fetch_topics.php', function (topics) {
      const $filter = $('#filterTopic');
      topics.forEach(t => {
        $filter.append(`<option value="${t}">${t}</option>`);
      });
    });
  
    // ===== Lọc chủ đề =====
    $('#filterTopic').on('change', function () {
      const val = this.value;
      table.column(1).search(val ? '^' + val + '$' : '', true, false).draw();
    });
  
    // ===== Click chọn dòng =====
    $('#mcTable tbody').on('click', 'tr', function () {
      $(this).toggleClass('selected').siblings().removeClass('selected');
    });
  });
  