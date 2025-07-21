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
          <!-- Các trường nhập liệu -->
          ...
        </div>

        <!-- Cột phải -->
        <div class="mc-col mc-col-right">
          <!-- Ảnh minh họa và các nút thao tác -->
          ...
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
  <script src="../../js/form/mc_preview_all.js"></script> <!-- ✅ đã thêm dòng này -->

  <!-- Nhận dữ liệu từ iframe -->
  <script>
  window.addEventListener("message", function (event) {
    if (event.data?.type === "mc_select_row") {
      const row = event.data.data;

      document.querySelector('#mc_id')?.value = row.id || '';
      document.querySelector('#mc_topic')?.value = row.topic || '';
      document.querySelector('#mc_question')?.value = row.question || '';

      ['1', '2', '3', '4'].forEach(i => {
        const input = document.querySelector('#mc_answer' + i);
        if (input) input.value = row['answer' + i] || '';
      });

      const answer = document.querySelector('#mc_answer');
      if (answer) answer.value = row.correct || '';

      // Hình ảnh
      const imgPreview = document.querySelector('.mc-image-preview');
      if (imgPreview) {
        if (row.image) {
          imgPreview.innerHTML = `<img src="${row.image}" alt="Hình minh hoạ">`;

          // hidden input
          let input = document.querySelector('input[name="existing_image"]');
          if (!input) {
            input = document.createElement("input");
            input.type = "hidden";
            input.name = "existing_image";
            document.querySelector('#mcForm').appendChild(input);
          }
          input.value = row.image;
        } else {
          imgPreview.innerHTML = '';
          const input = document.querySelector('input[name="existing_image"]');
          if (input) input.remove();
        }
      }

      if (window.MathJax) MathJax.typesetPromise();
    }
  });
  </script>
</body>
</html>
