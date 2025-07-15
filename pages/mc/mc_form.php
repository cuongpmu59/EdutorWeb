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

    <div class="form-group">
      <label for="mc_image">🖼️ Ảnh minh hoạ:</label>
      <input type="file" id="mc_image" name="mc_image" accept="image/*">
      <br>
      <img id="mc_imagePreview" src="" style="display:none; max-height:150px; margin-top:10px">
    </div>

    <div class="form-actions">
      <button type="submit" id="saveBtn">💾 Lưu câu hỏi</button>
      <button type="reset" id="resetBtn">🔄 Làm lại</button>
      <button type="button" id="deleteBtn" style="display:none;">🗑️ Xoá câu hỏi</button>
    </div>
  </form>
</div>

<iframe id="mcIframe" src="mc_table.php" width="100%" height="500" style="border:1px solid #ccc; margin-top:20px;"></iframe>

<script src="js/modules/previewView.js"></script>

<script>
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

// Nhận dữ liệu từ bảng
window.addEventListener("message", function (event) {
  if (event.data.type === "saved") {
    alert("✅ Đã lưu thành công!");
    document.getElementById("mcIframe").contentWindow.location.reload();
    document.getElementById("mcForm").reset();
    document.getElementById("mc_imagePreview").style.display = "none";
    document.getElementById("deleteBtn").style.display = "none";
  } else if (event.data.type === "error") {
    alert("❌ Lỗi: " + event.data.message);
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
      const img = document.getElementById("mc_imagePreview");
      img.src = d.image;
      img.style.display = "block";
    } else {
      document.getElementById("mc_imagePreview").style.display = "none";
    }

    // Hiện nút xoá nếu có id
    document.getElementById("deleteBtn").style.display = d.id ? "inline-block" : "none";

    window.scrollTo({ top: 0, behavior: 'smooth' });

    if (typeof updatePreviews === "function") updatePreviews();
    if (window.MathJax) MathJax.typesetPromise();
  }
});

// Xem trước ảnh khi chọn file
document.getElementById("mc_image").addEventListener("change", function (e) {
  const file = e.target.files[0];
  const img = document.getElementById("mc_imagePreview");
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

// Xoá câu hỏi
document.getElementById("deleteBtn").addEventListener("click", async function () {
  const id = document.getElementById("mc_id").value;
  const imageUrl = document.getElementById("mc_imagePreview").src || "";
  if (!id) return alert("❌ Không có câu hỏi để xoá.");

  if (!confirm("❓ Bạn có chắc chắn muốn xoá câu hỏi này không?")) return;

  try {
    const res = await fetch("utils/mc_delete.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id, image: imageUrl })
    });
    const result = await res.json();
    if (result.success) {
      alert("🗑️ Đã xoá thành công!");
      document.getElementById("mcForm").reset();
      document.getElementById("mc_imagePreview").style.display = "none";
      document.getElementById("deleteBtn").style.display = "none";
      document.getElementById("mcIframe").contentWindow.location.reload();
    } else {
      alert("❌ Không xoá được: " + result.message);
    }
  } catch (err) {
    alert("❌ Lỗi khi xoá: " + err.message);
  }
});
</script>
</body>
</html>
