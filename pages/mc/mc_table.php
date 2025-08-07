<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý câu hỏi</title>

  <!-- MathJax cấu hình hiển thị công thức toán học -->
  <script>
    window.MathJax = {
      tex: { inlineMath: [['$', '$'], ['\\(', '\\)']] },
      svg: { fontCache: 'global' }
    };
  </script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js" async></script>

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

  <!-- CSS tùy chỉnh -->
  <style>
    table img {
      border-radius: 4px;
      object-fit: cover;
      max-width: 50px;
      max-height: 50px;
    }

    /* Highlight dòng được chọn */
    #mcTable tbody tr.selected {
      background-color: #e0f7ff !important;
      font-weight: 500;
    }

    body {
      font-family: system-ui, sans-serif;
      padding: 16px;
    }

    h2 {
      margin-bottom: 16px;
      color: #0077b6;
    }

    table.dataTable td {
      vertical-align: middle;
    }
  </style>
</head>
<body>

  <h2>📋 Danh sách câu hỏi trắc nghiệm</h2>

  <table id="mcTable" class="display nowrap" style="width:100%">
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

  <!-- Thư viện JS -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <!-- Tập tin JS xử lý -->
  <script src="../../js/mc/mc_fetch_data.js"></script>
  <script src="../../js/mc/mc_table_arrow_key.js"></script>

  <!-- Nhận tín hiệu reload từ iframe/parent -->
  <script>
    window.addEventListener("message", function (event) {
      if (event.data && event.data.action === "reload_table") {
        const table = $('#mcTable').DataTable();
        table.ajax.reload(null, false); // Giữ nguyên trang hiện tại
      }
    });
  </script>

</body>
</html>
