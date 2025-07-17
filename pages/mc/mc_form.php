<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm nhiều lựa chọn</title>
  <link rel="stylesheet" href="/css/form.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
  <script src="/js/mc_form.js" defer></script>
</head>
<body>
  <div class="form-wrapper">
    <!-- 🔽 Tiêu đề có icon toggle -->
    <div class="form-header">
      <h2>
        <span id="togglePreview" style="cursor: pointer;">📘</span>
        Nhập câu hỏi trắc nghiệm nhiều lựa chọn
      </h2>
    </div>

    <!-- 🔽 Khung ẩn/hiện xem trước toàn bộ -->
    <div id="fullPreview" class="full-preview hidden">
      <div class="preview-block" id="preview-question"></div>
      <div class="preview-block" id="preview-image" style="margin: 10px 0;"></div>
      <ul id="preview-answers"></ul>
    </div>

    <!-- 🔽 Form chia 2 cột -->
    <div class="form-layout">
      <!-- Bên trái -->
      <div class="form-left">
        <label>Câu hỏi:</label>
        <textarea id="mc_question" rows="4"></textarea>

        <label>Hình minh họa:</label>
        <input type="file" id="mc_image" accept="image/*">
        <div id="mc_image_preview"></div>
        <button type="button" id="delete_image">Xoá ảnh</button>

        <label>Chủ đề:</label>
        <input type="text" id="mc_topic">

        <label>Các đáp án:</label>
        <div id="answer_container">
          <div class="answer">
            <input type="radio" name="mc_correct" value="0">
            <input type="text" class="mc_answer">
          </div>
          <div class="answer">
            <input type="radio" name="mc_correct" value="1">
            <input type="text" class="mc_answer">
          </div>
          <div class="answer">
            <input type="radio" name="mc_correct" value="2">
            <input type="text" class="mc_answer">
          </div>
          <div class="answer">
            <input type="radio" name="mc_correct" value="3">
            <input type="text" class="mc_answer">
          </div>
        </div>
      </div>

      <!-- Bên phải -->
      <div class="form-right">
        <label>ID:</label>
        <input type="text" id="mc_id" readonly>

        <label>Ngày tạo:</label>
        <input type="text" id="mc_created_at" readonly>

        <label>Ngày sửa:</label>
        <input type="text" id="mc_updated_at" readonly>

        <div class="form-buttons">
          <button type="button" id="btn_save">💾 Lưu</button>
          <button type="button" id="btn_reset">🧹 Làm mới</button>
          <button type="button" id="btn_delete">🗑️ Xoá</button>
          <button type="button" id="btn_export">📄 Xuất PDF</button>
        </div>
      </div>
    </div>

    <!-- Iframe danh sách câu hỏi -->
    <iframe id="question_table" src="mc_table.php"></iframe>
  </div>

  <script>
    // Toggle khung xem trước toàn bộ
    $('#togglePreview').on('click', function () {
      $('#fullPreview').toggleClass('hidden');
    });
  </script>

  <style>
    .hidden { display: none; }
    .form-header {
      background: #eef3f7;
      padding: 10px 15px;
      border-radius: 6px;
      margin-bottom: 10px;
    }
    .form-header h2 {
      margin: 0;
      font-size: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .full-preview {
      background: #f9f9f9;
      padding: 10px;
      margin-bottom: 15px;
      border-left: 4px solid #0a74da;
    }
    .preview-block {
      margin-bottom: 10px;
    }
    #preview-answers li {
      margin-left: 20px;
      list-style-type: upper-alpha;
    }
  </style>
</body>
</html>
