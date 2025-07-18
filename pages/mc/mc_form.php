<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="/css/main_ui.css">
</head>
<body>
  <div class="form-layout">
    
    <!-- ==== FORM TRÁI ==== -->
    <div class="form-left">
      <h2>📝 Nhập câu hỏi</h2>

      <div class="form-group">
        <label for="topic">Chủ đề:</label>
        <input type="text" id="topic" name="topic">
      </div>

      <div class="form-group">
        <label for="question">Câu hỏi:</label>
        <textarea id="question" name="question" rows="3"></textarea>
      </div>

      <div class="form-group">
        <label for="optionA">Đáp án A:</label>
        <input type="text" id="optionA" name="optionA">
      </div>

      <div class="form-group">
        <label for="optionB">Đáp án B:</label>
        <input type="text" id="optionB" name="optionB">
      </div>

      <div class="form-group">
        <label for="optionC">Đáp án C:</label>
        <input type="text" id="optionC" name="optionC">
      </div>

      <div class="form-group">
        <label for="optionD">Đáp án D:</label>
        <input type="text" id="optionD" name="optionD">
      </div>

      <div class="form-group">
        <label for="correct">Đáp án đúng:</label>
        <select id="correct" name="correct">
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="C">C</option>
          <option value="D">D</option>
        </select>
      </div>
    </div>

    <!-- ==== FORM PHẢI: ẢNH + NÚT ==== -->
    <div class="form-right">
      <div class="image-wrapper">
        <div class="image-box" id="imagePreview">
          <span>Ảnh minh họa</span>
        </div>
        <input type="file" id="imageInput" accept="image/*">
        <button type="button" class="upload-btn" onclick="document.getElementById('imageInput').click();">
          📷 Tải ảnh
        </button>
      </div>

      <div class="button-group">
        <button type="button" class="save-btn">💾 Lưu</button>
        <button type="button" class="reset-btn">🔄 Làm mới</button>
        <button type="button" class="delete-btn">🗑️ Xóa</button>
        <button type="button" class="export-btn">📤 Xuất PDF</button>
      </div>
    </div>

  </div>
</body>
</html>
