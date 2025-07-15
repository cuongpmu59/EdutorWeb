<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="../../css/main_ui.css">
  <link rel="stylesheet" href="../../css/modules/preview.css">
  <style>
    .form-group textarea {
      resize: none;
      min-height: 60px;
    }
    .form-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 20px;
    }
    .preview-box {
      display: none;
      background: #f8f9fa;
      border: 1px dashed #ccc;
      padding: 10px;
      margin-top: 5px;
      font-size: 14px;
    }
    .preview-toggle {
      margin-top: -8px;
      margin-bottom: 5px;
      text-align: right;
    }
    .preview-toggle button {
      background: transparent;
      border: none;
      color: #007bff;
      cursor: pointer;
      font-size: 13px;
      padding: 2px;
    }
    .preview-toggle button:hover {
      color: #0056b3;
    }
  </style>
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>

<div class="form-container">
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
          id="<?= $id ?>" name="<?= $id ?>" required
          oninput="autoResize(this)"></<?= $isTextarea ? 'textarea' : 'input' ?>>
        <div class="preview-toggle">
          <button type="button" onclick="togglePreview('<?= $id ?>')">🔍 Xem trước</button>
        </div>
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
      <label>🖼️ Ảnh minh hoạ:</label><br>
      <input type="file" id="mc_image" name="mc_image" accept="image/*" style="display:none;">
      <button type="button" id="loadImageBtn">📂 Load ảnh</button>
      <button type="button" id="deleteImageBtn">❌ Xoá ảnh</button>
      <br>
      <img id="mc_imagePreview" src="" style="display:none; max-height:150px; margin-top:10px">
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
const imageInput = document.getElementById("mc_image");
const imagePreview = document.getElementById("mc_imagePreview");
const saveBtn = document.getElementById("saveBtn");

document.getElementById("mcForm").addEventListener("submit", async function (e) {
  e.preventDefault();
  const formData = new FormData(this);

  try {
    const response = await fetch("utils/mc_save.php", {
      method: "POST",
      body: formData
    });
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
  if (event.data.type === "saved") {
    alert("✅ Đã lưu thành công!");
    document.getElementById("mcIframe").contentWindow.location.reload();
    document.getElementById("mcForm").reset();
    imagePreview.style.display = "none";
  } else if (event.data.type === "error") {
    alert("❌ Lỗi: " + event.data.message);
  }

  if (event.data.type === "mc_select_row") {
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

document.getElementById("loadImageBtn").addEventListener("click", () => {
  imageInput.click();
});

imageInput.addEventListener("change", function (e) {
  const file = e.target.files[0];
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
  if (!id) return alert("❗ Câu hỏi chưa có ID. Không thể xoá ảnh.");

  if (!confirm("❌ Xác nhận xoá ảnh minh hoạ?")) return;

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
  } catch (err) {
    alert("❌ Lỗi khi gửi yêu cầu xoá.");
  }
});

const toggleBtn = document.getElementById("toggleIframeBtn");
const iframe = document.getElementById("mcIframe");

toggleBtn.addEventListener("click", () => {
  if (iframe.style.display === "none") {
    iframe.style.display = "block";
    toggleBtn.textContent = "🔽 Ẩn bảng câu hỏi";
  } else {
    iframe.style.display = "none";
    toggleBtn.textContent = "🔼 Hiện bảng câu hỏi";
  }
});

function togglePreview(id) {
  const box = document.getElementById('preview_' + id);
  if (box.style.display === 'block') {
    box.style.display = 'none';
  } else {
    if (typeof updatePreviews === 'function') updatePreviews();
    box.style.display = 'block';
  }
}

function autoResize(el) {
  el.style.height = "auto";
  el.style.height = (el.scrollHeight) + "px";
}
</script>
</body>
</html>
