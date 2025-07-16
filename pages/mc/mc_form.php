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

  <!-- Bên trái: Nội dung -->
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
  </div>

  <!-- Bên phải: Ảnh minh hoạ và các nút -->
  <div class="form-right">
    <div class="form-right-inner">
      <div class="image-box">
        <input type="file" id="mc_image" name="mc_image" accept="image/*" style="display: none;">
        <button type="button" id="loadImageBtn">📂 Load ảnh</button>
        <button type="button" id="deleteImageBtn">❌ Xoá ảnh</button>
        <img id="mc_imagePreview" src="">
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

<iframe id="mcIframe" src="/pages/mc/mc_table.php" width="100%" height="500"
        style="border:1px solid #ccc; margin-top:20px; display:none;"></iframe>

<script src="/js/modules/previewView.js"></script>
<script>
const imageInput = document.getElementById("mc_image");
const imagePreview = document.getElementById("mc_imagePreview");

document.getElementById("mcForm").addEventListener("submit", async function (e) {
  e.preventDefault();
  const formData = new FormData(this);
  try {
    const response = await fetch("../../utils/mc_save.php", {
      method: "POST",
      body: formData
    });
    const result = await response.text();
    const tempFrame = document.createElement("iframe");
    tempFrame.style.display = "none";
    document.body.appendChild(tempFrame);
    tempFrame.contentDocument.write(result);
    tempFrame.contentDocument.close();
    setTimeout(() => tempFrame.remove(), 1000);
  } catch (error) {
    alert("❌ Lỗi khi gửi dữ liệu: " + error.message);
  }
});

window.addEventListener("message", function (event) {
  const d = event.data;
  if (d.type === "saved") {
    alert("✅ Đã lưu thành công!");
    const iframe = document.getElementById("mcIframe");
    iframe.style.display = "block";
    iframe.src = iframe.src;
    document.getElementById("mcForm").reset();
    imagePreview.style.display = "none";
  } else if (d.type === "error") {
    alert("❌ Lỗi: " + d.message);
  } else if (d.type === "mc_select_row") {
    const data = d.data;
    document.getElementById("mc_id").value = data.id || "";
    document.getElementById("mc_topic").value = data.topic || "";
    document.getElementById("mc_question").value = data.question || "";
    document.getElementById("mc_answer1").value = data.answer1 || "";
    document.getElementById("mc_answer2").value = data.answer2 || "";
    document.getElementById("mc_answer3").value = data.answer3 || "";
    document.getElementById("mc_answer4").value = data.answer4 || "";
    document.getElementById("mc_correct_answer").value = data.correct || "";

    if (data.image) {
      imagePreview.src = data.image;
      imagePreview.style.display = "block";
    } else {
      imagePreview.style.display = "none";
    }

    if (typeof updatePreviews === "function") {
      updatePreviews();
      if (window.MathJax) MathJax.typesetPromise();
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
});

document.getElementById("loadImageBtn").addEventListener("click", () => imageInput.click());

imageInput.addEventListener("change", function (e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = e => {
      imagePreview.src = e.target.result;
      imagePreview.style.display = "block";
    };
    reader.readAsDataURL(file);
  } else {
    imagePreview.style.display = "none";
  }
});

document.getElementById("deleteImageBtn").addEventListener("click", async () => {
  const id = document.getElementById("mc_id").value;
  if (!id) return alert("❗ Câu hỏi chưa có ID. Không thể xoá ảnh.");
  if (!confirm("❌ Xác nhận xoá ảnh minh hoạ?")) return;

  try {
    const res = await fetch("/utils/mc_delete_image.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ mc_id: id })
    });
    const result = await res.json();
    if (result.success) {
      imagePreview.style.display = "none";
      imageInput.value = "";
      alert("🧹 Đã xoá ảnh!");
      document.getElementById("saveBtn").click();
    } else {
      alert("❌ Lỗi khi xoá ảnh.");
    }
  } catch (err) {
    alert("❌ Xảy ra lỗi khi xoá ảnh.");
  }
});

document.getElementById("deleteQuestionBtn").addEventListener("click", async () => {
  const id = document.getElementById("mc_id").value;
  if (!id) return alert("❗ Chưa có câu hỏi nào được chọn.");
  if (!confirm("🗑️ Bạn có chắc muốn xoá câu hỏi này?")) return;

  try {
    const res = await fetch("/utils/mc_delete.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ mc_id: id })
    });
    const result = await res.json();
    if (result.success) {
      alert("🗑️ Đã xoá câu hỏi!");
      document.getElementById("mcForm").reset();
      imagePreview.style.display = "none";
      document.getElementById("mcIframe").src = document.getElementById("mcIframe").src;
    } else {
      alert("❌ Xoá thất bại.");
    }
  } catch (err) {
    alert("❌ Lỗi khi gửi yêu cầu xoá.");
  }
});

document.getElementById("toggleIframeBtn").addEventListener("click", () => {
  const iframe = document.getElementById("mcIframe");
  const show = iframe.style.display === "none";
  iframe.style.display = show ? "block" : "none";
  toggleIframeBtn.textContent = show ? "🔽 Ẩn bảng câu hỏi" : "🔼 Hiện bảng câu hỏi";
});
</script>

</body>
</html>
