<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="/css/mc_form.css">
</head>
<body>
  <div class="form-layout">
    <!-- CỘT TRÁI: FORM NHẬP -->
    <div class="form-left">
      <h2>Nhập câu hỏi</h2>
      <label>Chủ đề:</label>
      <input type="text" name="topic" id="topic" placeholder="Chủ đề:">

      <label>Câu hỏi:</label>
      <textarea name="question" id="question" rows="4" placeholder="Câu hỏi:"></textarea>

      <label>Đáp án A:</label>
      <input type="text" name="answer_a" id="answer_a" placeholder="A.">

      <label>Đáp án B:</label>
      <input type="text" name="answer_b" id="answer_b" placeholder="B.">

      <label>Đáp án C:</label>
      <input type="text" name="answer_c" id="answer_c" placeholder="C.">

      <label>Đáp án D:</label>
      <input type="text" name="answer_d" id="answer_d" placeholder="D.">

      <label>Đáp án đúng:</label>
      <select name="correct" id="correct">
        <option value="A">A</option>
        <option value="B">B</option>
        <option value="C">C</option>
        <option value="D">D</option>
      </select>
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
        <button onclick="saveQuestion()">💾 Lưu câu hỏi</button>
        <button onclick="resetForm()">🔄 Làm lại</button>
        <button onclick="deleteQuestion()">🗑️ Xoá câu hỏi</button>
        <button onclick="openTable()">📋 Xem bảng</button>
      </div>
    </div>
  </div>

  <script src="/js/mc_form.js"></script>
</body>
</html>
