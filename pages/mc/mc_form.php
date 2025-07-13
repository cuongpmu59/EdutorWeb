<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="../../css/main_ui.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>

<div class="form-container">
    <form id="mcForm" class="question-form" method="POST" action="mc_insert.php" enctype="multipart/form-data">
    <!-- ID ẩn để cập nhật -->
    <input type="hidden" id="mc_id" name="mc_id">

    <div class="form-group">
      <label for="mc_topic">📚 Chủ đề:</label>
      <input type="text" id="mc_topic" name="mc_topic" required>
    </div>

    <div class="form-group">
      <label for="mc_question">❓ Câu hỏi:</label>
      <textarea id="mc_question" name="mc_question" required></textarea>
    </div>

    <div class="form-group">
      <label for="mc_answer1">A:</label>
      <input type="text" id="mc_answer1" name="mc_answer1" required>
    </div>
    <div class="form-group">
      <label for="mc_answer2">B:</label>
      <input type="text" id="mc_answer2" name="mc_answer2" required>
    </div>
    <div class="form-group">
      <label for="mc_answer3">C:</label>
      <input type="text" id="mc_answer3" name="mc_answer3" required>
    </div>
    <div class="form-group">
      <label for="mc_answer4">D:</label>
      <input type="text" id="mc_answer4" name="mc_answer4" required>
    </div>

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
      <label for="mc_image">🖼️ Ảnh minh hoạ:</label>
      <input type="file" id="mc_image" name="mc_image" accept="image/*">
      <br>
      <img id="mc_imagePreview" src="" style="display:none; max-height:150px; margin-top:10px">
    </div>

    <div class="form-actions">
      <button type="submit" id="saveBtn">💾 Lưu câu hỏi</button>
      <button type="reset" id="resetBtn">🔄 Làm lại</button>
      <button type="button" onclick="scrollToListTabInIframe()">📄 Xem danh sách</button>
    </div>
  </form>
</div>

<!-- Iframe hiển thị bảng câu hỏi -->
<iframe id="mcIframe" src="mc_table.php" width="100%" height="500" style="border:1px solid #ccc; margin-top:20px;"></iframe>

<script>
window.addEventListener('message', function (event) {
  if (event.data?.type === 'mc_selected_row') {
    console.log('✅ Đã nhận từ iframe:', event.data.data);
    const d = event.data.data;

    document.getElementById('mc_id').value = d.mc_id || '';
    document.getElementById('mc_topic').value = d.mc_topic || '';
    document.getElementById('mc_question').value = d.mc_question || '';
    document.getElementById('mc_answer1').value = d.mc_answer1 || '';
    document.getElementById('mc_answer2').value = d.mc_answer2 || '';
    document.getElementById('mc_answer3').value = d.mc_answer3 || '';
    document.getElementById('mc_answer4').value = d.mc_answer4 || '';
    document.getElementById('mc_correct_answer').value = d.mc_correct_answer || '';

    const img = document.getElementById('mc_imagePreview');
    if (img && d.mc_image_url) {
      img.src = d.mc_image_url;
      img.style.display = 'block';
    } else {
      img.style.display = 'none';
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
});

// Gửi yêu cầu chuyển tab trong iframe
function scrollToListTabInIframe() {
  const iframe = document.getElementById('mcIframe');
  if (iframe?.contentWindow) {
    iframe.contentWindow.postMessage({ type: 'scrollToListTab' }, '*');
  }
}
</script>

</body>
</html>
