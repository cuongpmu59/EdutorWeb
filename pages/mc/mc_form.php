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
<div class="mc-wrapper">
  <form id="mcForm" class="question-form" enctype="multipart/form-data">
    <input type="hidden" id="mc_id" name="mc_id" />

    <div class="form-left">
      <div class="form-group">
        <label for="mc_topic">📚 Chủ đề:</label>
        <input type="text" id="mc_topic" name="mc_topic" required />
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
      ?>
        <div class="form-group">
          <label for="<?= $id ?>">
            <?= $label ?> <span class="toggle-preview-icon" data-target="<?= $id ?>">👁️</span>
          </label>
          <textarea id="<?= $id ?>" name="<?= $id ?>" rows="2" required></textarea>
          <div id="preview_<?= $id ?>" class="preview-box" style="display:none;"></div>
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
        <input type="file" id="mc_image" name="mc_image" accept="image/*" style="display: none;" />
        <button type="button" id="loadImageBtn">📂 Load ảnh</button>
        <button type="button" id="deleteImageBtn">❌ Xoá ảnh</button>
        <br>
        <img id="mc_imagePreview" src="" style="display:none; max-height:150px; margin-top:10px" />
      </div>
    </div>

    <div class="form-right">
      <button type="submit" id="saveBtn">💾 Lưu câu hỏi</button>
      <button type="reset" id="resetBtn">🔄 Làm lại</button>
      <button type="button" id="deleteQuestionBtn">🗑️ Xoá câu hỏi</button>
      <button type="button" id="toggleIframeBtn">🔼 Hiện bảng câu hỏi</button>
    </div>
  </form>

  <iframe id="mcIframe" src="mc_table.php" width="100%" height="500" style="border:1px solid #ccc; margin-top:20px; display:none;"></iframe>
</div>

<script src="js/modules/previewView.js"></script>
<script>
const imageInput = document.getElementById("mc_image");
const imagePreview = document.getElementById("mc_imagePreview");

// Load ảnh minh hoạ
document.getElementById("loadImageBtn").addEventListener("click", () => imageInput.click());

imageInput.addEventListener("change", (e) => {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.src = e.target.result;
      imagePreview.style.display = "block";
    };
    reader.readAsDataURL(file);
  } else {
    imagePreview.style.display = "none";
  }
});

// Xoá ảnh minh hoạ
document.getElementById("deleteImageBtn").addEventListener("click", async () => {
  const id = document.getElementById("mc_id").value;
  if (!id) return alert("❗ Không có ID câu hỏi.");
  if (!confirm("❌ Xoá ảnh khỏi Cloudinary?")) return;

  try {
    const res = await fetch("utils/mc_delete_image.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ mc_id: id })
    });
    const result = await res.json();
    if (result.success) {
      imageInput.value = "";
      imagePreview.style.display = "none";
      alert("🧹 Đã xoá ảnh!");
      document.getElementById("saveBtn").click(); // Tự động lưu lại
    } else {
      alert("❌ Không thể xoá ảnh.");
    }
  } catch {
    alert("❌ Lỗi xoá ảnh.");
  }
});

// Gửi form lưu câu hỏi
document.getElementById("mcForm").addEventListener("submit", async function (e) {
  e.preventDefault();
  const formData = new FormData(this);
  try {
    const response = await fetch("utils/mc_save.php", {
      method: "POST",
      body: formData
    });
    const result = await response.text();
    alert(result.includes("✅") ? "✅ Đã lưu thành công!" : result);
    document.getElementById("mcIframe").contentWindow.location.reload();
    if (result.includes("✅")) {
      this.reset();
      imagePreview.style.display = "none";
    }
  } catch (err) {
    alert("❌ Lỗi: " + err.message);
  }
});

// Xoá câu hỏi
document.getElementById("deleteQuestionBtn").addEventListener("click", async () => {
  const id = document.getElementById("mc_id").value;
  if (!id) return alert("❗ Không có ID câu hỏi.");
  if (!confirm("🗑️ Xoá câu hỏi và ảnh liên quan?")) return;

  try {
    const res = await fetch("utils/mc_delete.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ mc_id: id })
    });
    const result = await res.json();
    if (result.success) {
      alert("🗑️ Đã xoá!");
      document.getElementById("mcForm").reset();
      imagePreview.style.display = "none";
      document.getElementById("mcIframe").contentWindow.location.reload();
    } else {
      alert("❌ Xoá thất bại.");
    }
  } catch {
    alert("❌ Lỗi khi xoá.");
  }
});

// Ẩn/hiện bảng
const iframe = document.getElementById("mcIframe");
const toggleBtn = document.getElementById("toggleIframeBtn");
toggleBtn.addEventListener("click", () => {
  iframe.style.display = iframe.style.display === "none" ? "block" : "none";
  toggleBtn.textContent = iframe.style.display === "none" ? "🔼 Hiện bảng câu hỏi" : "🔽 Ẩn bảng câu hỏi";
});

// Nhận dữ liệu từ bảng
window.addEventListener("message", (e) => {
  if (e.data?.type === "mc_select_row") {
    const d = e.data.data;
    document.getElementById("mc_id").value = d.id;
    document.getElementById("mc_topic").value = d.topic;
    document.getElementById("mc_question").value = d.question;
    document.getElementById("mc_answer1").value = d.answer1;
    document.getElementById("mc_answer2").value = d.answer2;
    document.getElementById("mc_answer3").value = d.answer3;
    document.getElementById("mc_answer4").value = d.answer4;
    document.getElementById("mc_correct_answer").value = d.correct;
    if (d.image) {
      imagePreview.src = d.image;
      imagePreview.style.display = "block";
    } else {
      imagePreview.style.display = "none";
    }
    if (typeof updatePreviews === "function") updatePreviews();
    window.scrollTo({ top: 0, behavior: "smooth" });
  }
});

// 👁️ Toggle preview theo biểu tượng
document.querySelectorAll(".toggle-preview-icon").forEach(icon => {
  icon.addEventListener("click", () => {
    const target = icon.dataset.target;
    const box = document.getElementById("preview_" + target);
    if (box.style.display === "none") {
      box.style.display = "block";
    } else {
      box.style.display = "none";
    }
    if (typeof updatePreviews === "function") updatePreviews();
  });
});
</script>
</body>
</html>
