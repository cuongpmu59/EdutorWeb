<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="../../css/main_ui.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>

<div class="form-layout">
  <!-- Cột trái: Form nội dung -->
  <div class="form-left">
    <form id="mcForm" class="question-form" enctype="multipart/form-data">
      <input type="hidden" id="mc_id" name="mc_id">

      <div class="form-group">
        <label for="mc_topic">📚 Chủ đề:</label>
        <input type="text" id="mc_topic" name="mc_topic" required>
      </div>

      <?php
      $fields = [
        'mc_question' => '❓ Câu hỏi',
        'mc_answer1' => '🔸 A',
        'mc_answer2' => '🔸 B',
        'mc_answer3' => '🔸 C',
        'mc_answer4' => '🔸 D'
      ];
      foreach ($fields as $id => $label):
        $isTextarea = $id === 'mc_question';
      ?>
        <div class="form-group">
          <label for="<?= $id ?>">
            <?= $label ?>
            <span id="eye_<?= $id ?>" class="toggle-preview">👁️</span>
            </label>
          <<?= $isTextarea ? 'textarea' : 'input type="text"' ?> id="<?= $id ?>" name="<?= $id ?>" required></<?= $isTextarea ? 'textarea' : 'input' ?>>
          <div id="preview_<?= $id ?>" class="preview-box"></div>
        </div>
      <?php endforeach; ?>

      <div class="form-group">
        <label for="mc_correct_answer">✅ Đáp án đúng:</label>
        <select id="mc_correct_answer" name="mc_correct_answer" required>
          <option value="">-- Chọn --</option>
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="C">C</option>
          <option value="D">D</option>
        </select>
      </div>

      <div class="form-group">
        <label>🖼️ Ảnh minh hoạ:</label><br>
        <input type="file" id="mc_image" name="mc_image" accept="image/*" style="display: none;">
        <button type="button" id="loadImageBtn">📂 Load ảnh</button>
        <button type="button" id="deleteImageBtn">❌ Xoá ảnh</button>
        <img id="mc_imagePreview" src="" style="display:none; max-height:150px; margin-top:10px">
      </div>
    </form>
  </div>

  <!-- Cột phải: Các nút thao tác -->
  <div class="form-right">
    <div class="form-actions">
      <button type="submit" form="mcForm" id="saveBtn">💾 Lưu câu hỏi</button>
      <button type="reset" form="mcForm" id="resetBtn">🔄 Làm lại</button>
      <button type="button" id="deleteQuestionBtn">🗑️ Xoá câu hỏi</button>
      <button type="button" id="toggleIframeBtn">🔼 Hiện bảng câu hỏi</button>
    </div>
  </div>
</div>
        <iframe id="mcIframe" src="mc_table.php" width="100%" height="500"
        style="border:1px solid #ccc; margin-top:20px; display:none;"></iframe>

<script src="js/form/previewView.js"></script>
<script src="js/form/mc_form.js"></script>
<script src="js/form/listener.js"></script>

</body>
</html>
