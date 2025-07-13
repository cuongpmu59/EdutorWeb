<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="../../css/main_ui.css">
  <link rel="stylesheet" href="css/modules/preview.css">

  <!-- MathJax hỗ trợ công thức -->
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>

<div class="form-container">
  <form id="mcForm" class="question-form" enctype="multipart/form-data">
    <input type="hidden" id="mc_id" name="mc_id">

    <!-- Chủ đề -->
    <div class="form-group">
      <label for="mc_topic">📚 Chủ đề:</label>
      <input type="text" id="mc_topic" name="mc_topic" required>
    </div>

    <!-- Câu hỏi và các đáp án -->
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
      <label for="<?= $id ?>"><?= $label ?>:</label>
      <<?= $isTextarea ? 'textarea' : 'input type="text"' ?> id="<?= $id ?>" name="<?= $id ?>" required></<?= $isTextarea ? 'textarea' : 'input' ?>>
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

    <!-- Ảnh minh hoạ -->
    <div class="form-group">
      <label for="mc_image">🖼️ Ảnh minh hoạ:</label>
      <input type="file" id="mc_image" name="mc_image" accept="image/*">
      <br>
      <img id="mc_imagePreview" src="" style="display:none; max-height:150px; margin-top:10px">
    </div>

    <!-- Nút thao tác -->
    <div class="form-actions">
      <button type="submit" id="saveBtn">💾 Lưu câu hỏi</button>
      <button type="reset" id="resetBtn">🔄 Làm lại</button>
      <button type="button" onclick="scrollToListTabInIframe()">📄 Xem danh sách</button>
    </div>
  </form>
</div>

<!-- Iframe danh sách -->
<iframe id="mcIframe" src="mc_table.php" width="100%" height="500" style="border:1px solid #ccc; margin-top:20px;"></iframe>

<!-- Các script chức năng -->
<script src="js/modules/previewView.js"></script>
<script src="js/modules/save.js"></script>

<!-- Nhận dữ liệu từ bảng (iframe) -->
<script>
window.addEventListener('message', function (event) {
  if (event.data?.type === 'mc_selected_row') {
    const d = event.data.data;
    ['mc_id','mc_topic','mc_question','mc_answer1','mc_answer2','mc_answer3','mc_answer4','mc_correct_answer'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.value = d[id] || '';
    });

    const img = document.getElementById('mc_imagePreview');
    if (d.mc_image_url) {
      img.src = d.mc_image_url;
      img.style.display = 'block';
    } else {
      img.style.display = 'none';
    }

    renderMathPreviewAll();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
});

// Yêu cầu iframe chuyển sang tab danh sách
function scrollToListTabInIframe() {
  const iframe = document.getElementById('mcIframe');
  if (iframe?.contentWindow) {
    iframe.contentWindow.postMessage({ type: 'scrollToListTab' }, '*');
  }
}
</script>

</body>
</html>
