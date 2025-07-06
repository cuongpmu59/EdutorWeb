<?php require 'dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi Đúng/Sai</title>
  <link rel="stylesheet" href="css/tru_false_form_inner.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
  <div class="true_false_form_inner">
    <form id="trueFalseForm" method="POST" action="insert_true_false_question.php">
      <!-- Chủ đề -->
      <label>📚 Chủ đề:</label>
      <input type="text" id="topic" class="form-control" placeholder="Nhập chủ đề..." required>

      <!-- Đề bài -->
      <label>🧠 Đề bài chính:</label>
      <textarea id="main_question" rows="3" placeholder="Nhập đề bài chính..." required></textarea>

      <hr>

      <!-- 4 ý Đúng/Sai -->
      <?php for ($i = 1; $i <= 4; $i++): ?>
        <label>Ý <?= $i ?>:</label>
        <textarea id="statement<?= $i ?>" rows="2" placeholder="Nhập nội dung ý <?= $i ?>" required></textarea>

        <div class="radio-group">
          <label><input type="radio" name="correct_answer<?= $i ?>" value="1"> ✅ Đúng</label>
          <label style="margin-left: 20px;"><input type="radio" name="correct_answer<?= $i ?>" value="0" checked> ❌ Sai</label>
        </div>
      <?php endfor; ?>

      <hr>

      <!-- Hidden fields -->
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

    // Khôi phục localStorage
    if (localStorage.getItem("true_false_topic")) $("topic").value = localStorage.getItem("true_false_topic");
    if (localStorage.getItem("true_false_main_question")) $("main_question").value = localStorage.getItem("true_false_main_question");

    for (let i = 1; i <= 4; i++) {
      if (localStorage.getItem("statement" + i)) $(`statement${i}`).value = localStorage.getItem("statement" + i);
      const correct = localStorage.getItem("correct_answer" + i);
      if (correct !== null) {
        const radio = document.querySelector(`input[name=correct_answer${i}][value="${correct}"]`);
        if (radio) radio.checked = true;
      }
    }

    // Cập nhật localStorage khi nhập
    $("topic").addEventListener("input", () => {
      localStorage.setItem("true_false_topic", $("topic").value);
    });
    $("main_question").addEventListener("input", () => {
      localStorage.setItem("true_false_main_question", $("main_question").value);
    });

    for (let i = 1; i <= 4; i++) {
      $(`statement${i}`).addEventListener("input", () => {
        localStorage.setItem("statement" + i, $(`statement${i}`).value);
      });
      document.querySelectorAll(`[name=correct_answer${i}]`).forEach(radio => {
        radio.addEventListener("change", () => {
          if (radio.checked) {
            localStorage.setItem("correct_answer" + i, radio.value);
          }
        });
      });
    }

    // Đồng bộ hidden fields trước khi submit
    $("trueFalseForm").addEventListener("submit", e => {
      $("hidden_topic").value = $("topic").value;
      $("hidden_question").value = $("main_question").value;
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
