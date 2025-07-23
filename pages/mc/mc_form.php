<?php
session_start();
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
    <form id="mcForm" method="POST" enctype="multipart/form-data" action="../../includes/save.php">
      <h2>
        Câu hỏi trắc nghiệm
        <span id="mcTogglePreview" title="Xem trước toàn bộ"><i class="fa fa-eye"></i></span>
      </h2>

      <div id="mcMainContent" class="mc-columns">
        <div class="mc-col mc-col-left">
          <div class="mc-field">
            <label for="mc_topic">Chủ đề:</label>
            <input type="text" id="mc_topic" name="topic" required value="">
          </div>

          <div class="mc-field">
            <label for="mc_question">Câu hỏi:
              <button type="button" class="toggle-preview" data-target="mc_question">
                <i class="fa fa-eye"></i></button>
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

        <div class="mc-col mc-col-right">
          <div class="mc-image-zone">
            <h4>Ảnh minh họa</h4>
            <div class="mc-image-preview"></div>

            <div class="mc-image-buttons">
              <label class="btn-upload">
                Tải ảnh
                <input type="file" id="mc_image" name="image" accept="image/*" hidden>
              </label>
              <button type="button" id="mc_remove_image">Xóa ảnh</button>
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

      <!-- Trường hidden sẽ được thêm bằng JS nếu có mc_id -->
      <!-- <input type="hidden" name="mc_id" id="mc_id" value=""> -->

      <!-- Trường ẩn định danh loại form -->
      <input type="hidden" name="form_type" value="mc_question">
    </form>

    <div id="mcTableWrapper" style="display:none;">
      <iframe id="mcTableFrame" src="mc_table.php" style="width:100%; height:600px; border:none;"></iframe>
    </div>

    <div id="mcPreview" class="mc-preview-zone" style="display:none;">
      <h3>Xem trước toàn bộ</h3>
      <div id="mcPreviewContent"></div>
    </div>
  </div>

  <script src="../../js/form/mc_layout.js"></script>
  <script src="../../js/form/mc_preview.js"></script>
  <script src="../../js/form/mc_image.js"></script>
  <script src="../../js/form/mc_button.js"></script>
  <script src="../../js/form/mc_listener.js"></script>
  <script src="../../js/form/mc_fetch.js"></script> <!-- Mới thêm -->
</body>
</html>
