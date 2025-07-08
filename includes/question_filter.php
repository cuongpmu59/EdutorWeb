// 📁 includes/shared_question_filter.php
<?php
$tableName = $tableName ?? 'questions';
$topics = [];
try {
    $stmtTopics = $conn->query("SELECT DISTINCT topic FROM `$tableName` WHERE topic IS NOT NULL AND topic != '' ORDER BY topic");
    $topics = $stmtTopics->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {}
?>

<select id="filterTopicInline">
  <option value="">-- Tất cả --</option>
  <?php foreach ($topics as $t): ?>
    <option value="<?= htmlspecialchars($t) ?>" <?= ($topicFilter === $t) ? 'selected' : '' ?>><?= htmlspecialchars($t) ?></option>
  <?php endforeach; ?>
</select>


// 📁 includes/question_import.php
<label><strong>📄 Nhập từ Excel:</strong></label>
<input type="file" id="excelInput" accept=".xlsx,.xls">
<br><br>
<button onclick="$('.buttons-excel').click()">📅 Xuất Excel</button>
<button onclick="$('.buttons-print').click()">🖨️ In bảng</button>


// 📁 includes/question_table.php
<table id="questionTable">
  <thead>
    <tr>
      <th>ID</th><th>Câu hỏi</th><th>A</th><th>B</th><th>C</th><th>D</th><th>Đúng</th><th>Chủ đề</th><th>Ảnh</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $q): ?>
    <tr>
      <td><?= $q['id'] ?></td>
      <td><?= htmlspecialchars($q['question']) ?></td>
      <td><?= htmlspecialchars($q['answer1'] ?? '') ?></td>
      <td><?= htmlspecialchars($q['answer2'] ?? '') ?></td>
      <td><?= htmlspecialchars($q['answer3'] ?? '') ?></td>
      <td><?= htmlspecialchars($q['answer4'] ?? '') ?></td>
      <td><?= htmlspecialchars($q['correct_answer']) ?></td>
      <td><?= htmlspecialchars($q['topic']) ?></td>
      <td>
        <?php if (!empty($q['image'])): ?>
        <img src="<?= htmlspecialchars($q['image']) ?>" class="thumb" onclick="showImage(this.src)" onerror="this.style.display='none'">
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<div id="previewArea" style="margin-top:20px;"><em>Chọn một câu hỏi để xem trước nội dung...</em></div>


// 📁 includes/question_modal.php
<div id="imageModal">
  <span onclick="closeModal()">&times;</span>
  <img id="modalImage" />
</div>
