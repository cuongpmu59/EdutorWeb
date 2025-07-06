<?php require 'dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi Đúng/Sai</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/true_false_form_inner.css">
</head>
<body>
  <div class="true_false_form_inner">
    <form id="trueFalseForm" method="POST" action="insert_true_false_question.php">
      
      <!-- Chủ đề -->
      <label>📚 Chủ đề:</label>
      <input type="text" id="topic" placeholder="Nhập chủ đề..." required>

      <!-- Đề bài -->
      <label>Câu hỏi:</label>
      <textarea id="main_question" rows="3" placeholder="Nhập câu hỏi ..." required></textarea>

      <hr>

      <!-- 4 ý Đúng/Sai -->
      <?php for ($i = 1; $i <= 4; $i++): ?>
        <label><?= $i ?>.</label>
        <textarea id="statement<?= $i ?>" rows="2" placeholder="Nhập nội dung ý <?= $i ?>" required></textarea>

        <div class="radio-group">
          <label><input type="radio" name="correct_answer<?= $i ?>" value="1"> ✅ Đúng</label>
          <label style="margin-left: 20px;"><input type="radio" name="correct_answer<?= $i ?>" value="0" checked> ❌ Sai</label>
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

  <script>
  document.addEventListener("DOMContentLoaded", () => {
    const $ = id => document.getElementById(id);
    const topic = $("topic");
    const question = $("main_question");
    const form = $("trueFalseForm");

    // ===== 1. Khôi phục từ localStorage =====
    if (localStorage.getItem("true_false_topic")) topic.value = localStorage.getItem("true_false_topic");
    if (localStorage.getItem("true_false_main_question")) question.value = localStorage.getItem("true_false_main_question");

    for (let i = 1; i <= 4; i++) {
      const statement = $(`statement${i}`);
      const correct = localStorage.getItem(`correct_answer${i}`);

      if (localStorage.getItem(`statement${i}`)) {
        statement.value = localStorage.getItem(`statement${i}`);
      }

      if (correct !== null) {
        const radio = document.querySelector(`input[name=correct_answer${i}][value="${correct}"]`);
        if (radio) radio.checked = true;
      }
    }

    // ===== 2. Ghi localStorage khi người dùng nhập =====
    topic.addEventListener("input", () => {
      localStorage.setItem("true_false_topic", topic.value);
    });

    question.addEventListener("input", () => {
      localStorage.setItem("true_false_main_question", question.value);
    });

    for (let i = 1; i <= 4; i++) {
      const statement = $(`statement${i}`);
      statement.addEventListener("input", () => {
        localStorage.setItem(`statement${i}`, statement.value);
      });

      document.querySelectorAll(`input[name=correct_answer${i}]`).forEach(radio => {
        radio.addEventListener("change", () => {
          if (radio.checked) {
            localStorage.setItem(`correct_answer${i}`, radio.value);
          }
        });
      });
    }

    // ===== 3. Trước khi submit, đồng bộ hidden inputs =====
    form.addEventListener("submit", () => {
      $("hidden_topic").value = topic.value;
      $("hidden_question").value = question.value;

      for (let i = 1; i <= 4; i++) {
        $(`hidden_statement${i}`).value = $(`statement${i}`).value;
        const selected = document.querySelector(`input[name=correct_answer${i}]:checked`);
        $(`hidden_correct${i}`).value = selected ? selected.value : "0";
      }
    });
  });
  </script>
</body>
</html>
