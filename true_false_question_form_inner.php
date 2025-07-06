<?php require 'dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi Đúng/Sai</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- CSS giao diện -->
  <link rel="stylesheet" href="css/true_false_form_inner.css">
</head>
<body>
  <div class="true_false_form_inner">
    <form id="trueFalseForm" method="POST" action="insert_true_false_question.php">
      
      <!-- Chủ đề -->
      <label>📚 Chủ đề:</label>
      <input type="text" id="topic" placeholder="Nhập chủ đề..." required>

      <!-- Đề bài -->
      <label>🧠 Đề bài chính:</label>
      <textarea id="main_question" rows="3" placeholder="Nhập đề bài chính..." required></textarea>

      <hr>

      <!-- 4 ý Đúng/Sai -->
      <?php for ($i = 1; $i <= 4; $i++): ?>
        <div class="statement-block">
          <label>Ý <?= $i ?>:</label>
          <textarea id="statement<?= $i ?>" rows="2" placeholder="Nhập nội dung ý <?= $i ?>" required></textarea>

          <div class="radio-group">
            <label><input type="radio" name="correct_answer<?= $i ?>" value="1"> ✅ Đúng</label>
            <label><input type="radio" name="correct_answer<?= $i ?>" value="0" checked> ❌ Sai</label>
          </div>
        </div>
      <?php endfor; ?>

      <hr>

      <!-- Hidden fields để submit -->
      <input type="hidden" name="topic" id="hidden_topic">
      <input type="hidden" name="main_question" id="hidden_question">
      <?php for ($i = 1; $i <= 4; $i++): ?>
        <input type="hidden" name="statement<?= $i ?>" id="hidden_statement<?= $i ?>">
        <input type="hidden" name="correct_answer<?= $i ?>" id="hidden_correct<?= $i ?>">
      <?php endfor; ?>

      <button type="submit">💾 Lưu câu hỏi</button>
    </form>
  </div>

  <!-- JavaScript xử lý logic -->
  <script src="js/true_false_form_inner.js"></script>
</body>
</html>
