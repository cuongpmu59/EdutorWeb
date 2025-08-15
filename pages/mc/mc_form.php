<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Câu hỏi trắc nghiệm</title>

  <!-- CSS gốc -->
  <link rel="stylesheet" href="../../css/mc/mc_form_image.css">
  <link rel="stylesheet" href="../../css/mc/mc_form_preview.css">
  <link rel="stylesheet" href="../../css/mc/mc_form_button.css">
  <link rel="stylesheet" href="../../css/mc/mc_formtype.css">

  <!-- CSS thêm để cố định form và bảng full width -->
  <style>
    /* Cố định form trên cùng */
    #formContainer {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      background: #fff;
      padding: 10px 15px;
      border-bottom: 1px solid #ccc;
      z-index: 1000;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* DataTable nằm dưới form */
    .datatable-wrapper {
      display: none; /* Ẩn mặc định */
      position: relative;
      width: 100%;
    }

    .datatable-wrapper.show {
      display: block;
    }

    .datatable-wrapper iframe {
      width: 100%;
      height: calc(100vh - var(--form-height));
      border: none;
    }
  </style>

  <!-- MathJax -->
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

  <!-- Bọc form trong div cố định -->
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
        <div class="mc-col mc-col-left">
          <div class="mc-field">
            <label for="mc_topic">Chủ đề:</label>
            <input type="text" id="mc_topic" name="topic" required>
          </div>

          <div class="mc-field">
            <label for="mc_question">Câu hỏi:
              <button type="button" class="toggle-preview" data-target="mc_question"><i class="fa fa-eye"></i></button>
            </label>
            <textarea id="mc_question" name="question" required></textarea>
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
              <img id="mc_preview_image" src="" alt="Hình minh hoạ" style="display:none; max-width:200px;">
            </div>
            <div class="mc-image-buttons">
              <label class="btn-upload">
                Tải ảnh
                <input type="file" id="mc_image" name="image" accept="image/*" hidden>
              </label>
              <button type="button" id="mc_clear_image">Xóa ảnh</button>
            </div>
            <input type="hidden" name="mc_image_url" id="mc_image_url">
            <div id="statusMsg"></div>
          </div>

          <!-- Nút thao tác -->
          <div class="mc-buttons-wrapper">
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

  <!-- Khung chứa DataTable ngay dưới form -->
  <div id="mcTableWrapper" class="datatable-wrapper">
    <iframe id="mcTableFrame" src="../../pages/mc/mc_table.php"></iframe>
  </div>

  <!-- JS gốc -->
  <script src="../../js/mc/mc_form_preview.js"></script>
  <script src="../../js/mc/mc_form_image.js?"></script>
  <script src="../../js/mc/mc_form_button.js"></script>

  <script>
    // Hàm cập nhật chiều cao DataTable theo form
    function updateTableHeight() {
      const formHeight = document.getElementById('formContainer').offsetHeight;
      document.documentElement.style.setProperty('--form-height', formHeight + 'px');
    }

    window.addEventListener('resize', updateTableHeight);
    window.addEventListener('load', updateTableHeight);

    // Nút "Ẩn/hiện danh sách"
    document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('mc_view_list');
    const wrapper = document.getElementById('mcTableWrapper');

    btn.addEventListener('click', () => {
    wrapper.classList.toggle('show');
    updateTableHeight(); // Cập nhật chiều cao iframe
    });
    });


    // Lắng nghe dữ liệu từ iframe (bảng DataTable) gửi về
    window.addEventListener('message', function (event) {
      const { type, data } = event.data || {};
      if (type !== 'fill-form' || !data) return;

      $('#mc_id').val(data.mc_id || '');
      $('#mc_topic').val(data.mc_topic || '');
      $('#mc_question').val(data.mc_question || '');
      $('#mc_answer1').val(data.mc_answer1 || '');
      $('#mc_answer2').val(data.mc_answer2 || '');
      $('#mc_answer3').val(data.mc_answer3 || '');
      $('#mc_answer4').val(data.mc_answer4 || '');
      $('#mc_correct_answer').val(data.mc_correct_answer || '');

      if (data.mc_image_url) {
        $('#mc_preview_image').attr('src', data.mc_image_url).show();
        $('#mc_image_url').val(data.mc_image_url);
      } else {
        $('#mc_preview_image').hide().attr('src', '');
        $('#mc_image_url').val('');
      }

      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  </script>
</body>
</html>
