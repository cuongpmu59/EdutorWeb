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

<!-- 🔽 Tiêu đề có nút ẩn/hiện toàn bộ form -->
<h2 id="formToggleHeader" style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
  <span id="toggleIcon">📂</span>
  <span>Nhập câu hỏi trắc nghiệm nhiều lựa chọn</span>
</h2>

<!-- 🔁 Khối chứa form, mặc định bị ẩn -->
<div id="formContainer" style="display: none;">
  <form id="mcForm" class="form-layout" enctype="multipart/form-data">
    <input type="hidden" id="mc_id" name="mc_id">

    <div class="form-left">
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
            <?= $label ?> <span id="eye_<?= $id ?>" class="toggle-preview">👁️</span>
          </label>
          <?= $isTextarea ? 
            "<textarea id='$id' name='$id' required></textarea>" :
            "<input type='text' id='$id' name='$id' required />" ?>
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

      <!-- 👁️ Xem trước toàn bộ -->
      <div class="form-group">
        <label style="cursor:pointer;" id="togglePreviewHeader">
          <span id="previewToggleIcon">▶️</span> 👁️ Xem trước toàn bộ
        </label>
        <div id="fullPreviewBox" class="preview-box" style="display: none;"></div>
      </div>
    </div>

    <div class="form-right">
      <div class="form-right-inner">
        <div class="image-box">
          <input type="file" id="mc_image" name="mc_image" accept="image/*" style="display: none;">
          <button type="button" id="loadImageBtn">📂 Load ảnh</button>
          <button type="button" id="deleteImageBtn">❌ Xoá ảnh</button>
          <img id="mc_imagePreview" src="" style="display:none">
        </div>

        <div class="form-actions">
          <button type="submit" id="saveBtn">💾 Lưu câu hỏi</button>
          <button type="reset" id="resetBtn">🔄 Làm lại</button>
          <button type="button" id="deleteQuestionBtn">🗑️ Xoá câu hỏi</button>
          <button type="button" id="toggleIframeBtn">🔼 Hiện bảng câu hỏi</button>
        </div>
      </div>
    </div>
  </form>
</div>

<iframe id="mcIframe" src="/pages/mc/mc_table.php" width="100%" height="500"
        style="border:1px solid #ccc; margin-top:20px; display:none;"></iframe>

<script src="/js/modules/previewView.js"></script>
<script src="/js/modules/mc_form.js"></script>

<!-- 🔁 Script toggle & xem trước toàn bộ -->
<script>
  // Toggle hiển thị toàn bộ form
  document.getElementById("formToggleHeader").addEventListener("click", function () {
    const form = document.getElementById("formContainer");
    const icon = document.getElementById("toggleIcon");
    const isVisible = form.style.display !== "none";
    form.style.display = isVisible ? "none" : "block";
    icon.textContent = isVisible ? "📂" : "📁";
  });

  // Toggle khối xem trước toàn bộ
  document.getElementById("togglePreviewHeader").addEventListener("click", function () {
    const box = document.getElementById("fullPreviewBox");
    const icon = document.getElementById("previewToggleIcon");
    const isVisible = box.style.display !== "none";
    box.style.display = isVisible ? "none" : "block";
    icon.textContent = isVisible ? "▶️" : "🔽";
  });

  // Cập nhật nội dung xem trước toàn bộ
  function updateFullPreview() {
    const q = document.getElementById("mc_question").value;
    const a1 = document.getElementById("mc_answer1").value;
    const a2 = document.getElementById("mc_answer2").value;
    const a3 = document.getElementById("mc_answer3").value;
    const a4 = document.getElementById("mc_answer4").value;
    const correct = document.getElementById("mc_correct_answer").value;
    const img = document.getElementById("mc_imagePreview");

    let html = `
      <div><b>❓ Câu hỏi:</b> ${q}</div>
      ${img.src && img.style.display !== "none" ? `<div><b>🖼️ Ảnh minh hoạ:</b><br><img src="${img.src}" style="max-width:100%; max-height:200px;"></div>` : ''}
      <div><b>🔸 A:</b> ${a1}</div>
      <div><b>🔸 B:</b> ${a2}</div>
      <div><b>🔸 C:</b> ${a3}</div>
      <div><b>🔸 D:</b> ${a4}</div>
      <div><b>✅ Đáp án đúng:</b> ${correct}</div>
    `;
    const box = document.getElementById("fullPreviewBox");
    box.innerHTML = html;
    if (window.MathJax) MathJax.typesetPromise([box]);
  }

  // Gắn sự kiện cho tất cả trường nhập
  ['mc_question', 'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4', 'mc_correct_answer'].forEach(id => {
    document.getElementById(id).addEventListener('input', updateFullPreview);
  });

  window.addEventListener("DOMContentLoaded", updateFullPreview);
</script>
</body>
</html>
