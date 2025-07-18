<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="/css/mc/mc_form.css">
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>
  <div class="form-container">
    <!-- CỘT TRÁI -->
    <div class="form-left">
      <h2>
        Nhập câu hỏi trắc nghiệm
        <button class="preview-toggle" data-target="full"><span>👁️</span></button>
      </h2>

      <form id="questionForm">
        <!-- Chủ đề -->
        <div class="form-group">
          <label for="topic">Chủ đề:</label>
          <input type="text" id="topic" name="topic" required />
        </div>

        <!-- Câu hỏi -->
        <div class="form-group">
          <label for="question">Câu hỏi:
            <button class="preview-toggle" data-target="question"><span>👁️</span></button>
          </label>
          <textarea id="question" name="question" rows="4" required></textarea>
        </div>

        <!-- Đáp án -->
        <?php
        $answers = ['A', 'B', 'C', 'D'];
        foreach ($answers as $a) {
          echo <<<HTML
          <div class="form-group answer-row">
            <label for="answer$a">$a:
              <button class="preview-toggle" data-target="answer$a"><span>👁️</span></button>
            </label>
            <input type="text" id="answer$a" name="answer$a" required />
            <input type="radio" name="correct" value="$a" required />
          </div>
          HTML;
        }
        ?>

        <!-- Nhóm nút chức năng chính -->
        <div class="button-group">
          <button type="submit" id="saveBtn">💾 Lưu</button>
          <button type="reset">🔄 Làm lại</button>
          <button type="button" id="deleteBtn">🗑️ Xoá</button>
          <a href="mc_table.php" target="_blank">📋 Xem bảng</a>
        </div>
      </form>

      <!-- Khung xem trước -->
      <div class="preview-box" id="previewBox" style="display:none;">
        <h3>Xem trước</h3>
        <div id="previewContent"></div>
      </div>
    </div>

    <!-- CỘT PHẢI -->
    <div class="form-right">
      <h3>🖼️ Ảnh minh hoạ</h3>

      <div class="image-frame">
        <img id="previewImage" src="" alt="Chưa có ảnh" />
      </div>

      <div class="image-buttons">
        <label for="imageInput" class="image-upload">
          📂 Chọn ảnh
        </label>
        <input type="file" id="imageInput" name="image" accept="image/*" hidden />

        <button type="button" id="removeImageBtn">❌ Xoá ảnh</button>
      </div>
    </div>
  </div>

  <script src="/js/mc/mc_form.js"></script>
</body>
</html>
