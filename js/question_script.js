const $ = id => document.getElementById(id);
const previewArea = $("preview_area");

let tempPublicId = "";

// === MathJax Preview ===
function renderMath() {
  if (window.MathJax) MathJax.typesetPromise();
}

function updatePreview() {
  const q = $("question").value.trim();
  const a = $("answer1").value.trim();
  const b = $("answer2").value.trim();
  const c = $("answer3").value.trim();
  const d = $("answer4").value.trim();
  const img = $("image_url").value.trim();

  const showAll = $("toggle_preview_all").checked;
  const showQuestion = $("toggle_preview_question").checked;
  const showAnswers = $("toggle_preview_answers").checked;

  let html = "";

  if (showAll || showQuestion) {
    html += `<div><strong>Câu hỏi:</strong><br>${q}</div>`;
    if (img) {
      html += `<div style="margin-top:10px;"><img src="${img}" style="max-height:150px; border:1px solid #ccc;"></div>`;
    }
  }

  if (showAll || showAnswers) {
    html += `<div style="margin-top:10px;">
      <strong>A:</strong> ${a}<br>
      <strong>B:</strong> ${b}<br>
      <strong>C:</strong> ${c}<br>
      <strong>D:</strong> ${d}</div>`;
  }

  previewArea.innerHTML = html || "<em>⚡ Nội dung xem trước sẽ hiển thị tại đây...</em>";
  renderMath();
}

["question", "answer1", "answer2", "answer3", "answer4"].forEach(id => {
  $(id).addEventListener("input", updatePreview);
});
["toggle_preview_question", "toggle_preview_answers", "toggle_preview_all"].forEach(id => {
  $(id).addEventListener("change", updatePreview);
});
updatePreview();

// === Upload ảnh Cloudinary ===
$("select_image").addEventListener("click", () => $("image").click());

$("image").addEventListener("change", async function () {
  const file = this.files[0];
  if (!file) return;

  const formData = new FormData();
  tempPublicId = "temp_" + Date.now();
  formData.append("file", file);
  formData.append("upload_preset", CLOUDINARY_UPLOAD_PRESET);
  formData.append("public_id", tempPublicId);

  const res = await fetch(`https://api.cloudinary.com/v1_1/${CLOUDINARY_CLOUD_NAME}/upload`, {
    method: "POST",
    body: formData
  });

  const data = await res.json();
  if (data.secure_url) {
    $("image_url").value = data.secure_url;
    $("imageFileName").textContent = file.name;
    $("imagePreview").src = data.secure_url;
    $("imagePreview").style.display = "block";
    $("delete_image").style.display = "inline-block";

    $("imageTabPreview").src = data.secure_url;
    $("imageTabPreview").style.display = "block";
    $("imageTabFileName").textContent = file.name;
    $("delete_image_tab").style.display = "inline-block";
  }
});

// === Xoá ảnh Cloudinary ===
$("delete_image").addEventListener("click", async () => {
  const imageUrl = $("image_url").value;
  if (!imageUrl) return;

  const res = await fetch("delete_cloudinary_image.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `image_url=${encodeURIComponent(imageUrl)}`
  });

  const data = await res.json();
  if (data.success) {
    $("image_url").value = "";
    $("image").value = "";
    $("imageFileName").textContent = "";
    $("imagePreview").src = "";
    $("imagePreview").style.display = "none";
    $("delete_image").style.display = "none";

    $("imageTabPreview").src = "";
    $("imageTabPreview").style.display = "none";
    $("imageTabFileName").textContent = "";
    $("delete_image_tab").style.display = "none";

    tempPublicId = "";
  } else {
    alert("❌ Không thể xoá ảnh!");
  }
});

// === Submit Form (Thêm / Cập nhật) ===
$("questionForm").addEventListener("submit", async function (e) {
  e.preventDefault();
  const formData = new FormData(this);
  const id = formData.get("id");
  const isUpdate = !!id;

  const response = await fetch(isUpdate ? "update_question.php" : "insert_question.php", {
    method: "POST",
    body: formData
  });

  const result = await response.json();

  if (result.success) {
    const questionId = result.id || id;

    // Nếu có ảnh tạm → đổi tên và cập nhật DB
    if (tempPublicId) {
      const renameRes = await fetch("rename_cloudinary_image.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `temp_public_id=${tempPublicId}&new_public_id=pic_${questionId}`
      });

      const renameData = await renameRes.json();
      if (renameData.success && renameData.url) {
        await fetch("update_image_url.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `id=${questionId}&image_url=${encodeURIComponent(renameData.url)}`
        });
      }
    }

    alert("✅ Đã lưu thành công!");
    $("questionForm").reset();
    $("imagePreview").style.display = "none";
    $("delete_image").style.display = "none";
    $("imageFileName").textContent = "";
    $("imageTabPreview").style.display = "none";
    $("imageTabFileName").textContent = "";
    $("delete_image_tab").style.display = "none";
    tempPublicId = "";
    updatePreview();
    $("questionIframe").contentWindow.location.reload();
  } else {
    alert("❌ Không thể lưu câu hỏi!");
  }
});

// === Xoá câu hỏi ===
$("deleteBtn").addEventListener("click", async () => {
  const id = $("question_id").value;
  if (!id || !confirm("Bạn có chắc chắn muốn xoá?")) return;

  const res = await fetch("delete_question.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `id=${id}`
  });

  const data = await res.json();

  if (data.success) {
    alert("🗑️ Đã xoá câu hỏi!");
    $("questionForm").reset();
    $("imagePreview").style.display = "none";
    $("delete_image").style.display = "none";
    $("imageFileName").textContent = "";
    $("imageTabPreview").style.display = "none";
    $("imageTabFileName").textContent = "";
    $("delete_image_tab").style.display = "none";
    $("preview_area").innerHTML = "<em>⚡ Nội dung xem trước sẽ hiển thị tại đây...</em>";
    tempPublicId = "";
    $("questionIframe").contentWindow.location.reload();
  } else {
    alert("❌ Không xoá được!\n" + (data.message || ""));
  }
});

// === Reset Form ===
$("resetBtn").addEventListener("click", () => {
  $("questionForm").reset();
  $("imagePreview").style.display = "none";
  $("delete_image").style.display = "none";
  $("imageFileName").textContent = "";
  $("imageTabPreview").style.display = "none";
  $("imageTabFileName").textContent = "";
  $("delete_image_tab").style.display = "none";
  $("preview_area").innerHTML = "<em>⚡ Nội dung xem trước sẽ hiển thị tại đây...</em>";
  tempPublicId = "";
  updatePreview();
});

// === Export PDF ===
$("exportPdfBtn").addEventListener("click", () => {
  window.open("export_exam_pdf.php", "_blank");
});

// === Nhận dữ liệu từ iframe (click hàng) ===
window.addEventListener("message", (event) => {
  const data = event.data;
  if (!data || !data.id) return;

  $("question_id").value = data.id;
  $("topic").value = data.topic || "";
  $("question").value = data.question || "";
  $("answer1").value = data.answer1 || "";
  $("answer2").value = data.answer2 || "";
  $("answer3").value = data.answer3 || "";
  $("answer4").value = data.answer4 || "";
  $("correct_answer").value = data.correct_answer || "";
  $("image_url").value = data.image || "";

  if (data.image) {
    $("imagePreview").src = data.image;
    $("imagePreview").style.display = "block";
    $("imageFileName").textContent = "(Đã có ảnh)";
    $("delete_image").style.display = "inline-block";

    $("imageTabPreview").src = data.image;
    $("imageTabPreview").style.display = "block";
    $("imageTabFileName").textContent = "(Đã có ảnh)";
    $("delete_image_tab").style.display = "inline-block";
  } else {
    $("imagePreview").style.display = "none";
    $("delete_image").style.display = "none";
    $("imageFileName").textContent = "";

    $("imageTabPreview").style.display = "none";
    $("imageTabFileName").textContent = "";
    $("delete_image_tab").style.display = "none";
  }

  tempPublicId = "";
  updatePreview();
  window.scrollTo({ top: 0, behavior: "smooth" });
});

// === Đồng bộ tab ảnh chọn ===
$("select_image_tab")?.addEventListener("click", () => $("image").click());

$("image").addEventListener("change", () => {
  setTimeout(() => {
    const url = $("image_url").value;
    if (url) {
      $("imageTabPreview").src = url;
      $("imageTabPreview").style.display = "block";
      $("imageTabFileName").textContent = $("imageFileName").textContent;
      $("delete_image_tab").style.display = "inline-block";
    }
  }, 500);
});

$("delete_image_tab")?.addEventListener("click", () => {
  $("delete_image").click();
  $("imageTabPreview").src = "";
  $("imageTabPreview").style.display = "none";
  $("imageTabFileName").textContent = "";
  $("delete_image_tab").style.display = "none";
});
