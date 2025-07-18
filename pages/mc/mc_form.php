<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="/css/main_ui.css">
</head>
<body>
  <div class="mc-container">
    <!-- CỘT TRÁI: FORM CÂU HỎI -->
    <form id="questionForm" class="mc-form">
      <h2>📄 Nhập câu hỏi</h2>

      <div class="form-group">
        <label for="topic">Chủ đề:</label>
        <input type="text" id="topic" name="topic" required>
      </div>

      <div class="form-group">
        <label for="question">Câu hỏi:</label>
        <textarea id="question" name="question" rows="3" required></textarea>
      </div>

      <div class="form-group answer-group">
        <label for="answerA">Đáp án A:</label>
        <input type="text" id="answerA" name="answerA" required>
      </div>

      <div class="form-group answer-group">
        <label for="answerB">Đáp án B:</label>
        <input type="text" id="answerB" name="answerB" required>
      </div>

      <div class="form-group answer-group">
        <label for="answerC">Đáp án C:</label>
        <input type="text" id="answerC" name="answerC" required>
      </div>

      <div class="form-group answer-group">
        <label for="answerD">Đáp án D:</label>
        <input type="text" id="answerD" name="answerD" required>
      </div>

      <div class="form-group">
        <label for="correct">Đáp án đúng:</label>
        <select id="correct" name="correct" required>
          <option value="">--Chọn--</option>
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="C">C</option>
          <option value="D">D</option>
        </select>
      </div>
    </form>

    <!-- CỘT PHẢI: ẢNH MINH HOẠ + NÚT -->
    <div class="mc-side-panel">
      <!-- KHUNG ẢNH -->
      <div class="image-preview-container">
        <label>🖼️ Ảnh minh hoạ:</label>
        <input type="file" id="imageInput" accept="image/*">
        <div class="image-box">
          <img id="previewImage" src="" alt="Ảnh minh hoạ" style="max-width: 100%; max-height: 100%;">
        </div>
        <button type="button" id="removeImageBtn" class="small-button">Xoá ảnh</button>
      </div>

      <!-- NÚT CHỨC NĂNG -->
      <div class="form-buttons">
        <button type="submit" form="questionForm">💾 Lưu</button>
        <button type="reset" form="questionForm">🧹 Làm lại</button>
        <button type="button" id="deleteBtn">🗑️ Xoá</button>
        <button type="button" id="exportBtn">📤 Xuất</button>
      </div>
    </div>
  </div>

  <script src="/js/form/mc_form.js"></script>
</body>
</html>
