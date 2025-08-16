$(function () {
    const table = $('#mcTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: '../../includes/mc/mc_fetch_data.php',
        type: 'POST',
        error: function (xhr, error, code) {
          console.error("Lỗi tải dữ liệu:", error, code);
          alert("❌ Không thể tải dữ liệu bảng.");
        }
      },
      order: [[0, 'desc']],
      columns: [
        { data: 'mc_id' },
        { data: 'mc_topic' },
        { data: 'mc_question' },
        { data: 'mc_answer1' },
        { data: 'mc_answer2' },
        { data: 'mc_answer3' },
        { data: 'mc_answer4' },
        { data: 'mc_correct_answer' },
        {
          data: 'mc_image_url',
          render: function (data) {
            return data ? `<img src="${data}" alt="ảnh" loading="lazy">` : '';
          }
        }
      ],
      dom: 'Bfrtip',
      buttons: [
        { extend: 'excelHtml5', title: 'Danh sách câu hỏi', className: 'd-none', exportOptions: { columns: ':visible' } },
        { extend: 'print', title: 'Danh sách câu hỏi', className: 'd-none', exportOptions: { columns: ':visible' } }
      ],
      initComplete: function () {
        // load chủ đề filter
        $.getJSON('../../includes/mc/mc_get_topics.php')
          .done(function (topics) {
            topics.forEach(t => {
              $('#filterTopic').append(`<option value="${t}">${t}</option>`);
            });
          })
          .fail(() => alert("⚠️ Không tải được danh sách chủ đề"));
      }
    });
  
    // MathJax re-render sau mỗi lần vẽ lại bảng
    table.on('draw.dt', function () {
      if (window.MathJax) {
        MathJax.typesetPromise();
      }
    });
  
    // Lọc theo chủ đề
    $('#filterTopic').on('change', function () {
      table.column(1).search(this.value).draw();
    });
  
    // Export Excel
    $('#btnExportExcel').on('click', () => table.button(0).trigger());
  
    // Print
    $('#btnPrint').on('click', () => table.button(1).trigger());
  });
  