<?php
header("X-Frame-Options: SAMEORIGIN");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📋 Câu hỏi Nhiều lựa chọn</title>

  <!-- Thư viện CSS ngoài -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

  <!-- Giao diện riêng -->
  <link rel="stylesheet" href="../../css/table/mc_table.css">
  <link rel="stylesheet" href="../../css/table/mc_filter.css"> <!-- nếu bạn tách riêng -->
  <link rel="stylesheet" href="../../css/table_ui.css"> <!-- nếu bạn gộp -->

  <!-- MathJax -->
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

  <!-- Thư viện xử lý Excel -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>
<body>

<h2>📋 Bảng câu hỏi nhiều lựa chọn</h2>

<!-- Bộ lọc và tìm kiếm -->
<div class="mc-filter-container">
  <div class="search-box">
    <input type="search" id="mcSearchBox" placeholder="Tìm kiếm...">
  </div>
  <div class="filter-box">
    <label for="topicFilter">Chủ đề:</label>
    <select id="topicFilter">
      <option value="">-- Tất cả --</option>
    </select>
  </div>
</div>

<!-- Bảng dữ liệu -->
<div class="table-wrapper">
  <table id="mcTable" class="display nowrap" style="width:100%">
    <thead>
      <tr>
        <th>ID</th><th>Chủ đề</th><th>Câu hỏi</th>
        <th>A</th><th>B</th><th>C</th><th>D</th>
        <th>Đáp án đúng</th><th>Ảnh</th>
      </tr>
    </thead>
    <tbody>
      <!-- Dữ liệu sẽ được load bằng AJAX -->
    </tbody>
  </table>
</div>

<!-- Modal ảnh -->
<div id="imgModal" style="display:none; position:fixed;top:0;left:0;width:100%;height:100%;background:#000000bb;align-items:center;justify-content:center;z-index:1000;">
  <img id="imgModalContent" src="" style="max-width:90%;max-height:90%;border:4px solid white;box-shadow:0 0 10px white;">
</div>

<!-- Nhập file Excel -->
<input type="file" id="excelFile" accept=".xlsx" />

<!-- JS thư viện ngoài -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- JS xử lý riêng -->
<script src="../../js/table/mc_table_function.js"></script>
<script src="../../js/table/mc_table_image.js"></script>
<script src="../../js/table/mc_table_transmittion.js"></script>
<script src="../../js/table/mc_table_excel.js"></script>
<script src="../../js/table/mc_table_arrow.js"></script>

</body>
</html>
