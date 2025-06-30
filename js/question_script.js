// ========== Utility Functions ==========
const $ = id => document.getElementById(id);

const containsMath = text =>
  /(\\\(.+?\\\))|(\\\[.+?\\\])|(\$\$.+?\$\$)|(\$.+?\$)/.test(text);

const escapeHtml = str =>
  str.replace(/[&<>"]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' })[m]);

function wrapMath(text) {
  const trimmed = text.trim();
  return containsMath(trimmed) ? trimmed : `\\(${escapeHtml(trimmed)}\\)`;
}

let mathTimer, previewTimer, formChanged = false;

function debounceRender(el) {
  clearTimeout(mathTimer);
  mathTimer = setTimeout(() => {
    if (window.MathJax && containsMath(el.innerHTML)) {
      MathJax.typesetPromise([el]);
    }
  }, 250);
}

function renderPreview(id) {
  const val = $(id)?.value ?? "";
  const preview = $(`preview_${id}`);
  if (!preview) return;
  preview.innerHTML = wrapMath(val);
  debounceRender(preview);
  validateInput(id);
}

function debounceFullPreview() {
  clearTimeout(previewTimer);
  previewTimer = setTimeout(updateFullPreview, 300);
}

function updateFullPreview() {
  const q = wrapMath($("question").value);
  const answers = [1, 2, 3, 4].map(i => wrapMath($(`answer${i}`).value));
  const correct = $("correct_answer").value;

  $("fullPreview").innerHTML = `
    <p><strong>Câu hỏi:</strong> ${q}</p>
    <ul>
      ${["A", "B", "C", "D"].map((label, i) => `<li><strong>${label}.</strong> ${answers[i]}</li>`).join("")}
    </ul>
    <p><strong>Đáp án đúng:</strong> ${correct}</p>
  `;
  ["question", "answer1", "answer2", "answer3", "answer4"].forEach(validateInput);
  debounceRender($("fullPreview"));
}

function togglePreviewBox(id, target) {
  $(target).style.display = $(id).checked ? "block" : "none";
}

function resetPreview() {
  const img = $("imagePreview"), url = $("image_url"), delLbl = $("deleteImageLabel"), delChk = $("delete_image");
  img.src = url.value || "";
  img.classList.toggle("show", !!url.value);
  delLbl.style.display = url.value ? "inline-block" : "none";
  if (delChk.checked) img.style.display = "none";
  url.value = "";
  delChk.checked = false;
  debounceFullPreview();
}

function getFormData() {
  return new FormData($("questionForm"));
}

function refreshIframe() {
  const iframe = $("questionIframe");
  if (iframe) {
    iframe.contentWindow.location.reload();
    iframe.onload = () => iframe.contentWindow.MathJax?.typesetPromise();
  }
}

async function handleSaveQuestion(isEdit) {
  const formData = getFormData();
  formData.set("delete_image", $("delete_image").checked ? "1" : "0");

  const requiredFields = ["question", "answer1", "answer2", "answer3", "answer4", "correct_answer", "topic"];
  for (let field of requiredFields) {
    if (!formData.get(field)?.trim()) return alert("Vui lòng điền đầy đủ thông tin.");
  }

  const file = formData.get("image");
  if (file?.size > 0) {
    if (!file.type.startsWith("image/")) return alert("Chỉ chấp nhận ảnh.");
    if (file.size > 2 * 1024 * 1024) return alert("Ảnh quá lớn. < 2MB thôi.");
  }

  const buttons = document.querySelectorAll(".form-right button");
  buttons.forEach(btn => btn.disabled = true);

  try {
    if (file?.size > 0) {
      const upForm = new FormData();
      upForm.append("file", file);
      upForm.append("upload_preset", "quiz_photo");
      const res = await fetch("https://api.cloudinary.com/v1_1/dbdf2gwc9/image/upload", { method: "POST", body: upForm });
      const data = await res.json();
      if (data.secure_url) formData.set("image_url", data.secure_url);
    }

    const api = isEdit ? "update_question.php" : "insert_question.php";
    const res = await fetch(api, { method: "POST", body: formData });
    let result;
    try {
      result = await res.json();
    } catch {
      const text = await res.text();
      throw new Error(text || "Lỗi không xác định");
    }

    if (!res.ok) throw new Error(result.message || "Lỗi không xác định");
    alert(result.message);

    isEdit ? resetPreview() : resetForm();
    refreshIframe();
    $("questionIframe").scrollIntoView({ behavior: "smooth" });
    formChanged = false;

  } catch (e) {
    alert("❌ " + (e.message || "Lỗi khi lưu câu hỏi."));
  } finally {
    buttons.forEach(btn => btn.disabled = false);
  }
}

function deleteQuestion() {
  const id = $("question_id").value.trim();
  if (!id || !confirm("Bạn có chắc muốn xoá?")) return;

  fetch("delete_question.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "id=" + encodeURIComponent(id)
  })
    .then(res => res.json())
    .then(data => {
      alert(data.message);
      if (data.status === "success") {
        resetForm();
        refreshIframe();
      }
    });
}

function exportToExcel() {
  const table = $("questionIframe").contentWindow.document.querySelector("#questionTable");
  if (!table) return alert("Không tìm thấy bảng.");
  const wb = XLSX.utils.book_new();
  const ws = XLSX.utils.table_to_sheet(table);
  XLSX.utils.book_append_sheet(wb, ws, "Danh sách câu hỏi");
  XLSX.writeFile(wb, "danh_sach_cau_hoi.xlsx");
}

function importExcel(file) {
  const reader = new FileReader();
  reader.onload = e => {
    const workbook = XLSX.read(new Uint8Array(e.target.result), { type: "array" });
    const sheetName = workbook.SheetNames[0];
    const rows = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName], { header: 1 });

    rows.slice(1).forEach(([id, question, a1, a2, a3, a4, correct, topic]) => {
      if (question && correct) {
        fetch("insert_question.php", {
          method: "POST",
          body: new URLSearchParams({ id: id || "", question, answer1: a1, answer2: a2, answer3: a3, answer4: a4, correct_answer: correct, topic })
        });
      }
    });
    alert("Đã nhập Excel. Hệ thống sẽ tự tải lại sau vài giây.");
    setTimeout(refreshIframe, 2000);
  };
  reader.readAsArrayBuffer(file);
}

$("image").addEventListener("change", function () {
  const file = this.files[0];
  $("imageFileName").textContent = file ? file.name : "";
  if (file) {
    const reader = new FileReader();
    reader.onload = e => {
      const preview = $("imagePreview");
      preview.src = e.target.result;
      preview.style.display = "block";
      preview.style.maxWidth = "100%";
    };
    reader.readAsDataURL(file);
  }
});

$("questionForm").addEventListener("input", e => {
  formChanged = true;
  const id = e.target.id;
  if (["question", "answer1", "answer2", "answer3", "answer4"].includes(id)) {
    renderPreview(id);        // ✅ cập nhật từng preview nhỏ
    debounceFullPreview();    // ✅ cập nhật preview toàn bộ
  }
});


window.addEventListener("beforeunload", e => {
  if (formChanged) {
    e.preventDefault();
    e.returnValue = "";
  }
});

function resetForm() {
  $("questionForm").reset();
  $("question_id").value = "";
  const img = $("imagePreview");
  img.src = "";
  img.style.display = "none";
  img.classList.remove("show");
  $("deleteImageLabel").style.display = "none";
  $("delete_image").checked = false;
  $("image_url").value = "";
  $("imageFileName").textContent = "";
  debounceFullPreview();
  ["question", "answer1", "answer2", "answer3", "answer4"].forEach(renderPreview);
  formChanged = false;
}

function isValidMath(text) {
  try {
    MathJax.tex2chtml(text);
    return true;
  } catch {
    return false;
  }
}

function validateInput(id) {
  const el = $(id);
  const preview = $(`preview_${id}`);
  if (!preview) return;
  const valid = isValidMath(el.value);
  preview.classList.toggle("invalid-math", !valid);
  preview.title = valid ? "" : "Công thức không hợp lệ";
}

window.addEventListener("load", () => {
  if (!window.MathJax?.typesetPromise)
    return console.error("❌ MathJax chưa sẵn sàng!");
  ["question", "answer1", "answer2", "answer3", "answer4"].forEach(renderPreview);
  debounceFullPreview();
  togglePreview(); // 👈 Thêm dòng này để áp dụng trạng thái ban đầu
});

// Toggle Dark Mode
document.getElementById("toggleDarkMode").addEventListener("click", () => {
  document.body.classList.toggle("dark-mode");
  localStorage.setItem("theme", document.body.classList.contains("dark-mode") ? "dark" : "light");
});

// Auto-load theme
window.addEventListener("DOMContentLoaded", () => {
  if (localStorage.getItem("theme") === "dark") {
    document.body.classList.add("dark-mode");
  }
});

function togglePreview() {
  const isChecked = document.getElementById("togglePreview").checked;
  const previewFields = document.querySelectorAll(".latex-preview");

  previewFields.forEach(div => {
    div.style.display = isChecked ? "block" : "none";
  });
}

document.getElementById("togglePreview").addEventListener("change", togglePreview);

preview.classList.toggle("invalid-math", !valid);
preview.title = valid ? "" : "Công thức không hợp lệ";

$("questionForm").addEventListener("input", () => {
  formChanged = true;
  const toggle = $("togglePreview");
  if (!toggle.checked) {
    toggle.checked = true;
    togglePreview();
  }
});
