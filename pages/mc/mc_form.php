<?php require 'dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi</title>
  <link rel="stylesheet" href="css/styles_question.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" async></script>
</head>
<body>

<h2>📋 Quản lý câu hỏi trắc nghiệm</h2>

<!-- Tabs -->
<div class="tab-container">
  <div class="tab-button active" data-tab="tab-form">📝 Nhập câu hỏi</div>
  <div class="tab-button" data-tab="tab-image">🖼️ Ảnh minh hoạ</div>
  <div class="tab-button" data-tab="tab-preview">👁️ Xem trước toàn bộ</div>
</div>

<!-- Tab 1: Form nhập liệu -->
<div class="tab-content active" id="tab-form">
  <div class="mc-form-container card shadow-xl p-4 rounded-2xl bg-light dark:bg-dark">
    <form id="mcForm" class="question-form" method="POST" action="mc_insert.php" enctype="multipart/form-data">
      <input type="hidden" id="mc_id" name="mc_id" value="">
      <input type="hidden" id="image_url" name="image_url">
      <input type="file" id="image" name="image" style="display:none;">

      <!-- Chủ đề -->
      <div class="form-group">
        <label for="mc_topic">📚 Chủ đề:</label>
        <input type="text" id="mc_topic" name="mc_topic" class="form-control" placeholder="Nhập tên chủ đề..." required>
      </div>

      <!-- Câu hỏi -->
      <div class="form-group">
        <label for="mc_question">🧠 Câu hỏi:</label>
        <textarea id="mc_question" name="mc_question" rows="3" class="form-control" placeholder="Nhập nội dung câu hỏi..." required></textarea>
      </div>

      <!-- Xem trước công thức -->
      <div class="form-group">
        <label for="previewFormulaInput">📌 Xem trước công thức (LaTeX):</label>
        <textarea id="previewFormulaInput" rows="2" class="form-control" placeholder="Ví dụ: \\( a^2 + b^2 = c^2 \\)"></textarea>
        <div id="previewFormulaOutput" class="preview-box mt-2 p-3 border border-dashed bg-white dark:bg-gray-800 rounded shadow-sm"></div>
      </div>

      <!-- Đáp án -->
      <div class="form-group"><label for="mc_answer1">🔠 Đáp án 1 (A):</label><input type="text" id="mc_answer1" name="mc_answer1" class="form-control" required></div>
      <div class="form-group"><label for="mc_answer2">🔠 Đáp án 2 (B):</label><input type="text" id="mc_answer2" name="mc_answer2" class="form-control" required></div>
      <div class="form-group"><label for="mc_answer3">🔠 Đáp án 3 (C):</label><input type="text" id="mc_answer3" name="mc_answer3" class="form-control" required></div>
      <div class="form-group"><label for="mc_answer4">🔠 Đáp án 4 (D):</label><input type="text" id="mc_answer4" name="mc_answer4" class="form-control" required></div>

      <!-- Đáp án đúng -->
      <div class="form-group">
        <label for="mc_correct_answer">✅ Đáp án đúng:</label>
        <select id="mc_correct_answer" name="mc_correct_answer" class="form-control" required>
          <option value="">-- Chọn đáp án đúng --</option>
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="C">C</option>
          <option value="D">D</option>
        </select>
      </div>

      <!-- Cảnh báo -->
      <div id="formWarning" class="form-warning alert alert-warning" style="display: none;">
        ⚠️ Vui lòng nhập đầy đủ tất cả các trường bắt buộc.
      </div>

      <!-- Nút điều khiển -->
      <div class="form-actions mt-3 flex flex-wrap gap-2">
        <button type="submit" class="btn btn-primary">💾 Lưu</button>
        <button type="reset" class="btn btn-secondary">🔄 Làm mới</button>
        <button type="button" id="deleteBtn" class="btn btn-danger" style="display: none;">🗑️ Xoá</button>
        <button type="button" id="exportPdfBtn" class="btn btn-secondary">📝 Xuất đề PDF</button>
      </div>
    </form>
  </div>
</div>

<!-- Tab 2: Quản lý ảnh minh hoạ -->
<div class="tab-content" id="tab-image">
  <p><strong>Ảnh minh hoạ hiện tại:</strong></p>
  <img id="imageTabPreview" style="max-height: 150px; border: 1px solid #ccc; display: none;">
  <div id="imageTabFileName" style="color: gray; font-style: italic;"></div>
  <button type="button" class="btn-danger" id="delete_image_tab" style="display:none;">🗑️ Xoá ảnh</button>
  <button type="button" class="btn-secondary" id="select_image_tab">📂 Chọn ảnh</button>
</div>

<!-- Tab 3: Xem trước toàn bộ -->
<div class="tab-content" id="tab-preview">
  <div id="preview_area"><em>⚡ Nội dung xem trước sẽ hiển thị tại đây...</em></div>
  <img id="preview_image" style="display:none; max-height: 150px; margin-top: 10px; border: 1px solid #ccc;">
</div>

<!-- Iframe hiển thị bảng -->
<iframe id="questionIframe" src="get_question.php" width="100%" height="500" style="margin-top:30px; border: 1px solid #aaa;"></iframe>

<!-- Cloudinary credentials -->
<script>
  const CLOUDINARY_CLOUD_NAME = "<?= env('CLOUDINARY_CLOUD_NAME') ?>";
  const CLOUDINARY_UPLOAD_PRESET = "<?= env('CLOUDINARY_UPLOAD_PRESET') ?>";
</script>

<!-- Module xử lý -->
<script src="../../js/modules/mathPreview.js"></script>
<script type="module" src="js/modules/controller.js"></script>

<!-- Validate và chuyển tab -->
<script>
  // Validate trước khi submit
  document.getElementById("mcForm").addEventListener("submit", function (e) {
    const fields = ["mc_topic", "mc_question", "mc_answer1", "mc_answer2", "mc_answer3", "mc_answer4", "mc_correct_answer"];
    let isValid = true;

    for (const id of fields) {
      const el = document.getElementById(id);
      if (!el || !el.value.trim()) {
        isValid = false;
        break;
      }
    }

    document.getElementById("formWarning").style.display = isValid ? "none" : "block";
    if (!isValid) e.preventDefault();
  });

  // Xem trước công thức toán
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
      const tabId = btn.getAttribute("data-tab");
      document.getElementById(tabId).classList.add("active");
    });
  });
</script>

</body>
</html>
