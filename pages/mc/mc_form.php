<?php
require_once __DIR__ . '/../../includes/db_connection.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="../../css/form_ui.css">

  <script>
    window.MathJax = {
      tex: { inlineMath: [['$', '$'], ['\\(', '\\)']] },
      svg: { fontCache: 'global' }
    };
  </script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js" async></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="container">
    <form id="mcForm" method="POST" enctype="multipart/form-data">
      <h2>
        Câu hỏi trắc nghiệm
        <span id="mcTogglePreview" title="Xem trước toàn bộ"><i class="fa fa-eye"></i></span>
      </h2>
      <!-- Xem trước toàn bộ -->
      <div id="mcPreview" class="mc-preview-zone" style="display:none;">
        <div id="mcPreviewContent"></div>
      </div>

      <div id="mcMainContent" class="mc-columns">
        <!-- Cột trái -->
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

          <?php
          $labels = ['A', 'B', 'C', 'D'];
          for ($i = 1; $i <= 4; $i++):
            $label = $labels[$i - 1];
          ?>
            <div class="mc-field mc-inline-field">
              <label for="mc_answer<?= $i ?>"><?= $label ?>.</label>
              <button type="button" class="toggle-preview" data-target="mc_answer<?= $i ?>"><i class="fa fa-eye"></i></button>
              <input type="text" id="mc_answer<?= $i ?>" name="answer<?= $i ?>" required value="">
              <div class="preview-box" id="preview-mc_answer<?= $i ?>" style="display:none;"></div>
            </div>
          <?php endfor; ?>

          <div class="mc-field mc-inline-field">
            <label for="mc_correct_answer">Đáp án:</label>
            <select id="mc_correct_answer" name="answer" required>
              <?php foreach ($labels as $label): ?>
                <option value="<?= $label ?>"><?= $label ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Cột phải -->
        <div class="mc-col mc-col-right">
          <div class="mc-image-zone">
            <h4>Ảnh minh họa</h4>
            <div class="mc-image-preview">
              <img id="mc_image_preview" src="" alt="Hình minh hoạ" style="display:none;">
              <div class="image-name">ten_file.jpg</div>
            </div>

            <div class="mc-image-buttons">
              <label class="btn-upload">
                Tải ảnh
                <input type="file" id="mc_image" name="image" accept="image/*" hidden>
              </label>
              <button type="button" id="mc_remove_image">Xóa ảnh</button>
            </div>
            <input type="hidden" name="existing_image" id="existing_image" value="">
            <input type="hidden" name="public_id" id="public_id" value="">
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

      <!-- Hidden: mc_id để cập nhật -->
      <input type="hidden" id="mc_id" name="mc_id" value="">
    </form>

    <!-- iframe bảng -->
    <div id="mcTableWrapper" style="display:none;">
      <iframe id="mcTableFrame" src="mc_table.php" style="width:100%; height:600px; border:none;"></iframe>
    </div>
  </div>

  <!-- JavaScript xử lý -->
  <script src="../../js/form/mc_layout.js"></script>
  <script src="../../js/form/mc_preview.js"></script>
  <script src="../../js/form/mc_image.js"></script>
  <script src="../../js/form/mc_fetch_data.js"></script>
  <script src="../../js/form/mc_button_action.js"></script>

</body>
</html>
