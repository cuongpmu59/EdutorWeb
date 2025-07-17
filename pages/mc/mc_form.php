<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="../../css/form.css">
</head>
<body>
  <div class="form-container">
    <h2>Nhập câu hỏi trắc nghiệm</h2>
    <form id="mcForm" method="post" enctype="multipart/form-data">
      <div class="form-layout">
        <!-- Cột trái -->
        <div class="form-left">
          <div class="form-group">
            <label for="mc_topic">Chủ đề</label>
            <input type="text" id="mc_topic" name="mc_topic" required>
          </div>
          <div class="form-group">
            <label for="mc_question">Câu hỏi</label>
            <textarea id="mc_question" name="mc_question" rows="3" required></textarea>
          </div>
          <div class="form-group">
            <label for="mc_optionA">A.</label>
            <input type="text" id="mc_optionA" name="mc_optionA" required>
          </div>
          <div class="form-group">
            <label for="mc_optionB">B.</label>
            <input type="text" id="mc_optionB" name="mc_optionB" required>
          </div>
          <div class="form-group">
            <label for="mc_optionC">C.</label>
            <input type="text" id="mc_optionC" name="mc_optionC" required>
          </div>
          <div class="form-group">
            <label for="mc_optionD">D.</label>
            <input type="text" id="mc_optionD" name="mc_optionD" required>
          </div>
          <div class="form-group">
            <label for="mc_answer">Đáp án đúng</label>
            <select id="mc_answer" name="mc_answer" required>
              <option value="">-- Chọn đáp án --</option>
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
              <option value="D">D</option>
            </select>
          </div>
        </div>

        <!-- Cột phải -->
        <div class="form-right">
          <div class="image-group">
            <label>Ảnh minh họa</label>
            <div style="display: flex; gap: 10px;">
              <button type="button" class="btn-upload" onclick="document.getElementById('mc_image').click();">📷 Tải ảnh</button>
              <button type="button" class="btn-delete" onclick="deleteImage()">🗑️ Xoá ảnh</button>
            </div>
            <input type="file" id="mc_image" name="mc_image" accept="image/*" style="display:none;">
            <img id="imagePreview" class="image-preview" src="#" alt="Ảnh minh họa" style="display:none;">
          </div>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-save">💾 Lưu câu hỏi</button>
        <button type="reset" class="btn-reset">🔄 Làm lại</button>
      </div>
    </form>
  </div>

  <script>
    // Preview ảnh
    document.getElementById('mc_image').addEventListener('change', function () {
      const preview = document.getElementById('imagePreview');
      const file = this.files[0];
      if (file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
      } else {
        preview.src = '#';
        preview.style.display = 'none';
      }
    });

    function deleteImage() {
      const input = document.getElementById('mc_image');
      const preview = document.getElementById('imagePreview');
      input.value = '';
      preview.src = '#';
      preview.style.display = 'none';
    }
  </script>
</body>
</html>
