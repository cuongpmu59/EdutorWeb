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
      <!-- Cột trái -->
      <div class="mc-left-column">
        <div class="mc-form-group">
          <label for="mc_topic">Chủ đề:</label>
          <input type="text" id="mc_topic" name="topic" required>
        </div>

        <div class="mc-form-group mc-inline">
          <label for="mc_question">Câu hỏi:</label>
          <span class="mc-eye-icon" data-toggle="mc_question">👁</span>
        </div>
        <textarea id="mc_question" name="question" rows="3" required></textarea>
        <div class="mc-preview" data-preview="mc_question"></div>

        <?php
          $labels = ['A', 'B', 'C', 'D'];
          for ($i = 1; $i <= 4; $i++): ?>
          <div class="mc-form-group mc-inline">
            <label for="mc_answer<?= $i ?>">Đáp án <?= $labels[$i - 1] ?>:</label>
            <span class="mc-eye-icon" data-toggle="mc_answer<?= $i ?>">👁</span>
          </div>
          <input type="text" id="mc_answer<?= $i ?>" name="answer<?= $i ?>" required>
          <div class="mc-preview" data-preview="mc_answer<?= $i ?>"></div>
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

      <!-- Cột phải -->
      <div class="mc-right-column">
        <div class="mc-form-group">
          <label for="mc_image">Ảnh minh hoạ:</label>
          <input type="file" id="mc_image" name="image" accept="image/*">
          <input type="hidden" id="mc_existing_image" name="existing_image">
          <div class="mc-image-preview"></div>
        </div>

        <div class="mc-form-group mc-inline">
          <label>Xem trước toàn bộ:</label>
          <span class="mc-eye-toggle-all">👁</span>
        </div>
        <div id="mc_full_preview" class="mc-preview-all"></div>

        <div class="mc-button-group">
          <input type="hidden" name="mc_id" id="mc_id">
          <button type="submit" class="btn-save">💾 Lưu</button>
          <button type="reset" class="btn-reset">🔄 Làm lại</button>
          <button type="button" class="btn-delete" id="btnDelete">🗑 Xoá</button>
          <button type="button" class="btn-toggle-table" id="btnToggleTable">📋 Ẩn/Hiện danh sách</button>
        </div>
      </div>
    </div>

    <!-- iframe danh sách -->
    <div class="mc-table-wrapper" id="mcTableWrapper">
      <iframe id="mcTableFrame" src="mc_table.php"></iframe>
    </div>
  </form>

  <!-- Scripts -->
  <script src="../../js/form/mc_form.js"></script>
  <script src="../../js/form/mc_fetch.js"></script>
  <script>
    // Xoá câu hỏi
    document.getElementById('btnDelete').addEventListener('click', function () {
      const id = document.getElementById('mc_id').value;
      if (id && confirm('Bạn có chắc muốn xoá câu hỏi này?')) {
        window.location.href = '../../includes/delete.php?mc_id=' + id;
      }
    });

    // Ẩn/hiện iframe danh sách
    document.getElementById('btnToggleTable').addEventListener('click', function () {
      const wrapper = document.getElementById('mcTableWrapper');
      wrapper.style.display = (wrapper.style.display === 'none') ? 'block' : 'none';
    });
  </script>
</body>
</html>
