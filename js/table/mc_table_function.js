$(document).ready(function () {
  // Khởi tạo bảng với AJAX
  const table = $('#mcTable').DataTable({
    ajax: {
      url: '../../api/mc/get_all.php',
      dataSrc: ''
    },
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
          if (data) {
            return `<img src="${data}" class="mc-thumbnail" data-full="${data}" onerror="this.style.display='none'">`;
          } else {
            return '';
          }
        }
      }
    ],
    responsive: true,
    dom: 'Bfrtip',
    buttons: [
      {
        extend: 'excelHtml5',
        title: 'Cau_Hoi_Trac_Nghiem',
        text: '📥 Xuất Excel',
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'print',
        text: '🖨 In bảng'
      }
    ],
    language: {
      search: '🔍 Tìm kiếm:',
      lengthMenu: 'Hiển thị _MENU_ dòng',
      info: 'Hiển thị _START_ đến _END_ trong _TOTAL_ dòng',
      paginate: {
        previous: '←',
        next: '→'
      },
      zeroRecords: 'Không tìm thấy kết quả'
    },
    initComplete: function () {
      // Sau khi bảng khởi tạo: render công thức + tạo bộ lọc chủ đề
      MathJax.typeset();

      const api = this.api();
      const topics = new Set();

      api.column(1).data().each(function (d) {
        if (d) topics.add(d);
      });

      const select = $('#topicFilter');
      select.append(`<option value="">Tất cả</option>`);
      [...topics].sort().forEach(topic => {
        select.append(`<option value="${topic}">${topic}</option>`);
      });

      select.on('change', function () {
        const val = $.fn.dataTable.util.escapeRegex($(this).val());
        api.column(1).search(val ? '^' + val + '$' : '', true, false).draw();
      });
    }
  });

  // Re-render MathJax sau mỗi lần vẽ lại bảng
  $('#mcTable').on('draw.dt', function () {
    MathJax.typeset();
  });
});
