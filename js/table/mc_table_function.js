$(document).ready(function () {
  // Khởi tạo bảng với AJAX
  $('#mcTable').DataTable({
    data: [], // dữ liệu sẽ được load sau qua AJAX
    columns: [
      { data: 'id' },
      { data: 'topic' },
      { data: 'question' },
      { data: 'answer1' },
      { data: 'answer2' },
      { data: 'answer3' },
      { data: 'answer4' },
      { data: 'correct' },
      {
        data: 'image',
        render: function (data, type, row) {
          if (!data) return '';
          
          // Xử lý thumb từ link gốc Cloudinary
          const thumbUrl = data.replace("/upload/", "/upload/c_thumb,w_60,h_60/");
          const fullUrl = data;
  
          return `<img src="${thumbUrl}" class="mc-thumbnail" data-full="${fullUrl}" alt="Ảnh" />`;
        },
        orderable: false,
        searchable: false
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
