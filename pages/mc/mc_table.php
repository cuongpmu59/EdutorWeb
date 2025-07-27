<?php
require_once __DIR__ . '/../../includes/db_connection.php';
if (!isset($conn)) {
  die("❌ Không thể kết nối CSDL. Kiểm tra db_connection.php");
}
header("X-Frame-Options: SAMEORIGIN");

try {
  $stmt = $conn->prepare("SELECT * FROM mc_questions ORDER BY mc_id DESC");
  $stmt->execute();
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $rows = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📋 Câu hỏi Nhiều lựa chọn</title>

  <!-- Thư viện CSS ngoài -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

  <!-- CSS giao diện -->
  <link rel="stylesheet" href="../../css/table/mc_table.css">
  <link rel="stylesheet" href="../../css/table/mc_filter.css">


  <!-- MathJax -->
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

  <!-- Excel xử lý -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>
<body>

<h2>📋 Bảng câu hỏi nhiều lựa chọn</h2>

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
      <?php foreach ($rows as $q): ?>
        <tr>
          <td data-raw="<?= $q['mc_id'] ?>"><?= $q['mc_id'] ?></td>
          <td data-raw="<?= htmlspecialchars($q['mc_topic']) ?>"><?= htmlspecialchars($q['mc_topic']) ?></td>
          <td data-raw="<?= htmlspecialchars($q['mc_question']) ?>"><?= htmlspecialchars($q['mc_question']) ?></td>
          <td data-raw="<?= htmlspecialchars($q['mc_answer1']) ?>"><?= htmlspecialchars($q['mc_answer1']) ?></td>
          <td data-raw="<?= htmlspecialchars($q['mc_answer2']) ?>"><?= htmlspecialchars($q['mc_answer2']) ?></td>
          <td data-raw="<?= htmlspecialchars($q['mc_answer3']) ?>"><?= htmlspecialchars($q['mc_answer3']) ?></td>
          <td data-raw="<?= htmlspecialchars($q['mc_answer4']) ?>"><?= htmlspecialchars($q['mc_answer4']) ?></td>
          <td data-raw="<?= htmlspecialchars($q['mc_correct_answer']) ?>"><?= htmlspecialchars($q['mc_correct_answer']) ?></td>
          <td data-raw="<?= htmlspecialchars($q['mc_image_url']) ?>">
            <?php if (!empty($q['mc_image_url'])): ?>
              <img src="<?= htmlspecialchars($q['mc_image_url']) ?>" class="thumb" onerror="this.style.display='none'">
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Modal ảnh -->
<div id="imgModal" style="display:none; position:fixed;top:0;left:0;width:100%;height:100%;background:#000000bb;align-items:center;justify-content:center;z-index:1000;">
  <img id="imgModalContent" src="" style="max-width:90%;max-height:90%;border:4px solid white;box-shadow:0 0 10px white;">
</div>

<!-- File Excel -->
<input type="file" id="excelFile" accept=".xlsx" />

<!-- Thư viện JS ngoài -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- Script chính -->
<!-- <script src="../../js/table/mc_table_function.js"></script> -->
<script src="../../js/table/mc_table_transmittion.js"></script>
<script src="../../js/table/mc_table_image.js"></script>
<script src="../../js/table/mc_table_excel.js"></script>
<script src="../../js/table/mc_table_arrow.js"></script>
<!-- <script src="../../js/table/mc_table_filter.js"></script> -->


</body>
</html>
