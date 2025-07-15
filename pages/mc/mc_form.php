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
      max-width: 900px;
      margin: auto;
      padding: 20px;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .form-group label {
      font-weight: bold;
    }
    .form-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 20px;
    }
    .preview-box {
      display: none;
      background: #f7f7f7;
      padding: 10px;
      border: 1px solid #ccc;
      margin-top: 5px;
    }
    .preview-box.visible {
      display: block;
    }
    @media (max-width: 600px) {
      .form-actions {
        flex-direction: column;
      }
    }
  </style>
  <script>
    window.MathJax = {
      tex: {
        inlineMath: [['$', '$'], ['\\(', '\\)']],
        displayMath: [['$$', '$$'], ['\\[', '\\]']]
      },
      svg: { fontCache: 'global' }
    };
  </script>
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>

<h2>📋 Nhập câu hỏi trắc nghiệm</h2>

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
  foreach ($fields as $id => $label):
    $isTextarea = $id === 'mc_question';
  ?>
    <div class="form-group">
      <label for="<?= $id ?>"><?= $label ?>:</label>
      <<?= $isTextarea ? 'textarea' : 'input type="text"' ?> id="<?= $id ?>" name="<?= $id ?>" required oninput="updatePreviews()"></<?= $isTextarea ? 'textarea' : 'input' ?>>
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
    <label for="mc_image">🖼️ Ảnh minh hoạ:</label><br>
    <input type="file" id="mc_image" name="mc_image" accept="image/*" style="display:none">
    <button type="button" id="loadImageBtn">📂 Load ảnh</button>
    <button type="button" id="deleteImageBtn">❌ Xoá ảnh</button><br>
    <img id="mc_imagePreview" src="" style="display:none; max-height:150px; margin-top:10px">
  </div>

  <div class="form-actions">
    <button type="submit" id="saveBtn">💾 Lưu câu hỏi</button>
    <button type="reset" id="resetBtn">🔄 Làm lại</button>
    <button type="button" id="deleteQuestionBtn">🗑️ Xoá câu hỏi</button>
    <button type="button" id="toggleIframeBtn">🔽 Hiện bảng câu hỏi</button>
  </div>
</form>

<iframe id="mcIframe" src="mc_table.php" width="100%" height="500" style="display:none; border:1px solid #ccc; margin-top:20px;"></iframe>

<script>
function renderLatex(text) {
  if (!text) return '';
  const escapeHTML = str => str.replace(/[&<>"']/g, m => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
  }[m]));
  text = escapeHTML(text);
  text = text.replace(/\$\$(.+?)\$\$/gs, (_, expr) => `\\[${expr.trim()}\\]`);
  text = text.replace(/\$(.+?)\$/g, (_, expr) => `\\(${expr.trim()}\\)`);
  return text;
}

function updatePreviews() {
  ['mc_question', 'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4'].forEach(id => {
    const input = document.getElementById(id);
    const preview = document.getElementById("preview_" + id);
    if (input && preview) {
      const raw = input.value;
      preview.innerHTML = renderLatex(raw);
      preview.classList.toggle('visible', raw.trim() !== '');
    }
  });
  if (typeof MathJax !== 'undefined' && window.MathJax.typesetPromise) {
    MathJax.typesetPromise();
  }
}

const imageInput = document.getElementById("mc_image");
const imagePreview = document.getElementById("mc_imagePreview");

document.getElementById("loadImageBtn").onclick = () => imageInput.click();
imageInput.addEventListener("change", e => {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = e => {
      imagePreview.src = e.target.result;
      imagePreview.style.display = "block";
    };
    reader.readAsDataURL(file);
  }
});

document.getElementById("deleteImageBtn").addEventListener("click", async () => {
  const id = document.getElementById("mc_id").value;
  if (!id) return alert("❗ Không có ID để xoá ảnh");
  if (!confirm("❌ Xoá ảnh minh hoạ khỏi Cloudinary?")) return;
  const res = await fetch("utils/mc_delete_image.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ mc_id: id })
  });
  const result = await res.json();
  if (result.success) {
    imagePreview.style.display = "none";
    imageInput.value = "";
    document.getElementById("saveBtn").click(); // tự lưu sau xoá
  } else {
    alert("❌ Lỗi khi xoá ảnh.");
  }
});

document.getElementById("mcForm").addEventListener("submit", async function (e) {
  e.preventDefault();
  const formData = new FormData(this);
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
});

document.getElementById("deleteQuestionBtn").addEventListener("click", async () => {
  const id = document.getElementById("mc_id").value;
  if (!id || !confirm("❗ Bạn có chắc muốn xoá câu hỏi?")) return;
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
    alert("❌ Lỗi khi xoá.");
  }
});

const toggleBtn = document.getElementById("toggleIframeBtn");
const iframe = document.getElementById("mcIframe");
toggleBtn.onclick = () => {
  const isHidden = iframe.style.display === "none";
  iframe.style.display = isHidden ? "block" : "none";
  toggleBtn.textContent = isHidden ? "🔼 Ẩn bảng câu hỏi" : "🔽 Hiện bảng câu hỏi";
};

// Nhận dữ liệu từ bảng (postMessage)
window.addEventListener("message", function (event) {
  if (event.data?.type === "mc_select_row") {
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
