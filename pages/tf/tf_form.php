<?php
// tf_form.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Câu hỏi Đúng/Sai</title>

  <!-- CSS -->
  <link rel="stylesheet" href="../../css/tf/tf_form_layout.css">
  <link rel="stylesheet" href="../../css/tf/tf_form_button.css">
  <link rel="stylesheet" href="../../css/tf/tf_form_preview.css">
  <link rel="stylesheet" href="../../css/tf/tf_form_image.css">

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
  <form id="tfForm" method="POST" enctype="multipart/form-data">
    <h2>
      Câu hỏi trắc nghiệm Đúng/Sai
      <span id="tfTogglePreview" title="Xem trước toàn bộ"><i class="fa fa-eye"></i></span>
    </h2>

    <!-- Preview toàn bộ -->
    <div id="tfPreview" class="tf-preview-zone" style="display:none;">
      <div id="tfPreviewContent"></div>
    </div>

    <div id="tfMainContent" class="tf-columns">
      <!-- Cột trái -->
      <div class="tf-col tf-col-left">
        <fieldset class="tf-group">
          <legend>Thông tin câu hỏi</legend>

          <!-- Chủ đề -->
          <div class="tf-field">
            <label for="tf_topic">Chủ đề:</label>
            <input type="text" id="tf_topic" name="topic" required>
          </div>

          <!-- Câu hỏi chính -->
          <div class="tf-field preview-field">
            <label for="tf_question">Câu hỏi:
              <button type="button" class="toggle-preview" data-target="tf_question"><i class="fa fa-eye"></i></button>
            </label>
            <textarea id="tf_question" name="question" required></textarea>
            <div class="preview-box" id="preview-tf_question" style="display:none;"></div>
          </div>

          <!-- 4 mệnh đề + Đúng/Sai -->
          <?php
          for ($i = 1; $i <= 4; $i++) {
            echo '
            <div class="tf-field tf-inline-field">
              <label for="tf_statement'.$i.'">'.$i.'. 
                <button type="button" class="toggle-preview" data-target="tf_statement'.$i.'"><i class="fa fa-eye"></i></button>
              </label>
              
              <textarea id="tf_statement'.$i.'" name="tf_statement'.$i.'" required></textarea>
              <div class="preview-box" id="preview-tf_statement'.$i.'" style="display:none;"></div>
              <div class="tf-radio-group">
                <label><input type="radio" name="tf_correct_answer'.$i.'" value="1" required> Đúng</label>
                <label><input type="radio" name="tf_correct_answer'.$i.'" value="0"> Sai</label>
              </div>
            </div>';
          }
          ?>
        </fieldset>
      </div>

      <!-- Cột phải -->
      <div class="tf-col tf-col-right">
        <div class="tf-image-zone tf-group">
          <h4>Ảnh minh họa</h4>
          <div class="tf-image-preview">
            <img id="tf_preview_image" src="" alt="Hình minh hoạ" style="display:none; max-width:200px;">
          </div>
          <div class="tf-image-buttons">
            <label class="btn-upload">
              Tải ảnh
              <input type="file" id="tf_image" name="image" accept="image/*" hidden>
            </label>
            <button type="button" id="tf_clear_image">Xóa ảnh</button>
          </div>
          <input type="hidden" name="tf_image_url" id="tf_image_url">
          <div id="statusMsg"></div>
        </div>

        <div class="tf-buttons-wrapper tf-group">
          <h4>Thao tác</h4>
          <div class="tf-buttons">
            <button type="submit" id="tf_save">Lưu</button>
            <button type="button" id="tf_delete">Xóa</button>
            <button type="button" id="tf_reset">Làm mới</button>
            <button type="button" id="tf_view_list">Ẩn/hiện danh sách</button>
            <button type="button" id="tf_preview_exam" class="full-width">Làm đề</button>
          </div>
          <input type="hidden" id="tf_id" name="tf_id">
        </div>
      </div>
    </div>
  </form>
</div>

<!-- Bảng quản lý -->
<div id="tfTableWrapper" style="display:none;">
  <iframe id="tfTableFrame" src="../../pages/tf/tf_table.php" style="width:100%; height:600px; border:none;"></iframe>
</div>

<!-- JS -->
<script src="../../js/tf/tf_form_preview.js"></script>
<script src="../../js/tf/tf_form_image.js"></script>
<script src="../../js/tf/tf_form_button.js"></script>

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
// Nhận dữ liệu từ iframe DataTable để fill form (TF Questions)
window.addEventListener('message', function (event) {
  const { type, data } = event.data || {};
  if (type !== 'fill-form' || !data) return;

  const $form = $('#tfForm');

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
  if (data.tf_image_url) {
    $('#tf_preview_image').attr('src', data.tf_image_url).show();
  } else {
    $('#tf_preview_image').hide().attr('src', '');
  }

  // Cập nhật preview nhỏ (nếu có)
  if (typeof previewFields !== 'undefined' && typeof updatePreview === 'function') {
    previewFields.forEach(({ id }) => updatePreview(id));
  }

  // 👉 chỉ update full preview nếu đang hiển thị
  const fullPreviewZone = document.getElementById('tfPreview');
  if (fullPreviewZone && window.getComputedStyle(fullPreviewZone).display !== 'none') {
    if (typeof updateFullPreview === 'function') {
      updateFullPreview();
    }
  }

  // Cuộn lên đầu form
  window.scrollTo({ top: 0, behavior: 'smooth' });
});
</script>

<script>
// Xử lý nút "Làm đề"
  document.getElementById("tf_preview_exam").addEventListener("click", function() {
  window.location.href = "../../pages/main/tf_exam_form.php";
});
</script>

</body>
</html>
