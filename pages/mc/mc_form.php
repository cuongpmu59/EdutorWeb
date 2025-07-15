<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="../../css/main_ui.css">
  <link rel="stylesheet" href="../../css/modules/preview.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>

<div class="mc-layout">
  <!-- Cột nội dung -->
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
        <label for="<?= $id ?>"><?= $label ?>:</label>
        <<?= $isTextarea ? 'textarea' : 'input type="text"' ?> 
          id="<?= $id ?>" 
          name="<?= $id ?>" 
          required 
          class="autosize-text"><?= $isTextarea ? '' : '' ?></<?= $isTextarea ? 'textarea' : 'input' ?>>
        <div id="preview_<?= $id ?>" class="preview-box" style="display: none;"></div>
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
      <label for="mc_image">🖼️ Ảnh minh hoạ:</label>
      <input type="file" id="mc_image" name="mc_image" accept="image/*" style="display: none;">
      <button type="button" id="loadImageBtn">📂 Load ảnh</button>
      <button type="button" id="deleteImageBtn">❌ Xoá ảnh</button>
      <br>
      <img id="mc_imagePreview" src="" style="display:none; max-height:150px; margin-top:10px">
    </div>
  </form>

  <!-- Cột bên phải: nút điều khiển -->
  <div class="control-panel">
    <button type="submit" form="mcForm" id="saveBtn">💾 Lưu câu hỏi</button>
    <button type="reset" form="mcForm" id="resetBtn">🔄 Làm lại</button>
    <button type="button" id="deleteQuestionBtn">🗑️ Xoá câu hỏi</button>
    <button type="button" id="toggleIframeBtn">🔼 Hiện bảng câu hỏi</button>
    <button type="button" id="togglePreviewBtn">👁️‍🗨️ Xem trước</button>
  </div>
</div>

<!-- Iframe bảng câu hỏi -->
<iframe id="mcIframe" src="mc_table.php" width="100%" height="500" style="border:1px solid #ccc; margin-top:20px; display:none;"></iframe>

<!-- JS xử lý -->
<script src="js/modules/previewView.js"></script>

<script>
const imageInput = document.getElementById("mc_image");
const imagePreview = document.getElementById("mc_imagePreview");
const saveBtn = document.getElementById("saveBtn");

document.getElementById("mcForm").addEventListener("submit", async function (e) {
  e.preventDefault();
  const formData = new FormData(this);
  try {
    const response = await fetch("utils/mc_save.php", { method: "POST", body: formData });
    const result = await response.text();
    const iframe = document.createElement("iframe");
    iframe.style.display = "none";
    document.body.appendChild(iframe);
    iframe.contentDocument.write(result);
    iframe.contentDocument.close();
    setTimeout(() => iframe.remove(), 1000);
  } catch (error) {
    alert("❌ Lỗi khi gửi dữ liệu: " + error.message);
  }
});

window.addEventListener("message", function (event) {
  const d = event.data?.data || {};
  if (event.data.type === "saved") {
    alert("✅ Đã lưu thành công!");
    document.getElementById("mcIframe").contentWindow.location.reload();
    document.getElementById("mcForm").reset();
    imagePreview.style.display = "none";
  } else if (event.data.type === "error") {
    alert("❌ Lỗi: " + event.data.message);
  } else if (event.data.type === "mc_select_row") {
    document.getElementById("mc_id").value = d.id || "";
    document.getElementById("mc_topic").value = d.topic || "";
    document.getElementById("mc_question").value = d.question || "";
    document.getElementById("mc_answer1").value = d.answer1 || "";
    document.getElementById("mc_answer2").value = d.answer2 || "";
    document.getElementById("mc_answer3").value = d.answer3 || "";
    document.getElementById("mc_answer4").value = d.answer4 || "";
    document.getElementById("mc_correct_answer").value = d.correct || "";
    if (d.image) {
      imagePreview.src = d.image;
      imagePreview.style.display = "block";
    } else {
      imagePreview.style.display = "none";
    }
    updatePreviews();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
});

document.getElementById("loadImageBtn").addEventListener("click", () => {
  imageInput.click();
});

imageInput.addEventListener("change", function () {
  const file = this.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
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
  if (!id) return alert("❗ Câu hỏi chưa có ID.");
  if (!confirm("❌ Xác nhận xoá ảnh khỏi Cloudinary?")) return;

  try {
    const res = await fetch("utils/mc_delete_image.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ mc_id: id })
    });
    const result = await res.json();
    if (result.success) {
      imagePreview.style.display = "none";
      imageInput.value = "";
      alert("🧹 Đã xoá ảnh!");
      saveBtn.click(); // Tự lưu sau khi xoá
    } else {
      alert("❌ Lỗi khi xoá ảnh.");
    }
  } catch {
    alert("❌ Xảy ra lỗi khi xoá ảnh.");
  }
});

document.getElementById("deleteQuestionBtn").addEventListener("click", async () => {
  const id = document.getElementById("mc_id").value;
  if (!id) return alert("❗ Chưa có câu hỏi nào được chọn.");
  if (!confirm("🗑️ Xác nhận xoá câu hỏi này?")) return;

  try {
    const res = await fetch("utils/mc_delete.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ mc_id: id })
    });
    const result = await res.json();
    if (result.success) {
      alert("🗑️ Đã xoá câu hỏi!");
      document.getElementById("mcForm").reset();
      imagePreview.style.display = "none";
      document.getElementById("mcIframe").contentWindow.location.reload();
    } else {
      alert("❌ Xoá thất bại.");
    }
  } catch {
    alert("❌ Lỗi khi gửi yêu cầu xoá.");
  }
});

document.getElementById("toggleIframeBtn").addEventListener("click", () => {
  const iframe = document.getElementById("mcIframe");
  const btn = document.getElementById("toggleIframeBtn");
  const shown = iframe.style.display === "block";
  iframe.style.display = shown ? "none" : "block";
  btn.textContent = shown ? "🔼 Hiện bảng câu hỏi" : "🔽 Ẩn bảng câu hỏi";
});
</script>
</body>
</html>
