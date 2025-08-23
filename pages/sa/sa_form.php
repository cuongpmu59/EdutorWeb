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
      Câu hỏi trắc nghiệm trả lời ngắn
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
  // Auto-resize tất cả textarea trong form SA
  document.addEventListener("input", function(e) {
    if (e.target.tagName.toLowerCase() !== "textarea") return;
    e.target.style.height = "auto";                     // reset trước
    e.target.style.height = e.target.scrollHeight + "px"; // cao vừa đủ
  });

  // Chạy 1 lần khi trang load (để resize theo dữ liệu sẵn có)
  window.addEventListener("load", function() {
    // Chỉ áp dụng cho form SA
    const saForm = document.getElementById("saForm");
    if (!saForm) return;

    saForm.querySelectorAll("textarea").forEach(function(el) {
      el.style.height = "auto";
      el.style.height = el.scrollHeight + "px";
    });
  });
</script>

<script>
// Nhận dữ liệu từ iframe DataTable để fill form
window.addEventListener('message', function (event) {
  const { type, data } = event.data || {};
  if (type !== 'fill-form' || !data) return;

  const $form = $('#saForm');

  Object.keys(data).forEach(key => {
    const $field = $form.find(`[name="${key}"], #${key}`);
    if (!$field.length) return;

    const value = data[key];

    if ($field.is(':radio')) {
      $form.find(`input[name="${key}"][value="${value}"]`).prop('checked', true);
    } 
    else if ($field.is(':checkbox')) {
      $form.find(`input[name="${key}"]`).prop('checked', false);
      if (Array.isArray(value)) {
        value.forEach(v => $form.find(`input[name="${key}"][value="${v}"]`).prop('checked', true));
      } else {
        $form.find(`input[name="${key}"][value="${value}"]`).prop('checked', true);
      }
    } 
    else if ($field.is('select')) {
      $field.val(value).trigger('change');
    } 
    else {
      $field.val(value);
    }
  });

  // Xử lý ảnh riêng
  if (data.sa_image_url) {
    $('#sa_preview_image').attr('src', data.sa_image_url).show();
  } else {
    $('#sa_preview_image').hide().attr('src', '');
  }

  // Cập nhật preview nhỏ (nếu có)
  if (typeof previewFields !== 'undefined' && typeof updatePreview === 'function') {
    previewFields.forEach(({ id }) => updatePreview(id));
  }

  // 👉 chỉ update full preview nếu đang hiển thị
  const fullPreviewZone = document.getElementById('saPreview');
  if (fullPreviewZone && window.getComputedStyle(fullPreviewZone).display !== 'none') {
    if (typeof updateFullPreview === 'function') {
      updateFullPreview();
    }
  }

  // Cuộn lên đầu form
  window.scrollTo({ top: 0, behavior: 'smooth' });
});
</script>

</body>
</html>
