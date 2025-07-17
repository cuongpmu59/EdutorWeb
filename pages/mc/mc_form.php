<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="/css/main_ui.css">
  <link rel="stylesheet" href="/css/modules/form.css">
  <link rel="stylesheet" href="/css/modules/preview.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>

<form id="mcForm" class="form-layout" enctype="multipart/form-data">
  <input type="hidden" id="mc_id" name="mc_id">

  <div class="form-left">
    <!-- Chủ đề + bộ lọc -->
    <div class="form-group">
      <label for="mc_topic">📚 Chủ đề:</label>
      <input type="text" id="mc_topic" name="mc_topic" list="mc_topic_list" required autocomplete="off">
      <?php include_once __DIR__ . '/filter.php'; ?>
    </div>

    <!-- Câu hỏi và đáp án -->
    <?php
    $fields = [
      ['id' => 'mc_question', 'label' => '❓ Câu hỏi', 'type' => 'textarea'],
      ['id' => 'mc_answer1', 'label' => '🔸 A'],
      ['id' => 'mc_answer2', 'label' => '🔸 B'],
      ['id' => 'mc_answer3', 'label' => '🔸 C'],
      ['id' => 'mc_answer4', 'label' => '🔸 D'],
    ];

    foreach ($fields as $field):
      $id = $field['id'];
      $label = $field['label'];
      $isTextarea = ($field['type'] ?? '') === 'textarea';
    ?>
      <div class="form-group">
        <label for="<?= $id ?>">
          <?= $label ?> <span id="eye_<?= $id ?>" class="toggle-preview">👁️</span>
        </label>
        <?php if ($isTextarea): ?>
          <textarea id="<?= $id ?>" name="<?= $id ?>" required autocomplete="off"></textarea>
        <?php else: ?>
          <input type="text" id="<?= $id ?>" name="<?= $id ?>" required autocomplete="off">
        <?php endif; ?>
        <div id="preview_<?= $id ?>" class="preview-box"></div>
      </div>
    <?php endforeach; ?>

    <!-- Đáp án đúng -->
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
  </div>

  <div class="form-right">
    <div class="form-right-inner">

      <!-- Hình ảnh minh hoạ -->
      <div class="image-box">
        <input type="file" id="mc_image" name="mc_image" accept="image/*" style="display: none;">
        <button type="button" id="loadImageBtn">📂 Load ảnh</button>
        <button type="button" id="deleteImageBtn">❌ Xoá ảnh</button>
        <img id="mc_imagePreview" src="" style="display:none">
      </div>

      <!-- Nút chức năng -->
      <div class="form-actions">
        <button type="submit" id="saveBtn">💾 Lưu câu hỏi</button>
        <button type="reset" id="resetBtn">🔄 Làm lại</button>
        <button type="button" id="deleteQuestionBtn">🗑️ Xoá câu hỏi</button>
        <button type="button" id="toggleIframeBtn">🔼 Hiện bảng câu hỏi</button>
      </div>
    </div>
  </div>
</form>

<!-- Bảng câu hỏi -->
<iframe id="mcIframe" src="/pages/mc/mc_table.php" width="100%" height="500"
        style="border:1px solid #ccc; margin-top:20px; display:none;"></iframe>

<!-- Script xử lý -->
<script src="/js/modules/previewView.js"></script>
<script src="/js/modules/mc_form.js"></script>

</body>
</html>
