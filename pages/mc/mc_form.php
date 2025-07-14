<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="../../css/main_ui.css">
  <link rel="stylesheet" href="../../css/modules/preview.css">

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

<!-- Script xem trước công thức MathJax -->
<script src="js/modules/previewView.js"></script>

<!-- Script xử lý gửi form đến utils/mc_save.php -->
<script>
document.getElementById("mcForm").addEventListener("submit", async function (e) {
  e.preventDefault();
  const form = document.getElementById("mcForm");
  const formData = new FormData(form);

  try {
    const response = await fetch("utils/mc_save.php", {
      method: "POST",
      body: formData
    });
    const text = await response.text();
    const iframe = document.createElement("iframe");
    iframe.style.display = "none";
    document.body.appendChild(iframe);
    iframe.contentDocument.write(text);
    iframe.contentDocument.close();
    setTimeout(() => iframe.remove(), 1000);
  } catch (error) {
    alert("❌ Lỗi khi gửi dữ liệu: " + error.message);
  }
});

// Nhận phản hồi từ iframe (postMessage)
window.addEventListener("message", function (event) {
  if (event.data.type === "saved") {
    alert("✅ Đã lưu thành công!");
    document.getElementById("mcIframe").contentWindow.location.reload();
    document.getElementById("mcForm").reset();
    document.getElementById("mc_imagePreview").style.display = "none";
  } else if (event.data.type === "error") {
    alert("❌ Lỗi: " + event.data.message);
  }
});

// Xem trước ảnh
document.getElementById("mc_image").addEventListener("change", function (e) {
  const img = document.getElementById("mc_imagePreview");
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      img.src = e.target.result;
      img.style.display = "block";
    };
    reader.readAsDataURL(file);
  } else {
    img.style.display = "none";
  }
});

// Cuộn đến bảng bên dưới
function scrollToListTabInIframe() {
  document.getElementById("mcIframe").scrollIntoView({ behavior: 'smooth' });
}
</script>

</body>
</html>
