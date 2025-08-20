<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Câu hỏi Trắc nghiệm ngắn (Short Answer)</title>

  <!-- CSS -->
  <link rel="stylesheet" href="../../css/sa/sa_form_image.css">
  <link rel="stylesheet" href="../../css/sa/sa_form_preview.css">
  <link rel="stylesheet" href="../../css/sa/sa_form_button.css">
  <link rel="stylesheet" href="../../css/sa/sa_form_layout.css">

  <!-- MathJax -->
  <script>
    window.MathJax = {
      tex: {
        inlineMath: [['$', '$'], ['\\(', '\\)']],
        displayMath: [['\\[', '\\]'], ['$$', '$$']],
        processEscapes: true
      },
      options: {
        skipHtmlTags: ['script','noscript','style','textarea','pre'],
        ignoreHtmlClass: 'tex2jax_ignore'
      }
    };
  </script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" async></script>

  <!-- jQuery + FontAwesome -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div id="formContainer">
  <form id="saForm" method="POST" enctype="multipart/form-data">
    <h2>
      Câu hỏi Short Answer
      <span id="saTogglePreview" title="Xem trước toàn bộ"><i class="fa fa-eye"></i></span>
    </h2>

    <div id="saPreview" class="sa-preview-zone" style="display:none;">
      <div id="saPreviewContent"></div>
    </div>

    <div id="saMainContent" class="sa-columns">
      <!-- Cột trái -->
      <div class="sa-col sa-col-left">
        <fieldset class="sa-group">
          <legend>Thông tin câu hỏi</legend>

          <div class="sa-field">
            <label for="sa_topic">Chủ đề:</label>
            <input type="text" id="sa_topic" name="topic" required>
          </div>

          <div class="sa-field preview-field">
            <label for="sa_question">Câu hỏi:
              <button type="button" class="toggle-preview" data-target="sa_question"><i class="fa fa-eye"></i></button>
            </label>
            <textarea id="sa_question" name="question" required></textarea>
            <div class="preview-box" id="preview-sa_question" style="display:none;"></div>
          </div>

          <div class="sa-field sa-inline-field">
            <label for="sa_correct_answer">Đáp án đúng:</label>
            <input type="text" id="sa_correct_answer" name="correct_answer" required>
          </div>
        </fieldset>
      </div>

      <!-- Cột phải -->
      <div class="sa-col sa-col-right">
        <div class="sa-image-zone sa-group">
          <h4>Ảnh minh họa</h4>
          <div class="sa-image-preview">
            <img id="sa_preview_image" src="" alt="Hình minh hoạ" style="display:none; max-width:200px;">
          </div>
          <div class="sa-image-buttons">
            <label class="btn-upload">
              Tải ảnh
              <input type="file" id="sa_image" name="image" accept="image/*" hidden>
            </label>
            <button type="button" id="sa_clear_image">Xóa ảnh</button>
          </div>
          <input type="hidden" name="sa_image_url" id="sa_image_url">
          <div id="statusMsg"></div>
        </div>

        <div class="sa-buttons-wrapper sa-group">
          <h4>Thao tác</h4>
          <div class="sa-buttons">
            <button type="submit" id="sa_save">Lưu</button>
            <button type="button" id="sa_delete">Xóa</button>
            <button type="button" id="sa_reset">Làm mới</button>
            <button type="button" id="sa_view_list">Ẩn/hiện danh sách</button>
            <button type="button" id="sa_preview_exam" class="full-width">Làm đề</button>
          </div>
          <input type="hidden" id="sa_id" name="sa_id">
        </div>
      </div>
    </div>
  </form>
</div>

<!-- Bảng quản lý -->
<div id="saTableWrapper" style="display:none;">
  <iframe id="saTableFrame" src="../../pages/sa/sa_table.php" style="width:100%; height:600px; border:none;"></iframe>
</div>

<!-- JS -->
<script src="../../js/sa/sa_form_preview.js"></script>
<script src="../../js/sa/sa_form_image.js"></script>
<script src="../../js/sa/sa_form_button.js"></script>

<script>
  // Auto-resize textarea
  document.querySelectorAll("textarea").forEach(el => {
    el.style.height = "auto";
    el.style.height = el.scrollHeight + "px";
    el.addEventListener("input", () => {
      el.style.height = "auto";
      el.style.height = el.scrollHeight + "px";
    });
  });

  // Toggle preview từng trường
  $('.toggle-preview').click(function() {
    const target = $(this).data('target');
    const content = $('#' + target).val();
    $('#preview-' + target).text(content).slideToggle(200);
    MathJax.typesetPromise([document.getElementById('preview-' + target)]);
  });

  // Toggle preview toàn bộ
  $('#saTogglePreview').click(() => $('#saPreview').fadeToggle(200));

  // Nhận dữ liệu từ iframe DataTable
  window.addEventListener('message', function (event) {
    const { type, data } = event.data || {};
    if (type !== 'fill-form' || !data) return;

    $('#sa_id').val(data.sa_id || '');
    $('#sa_topic').val(data.sa_topic || '');
    $('#sa_question').val(data.sa_question || '');
    $('#sa_correct_answer').val(data.sa_correct_answer || '');

    if (data.sa_image_url) {
      $('#sa_preview_image').attr('src', data.sa_image_url).show();
      $('#sa_image_url').val(data.sa_image_url);
    } else {
      $('#sa_preview_image').hide().attr('src','');
      $('#sa_image_url').val('');
    }

    MathJax.typesetPromise();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
</script>
</body>
</html>
