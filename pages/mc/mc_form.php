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
  <form id="mcForm">
    <div class="form-grid">
      <!-- CỘT TRÁI: KHU NHẬP LIỆU -->
      <div class="column column-left">
        <div class="form-group">
          <label for="mc_topic">Chủ đề:</label>
          <input type="text" id="mc_topic" name="mc_topic" required>
        </div>

        <div class="form-group">
          <label for="mc_question">Câu hỏi:</label>
          <textarea id="mc_question" name="mc_question" rows="4" required></textarea>
        </div>

        <div class="form-group">
          <label>Đáp án A:</label>
          <input type="text" name="mc_option_a" required>
        </div>
        <div class="form-group">
          <label>Đáp án B:</label>
          <input type="text" name="mc_option_b" required>
        </div>
        <div class="form-group">
          <label>Đáp án C:</label>
          <input type="text" name="mc_option_c" required>
        </div>
        <div class="form-group">
          <label>Đáp án D:</label>
          <input type="text" name="mc_option_d" required>
        </div>

        <div class="form-group-inline">
          <label for="mc_answer">Đáp án đúng:</label>
          <select id="mc_answer" name="mc_answer" required>
            <option value="">--Chọn--</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
          </select>
        </div>
      </div>

      <!-- CỘT PHẢI: ẢNH VÀ NÚT -->
      <div class="column column-right">
        <div class="image-section">
          <div class="image-box" id="imagePreview">Ảnh minh họa</div>
          <input type="file" id="mc_image" name="mc_image" accept="image/*" hidden>
          <div class="image-buttons">
            <button type="button" onclick="document.getElementById('mc_image').click()">🖼️ Tải ảnh</button>
            <button type="button" onclick="removeImage()">❌ Xoá ảnh</button>
          </div>
        </div>

        <div class="button-group">
          <button type="submit">💾 Lưu</button>
          <button type="button" onclick="resetForm()">🔁 Làm lại</button>
          <button type="button" onclick="deleteQuestion()">🗑️ Xoá</button>
          <button type="button" onclick="viewList()">📋 Danh sách</button>
        </div>
      </div>
    </div>
  </form>

  <script src="/js/form/mc_form.js"></script>
</body>
</html>
