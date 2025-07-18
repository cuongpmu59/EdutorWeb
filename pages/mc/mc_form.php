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
  <form class="mc-form" method="POST" enctype="multipart/form-data">
    <div class="mc-form-container">
      
      <!-- KHU VỰC NHẬP LIỆU -->
      <div class="mc-input-section">
        <h2>📝 Nhập câu hỏi</h2>

        <div class="form-group">
          <label for="mc_topic">Chủ đề:</label>
          <input type="text" id="mc_topic" name="mc_topic">
        </div>

        <div class="form-group">
          <label for="mc_question">Câu hỏi:</label>
          <textarea id="mc_question" name="mc_question" rows="4"></textarea>
        </div>

        <div class="form-group">
          <label for="mc_answerA">Đáp án A:</label>
          <input type="text" id="mc_answerA" name="mc_answerA">
        </div>

        <div class="form-group">
          <label for="mc_answerB">Đáp án B:</label>
          <input type="text" id="mc_answerB" name="mc_answerB">
        </div>

        <div class="form-group">
          <label for="mc_answerC">Đáp án C:</label>
          <input type="text" id="mc_answerC" name="mc_answerC">
        </div>

        <div class="form-group">
          <label for="mc_answerD">Đáp án D:</label>
          <input type="text" id="mc_answerD" name="mc_answerD">
        </div>

        <div class="form-group">
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

      <!-- KHU VỰC ẢNH -->
      <div class="mc-side-section">
        <div class="image-section">
          <h4>🖼️ Ảnh minh hoạ</h4>
          <img id="previewImage" src="" alt="Chưa có ảnh">
          <input type="file" id="mc_image" name="mc_image" accept="image/*" hidden>
          <div class="image-buttons">
            <label for="mc_image" class="upload-label">📤 Tải ảnh</label>
            <button type="button" class="delete-image-btn" id="btnDeleteImage">❌ Xoá ảnh</button>
          </div>
        </div>

        <!-- KHU VỰC NÚT -->
        <div class="button-group">
          <button type="submit" class="save-btn">💾 Lưu</button>
          <button type="reset" class="reset-btn">🔁 Làm lại</button>
          <button type="button" class="delete-btn">🗑️ Xoá</button>
          <button type="button" class="view-btn">📋 Xem danh sách</button>
        </div>
      </div>

    </div>
  </form>

  <script src="/js/form/mc_form.js"></script>
</body>
</html>
