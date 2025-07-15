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
  <style>
    .main-container {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      align-items: flex-start;
    }
    .form-left {
      flex: 2;
      min-width: 300px;
    }
    .form-right {
      flex: 1;
      min-width: 200px;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .preview-box {
      margin-top: 5px;
      padding: 5px;
      background: #f1f1f1;
      border: 1px dashed #ccc;
      display: none;
    }
    .small-font {
      font-size: 13px;
    }
    textarea {
      resize: none;
      overflow: hidden;
      min-height: 60px;
      transition: all 0.2s ease;
    }
    @media (max-width: 768px) {
      .main-container {
        flex-direction: column;
      }
      .form-right {
        flex-direction: row;
        flex-wrap: wrap;
      }
    }
  </style>
</head>
<body>

<form id="mcForm" class="question-form" enctype="multipart/form-data">
  <input type="hidden" id="mc_id" name="mc_id">
  <div class="main-container">
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
          <label for="<?= $id ?>"><?= $label ?>:</label>
          <<?= $isTextarea ? 'textarea' : 'input type="text"' ?> id="<?= $id ?>" name="<?= $id ?>" required></<?= $isTextarea ? 'textarea' : 'input' ?>>
          <div id="preview_<?= $id ?>" class="preview-box"></div>
          <button type="button" onclick="togglePreview('<?= $id ?>')">👁️ Xem trước</button>
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
        <input type="file" id="mc_image" name="mc_image" accept="image/*" style="display:none;">
        <button type="button" id="loadImageBtn">📂 Load ảnh</button>
        <button type="button" id="deleteImageBtn">❌ Xoá ảnh</button><br>
        <img id="mc_imagePreview" src="" style="display:none; max-height:150px; margin-top:10px">
      </div>
    </div>

    <div class="form-right">
      <button type="submit" id="saveBtn">💾 Lưu câu hỏi</button>
      <button type="reset" id="resetBtn">🔄 Làm lại</button>
      <button type="button" id="deleteQuestionBtn">🗑️ Xoá câu hỏi</button>
      <button type="button" id="toggleIframeBtn">📑 Hiện bảng</button>
    </div>
  </div>
</form>

<iframe id="mcIframe" src="mc_table.php" width="100%" height="500" style="border:1px solid #ccc; margin-top:20px; display:none;"></iframe>

<script>
function renderLatex(text) {
  if (!text) return '';
  const inline = /\$(.+?)\$/g;
  const display = /\$\$(.+?)\$\$/g;
  return text
    .replace(display, (_, expr) => `\\[${expr}\\]`)
    .replace(inline, (_, expr) => `\\(${expr}\\)`);
}

function updatePreviews() {
  const fields = ['mc_question', 'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4'];
  fields.forEach(id => {
    const input = document.getElementById(id);
    const preview = document.getElementById("preview_" + id);
    if (input && preview) {
      preview.innerHTML = renderLatex(input.value);
    }
  });
  if (typeof MathJax !== 'undefined') MathJax.typesetPromise();
}

function togglePreview(id) {
  const box = document.getElementById("preview_" + id);
  box.style.display = box.style.display === 'none' ? 'block' : 'none';
  updatePreviews();
}

// Auto-resize textarea
document.querySelectorAll("textarea").forEach((ta) => {
  ta.addEventListener("input", function () {
    this.style.height = "auto";
    this.style.height = (this.scrollHeight + 2) + "px";
    this.classList.toggle("small-font", this.scrollHeight > 200);
  });
});

const imageInput = document.getElementById("mc_image");
const imagePreview = document.getElementById("mc_imagePreview");

document.getElementById("mcForm").addEventListener("submit", async function (e) {
  e.preventDefault();
  const formData = new FormData(this);
  try {
    const response = await fetch("utils/mc_save.php", {
      method: "POST",
      body: formData
    });
    const result = await response.text();
    alert(result);
    document.getElementById("mcIframe").contentWindow.location.reload();
    this.reset();
    imagePreview.style.display = "none";
  } catch (error) {
    alert("❌ Lỗi khi gửi dữ liệu: " + error.message);
  }
});

document.getElementById("loadImageBtn").onclick = () => imageInput.click();

imageInput.addEventListener("change", function () {
  const file = this.files[0];
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
  if (!id) return alert("❗ Câu hỏi chưa có ID.");
  if (!confirm("❌ Xác nhận xoá ảnh?")) return;
  const res = await fetch("utils/mc_delete_image.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ mc_id: id })
  });
  const result = await res.json();
  if (result.success) {
    imageInput.value = "";
    imagePreview.style.display = "none";
    alert("🧹 Đã xoá ảnh.");
    document.getElementById("saveBtn").click(); // Auto-save
  } else {
    alert("❌ Lỗi xoá ảnh.");
  }
});

document.getElementById("deleteQuestionBtn").addEventListener("click", async () => {
  const id = document.getElementById("mc_id").value;
  if (!id) return alert("❗ Chưa có câu hỏi.");
  if (!confirm("🗑️ Xoá câu hỏi này?")) return;
  const res = await fetch("utils/mc_delete.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ mc_id: id })
  });
  const result = await res.json();
  if (result.success) {
    alert("🗑️ Đã xoá câu hỏi.");
    document.getElementById("mcForm").reset();
    imagePreview.style.display = "none";
    document.getElementById("mcIframe").contentWindow.location.reload();
  } else {
    alert("❌ Xoá thất bại.");
  }
});

document.getElementById("toggleIframeBtn").addEventListener("click", () => {
  const iframe = document.getElementById("mcIframe");
  const btn = document.getElementById("toggleIframeBtn");
  const show = iframe.style.display === "none";
  iframe.style.display = show ? "block" : "none";
  btn.textContent = show ? "📑 Ẩn bảng" : "📑 Hiện bảng";
});

window.addEventListener("message", function (event) {
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
    updatePreviews();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
});
</script>
</body>
</html>
