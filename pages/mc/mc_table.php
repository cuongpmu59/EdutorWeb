<?php
require 'db_connection.php';
header("X-Frame-Options: SAMEORIGIN");

// Lấy danh sách chủ đề
$topics = [];
try {
    $stmtTopics = $conn->query("SELECT DISTINCT mc_topic FROM questions WHERE mc_topic IS NOT NULL AND mc_topic != '' ORDER BY mc_topic");
    $topics = $stmtTopics->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {}

$topicFilter = $_GET['topic'] ?? '';
try {
    $sql = $topicFilter !== ''
        ? "SELECT * FROM questions WHERE mc_topic = :topic ORDER BY mc_id DESC"
        : "SELECT * FROM questions ORDER BY mc_id DESC";
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
  <link rel="stylesheet" href="css/modules/table.css"> <!-- ✅ CSS riêng -->

  <style>
    /* Hiển thị cảnh báo nếu truy cập trực tiếp */
    #directWarning {
      display: none;
      padding: 60px;
      text-align: center;
      font-size: 18px;
      color: #c0392b;
      font-weight: bold;
    }
  </style>
</head>
<body>

<div id="directWarning">⛔ Trang này chỉ hoạt động trong hệ thống quản lý. Vui lòng không truy cập trực tiếp.</div>

<div id="mcTableWrapper" style="display:none">
  <h2>📋 Bảng câu hỏi nhiều lựa chọn</h2>

  <!-- Tabs -->
  <div class="tab-container">
    <button class="tab-button active" data-tab="filterTab">🔍 Bộ lọc</button>
    <button class="tab-button" data-tab="importTab">📁 Nhập / Xuất</button>
    <button class="tab-button" data-tab="listTab">📄 Danh sách</button>
    <button class="tab-button" data-tab="otherTab">⚙️ Khác</button>
  </div>

  <!-- Tab: Bộ lọc -->
  <div id="filterTab" class="tab-content active">
    <label><strong>🔍 Lọc theo chủ đề:</strong></label>
    <select id="filterTopic">
      <option value="">-- Tất cả --</option>
      <?php foreach ($topics as $t): ?>
        <option value="<?= htmlspecialchars($t) ?>" <?= $topicFilter === $t ? 'selected' : '' ?>>
          <?= htmlspecialchars($t) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <!-- Tab: Nhập / Xuất -->
  <div id="importTab" class="tab-content">
    <label><strong>📤 Nhập từ Excel:</strong></label>
    <input type="file" id="excelInput" accept=".xlsx,.xls">
    <br><br>
    <button onclick="$('.buttons-excel').click()">📥 Xuất Excel</button>
    <button onclick="$('.buttons-print').click()">🖨️ In bảng</button>
  </div>

  <!-- Tab: Danh sách -->
  <div id="listTab" class="tab-content">
    <table id="mcTable" class="display">
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
          <td><?= htmlspecialchars($q['mc_question']) ?></td>
          <td><?= htmlspecialchars($q['mc_answer1']) ?></td>
          <td><?= htmlspecialchars($q['mc_answer2']) ?></td>
          <td><?= htmlspecialchars($q['mc_answer3']) ?></td>
          <td><?= htmlspecialchars($q['mc_answer4']) ?></td>
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

  <!-- Tab: Khác -->
  <div id="otherTab" class="tab-content">
    <em>🔧 Các chức năng bổ sung sẽ cập nhật sau...</em>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="js/table/mc_table.js"></script>

<!-- script> -->
  // Nếu không mở trong iframe, hiển thị cảnh báo
//if (window.top === window.self) {
 //   document.getElementById("directWarning").style.display = "block";
 // } else {
 //   document.getElementById("mcTableWrapper").style.display = "block";
//  }
<!-- </script> -->
</body>
</html>
