<?php
// mc_form.php
session_start();

// Kết nối CSDL (sửa lại theo cấu trúc dự án của bạn)
require_once 'includes/db_connect.php'; // file này khởi tạo $conn = new PDO(...)

$mc = null;
if (!empty($_GET['mc_id'])) {
  $id = intval($_GET['mc_id']);
  $stmt = $conn->prepare("SELECT * FROM mc_questions WHERE mc_id = ?");
  $stmt->execute([$id]);
  $mc = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Form Câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="css/mc_form.css">
  <!-- MathJax -->
  <script>
    window.MathJax = {
      tex: { inlineMath: [['$', '$'], ['\\(', '\\)']] },
      svg: { fontCache: 'global' }
    };
  </script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js" async></script>
</head>
<body>
  <div class="container">
    <form id="mcForm" enctype="multipart/form-data">
      <div class="mc-columns">
        <!-- Cột trái -->
        <div class="mc-col-left">
          <h2>Nhập câu trắc nghiệm
            <span id="mcTogglePreview" title="Xem trước LaTeX"><i class="icon-eye"></i></span>
          </h2>
          <div class="mc-field">
            <label for="mc_topic">Chủ đề:</label>
            <input type="text" id="mc_topic" name="topic" value="<?= htmlspecialchars($mc['mc_topic'] ?? '') ?>">
          </div>
          <div class="mc-field">
            <label for="mc_question">Câu hỏi:</label>
            <textarea id="mc_question" name="question"><?= htmlspecialchars($mc['mc_question'] ?? '') ?></textarea>
          </div>
          <?php foreach (['A','B','C','D'] as $opt): ?>
          <div class="mc-field">
            <label for="mc_opt_<?= $opt ?>"><?= $opt ?>.</label>
            <input type="text" id="mc_opt_<?= $opt ?>" name="opt_<?= $opt ?>" value="<?= htmlspecialchars($mc['mc_opt_'.$opt] ?? '') ?>">
          </div>
          <?php endforeach; ?>
          <div class="mc-field">
            <label for="mc_answer">Đáp án:</label>
            <select id="mc_answer" name="answer">
              <?php foreach (['A','B','C','D'] as $opt): ?>
              <option value="<?= $opt ?>" <?= (isset($mc['mc_answer']) && $mc['mc_answer'] == $opt) ? 'selected' : '' ?>><?= $opt ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Cột phải -->
        <div class="mc-col-right">
          <!-- Khu vực ảnh -->
          <div class="mc-image-zone">
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
          </div>

          <!-- Nút thao tác -->
          <div class="mc-buttons">
            <button type="button" id="mc_save">Lưu</button>
            <button type="button" id="mc_delete">Xóa</button>
            <button type="button" id="mc_reset">Làm lại</button>
            <button type="button" id="mc_view_list">Xem danh sách</button>
            <button type="button" id="mc_preview_exam">Làm đề</button>
          </div>
        </div>
      </div>

      <?php if (!empty($mc['mc_id'])): ?>
      <input type="hidden" id="mc_id" name="mc_id" value="<?= $mc['mc_id'] ?>">
      <?php endif; ?>
    </form>

    <!-- Khu vực xem trước -->
    <div id="mcPreview" class="mc-preview-zone" style="display:none;">
      <h3>Xem trước</h3>
      <div id="mcPreviewContent"></div>
    </div>
  </div>

  <!-- Các file JS -->
  <script src="js/mc_layout.js"></script>
  <script src="js/mc_preview.js"></script>
  <script src="js/mc_image.js"></script>
  <script src="js/mc_button.js"></script>
</body>
</html>
