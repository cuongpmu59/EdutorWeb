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
    <!-- CỘT TRÁI: FORM NHẬP -->
    <div class="form-left">
      <h2>Nhập câu hỏi</h2>

      <label for="topic">Chủ đề:</label>
      <input type="text" name="topic" id="topic" placeholder="Chủ đề:">

      <label for="question">Câu hỏi:</label>
      <textarea name="question" id="question" rows="4" placeholder="Câu hỏi:"></textarea>

      <div class="answer-group">
        <label for="answer_a">A.</label>
        <input type="text" id="answer_a" name="answer_a" required>
      </div>

      <div class="answer-group">
        <label for="answer_b">B.</label>
        <input type="text" id="answer_b" name="answer_b" required>
      </div>

      <div class="answer-group">
        <label for="answer_c">C.</label>
        <input type="text" id="answer_c" name="answer_c" required>
      </div>

      <div class="answer-group">
        <label for="answer_d">D.</label>
        <input type="text" id="answer_d" name="answer_d" required>
      </div>

      <div class="correct-answer-group">
        <label for="correct_answer">Đáp án:</label>
        <select id="correct_answer" name="correct_answer" required>
          <option value="">-- Chọn --</option>
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="C">C</option>
          <option value="D">D</option>
        </select>
      </div>
    </div>

    <!-- CỘT PHẢI: HÌNH ẢNH + NÚT -->
    <div class="form-right">
      <!-- ẢNH MINH HOẠ -->
      <div class="image-preview" id="imageBox">
        <img id="previewImage" src="" alt="Ảnh minh hoạ" style="display:none;">
      </div>

      <!-- NÚT TẢI ẢNH + XOÁ ẢNH -->
      <div class="row-buttons">
        <input type="file" id="imageInput" hidden>
        <button onclick="document.getElementById('imageInput').click()">📷 Tải ảnh</button>
        <button onclick="deleteImage()">❌ Xoá ảnh</button>
      </div>

      <!-- CÁC NÚT CHÍNH -->
      <div class="column-buttons">
        <button class="btn-save" onclick="saveQuestion()">💾 Lưu câu hỏi</button>
        <button class="btn-reset" onclick="resetForm()">🔄 Làm lại</button>
        <button class="btn-delete" onclick="deleteQuestion()">🗑️ Xoá câu hỏi</button>
        <button class="btn-export" onclick="openTable()">📋 Xem bảng</button>
      </div>
    </div>
  </div>

  <script src="/js/mc_form.js"></script>
</body>
</html>
