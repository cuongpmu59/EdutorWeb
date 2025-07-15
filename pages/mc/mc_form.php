<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="../../css/main_ui.css">
  <style>
    body {
      max-width: 900px;
      margin: auto;
      font-family: Arial, sans-serif;
      padding: 20px;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .preview-box {
      display: none;
      background: #f9f9f9;
      border: 1px dashed #ccc;
      padding: 10px;
      margin-top: 5px;
    }
    .preview-box.visible {
      display: block;
    }
    .form-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 20px;
    }
    @media (max-width: 600px) {
      .form-actions {
        flex-direction: column;
      }
    }
    .preview-toggle {
      font-size: 13px;
      margin-top: 4px;
      display: inline-block;
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
      <<?= $isTextarea ? 'textarea' : 'input type="text"' ?>
        id="<?= $id ?>" name="<?= $id ?>" required oninput="updatePreviews()"></<?= $isTextarea ? 'textarea' : 'input' ?>>
      <label class="preview-toggle">
        <input type="checkbox" onchange="togglePreview('<?= $id ?>')"> 🔍 Xem trước
      </label>
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
  const escapeHTML = s => s.replace(/[&<>"']/g, c => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
  })[c]);
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
      preview.innerHTML = renderLatex(input.value);
      if (typeof MathJax !== 'undefined') MathJax.typesetPromise();
    }
  });
}

function togglePreview(id) {
  const box = document.getElementById("preview_" + id);
  if (box) box.classList.toggle("visible");
}

document.getElementById("loadImageBtn").onclick = () => {
  document.getElementById("mc_image").click();
};

document.getElementById("mc_image").addEventListener("change", function () {
  const file = this.files[0];
  const preview = document.getElementById("mc_imagePreview");
  if (file) {
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      preview.style.display = "block";
    };
    reader.readAsDataURL(file);
  } else {
    preview.style.display = "none";
  }
});

document.getElementById("deleteImageBtn").addEventListener("click", async () => {
  const id = document.getElementById("mc_id").value;
  if (!id || !confirm("❌ Xác nhận xoá ảnh?")) return;
  const res = await fetch("utils/mc_delete_image.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ mc_id: id })
  });
  const result = await res.json();
  if (result.success) {
    document.getElementById("mc_imagePreview").style.display = "none";
    document.getElementById("mc_image").value = "";
    document.getElementById("saveBtn").click();
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
  if (!id || !confirm("🗑️ Bạn có chắc xoá câu hỏi này?")) return;
  const res = await fetch("utils/mc_delete.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ mc_id: id })
  });
  const result = await res.json();
  if (result.success) {
    alert("🗑️ Đã xoá!");
    document.getElementById("mcForm").reset();
    document.getElementById("mc_imagePreview").style.display = "none";
    document.getElementById("mcIframe").contentWindow.location.reload();
  } else {
    alert("❌ Lỗi khi xoá.");
  }
});

document.getElementById("toggleIframeBtn").addEventListener("click", () => {
  const iframe = document.getElementById("mcIframe");
  const btn = document.getElementById("toggleIframeBtn");
  const isHidden = iframe.style.display === "none";
  iframe.style.display = isHidden ? "block" : "none";
  btn.textContent = isHidden ? "🔼 Ẩn bảng câu hỏi" : "🔽 Hiện bảng câu hỏi";
});

// Nhận dữ liệu từ bảng
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
      document.getElementById("mc_imagePreview").src = d.image;
      document.getElementById("mc_imagePreview").style.display = "block";
    } else {
      document.getElementById("mc_imagePreview").style.display = "none";
    }
    updatePreviews();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
});
</script>

</body>
</html>
