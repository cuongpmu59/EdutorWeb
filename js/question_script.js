const $ = id => document.getElementById(id);
const previewArea = $("preview_area");
const form = $("questionForm");
let tempImagePublicId = "";
let originalData = new FormData(form);

// ========== Cloudinary config ==========
const CLOUD_NAME = "<your_cloud_name>";
const UPLOAD_PRESET = "<your_upload_preset>";
const CLOUD_API = `https://api.cloudinary.com/v1_1/${CLOUD_NAME}/image/upload`;

// ========== Ảnh minh hoạ ==========
$("select_image").addEventListener("click", () => $("image").click());

$("image").addEventListener("change", async function () {
  const file = this.files[0];
  if (!file) return;

  const formData = new FormData();
  const tempName = "temp_" + Date.now();
  formData.append("file", file);
  formData.append("upload_preset", UPLOAD_PRESET);
  formData.append("public_id", tempName);

  const res = await fetch(CLOUD_API, { method: "POST", body: formData });
  const data = await res.json();

  if (data.secure_url) {
    $("preview_image").src = data.secure_url;
    $("preview_image").style.display = "block";
    $("image_url").value = data.secure_url;
    $("delete_image").style.display = "inline-block";
    $("imageFileName").textContent = file.name;
    tempImagePublicId = data.public_id;
  }
});

$("delete_image").addEventListener("click", async () => {
  const imageUrl = $("image_url").value;
  if (!imageUrl) return;

  if (!confirm("Bạn có chắc chắn muốn xoá ảnh?")) return;

  // Gửi yêu cầu xoá ảnh Cloudinary
  const res = await fetch("delete_cloudinary_image.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `image_url=${encodeURIComponent(imageUrl)}`
  });

  const result = await res.json();
  if (result.success) {
    $("preview_image").src = "";
    $("preview_image").style.display = "none";
    $("image_url").value = "";
    $("image").value = "";
    $("imageFileName").textContent = "";
    $("delete_image").style.display = "none";
    tempImagePublicId = "";
  } else {
    alert("Lỗi khi xoá ảnh!");
  }
});

// ========== Xem trước ==========
const updatePreview = () => {
  const showQ = $("toggle_preview_question").checked;
  const showA = $("toggle_preview_answers").checked;
  const showAll = $("toggle_preview_all").checked;

  let html = "";

  if (showAll || showQ) {
    html += `<div><strong>🧠 Câu hỏi:</strong><br>${renderMath($("question").value)}</div><br>`;
  }
  if (showAll || showA) {
    ["1", "2", "3", "4"].forEach((i, idx) => {
      html += `<div><strong>Đáp án ${String.fromCharCode(65 + idx)}:</strong> ${renderMath($("answer" + i).value)}</div>`;
    });
  }
  if (html === "") html = "<em>Không có nội dung để xem trước...</em>";
  previewArea.innerHTML = html;
  MathJax.typesetPromise([previewArea]);
};

["question", "answer1", "answer2", "answer3", "answer4"].forEach(id => {
  $(id).addEventListener("input", updatePreview);
});

["toggle_preview_question", "toggle_preview_answers", "toggle_preview_all"].forEach(id => {
  $(id).addEventListener("change", updatePreview);
});

const renderMath = text => {
  return text.replace(/\\\((.*?)\\\)/g, (_, m) => `\\(${m}\\)`)
             .replace(/\\\[(.*?)\\\]/gs, (_, m) => `<div class="math-block">\\[${m}\\]</div>`);
};

// ========== Submit câu hỏi ==========
form.addEventListener("submit", async e => {
  e.preventDefault();

  const formData = new FormData(form);
  const id = formData.get("id").trim();

  if (!formData.get("question") || !formData.get("correct_answer")) {
    alert("Vui lòng nhập câu hỏi và chọn đáp án đúng!");
    return;
  }

  let imageUrl = formData.get("image_url");
  if (tempImagePublicId && !id) {
    // Nếu là thêm mới, đợi insert xong để rename ảnh
    const res = await fetch("insert_question.php", { method: "POST", body: formData });
    const data = await res.json();

    if (data.success && data.id) {
      const newId = data.id;
      const renameRes = await fetch("rename_cloudinary_image.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `old_id=${tempImagePublicId}&new_id=pic_${newId}`
      });

      const renamed = await renameRes.json();
      if (renamed.secure_url) {
        await fetch("update_image_url.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `id=${newId}&image_url=${encodeURIComponent(renamed.secure_url)}`
        });
      }

      alert("✅ Đã thêm câu hỏi!");
      form.reset();
      $("question_id").value = "";
      $("preview_image").style.display = "none";
      $("imageFileName").textContent = "";
      $("delete_image").style.display = "none";
      tempImagePublicId = "";
      updatePreview();
      $("questionIframe").contentWindow.location.reload();
    }
  } else {
    // Cập nhật
    const res = await fetch("update_question.php", { method: "POST", body: formData });
    const result = await res.json();
    if (result.success) {
      alert("✅ Đã cập nhật câu hỏi!");
      $("questionIframe").contentWindow.location.reload();
    } else {
      alert("❌ Có lỗi xảy ra khi cập nhật!");
    }
  }
});

// ========== Xoá câu hỏi ==========
$("deleteBtn").addEventListener("click", async () => {
  const id = $("question_id").value;
  if (!id) return;

  if (!confirm("Bạn có chắc chắn muốn xoá câu hỏi này?")) return;

  const res = await fetch("delete_question.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `id=${id}`
  });
  const result = await res.json();
  if (result.success) {
    alert("✅ Đã xoá câu hỏi!");
    form.reset();
    $("question_id").value = "";
    $("preview_image").style.display = "none";
    $("imageFileName").textContent = "";
    $("delete_image").style.display = "none";
    updatePreview();
    $("questionIframe").contentWindow.location.reload();
  } else {
    alert("❌ Lỗi khi xoá!");
  }
});

// ========== Làm mới ==========
$("resetBtn").addEventListener("click", () => {
  if (confirm("Làm mới nội dung đang nhập?")) {
    form.reset();
    $("question_id").value = "";
    $("preview_image").style.display = "none";
    $("imageFileName").textContent = "";
    $("delete_image").style.display = "none";
    tempImagePublicId = "";
    updatePreview();
  }
});

// ========== Xuất đề PDF ==========
$("exportPdfBtn").addEventListener("click", () => {
  window.open("export_exam_pdf.php", "_blank");
});

// ========== Đồng bộ iframe -> form ==========
window.addEventListener("message", event => {
  const data = event.data;
  if (!data || !data.id) return;

  $("question_id").value = data.id;
  $("topic").value = data.topic;
  $("question").value = data.question;
  $("answer1").value = data.answer1;
  $("answer2").value = data.answer2;
  $("answer3").value = data.answer3;
  $("answer4").value = data.answer4;
  $("correct_answer").value = data.correct_answer;

  if (data.image) {
    $("preview_image").src = data.image;
    $("preview_image").style.display = "block";
    $("image_url").value = data.image;
    $("delete_image").style.display = "inline-block";
  } else {
    $("preview_image").style.display = "none";
    $("image_url").value = "";
    $("delete_image").style.display = "none";
  }

  $("imageFileName").textContent = data.image?.split("/").pop() || "";
  updatePreview();
});

// ========== Cảnh báo khi có thay đổi chưa lưu ==========
window.addEventListener("beforeunload", e => {
  const current = new FormData(form);
  for (let [k, v] of current.entries()) {
    if (originalData.get(k) !== v) {
      e.preventDefault();
      e.returnValue = "";
      break;
    }
  }
});

updatePreview();
