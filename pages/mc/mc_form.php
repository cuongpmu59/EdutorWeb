<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm nhiều lựa chọn</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f6f8fa;
      padding: 20px;
      margin: 0;
      color: #333;
    }

    .form-container {
      max-width: 1100px;
      background: #fff;
      padding: 25px;
      margin: auto;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .form-title {
      font-size: 22px;
      font-weight: bold;
      margin-bottom: 10px;
    }

    .preview-icon {
      cursor: pointer;
      margin-left: 8px;
      font-size: 18px;
    }

    .form-layout {
      display: flex;
      gap: 30px;
      margin-top: 20px;
      flex-wrap: wrap;
    }

    .form-left, .form-right {
      flex: 1;
      min-width: 300px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      font-weight: 500;
      display: block;
      margin-bottom: 5px;
    }

    textarea, input[type="text"], select {
      width: 100%;
      padding: 10px;
      font-size: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .preview-toggle {
      float: right;
      cursor: pointer;
      font-size: 14px;
      color: #007bff;
    }

    .image-group {
      margin-bottom: 20px;
    }

    .image-preview {
      max-width: 100%;
      max-height: 200px;
      display: block;
      margin-top: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
    }

    .button-group {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .button-group button {
      flex: 1;
      padding: 10px;
      font-size: 15px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    .btn-save { background: #28a745; color: #fff; }
    .btn-reset { background: #ffc107; color: #000; }
    .btn-delete-img { background: #dc3545; color: #fff; }
    .btn-table { background: #17a2b8; color: #fff; }

  </style>
</head>
<body>
  <div class="form-container">
    <div class="form-title">
      Nhập câu hỏi trắc nghiệm nhiều lựa chọn
      <span class="preview-icon" title="Xem trước toàn bộ nội dung">👁️</span>
    </div>

    <form id="mcForm">
      <div class="form-layout">
        <!-- Cột trái -->
        <div class="form-left">
          <div class="form-group">
            <label for="mc_topic">Chủ đề</label>
            <input type="text" id="mc_topic" name="mc_topic" placeholder="Nhập chủ đề">
          </div>

          <div class="form-group">
            <label for="mc_question">Câu hỏi <span class="preview-toggle">👁️</span></label>
            <textarea id="mc_question" name="mc_question" rows="3" placeholder="Nhập nội dung câu hỏi"></textarea>
          </div>

          <?php
            $options = ['A', 'B', 'C', 'D'];
            foreach ($options as $opt) {
              echo <<<HTML
              <div class="form-group">
                <label for="mc_opt_$opt">Đáp án $opt <span class="preview-toggle">👁️</span></label>
                <input type="text" id="mc_opt_$opt" name="mc_opt_$opt" placeholder="Nhập đáp án $opt">
              </div>
              HTML;
            }
          ?>

          <div class="form-group">
            <label for="mc_answer">Đáp án đúng</label>
            <select id="mc_answer" name="mc_answer">
              <option value="">-- Chọn --</option>
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
            <input type="file" id="mc_image" name="mc_image" accept="image/*">
            <img id="imagePreview" class="image-preview" src="#" alt="Ảnh minh họa" style="display:none;">
            <button type="button" class="btn-delete-img" onclick="deleteImage()">🗑️ Xoá ảnh</button>
          </div>

          <div class="button-group">
            <button type="submit" class="btn-save">💾 Lưu</button>
            <button type="reset" class="btn-reset">🔄 Làm lại</button>
            <button type="button" class="btn-table" onclick="openQuestionTable()">📋 Xem bảng</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <script>
    function deleteImage() {
      const image = document.getElementById('imagePreview');
      image.src = '#';
      image.style.display = 'none';
      document.getElementById('mc_image').value = '';
    }

    document.getElementById('mc_image').addEventListener('change', function (e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (evt) {
          const img = document.getElementById('imagePreview');
          img.src = evt.target.result;
          img.style.display = 'block';
        };
        reader.readAsDataURL(file);
      }
    });

    function openQuestionTable() {
      window.open('get_question.php', '_blank');
    }
  </script>
</body>
</html>
