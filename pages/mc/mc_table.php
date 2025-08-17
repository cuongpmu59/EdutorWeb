<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi</title>
  <!-- MathJax -->
  <script>
  window.MathJax = {
    tex: {inlineMath: [['$', '$'], ['\\(', '\\)']]},
    svg: {fontCache: 'global'}
  };
  </script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js" async></script>

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.dataTables.min.css">

  <style>
    table img {
      border-radius: 4px;
      object-fit: cover;
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
        <th>Đáp án</th>
        <th>Hình minh hoạ</th>
      </tr>
    </thead>
  </table>

  <!-- jQuery + DataTables -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>

  <script>
    let mcTable; // biến toàn cục để dùng lại

    $(document).ready(function () {
      mcTable = $('#mcTable').DataTable({
        ajax: {
          url: '../../php/mc/mc_fetch_data.php', // đường dẫn PHP trả JSON
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
              return data
                ? '<img src="' + data + '" style="max-width:100px;">'
                : '';
            }
          }
        ],
        paging: true,
        fixedHeader: true,
        destroy: true
      });
    });

    // Hàm reload lại dữ liệu khi cần
    function reloadMcTable() {
      mcTable.ajax.reload(null, false);
    }
  </script>

  <!-- JS hỗ trợ -->
  <script src="../../js/mc/mc_table_arrow_key.js"></script>

</body>
</html>
