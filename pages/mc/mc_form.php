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
      <label>🖼️ Ảnh minh hoạ:</label><br>
      <button type="button" id="chooseImageBtn">📂 Chọn ảnh</button>
      <button type="button" id="deleteImageBtn" style="display:none;">🗑️ Xoá ảnh</button>
      <input type="file" id="mc_image" name="mc_image" accept="image/*" style="display:none;">
      <br>
      <img id="mc_imagePreview" src="" style="display:none; max-height:150px; margin-top:10px;">
    </div>

    <div class="form-actions">
      <button type="submit" id="saveBtn">💾 Lưu câu hỏi</button>
      <button type="reset" id="resetBtn">🔄 Làm lại</button>
      <button type="button" id="deleteBtn" style="background:#ff4444; color:white;">❌ Xoá câu hỏi</button>
    </div>
  </form>
</div>

<iframe id="mcIframe" src="mc_table.php" width="100%" height="500" style="border:1px solid #ccc; margin-top:20px;"></iframe>

<script src="js/modules/previewView.js"></script>

<script>
document.getElementById("chooseImageBtn").addEventListener("click", () => {
  document.getElementById("mc_image").click();
});

document.getElementById("mc_image").addEventListener("change", function () {
  const file = this.files[0];
  const img = document.getElementById("mc_imagePreview");
  if (file) {
    const reader = new FileReader();
    reader.onload = e => {
      img.src = e.target.result;
      img.style.display = "block";
      document.getElementById("deleteImageBtn").style.display = "inline-block";
      document.getElementById("saveBtn").style.display = "inline-block";
    };
    reader.readAsDataURL(file);
  }
});

document.getElementById("deleteImageBtn").addEventListener("click", async function () {
  const mc_id = document.getElementById("mc_id").value;
  if (!mc_id) return alert("❌ Chưa có ID để xoá ảnh.");

  if (!confirm("Bạn có chắc muốn xoá ảnh minh hoạ?")) return;

  try {
    const res = await fetch("utils/mc_delete_image.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "mc_id=" + encodeURIComponent(mc_id)
    });
    const result = await res.text();
    alert(result);

    document.getElementById("mc_imagePreview").style.display = "none";
    document.getElementById("mc_image").value = "";
    document.getElementById("deleteImageBtn").style.display = "none";
    document.getElementById("saveBtn").style.display = "inline-block";

    // Gửi lại form để lưu trạng thái không có ảnh
    document.getElementById("mcForm").dispatchEvent(new Event("submit"));
  } catch (err) {
    alert("❌ Lỗi khi xoá ảnh: " + err.message);
  }
});

document.getElementById("deleteBtn").addEventListener("click", async function () {
  const mc_id = document.getElementById("mc_id").value;
  if (!mc_id) return alert("❌ Không có ID để xoá.");

  if (!confirm("Bạn có chắc muốn xoá câu hỏi này?")) return;

  try {
    const res = await fetch("utils/mc_delete_question.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "mc_id=" + encodeURIComponent(mc_id)
    });
    const result = await res.text();
    alert(result);

    document.getElementById("mcForm").reset();
    document.getElementById("mc_imagePreview").style.display = "none";
    document.getElementById("deleteImageBtn").style.display = "none";
    document.getElementById("saveBtn").style.display = "inline-block";
    document.getElementById("mcIframe").contentWindow.location.reload();
  } catch (err) {
    alert("❌ Lỗi khi xoá câu hỏi: " + err.message);
  }
});

document.getElementById("mcForm").addEventListener("submit", async function (e) {
  e.preventDefault();
  const formData = new FormData(this);

  try {
    const res = await fetch("utils/mc_save.php", {
      method: "POST",
      body: formData
    });
    const result = await res.text();
    alert(result);
    document.getElementById("mcIframe").contentWindow.location.reload();
  } catch (err) {
    alert("❌ Lỗi khi lưu: " + err.message);
  }
});

// ✅ Nhận dữ liệu từ bảng mc_table.php
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

    const img = document.getElementById("mc_imagePreview");
    if (d.image) {
      img.src = d.image;
      img.style.display = "block";
      document.getElementById("deleteImageBtn").style.display = "inline-block";
    } else {
      img.style.display = "none";
      document.getElementById("deleteImageBtn").style.display = "none";
    }

    document.getElementById("saveBtn").style.display = "inline-block";

    // ✅ Gọi lại xem trước LaTeX
    if (typeof updatePreviews === "function") updatePreviews();

    // ✅ Cuộn lên đầu
    window.scrollTo({ top: 0, behavior: "smooth" });
  }
});
</script>

</body>
</html>
