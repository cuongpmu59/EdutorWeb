<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi</title>
  <script>
  window.MathJax = {
    tex: {inlineMath: [['$', '$'], ['\\(', '\\)']]},
    svg: {fontCache: 'global'}
  };
  </script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js" async></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <style>
    table img {
      border-radius: 4px;
      object-fit: cover;
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

  <!-- JS thư viện -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <!-- JS FixedHeader -->
  <script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>
  <script>
    $(document).ready(function () {
    $('#mcTable').DataTable({
      paging: true,          // có thể bật phân trang
      fixedHeader: true      // bật header cố định
    });
  });
  </script>
  <!-- File JS khởi tạo bảng -->
  <script src="../../js/mc/mc_fetch_data.js"></script>
  <script src="../../js/mc/mc_table_arrow_key.js"></script>

  

</body>
</html>
