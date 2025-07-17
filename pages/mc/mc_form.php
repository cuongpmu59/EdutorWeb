<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm nhiều lựa chọn</title>
  <link rel="stylesheet" href="/css/form.css">
  <link rel="stylesheet" href="/css/main_ui.css">

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
  <script src="/js/mc_form.js" defer></script>
</head>
<body>
  <form id="mc_form" class="form-wrapper">
    <!-- 🔽 Tiêu đề có nút toggle xem trước -->
    <div class="form-header">
      <h2>
        <span id="togglePreview" style="cursor: pointer;">📘</span>
        Nhập câu hỏi trắc nghiệm nhiều lựa chọn
      </h2>
    </div>

    <!-- 🔽 Xem trước toàn bộ nội dung -->
    <div id="fullPreview" class="full-preview hidden">
      <div class="preview-block" id="preview-question"></div>
      <div class="preview-block" id="preview-image"></div>
      <ul id="preview-answers"></ul>
    </div>

    <!-- 🔽 Chia 2 cột: trái và phải -->
    <div class="form-layout">
      <!-- Cột trái -->
      <div class="form-left">
        <!-- 📝 Câu hỏi -->
        <label for="mc_question">Câu hỏi:</label>
        <textarea id="mc_question" rows="4"></textarea>

        <!-- 🔤 Các đáp án -->
        <label>Đáp án:</label>
        <div class="answer">
          <label><input type="radio" name="mc_correct" value="0"> A.</label>
          <input type="text" class="mc_answer">
        </div>
        <div class="answer">
          <label><input type="radio" name="mc_correct" value="1"> B.</label>
          <input type="text" class="mc_answer">
        </div>
        <div class="answer">
          <label><input type="radio" name="mc_correct" value="2"> C.</label>
          <input type="text" class="mc_answer">
        </div>
        <div class="answer">
          <label><input type="radio" name="mc_correct" value="3"> D.</label>
          <input type="text" class="mc_answer">
        </div>

        <!-- 📘 Toggle xem trước nội dung -->
        <button type="button" id="preview_toggle_single">👁️ Xem trước nội dung</button>
      </div>

      <!-- Cột phải -->
      <div class="form-right">
        <!-- 🖼️ Nhóm ảnh minh hoạ -->
        <label>Hình minh họa:</label>
        <input type="file" id="mc_image" accept="image/*">
        <div id="mc_image_preview" style="margin: 10px 0;"></div>
        <button type="button" id="delete_image">🗑️ Xoá ảnh</button>

        <!-- 📅 Thông tin bổ sung -->
        <label>Chủ đề:</label>
        <input type="text" id="mc_topic">

        <label>ID:</label>
        <input type="text" id="mc_id" readonly>

        <label>Ngày tạo:</label>
        <input type="text" id="mc_created_at" readonly>

        <label>Ngày sửa:</label>
        <input type="text" id="mc_updated_at" readonly>

        <!-- 🔘 Nhóm nút chức năng -->
        <div class="form-buttons">
          <button type="button" id="btn_save">💾 Lưu</button>
          <button type="button" id="btn_reset">🧹 Làm lại</button>
          <button type="button" id="btn_delete">🗑️ Xoá câu hỏi</button>
          <button type="button" id="btn_view_table">📋 Xem bảng câu hỏi</button>
        </div>
      </div>
    </div>

    <!-- 📑 Iframe bảng câu hỏi -->
    <iframe id="question_table" src="mc_table.php"></iframe>
  </form>

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
    .form-layout {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }
    .form-left {
      flex: 2;
      min-width: 300px;
    }
    .form-right {
      flex: 1;
      min-width: 250px;
    }
    .form-buttons {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-top: 15px;
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
      list-style-type: upper-alpha;
      margin-left: 20px;
    }
    iframe#question_table {
      width: 100%;
      height: 400px;
      border: 1px solid #ccc;
      margin-top: 20px;
    }
  </style>

  <script>
    // Toggle toàn bộ preview
    $('#togglePreview').on('click', function () {
      $('#fullPreview').toggleClass('hidden');
    });

    // Toggle xem trước từng phần
    $('#preview_toggle_single').on('click', function () {
      $('#preview-question').text($('#mc_question').val());
      const answers = $('.mc_answer').map(function (i, el) {
        const prefix = String.fromCharCode(65 + i) + '. ';
        return '<li>' + prefix + $(el).val() + '</li>';
      }).get().join('');
      $('#preview-answers').html(answers);
      MathJax.typeset();
    });
  </script>
</body>
</html>
