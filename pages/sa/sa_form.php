<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../../env/config.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📝 Câu hỏi trắc nghiệm trả lời ngắn</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../css/main_ui.css">
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" defer></script>
</head>
<body>
<h2>✏️ Câu hỏi tự luận / ngắn</h2>

<!-- Tabs -->
<div class="tab-container">
  <div class="tab-button active" data-tab="tab-form">📝 Nhập câu hỏi</div>
  <div class="tab-button" data-tab="tab-image">🖼️ Ảnh minh hoạ</div>
  <div class="tab-button" data-tab="tab-preview">👁️ Xem trước toàn bộ</div>
</div>

<!-- Tab 1: Form nhập liệu -->
<div class="tab-content active" id="tab-form">
  <form id="saForm" class="question-form" method="POST" action="sa_insert.php" enctype="multipart/form-data">
    <input type="hidden" id="sa_id" name="sa_id">
    <input type="hidden" id="sa_image_url" name="sa_image_url">
    <input type="file" id="sa_image" name="sa_image" style="display:none;">

    <div class="form-group">
      <label for="sa_topic">📚 Chủ đề:</label>
      <input type="text" id="sa_topic" name="sa_topic" class="form-control" required>
    </div>

    <div class="form-group">
      <label for="sa_question">🧠 Câu hỏi:</label>
      <textarea id="sa_question" name="sa_question" rows="3" class="form-control" required></textarea>
    </div>

    <div class="form-group">
      <label for="previewFormulaInput">📌 Xem trước công thức (LaTeX):</label>
      <textarea id="previewFormulaInput" rows="2" class="form-control" placeholder="\\( a^2 + b^2 = c^2 \\)"></textarea>
      <div id="previewFormulaOutput" class="preview-box mt-2 p-3 border bg-white dark:bg-gray-800 rounded shadow-sm"></div>
    </div>

    <div class="form-group">
      <label for="sa_correct_answer">✅ Đáp án đúng (dạng văn bản):</label>
      <textarea id="sa_correct_answer" name="sa_correct_answer" rows="2" class="form-control" required></textarea>
    </div>

    <div id="formWarning" class="form-warning alert alert-warning" style="display:none;">
      ⚠️ Vui lòng nhập đầy đủ tất cả các trường bắt buộc.
    </div>

    <div class="form-actions mt-3 flex gap-2">
      <button type="submit" class="btn btn-primary">💾 Lưu</button>
      <button type="reset" class="btn btn-secondary">🔄 Làm mới</button>
      <button type="button" id="deleteBtn" class="btn btn-danger" style="display:none;">🗑️ Xoá</button>
      <button type="button" id="exportPdfBtn" class="btn btn-secondary">📝 Xuất PDF</button>
    </div>
  </form>
</div>

<!-- Tab 2: Ảnh minh hoạ -->
<div class="tab-content" id="tab-image">
  <p><strong>Ảnh minh hoạ hiện tại:</strong></p>
  <img id="imageTabPreview" style="max-height:150px; border:1px solid #ccc; display:none;">
  <div id="imageTabFileName" style="color:gray; font-style:italic;"></div>
  <button type="button" class="btn-danger" id="delete_image_tab" style="display:none;">🗑️ Xoá ảnh</button>
  <button type="button" class="btn-secondary" id="select_image_tab">📂 Chọn ảnh</button>
</div>

<!-- Tab 3: Xem trước toàn bộ -->
<div class="tab-content" id="tab-preview">
  <div id="preview_area"><em>⚡ Nội dung xem trước sẽ hiển thị tại đây...</em></div>
  <img id="preview_image" style="display:none; max-height:150px; margin-top:10px; border:1px solid #ccc;">
</div>

<!-- Danh sách câu hỏi -->
<iframe id="questionIframe" src="get_question.php" width="100%" height="500" style="margin-top:30px; border:1px solid #aaa;"></iframe>

<!-- Cloudinary từ env -->
<script>
  const CLOUDINARY_CLOUD_NAME = "<?= CLOUDINARY_CLOUD_NAME ?>";
  const CLOUDINARY_UPLOAD_PRESET = "<?= CLOUDINARY_UPLOAD_PRESET ?>";
</script>

<!-- JavaScript xử lý -->
<script src="../../js/modules/mathPreview.js"></script>
<script type="module" src="js/modules/controller.js"></script>

<script>
  // Validate
  document.getElementById("saForm").addEventListener("submit", function (e) {
    const ids = ["sa_topic", "sa_question", "sa_correct_answer"];
    let isValid = ids.every(id => document.getElementById(id)?.value.trim());
    document.getElementById("formWarning").style.display = isValid ? "none" : "block";
    if (!isValid) e.preventDefault();
  });

  // Xem trước công thức
  const formulaInput = document.getElementById("previewFormulaInput");
  const formulaOutput = document.getElementById("previewFormulaOutput");
  if (formulaInput && formulaOutput && typeof updateLivePreview === "function") {
    formulaInput.addEventListener("input", () => updateLivePreview(formulaInput, formulaOutput));
    updateLivePreview(formulaInput, formulaOutput);
  }

  // Chuyển tab
  document.querySelectorAll(".tab-button").forEach(btn => {
    btn.addEventListener("click", () => {
      document.querySelectorAll(".tab-button").forEach(b => b.classList.remove("active"));
      document.querySelectorAll(".tab-content").forEach(c => c.classList.remove("active"));
      btn.classList.add("active");
      document.getElementById(btn.dataset.tab).classList.add("active");
    });
  });
</script>
</body>
</html>
