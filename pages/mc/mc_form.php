<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="/css/main_ui.css" />
</head>
<body>
  <form id="mcForm" class="form-grid" enctype="multipart/form-data">
    <!-- KHU VỰC NHẬP LIỆU -->
    <div class="input-area">
      <h2>📝 Nhập câu hỏi</h2>

      <label for="mc_topic">Chủ đề</label>
      <input type="text" id="mc_topic" name="mc_topic" required />

      <label for="mc_question">Câu hỏi</label>
      <textarea id="mc_question" name="mc_question" rows="4" required></textarea>

      <div class="answer-group">
        <label for="mc_a">A</label>
        <input type="text" id="mc_a" name="mc_a" required />

        <label for="mc_b">B</label>
        <input type="text" id="mc_b" name="mc_b" required />

        <label for="mc_c">C</label>
        <input type="text" id="mc_c" name="mc_c" required />

        <label for="mc_d">D</label>
        <input type="text" id="mc_d" name="mc_d" required />
      </div>

      <div class="correct-answer">
        <label for="mc_answer">Đáp án đúng</label>
        <select id="mc_answer" name="mc_answer" required>
          <option value="">--Chọn--</option>
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="C">C</option>
          <option value="D">D</option>
        </select>
      </div>
    </div>

    <!-- KHU VỰC ẢNH MINH HỌA -->
    <div class="image-area">
      <h2>🖼️ Ảnh minh họa</h2>

      <div class="image-frame" id="imageFrame">
        <img id="mc_preview" src="#" alt="Xem trước ảnh" style="display:none;" />
      </div>

      <input type="file" id="mc_image" name="mc_image" accept="image/*" hidden />
      <div class="image-buttons">
        <button type="button" id="btnUpload">📤 Tải ảnh</button>
        <button type="button" id="btnRemove">❌ Xóa ảnh</button>
      </div>
    </div>

    <!-- KHU VỰC NÚT CHỨC NĂNG -->
    <div class="action-area">
      <button type="submit" id="btnSave">💾 Lưu</button>
      <button type="button" id="btnReset">🔄 Làm lại</button>
      <button type="button" id="btnDelete">🗑️ Xóa</button>
      <button type="button" id="btnList">📋 Xem bảng</button>
    </div>
  </form>

  <script src="/js/form/mc_form.js"></script>
</body>
</html>
