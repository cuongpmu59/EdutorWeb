<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="../../css/form_ui.css">

  <script>
    window.MathJax = {
      tex: {
        inlineMath: [['$', '$'], ['\\(', '\\)']],
        displayMath: [['\\[', '\\]'], ['$$', '$$']],
        processEscapes: true
      },
      options: {
        skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre'],
        ignoreHtmlClass: 'tex2jax_ignore',
      }
    };
  </script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" async></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="container">
    <form id="mcForm" method="POST" enctype="multipart/form-data">
      <h2>
        Câu hỏi trắc nghiệm
        <span id="mcTogglePreview" title="Xem trước toàn bộ"><i class="fa fa-eye"></i></span>
      </h2>

      <div id="mcPreview" class="mc-preview-zone" style="display:none;">
        <div id="mcPreviewContent"></div>
      </div>

      <div id="mcMainContent" class="mc-columns">
        <div class="mc-col mc-col-left">
          <div class="mc-field">
            <label for="mc_topic">Chủ đề:</label>
            <input type="text" id="mc_topic" name="topic" required value="">
          </div>

          <div class="mc-field">
            <label for="mc_question">Câu hỏi:
              <button type="button" class="toggle-preview" data-target="mc_question"><i class="fa fa-eye"></i></button>
            </label>
            <textarea id="mc_question" name="question" required></textarea>
            <div class="preview-box" id="preview-mc_question" style="display:none;"></div>
          </div>

          <!-- Câu trả lời A - D -->
          <div class="mc-field mc-inline-field">
            <label for="mc_answer1">A.</label>
            <button type="button" class="toggle-preview" data-target="mc_answer1"><i class="fa fa-eye"></i></button>
            <input type="text" id="mc_answer1" name="answer1" required>
            <div class="preview-box" id="preview-mc_answer1" style="display:none;"></div>
          </div>

          <div class="mc-field mc-inline-field">
            <label for="mc_answer2">B.</label>
            <button type="button" class="toggle-preview" data-target="mc_answer2"><i class="fa fa-eye"></i></button>
            <input type="text" id="mc_answer2" name="answer2" required>
            <div class="preview-box" id="preview-mc_answer2" style="display:none;"></div>
          </div>

          <div class="mc-field mc-inline-field">
            <label for="mc_answer3">C.</label>
            <button type="button" class="toggle-preview" data-target="mc_answer3"><i class="fa fa-eye"></i></button>
            <input type="text" id="mc_answer3" name="answer3" required>
            <div class="preview-box" id="preview-mc_answer3" style="display:none;"></div>
          </div>

          <div class="mc-field mc-inline-field">
            <label for="mc_answer4">D.</label>
            <button type="button" class="toggle-preview" data-target="mc_answer4"><i class="fa fa-eye"></i></button>
            <input type="text" id="mc_answer4" name="answer4" required>
            <div class="preview-box" id="preview-mc_answer4" style="display:none;"></div>
          </div>

          <div class="mc-field mc-inline-field">
            <label for="mc_correct_answer">Đáp án đúng:</label>
            <select id="mc_correct_answer" name="answer" required>
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
              <option value="D">D</option>
            </select>
          </div>
        </div>

        <div class="mc-col mc-col-right">
          <div class="mc-image-zone">
            <h4>Ảnh minh họa</h4>
            <div class="mc-image-preview">
              <img id="mc_preview_image" src="" alt="Hình minh hoạ" style="display:none;">
            </div>
            <div class="mc-image-buttons">
              <label class="btn-upload">
                Tải ảnh
                <input type="file" id="mc_image" name="image" accept="image/*" hidden>
              </label>
              <button type="button" id="mc_remove_image">Xóa ảnh</button>
            </div>
          </div>

          <div class="mc-buttons">
            <h4>Thao tác</h4>
            <button type="submit" id="mc_save">Lưu</button>
            <button type="button" id="mc_delete">Xóa</button>
            <button type="button" id="mc_reset">Làm lại</button>
            <button type="button" id="mc_view_list">Ẩn/hiện danh sách</button>
            <button type="button" id="mc_preview_exam">Làm đề</button>
          </div>
        </div>
      </div>

      <input type="hidden" id="mc_id" name="mc_id" value="">
    </form>

    <div id="mcTableWrapper" style="display:none;">
      <iframe id="mcTableFrame" src="mc_table.php" style="width:100%; height:600px; border:none;"></iframe>
    </div>
  </div>

  <script src="../../js/form/mc_listener.js"></script>
  <script src="../../js/form/mc_preview.js"></script>
  <script src="../../js/form/mc_image.js"></script>
  <script src="../../js/form/mc_layout.js"></script>
  <script src="../../js/form/mc_button_action.js"></script>

  <iframe id="mcTableFrame" src="mc_table.html" style="width:100%; height:400px;"></iframe>

<script>
  window.addEventListener('message', function (e) {
    if (e.data?.type === 'fill-form') {
      const data = e.data.data;

      // 🔧 Ví dụ đổ dữ liệu lên form
      document.querySelector('#mc_id').value = data.mc_id;
      document.querySelector('#mc_question').value = data.mc_question;
      document.querySelector('#mc_answer1').value = data.mc_answer1;
      document.querySelector('#mc_answer2').value = data.mc_answer2;
      document.querySelector('#mc_answer3').value = data.mc_answer3;
      document.querySelector('#mc_answer4').value = data.mc_answer4;
      document.querySelector('#mc_correct_answer').value = data.mc_correct_answer;
      document.querySelector('#mc_topic').value = data.mc_topic;
      // Xử lý hình ảnh nếu có
      if (data.mc_image_url) {
        document.querySelector('#preview-image').src = data.mc_image_url;
      }
    }
  });
</script>



</body>
</html>
