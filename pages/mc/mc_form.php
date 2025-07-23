<?php require_once __DIR__ . '/../../includes/db_connection.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="../../css/form_ui.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" async></script>
</head>
<body>
  <form id="mcForm" action="../../includes/save.php" method="POST" enctype="multipart/form-data">
    <div class="mc-container">
      <!-- Cột trái: nhập liệu -->
      <div class="mc-left-column">
        <div class="mc-form-group">
          <label for="mc_topic">Chủ đề:</label>
          <input type="text" id="mc_topic" name="topic" required>
        </div>

        <div class="mc-form-group">
          <label for="mc_question">Câu hỏi:</label>
          <textarea id="mc_question" name="question" rows="3" required></textarea>
          <div class="mc-preview" data-preview="mc_question"></div>
        </div>

        <?php
          $labels = ['A', 'B', 'C', 'D'];
          for ($i = 1; $i <= 4; $i++):
        ?>
        <div class="mc-form-group">
          <label for="mc_answer<?= $i ?>">Đáp án <?= $labels[$i - 1] ?>:</label>
          <input type="text" id="mc_answer<?= $i ?>" name="answer<?= $i ?>" required>
          <div class="mc-preview" data-preview="mc_answer<?= $i ?>"></div>
        </div>
        <?php endfor; ?>

        <div class="mc-form-group">
          <label for="mc_correct_answer">Đáp án đúng:</label>
          <select id="mc_correct_answer" name="answer" required>
            <option value="">Chọn đáp án đúng</option>
            <option value="A">Đáp án A</option>
            <option value="B">Đáp án B</option>
            <option value="C">Đáp án C</option>
            <option value="D">Đáp án D</option>
          </select>
        </div>
      </div>

      <!-- Cột phải: ảnh + nút -->
      <div class="mc-right-column">
        <div class="mc-form-group">
          <label for="mc_image">Ảnh minh hoạ:</label>
          <input type="file" id="mc_image" name="image" accept="image/*">
          <input type="hidden" id="mc_existing_image" name="existing_image">
          <div class="mc-image-preview"></div>
        </div>

        <div class="mc-form-group">
          <label>Xem trước toàn bộ:</label>
          <div id="mc_full_preview" class="mc-preview-all"></div>
        </div>

        <div class="mc-button-group">
          <input type="hidden" name="mc_id" id="mc_id">
          <button type="submit" class="btn-save">💾 Lưu</button>
          <button type="reset" class="btn-reset">🔄 Làm lại</button>
        </div>
      </div>
    </div>

    <!-- Bảng câu hỏi (iframe) -->
    <div class="mc-table-wrapper">
      <iframe id="mcTableFrame" src="mc_table.php"></iframe>
    </div>
  </form>

  <!-- JavaScript -->
  <script src="../../js/form/mc_form.js"></script>
  <script src="../../js/form/mc_fetch.js"></script>
</body>
</html>
