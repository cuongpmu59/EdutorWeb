<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="/css/main_ui.css">
  <link rel="stylesheet" href="/css/form/mc_form.css">
</head>
<body>

  <form class="form-container" method="post" enctype="multipart/form-data">
    <!-- Cột trái: Khu vực nhập liệu -->
    <div class="column-left">
      <div class="input-section">
        <h2>📝 Nhập câu hỏi</h2>

        <div class="input-group">
          <label for="mc_topic">Chủ đề:</label>
          <input type="text" id="mc_topic" name="mc_topic">
        </div>

        <div class="input-group">
          <label for="mc_question">Câu hỏi:</label>
          <textarea id="mc_question" name="mc_question" rows="4"></textarea>
        </div>

        <div class="answer-row">
          <label for="mc_answerA">A:</label>
          <input type="text" id="mc_answerA" name="mc_answerA">
        </div>

        <div class="answer-row">
          <label for="mc_answerB">B:</label>
          <input type="text" id="mc_answerB" name="mc_answerB">
        </div>

        <div class="answer-row">
          <label for="mc_answerC">C:</label>
          <input type="text" id="mc_answerC" name="mc_answerC">
        </div>

        <div class="answer-row">
          <label for="mc_answerD">D:</label>
          <input type="text" id="mc_answerD" name="mc_answerD">
        </div>

        <div class="input-group">
          <label for="mc_correct">Đáp án đúng:</label>
          <select id="mc_correct" name="mc_correct">
            <option value="">-- Chọn --</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Cột phải: Khu vực ảnh và nút -->
    <div class="column-right">
      <!-- Ảnh minh hoạ -->
      <div class="image-section">
        <h2>🖼️ Ảnh minh hoạ</h2>
        <div class="image-box">
          <img id="previewImage" src="" alt="Chưa có ảnh">
        </div>
        <input type="file" id="imageInput" name="mc_image" accept="image/*">
        <div class="image-buttons">
          <button type="button" id="btnUploadImage">📤 Tải ảnh</button>
          <button type="button" id="btnDeleteImage">❌ Xoá ảnh</button>
        </div>
      </div>

      <!-- Nhóm nút -->
      <div class="button-section">
        <div class="button-group">
          <button type="submit" id="btnSave">💾 Lưu</button>
          <button type="reset" id="btnReset">🔁 Làm lại</button>
          <button type="button" id="btnDelete">🗑️ Xoá</button>
          <button type="button" id="btnList">📋 Danh sách</button>
        </div>
      </div>
    </div>
  </form>

  <script src="/js/form/mc_form.js"></script>
</body>
</html>
