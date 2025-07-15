<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="../../css/main_ui.css">
  <link rel="stylesheet" href="../../css/modules/preview.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
    }
    .form-container {
      max-width: 1000px;
      margin: auto;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 12px #ccc;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .form-group label {
      font-weight: bold;
    }
    textarea, input[type="text"] {
      width: 100%;
      min-height: 38px;
      padding: 8px;
      font-size: 16px;
      resize: vertical;
    }
    select {
      padding: 6px;
      font-size: 16px;
    }
    #mc_imagePreview {
      max-height: 150px;
      margin-top: 10px;
      display: none;
    }
    .form-actions {
      margin-top: 20px;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }
    button {
      padding: 10px 15px;
      font-size: 16px;
      cursor: pointer;
    }
    .preview-box {
      display: none;
      margin-top: 5px;
      padding: 10px;
      background: #f4f4f4;
      border-left: 4px solid #007bff;
      white-space: pre-wrap;
    }
  </style>
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>

<div class="form-container">
  <form id="mcForm" enctype="multipart/form-data">
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
    foreach ($fields as $id => $label): ?>
      <div class="form-group">
        <label for="<?= $id ?>"><?= $label ?>:</label>
        <textarea id="<?= $id ?>" name="<?= $id ?>" required></textarea>
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

    <div class="form-group">
      <label>🖼️ Ảnh minh hoạ:</label>
      <input type="file" id="mc_image" name="mc_image" accept="image/*" style="display:none;">
      <button type="button" id="loadImageBtn">📂 Load ảnh</button>
      <button type="button" id="deleteImageBtn">❌ Xoá ảnh</button><br>
      <img id="mc_imagePreview" src="">
    </div>

    <div class="form-actions">
      <button type="submit" id="saveBtn">💾 Lưu câu hỏi</button>
      <button type="reset" id="resetBtn">🔄 Làm lại</button>
      <button type="button" id="deleteQuestionBtn">🗑️ Xoá câu hỏi</button>
      <button type="button" id="toggleIframeBtn">🔼 Hiện bảng câu hỏi</button>
    </div>
  </form>
</div>

<iframe id="mcIframe" src="mc_table.php" width="100%" height="500"
        style="border:1px solid #ccc; margin-top:20px; display:none;"></iframe>

<script src="js/modules/previewView.js"></script>
<script>
const form = document.getElementById("mcForm");
const previewFields = ["mc_question", "mc_answer1", "mc_answer2", "mc_answer3", "mc_answer4"];
const imageInput = document.getElementById("mc_image");
const imagePreview = document.getElementById("mc_imagePreview");

document.getElementById("loadImageBtn").onclick = () => imageInput.click();

imageInput.onchange = function () {
  const file = this.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = e => {
      imagePreview.src = e.target.result;
      imagePreview.style.display = "block";
    };
    reader.readAsDataURL(file);
  }
};

document.getElementById("deleteImageBtn").onclick = async () => {
  const id = document.getElementById("mc_id").value;
  if (!id) return alert("❗ Chưa có ID để xoá ảnh.");
  if (!confirm("❌ Xoá ảnh minh hoạ khỏi cloudinary?")) return;
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
      document.getElementById("saveBtn").click(); // tự lưu lại
    } else {
      alert("❌ Lỗi xoá ảnh.");
    }
  } catch {
    alert("❌ Lỗi xoá ảnh.");
  }
};

document.getElementById("deleteQuestionBtn").onclick = async () => {
  const id = document.getElementById("mc_id").value;
  if (!id) return alert("❗ Chưa chọn câu hỏi.");
  if (!confirm("🗑️ Xoá câu hỏi này khỏi hệ thống?")) return;
  try {
    const res = await fetch("utils/mc_delete.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ mc_id: id })
    });
    const result = await res.json();
    if (result.success) {
      alert("✅ Đã xoá!");
      form.reset();
      imagePreview.style.display = "none";
      document.getElementById("mcIframe").contentWindow.location.reload();
    } else {
      alert("❌ Không xoá được.");
    }
  } catch {
    alert("❌ Lỗi khi gửi xoá.");
  }
};

form.onsubmit = async function (e) {
  e.preventDefault();
  const formData = new FormData(form);
  try {
    const res = await fetch("utils/mc_save.php", {
      method: "POST",
      body: formData
    });
    const html = await res.text();
    const iframe = document.createElement("iframe");
    iframe.style.display = "none";
    document.body.appendChild(iframe);
    iframe.contentDocument.write(html);
    iframe.contentDocument.close();
    setTimeout(() => iframe.remove(), 1000);
  } catch (err) {
    alert("❌ Gửi dữ liệu thất bại.");
  }
};

window.addEventListener("message", function (event) {
  if (event.data.type === "saved") {
    alert("✅ Đã lưu!");
    document.getElementById("mcIframe").contentWindow.location.reload();
    form.reset();
    imagePreview.style.display = "none";
  } else if (event.data.type === "error") {
    alert("❌ " + event.data.message);
  } else if (event.data.type === "mc_select_row") {
    const d = event.data.data;
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
    if (typeof updatePreviews === "function") updatePreviews();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
});

// Toggle bảng
const iframe = document.getElementById("mcIframe");
const toggleBtn = document.getElementById("toggleIframeBtn");
toggleBtn.onclick = () => {
  const show = iframe.style.display === "none";
  iframe.style.display = show ? "block" : "none";
  toggleBtn.textContent = show ? "🔽 Ẩn bảng câu hỏi" : "🔼 Hiện bảng câu hỏi";
};
</script>
</body>
</html>
