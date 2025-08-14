<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi</title>

  <!-- MathJax -->
  <script>
    window.MathJax = {
      tex: { inlineMath: [['$', '$'], ['\\(', '\\)']] },
      svg: { fontCache: 'global' }
    };
  </script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js" async></script>

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <style>
    table img {
      border-radius: 4px;
      object-fit: cover;
      max-width: 80px;
      max-height: 80px;
    }
  </style>
</head>
<body>

  <h2>📋 Danh sách câu hỏi trắc nghiệm</h2>
  <table id="mcTable" class="display" style="width:100%">
    <thead>
      <tr>
        <th>ID</th>
        <th>Chủ đề</th>
        <th>Câu hỏi</th>
        <th>A</th>
        <th>B</th>
        <th>C</th>
        <th>D</th>
        <th>Đáp án</th>
        <th>Hình minh họa</th>
      </tr>
    </thead>
  </table>

  <!-- jQuery + DataTables -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <script>
    $(function () {
      $('#mcTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '../../includes/mc/mc_fetch_data.php',
          type: 'POST',
          error: function (xhr) {
            console.error('AJAX error:', xhr.status, xhr.responseText);
            alert('Không thể tải dữ liệu (HTTP ' + xhr.status + ')');
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
              return data
                ? '<img src="' + data + '" alt="ảnh" />'
                : '';
            }
          }
        ],
        language: {
          processing: 'Đang tải...',
          search: 'Tìm:',
          lengthMenu: 'Hiển thị _MENU_ dòng',
          info: 'Hiển thị _START_–_END_ / _TOTAL_ dòng',
          infoEmpty: 'Không có dữ liệu',
          infoFiltered: '(lọc từ _MAX_ dòng)',
          paginate: {
            first: 'Đầu',
            last: 'Cuối',
            next: 'Sau',
            previous: 'Trước'
          },
          zeroRecords: 'Không tìm thấy kết quả phù hợp'
        }
      });
    });
  </script>

  <script src="../../js/mc/mc_table_arrow_key.js"></script>
</body>
</html>
