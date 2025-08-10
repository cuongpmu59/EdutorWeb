<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="../../css/mc/mc_form_image.css">
  <link rel="stylesheet" href="../../css/mc/mc_form_preview.css">
  <link rel="stylesheet" href="../../css/mc/mc_form_button.css">
  <link rel="stylesheet" href="../../css/mc/mc_formtype.css">


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
            <label for="mc_answer1">A.
            <button type="button" class="toggle-preview" data-target="mc_answer1"><i class="fa fa-eye"></i></button>
            </label>
            <input type="text" id="mc_answer1" name="answer1" required>
            <div class="preview-box" id="preview-mc_answer1" style="display:none;"></div>
          </div>

          <div class="mc-field mc-inline-field">
            <label for="mc_answer2">B.
            <button type="button" class="toggle-preview" data-target="mc_answer2"><i class="fa fa-eye"></i></button>
            </label>
            <input type="text" id="mc_answer2" name="answer2" required>
            <div class="preview-box" id="preview-mc_answer2" style="display:none;"></div>
          </div>

          <div class="mc-field mc-inline-field">
            <label for="mc_answer3">C.
            <button type="button" class="toggle-preview" data-target="mc_answer3"><i class="fa fa-eye"></i></button>
            </label>
            <input type="text" id="mc_answer3" name="answer3" required>
            <div class="preview-box" id="preview-mc_answer3" style="display:none;"></div>
          </div>

          <div class="mc-field mc-inline-field">
            <label for="mc_answer4">D.
            <button type="button" class="toggle-preview" data-target="mc_answer4"><i class="fa fa-eye"></i></button>
            </label>
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
              <button type="button" id="mc_clear_image">Xóa ảnh</button>
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
      <iframe id="mcTableFrame" src="../../pages/mc/mc_table.php" style="width:100%; height:600px; border:none;"></iframe>
    </div>
  </div>

  <script src="../../js/mc/mc_form_preview.js"></script>
  <!-- <script src="../../js/mc/mc_form_image.js"></script> -->
  <script src="../../js/mc/mc_form_button.js"></script>
 
  <script>
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

    // Nếu có ảnh → hiển thị, ngược lại ẩn ảnh
    if (data.mc_image_url) {
      $('#mc_preview_image').attr('src', data.mc_image_url).show();
    } else {
      $('#mc_preview_image').hide().attr('src', '');
    }

    // Cuộn lên đầu trang
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
</script>

<script>
const apiUrl = '../../includes/mc/mc_form_image.php';

function updateNoImageText() {
    if ($('#mc_preview_image').attr('src')) {
        $('#noImageText').hide();
    } else {
        $('#noImageText').show();
    }
}

function resetPreview() {
    $('#mc_preview_image').attr('src', '');
    $('#mc_image').val('');
    $('#statusMsg').html('');
    updateNoImageText();
}

function getPublicIdFromUrl(url) {
    try {
        let path = new URL(url).pathname; // /<...>/upload/v1234567/folder/file.jpg
        let parts = path.split('/');
        let uploadIndex = parts.indexOf('upload');
        if (uploadIndex === -1) return null;

        let publicPathParts = parts.slice(uploadIndex + 1);
        if (publicPathParts[0].match(/^v[0-9]+$/)) {
            publicPathParts.shift();
        }

        let filename = publicPathParts.pop();
        let publicIdWithoutExt = filename.split('.')[0];

        return [...publicPathParts, publicIdWithoutExt].join('/');
    } catch (e) {
        return null;
    }
}

$(document).ready(function () {
  const fileInput = $('#mc_image');
  const previewImage = $('#mc_preview_image');

    fileInput.on('change', function () {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();

      reader.onload = function (e) {
        previewImage.attr('src', e.target.result).show(); // Gán ảnh và hiển thị
      };
    reader.readAsDataURL(file);
    }
  });

    $('#statusMsg').css('color', '#333').html('⏳ Đang upload ảnh...');
    const formData = new FormData();
    formData.append('action', 'upload');
    formData.append('file', file);

    $.ajax({
        url: apiUrl,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
            if (res.secure_url) {
                $('#mc_preview_image').attr('src', res.secure_url);
                updateNoImageText();
                $('#statusMsg').css('color', 'green').html('✅ Upload thành công!');
            } else {
                $('#statusMsg').css('color', 'red').html('❌ Upload thất bại.');
            }
        },
        error: function() {
            $('#statusMsg').css('color', 'red').html('❌ Lỗi khi upload.');
        }
    });
});

// Nút xóa ảnh
$('#mc_clear_image').on('click', function () {
    let imgUrl = $('#mc_preview_image').attr('src');
    if (!imgUrl) {
        $('#statusMsg').css('color', 'red').html('❌ Không có ảnh để xóa.');
        return;
    }

    let public_id = getPublicIdFromUrl(imgUrl);
    if (!public_id) {
        $('#statusMsg').css('color', 'red').html('❌ Không thể lấy public_id.');
        return;
    }

    if (!confirm('Bạn có chắc muốn xóa ảnh này?')) return;

    $('#statusMsg').css('color', '#333').html('⏳ Đang xóa ảnh...');

    $.ajax({
        url: apiUrl,
        type: 'POST',
        data: { action: 'delete', public_id: public_id },
        dataType: 'json',
        success: function(res) {
            if (res.result === 'ok') {
                resetPreview();
                $('#statusMsg').css('color', 'green').html('🗑 Ảnh đã được xóa.');
            } else {
                $('#statusMsg').css('color', 'red').html('❌ Xóa thất bại.');
            }
        },
        error: function() {
            $('#statusMsg').css('color', 'red').html('❌ Lỗi khi xóa.');
        }
    });
});

// Khởi tạo
updateNoImageText();
</script>

</body>
</html>
