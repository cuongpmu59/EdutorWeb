<?php
require __DIR__ . '/../../db_connection.php';
if (!isset($conn)) {
  die("❌ Không thể kết nối CSDL. Kiểm tra db_connection.php");
}
header("X-Frame-Options: SAMEORIGIN");

// Lấy danh sách chủ đề
$topics = [];
try {
  $stmtTopics = $conn->query("SELECT DISTINCT mc_topic FROM mc_questions WHERE mc_topic IS NOT NULL AND mc_topic != '' ORDER BY mc_topic");
  $topics = $stmtTopics->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {}

// Lọc chủ đề nếu có
$topicFilter = $_GET['topic'] ?? '';
try {
  $sql = $topicFilter !== ''
    ? "SELECT * FROM mc_questions WHERE mc_topic = :topic ORDER BY mc_id DESC"
    : "SELECT * FROM mc_questions ORDER BY mc_id DESC";
  $stmt = $conn->prepare($sql);
  $topicFilter !== '' ? $stmt->execute(['topic' => $topicFilter]) : $stmt->execute();
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
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.dataTables.min.css">
  <link rel="stylesheet" href="../../css/main_ui.css">
  <link rel="stylesheet" href="../../css/modules/table.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
  <style>
    .thumb {
      max-width: 50px;
      max-height: 50px;
      cursor: pointer;
    }
    #mcTable tbody tr.selected {
      background-color: #e0f7fa !important;
    }
    .toolbar-top {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      margin-bottom: 10px;
      gap: 10px;
    }
  </style>
</head>
<body>

<h2>📋 Bảng câu hỏi nhiều lựa chọn</h2>

<div class="toolbar-top">
  <div class="left-tools">
    <button id="btnAddQuestion">➕ Thêm câu hỏi</button>
    <button id="btnReloadTable" onclick="location.reload()">🔄 Làm mới</button>
  </div>
  <div class="right-tools">
    <label>📚 Chủ đề:
      <select id="topicSelect">
        <option value="">-- Tất cả --</option>
        <?php foreach ($topics as $tp): ?>
          <option value="<?= htmlspecialchars($tp) ?>" <?= $tp === $topicFilter ? 'selected' : '' ?>>
            <?= htmlspecialchars($tp) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <button id="btnExportExcel">⬇️ Xuất Excel</button>
    <button id="btnPrintTable">🖨️ In bảng</button>
  </div>
</div>

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
          <td><?= $q['mc_id'] ?></td>
          <td><?= htmlspecialchars($q['mc_topic']) ?></td>
          <td><?= $q['mc_question'] ?></td>
          <td><?= $q['mc_answer1'] ?></td>
          <td><?= $q['mc_answer2'] ?></td>
          <td><?= $q['mc_answer3'] ?></td>
          <td><?= $q['mc_answer4'] ?></td>
          <td><?= htmlspecialchars($q['mc_correct_answer']) ?></td>
          <td>
            <?php if (!empty($q['mc_image_url'])): ?>
              <img src="<?= htmlspecialchars($q['mc_image_url']) ?>" class="thumb" onerror="this.style.display='none'">
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>

<!-- File table.js gộp cả chuyển dòng và lọc -->
<script src="../../js/table/table.js"></script>
</body>
</html>
