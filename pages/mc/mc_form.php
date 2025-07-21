<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Kết nối CSDL
require_once __DIR__ . '/../../includes/db_connection.php';

$mc = null;
if (!empty($_GET['mc_id'])) {
    $id = intval($_GET['mc_id']);
    $stmt = $conn->prepare("SELECT * FROM mc_questions WHERE mc_id = ?");
    $stmt->execute([$id]);
    $mc = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="../../css/form_ui.css">

  <!-- MathJax -->
  <script>
    window.MathJax = {
      tex: { inlineMath: [['$', '$'], ['\\(', '\\)']] },
      svg: { fontCache: 'global' }
    };
  </script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js" async></script>

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="container">
    <form id="mcForm" method="POST" enctype="multipart/form-data">
      <h2>
        Câu hỏi trắc nghiệm
        <span id="mcTogglePreview" title="Xem trước toàn bộ"><i class="fa fa-eye"></i></span>
      </h2>

      <div id="mcMainContent" class="mc-columns">
        <!-- Cột trái -->
        <div class="mc-col mc-col-left">
          <div class="mc-field">
            <label for="mc_topic">Chủ đề:</label>
            <input type="text" id="mc_topic" name="topic" required value="<?= htmlspecialchars($mc['mc_topic'] ?? '', ENT_QUOTES) ?>">
          </div>

          <div class="mc-field">
            <label for="mc_question">Câu hỏi:
              <button type="button" class="toggle-preview" data-target="mc_question"><i class="fa fa-eye"></i></button>
            </label>
            <textarea id="mc_question" name="question" required><?= htmlspecialchars($mc['mc_question'] ?? '', ENT_QUOTES) ?></textarea>
            <div class="preview-box" id="preview-mc_question" style="display:none;"></div>
          </div>

          <?php foreach (['A','B','C','D'] as $opt): ?>
          <div class="mc-field">
            <label for="mc_opt_<?= $opt ?>"><?= $opt ?>.
              <button type="button" class="toggle-preview" data-target="mc_opt_<?= $opt ?>"><i class="fa fa-eye"></i></button>
            </label>
            <input type="text" id="mc_opt_<?= $opt ?>" name="opt_<?= $opt ?>" required value="<?= htmlspecialchars($mc["mc_opt_$opt"] ?? '', ENT_QUOTES) ?>">
            <div class="preview-box" id="preview-mc_opt_<?= $opt ?>" style="display:none;"></div>
          </div>
          <?php endforeach; ?>

          <div class="mc-field">
            <label for="mc_answer">Đáp án:</label>
            <select id="mc_answer" name="answer" required>
              <?php foreach (['A','B','C','D'] as $opt): ?>
              <option value="<?= $opt ?>" <?= (isset($mc['mc_answer']) && $mc['mc_answer'] === $opt) ? 'selected' : '' ?>><?= $opt ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Cột phải -->
        <div class="mc-col mc-col-right">
          <!-- Khu vực ảnh -->
          <div class="mc-image-zone">
            <h4>Ảnh minh họa</h4>
            <div class="mc-image-preview">
              <?php if (!empty($mc['mc_image_url'])): ?>
              <img src="<?= htmlspecialchars($mc['mc_image_url']) ?>" alt="Hình minh hoạ">
              <?php endif; ?>
            </div>
            <div class="mc-image-buttons">
              <label class="btn-upload">
                Tải ảnh
                <input type="file" id="mc_image" name="image" accept="image/*" hidden>
              </label>
              <button type="button" id="mc_remove_image">Xóa ảnh</button>
            </div>
            <?php if (!empty($mc['mc_image_url'])): ?>
              <input type="hidden" name="existing_image" value="<?= htmlspecialchars($mc['mc_image_url']) ?>">
            <?php endif; ?>
          </div>

          <!-- Khu vực nút thao tác -->
          <div class="mc-buttons">
            <h4>Thao tác</h4>
            <button type="button" id="mc_save">Lưu</button>
            <button type="button" id="mc_delete">Xóa</button>
            <button type="button" id="mc_reset">Làm lại</button>
            <button type="button" id="mc_view_list">Ẩn/ hiện danh sách</button>
            
            <button type="button" id="mc_preview_exam">Làm đề</button>
          </div>
        </div>
      </div>

      <?php if (!empty($mc['mc_id'])): ?>
        <input type="hidden" id="mc_id" name="mc_id" value="<?= (int)$mc['mc_id'] ?>">
      <?php endif; ?>
    </form>

    <div id="mcTableWrapper" style="display: block;">
      <iframe id="mcTableFrame" src="mc_table.php" style="width:100%; height:600px; border:none;"></iframe>
    </div>


    <!-- Khu vực xem trước toàn bộ -->
    <div id="mcPreview" class="mc-preview-zone" style="display:none;">
      <h3>Xem trước toàn bộ</h3>
      <div id="mcPreviewContent"></div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="../../js/form/mc_layout.js"></script>
  <script src="../../js/form/mc_preview.js"></script>
  <script src="../../js/form/mc_image.js"></script>
  <script src="../../js/form/mc_button.js"></script>
  <script src="../../js/form/mc_form_listener.js"></script>

  <script>
  // Nhận dữ liệu từ iframe (mc_table.php)
  window.addEventListener("message", function (event) {
    if (event.data?.type === "mc_select_row") {
      const row = event.data.data;

      document.querySelector('#mc_id')?.value = row.mc_id || '';
      document.querySelector('#mc_topic')?.value = row.mc_topic || '';
      document.querySelector('#mc_question')?.value = row.mc_question || '';

      ['A', 'B', 'C', 'D'].forEach(opt => {
        const input = document.querySelector('#mc_opt_' + opt);
        if (input) input.value = row['mc_opt_' + opt] || '';
      });

      const answer = document.querySelector('#mc_answer');
      if (answer) answer.value = row.mc_answer || '';

      // Cập nhật hình ảnh nếu có
      const imgPreview = document.querySelector('.mc-image-preview');
      if (imgPreview) {
        if (row.mc_image_url) {
          imgPreview.innerHTML = `<img src="${row.mc_image_url}" alt="Hình minh hoạ">`;

          // Gán lại hidden input nếu cần
          let input = document.querySelector('input[name="existing_image"]');
          if (!input) {
            input = document.createElement("input");
            input.type = "hidden";
            input.name = "existing_image";
            document.querySelector('#mcForm').appendChild(input);
          }
          input.value = row.mc_image_url;
        } else {
          imgPreview.innerHTML = '';
        }
      }

      if (window.MathJax) MathJax.typesetPromise();
    }
  });
</script>


  <!-- Xem trước toàn bộ -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const toggleBtn = document.getElementById("mcTogglePreview");
      const previewZone = document.getElementById("mcPreview");
      const previewContent = document.getElementById("mcPreviewContent");

      toggleBtn.addEventListener("click", function () {
        if (previewZone.style.display === "none" || previewZone.style.display === "") {
          // Lấy nội dung để xem trước
          const topic = document.getElementById("mc_topic").value;
          const question = document.getElementById("mc_question").value;
          const opts = ['A', 'B', 'C', 'D'].map(opt => {
            const val = document.getElementById("mc_opt_" + opt).value;
            return `<p><strong>${opt}:</strong> ${val}</p>`;
          }).join('');
          const answer = document.getElementById("mc_answer").value;

          let imageHtml = '';
          const imgTag = document.querySelector(".mc-image-preview img");
          if (imgTag) {
            imageHtml = `<div><strong>Hình minh hoạ:</strong><br><img src="${imgTag.src}" style="max-width: 200px;"></div>`;
          }

          // Tạo nội dung HTML
          previewContent.innerHTML = `
            <p><strong>Chủ đề:</strong> ${topic}</p>
            <p><strong>Câu hỏi:</strong> ${question}</p>
            ${opts}
            <p><strong>Đáp án đúng:</strong> ${answer}</p>
            ${imageHtml}
          `;

          previewZone.style.display = "block";
          toggleBtn.title = "Ẩn xem trước";
          toggleBtn.querySelector("i").classList.replace("fa-eye", "fa-eye-slash");

          // Gọi MathJax để hiển thị công thức
          if (window.MathJax) {
            MathJax.typesetPromise([previewContent]);
          }

        } else {
          previewZone.style.display = "none";
          toggleBtn.title = "Xem trước toàn bộ";
          toggleBtn.querySelector("i").classList.replace("fa-eye-slash", "fa-eye");
        }
      });
    });
  </script>
</body>
</html>
