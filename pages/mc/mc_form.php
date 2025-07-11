<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../../dotenv.php'; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>❓ Câu hỏi nhiều lựa chọn</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="../../css/main_ui.css">
  <style>:root { --accent: #3498db; }</style>

  <!-- MathJax -->
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" defer></script>
</head>
<body class="main-layout">

  <!-- Tabs -->
  <div class="tab-bar inner-tabs">
    <button class="tab-button active" data-tab="form">📝 Nhập câu hỏi</button>
    <button class="tab-button" data-url="mc_image.php">🖼️ Chọn ảnh minh hoạ</button>
    <button class="tab-button" data-url="mc_preview.php">👁️ Xem trước</button>
    <button class="tab-button" data-url="mc_table.php">📋 Danh sách</button>
  </div>

  <!-- Nội dung -->
  <div class="tab-content" id="tabContent">
    <!-- Nội dung mặc định: Form nhập liệu -->
    <div id="formTab">
      <div class="mc-form-container card shadow-xl p-4 rounded-2xl bg-light dark:bg-dark">
        <form id="mcForm" class="question-form" method="POST" action="mc_insert.php" enctype="multipart/form-data">
          <input type="hidden" id="mc_id" name="mc_id" value="">

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
            <textarea id="previewFormulaInput" rows="2" class="form-control" placeholder="\\( a^2 + b^2 = c^2 \\)"></textarea>
            <div id="previewFormulaOutput" class="preview-box mt-2 p-3 border border-dashed bg-white dark:bg-gray-800 rounded shadow-sm"></div>
          </div>

          <!-- Đáp án -->
          <?php
            $answers = ['A', 'B', 'C', 'D'];
            foreach ($answers as $i => $label) {
              $n = $i + 1;
              echo <<<HTML
              <div class="form-group">
                <label for="mc_answer$n">🔠 Đáp án $n ($label):</label>
                <input type="text" id="mc_answer$n" name="mc_answer$n" class="form-control" required>
              </div>
              HTML;
            }
          ?>

          <!-- Đáp án đúng -->
          <div class="form-group">
            <label for="mc_correct_answer">✅ Đáp án đúng:</label>
            <select id="mc_correct_answer" name="mc_correct_answer" class="form-control" required>
              <option value="">-- Chọn đáp án đúng --</option>
              <?php foreach ($answers as $opt) echo "<option value=\"$opt\">$opt</option>"; ?>
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
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Script xử lý LaTeX và validation -->
  <script src="../../js/modules/mathPreview.js"></script>
  <script>
    const $ = id => document.getElementById(id);
    const form = $("mcForm");

    form?.addEventListener("submit", function (e) {
      const fields = ["mc_topic", "mc_question", "mc_answer1", "mc_answer2", "mc_answer3", "mc_answer4", "mc_correct_answer"];
      const valid = fields.every(id => $(id)?.value.trim());
      $("formWarning").style.display = valid ? "none" : "block";
      if (!valid) e.preventDefault();
    });

    // Preview LaTeX
    const input = $("previewFormulaInput"), output = $("previewFormulaOutput");
    if (input && output && typeof updateLivePreview === "function") {
      input.addEventListener("input", () => updateLivePreview(input, output));
      updateLivePreview(input, output);
    }
  </script>

  <!-- Script điều khiển tab động -->
  <script type="module">
    document.querySelectorAll(".tab-button").forEach(btn => {
      btn.addEventListener("click", async () => {
        document.querySelectorAll(".tab-button").forEach(b => b.classList.remove("active"));
        btn.classList.add("active");

        const url = btn.dataset.url;
        const content = document.getElementById("tabContent");

        // Nếu là tab Nhập liệu thì hiện sẵn
        if (!url && btn.dataset.tab === "form") {
          document.getElementById("formTab").style.display = "block";
          return;
        }

        // Ẩn form và tải tab khác
        document.getElementById("formTab").style.display = "none";
        if (url && content) {
          const res = await fetch(url);
          content.innerHTML = `<div>${await res.text()}</div>`;
          MathJax.typesetPromise?.();
        }
      });
    });
  </script>
</body>
</html>
