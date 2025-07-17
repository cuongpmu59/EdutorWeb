<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="/css/form.css">
</head>
<body>
  <div class="form-wrapper">
    <div class="form-left">
      <h2>Nhập câu hỏi</h2>
      <form id="mcForm" enctype="multipart/form-data">
        <label for="mc_topic">Chủ đề:</label>
        <input type="text" id="mc_topic" name="mc_topic" required>

        <label for="mc_question">Câu hỏi:</label>
        <textarea id="mc_question" name="mc_question" rows="4" required></textarea>

        <label for="mc_a">A:</label>
        <input type="text" id="mc_a" name="mc_a" required>

        <label for="mc_b">B:</label>
        <input type="text" id="mc_b" name="mc_b" required>

        <label for="mc_c">C:</label>
        <input type="text" id="mc_c" name="mc_c" required>

        <label for="mc_d">D:</label>
        <input type="text" id="mc_d" name="mc_d" required>

        <label for="mc_answer">Đáp án đúng:</label>
        <select id="mc_answer" name="mc_answer" required>
          <option value="">-- Chọn --</option>
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="C">C</option>
          <option value="D">D</option>
        </select>

        <label for="mc_note">Ghi chú (nếu có):</label>
        <textarea id="mc_note" name="mc_note" rows="3"></textarea>

        <input type="hidden" id="mc_id" name="mc_id">
      </form>

      <div class="action-buttons">
        <button id="btnSave">💾 Lưu</button>
        <button id="btnReset">🔄 Làm lại</button>
        <button id="btnDelete">🗑️ Xoá câu hỏi</button>
        <button id="btnView">📋 Xem bảng</button>
      </div>
    </div>

    <div class="form-right">
      <h3>🖼️ Ảnh minh hoạ</h3>
      <div class="image-frame" id="imagePreview">
        <img id="mc_preview_img" src="" alt="Chưa có ảnh" />
      </div>

      <div class="image-controls">
        <label for="mc_image" class="upload-label">📤 Tải ảnh</label>
        <input type="file" id="mc_image" name="mc_image" accept="image/*" hidden>
        <button type="button" id="btnRemoveImage">❌ Xoá ảnh</button>
      </div>
    </div>
  </div>

  <script src="/js/modules/previewView.js"></script>
</body>
</html>
