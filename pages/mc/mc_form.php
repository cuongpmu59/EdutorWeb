<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Câu hỏi trắc nghiệm</title>

  <!-- CSS -->
  <link rel="stylesheet" href="../../css/mc/mc_form_image.css">
  <link rel="stylesheet" href="../../css/mc/mc_form_preview.css">
  <link rel="stylesheet" href="../../css/mc/mc_form_button.css">
  <link rel="stylesheet" href="../../css/mc/mc_form_layout.css">

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

  <!-- jQuery + icon -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div id="formContainer">
    <form id="mcForm" method="POST" enctype="multipart/form-data">
      <h2>
        Câu hỏi trắc nghiệm
        <span id="mcTogglePreview" title="Xem trước toàn bộ"><i class="fa fa-eye"></i></span>
      </h2>

      <div id="mcPreview" class="mc-preview-zone" style="display:none;">
        <div id="mcPreviewContent"></div>
      </div>

      <div id="mcMainContent" class="mc-columns">
        <!-- Cột trái -->
        <div class="mc-col mc-col-left">
          <fieldset class="mc-group">
            <legend>Thông tin câu hỏi</legend>

            <div class="mc-field">
              <label for="mc_topic">Chủ đề:</label>
              <input type="text" id="mc_topic" name="mc_topic" required>
            </div>

            <div class="mc-field">
              <label for="mc_question">Câu hỏi:
                <button type="button" class="toggle-preview" data-target="mc_question"><i class="fa fa-eye"></i></button>
              </label>
              <textarea id="mc_question" name="mc_question" required></textarea>
              <div class="preview-box" id="preview-mc_question" style="display:none;"></div>
            </div>

            <!-- Câu trả lời A - D -->
            <?php
            $answers = ['A','B','C','D'];
            foreach ($answers as $i => $label) {
                $num = $i+1;
                echo '<div class="mc-field mc-inline-field">
                        <label for="mc_answer'.$num.'">'.$label.'. 
                          <button type="button" class="toggle-preview" data-target="mc_answer'.$num.'"><i class="fa fa-eye"></i></button>
                        </label>
                        <input type="text" id="mc_answer'.$num.'" name="answer'.$num.'" required>
                        <div class="preview-box" id="preview-mc_answer'.$num.'" style="display:none;"></div>
                      </div>';
            }
            ?>

            <div class="mc-field mc-inline-field">
              <label for="mc_correct_answer">Đáp án đúng:</label>
              <select id="mc_correct_answer" name="mc_correct_answer" required>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
              </select>
            </div>
          </fieldset>
        </div>

        <!-- Cột phải -->
        <div class="mc-col mc-col-right">
          <div class="mc-image-zone mc-group">
            <h4>Ảnh minh họa</h4>
            <div class="mc-image-preview">
              <img id="mc_preview_image" src="" alt="Hình minh hoạ" style="display:none; max-width:200px;">
            </div>
            <div class="mc-image-buttons">
              <label class="btn-upload">
                Tải ảnh
                <input type="file" id="mc_image" name="mc_image" accept="image/*" hidden>
              </label>
              <button type="button" id="mc_clear_image">Xóa ảnh</button>
            </div>
            <input type="hidden" name="mc_image_url" id="mc_image_url">
            <div id="statusMsg"></div>
          </div>

          <div class="mc-buttons-wrapper mc-group">
            <h4>Thao tác</h4>
            <div class="mc-buttons">
              <button type="submit" id="mc_save">Lưu</button>
              <button type="button" id="mc_delete">Xóa</button>
              <button type="button" id="mc_reset">Làm mới</button>
              <button type="button" id="mc_view_list">Ẩn/hiện danh sách</button>
              <button type="button" id="mc_preview_exam" class="full-width">Làm đề</button>
            </div>
            <input type="hidden" id="mc_id" name="mc_id">
          </div>
        </div>
      </div>
    </form>
  </div>

  <!-- Bảng quản lý -->
  <div id="mcTableWrapper" style="display:none;">
    <iframe id="mcTableFrame" src="../../pages/mc/mc_table.php" style="width:100%; height:600px; border:none;"></iframe>
  </div>

  <!-- JS -->
  <script src="../../js/mc/mc_form_preview.js"></script>
  <script src="../../js/mc/mc_form_image.js"></script>
  <script src="../../js/mc/mc_form_button.js"></script>

  <script>
  // Auto-resize tất cả textarea
  document.addEventListener("input", function(e) {
    if (e.target.tagName.toLowerCase() !== "textarea") return;
    e.target.style.height = "auto";                     // reset trước
    e.target.style.height = e.target.scrollHeight + "px"; // cao vừa đủ
  });

  // Chạy 1 lần khi trang load (để resize theo dữ liệu sẵn có)
  window.addEventListener("load", function() {
    document.querySelectorAll("textarea").forEach(function(el) {
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

  const $form = $('#mcForm');

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
  if (data.mc_image_url) {
    $('#mc_preview_image').attr('src', data.mc_image_url).show();
  } else {
    $('#mc_preview_image').hide().attr('src', '');
  }

  // Cập nhật preview nhỏ (nếu có)
  if (typeof previewFields !== 'undefined' && typeof updatePreview === 'function') {
    previewFields.forEach(({ id }) => updatePreview(id));
  }

  // 👉 chỉ update full preview nếu đang hiển thị
  const fullPreviewZone = document.getElementById('mcPreview');
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
