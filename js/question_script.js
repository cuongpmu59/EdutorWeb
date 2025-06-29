// ========== Utility Functions ==========
const $ = id => document.getElementById(id);
const $$ = selector => document.querySelector(selector);

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

const containsMath = text => /(\\(.+?\\))|(\\[.+?\\])|(\$\$.+?\$\$)|(\$.+?\$)/.test(text);
const wrapMath = text => containsMath(text) ? text : `\(${text}\)`;

const escapeHtml = str => str.replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[m]);

let mathTimer;
function debounceRender(el) {
  clearTimeout(mathTimer);
  mathTimer = setTimeout(() => {
    if (window.MathJax && containsMath(el.innerHTML)) MathJax.typesetPromise([el]);
  }, 250);
}

function renderMathPage() {
  if (window.MathJax && containsMath(document.body.innerText)) MathJax.typesetPromise();
}

// ========== Preview ==========
function renderPreview(id) {
  const val = $(id).value;
  const preview = $("preview_" + id);
  preview.innerHTML = escapeHtml(val);
  debounceRender(preview);
}

let previewTimer;
function debounceFullPreview() {
  clearTimeout(previewTimer);
  previewTimer = setTimeout(updateFullPreview, 300);
}

function updateFullPreview() {
  const q = $("question").value;
  const answers = ["answer1", "answer2", "answer3", "answer4"].map(id => $(id).value);
  const correct = $("correct_answer").value;
  const html = `
    <p><strong>Câu hỏi:</strong> ${wrapMath(q)}</p>
    <ul>${["A","B","C","D"].map((l,i) => `<li><strong>${l}.</strong> ${answers[i]}</li>`).join('')}</ul>
    <p><strong>Đáp án đúng:</strong> ${correct}</p>
  `;
  const preview = $("fullPreview");
  preview.innerHTML = html;
  debounceRender(preview);
}

function togglePreviewBox(id, target) {
  $(target).style.display = $(id).checked ? "block" : "none";
}

function resetPreview() {
  const img = $("imagePreview"), url = $("image_url"), delLbl = $("deleteImageLabel"), delChk = $("delete_image");
  if (url.value) {
    img.src = url.value;
    img.classList.add("show");
    delLbl.style.display = "inline-block";
  } else {
    img.src = "";
    img.classList.remove("show");
    delLbl.style.display = "none";
  }
  url.value = "";
  delChk.checked = false;
  debounceFullPreview();
}

// ========== Save ==========
async function saveQuestion() {
  const id = $("question_id").value.trim();
  const formData = getFormData();
  formData.set("delete_image", $("delete_image").checked ? "1" : "0");

  for (let field of ["question", "answer1", "answer2", "answer3", "answer4", "correct_answer", "topic"]) {
    if (!formData.get(field)?.trim()) return alert("Vui lòng điền đầy đủ thông tin.");
  }

  const file = formData.get("image");
  if (file?.size > 0) {
    if (!file.type.startsWith("image/")) return alert("Chỉ chấp nhận ảnh.");
    if (file.size > 2 * 1024 * 1024) return alert("Ảnh quá lớn. < 2MB thôi.");
  }

  const btn = $$(".form-right button");
  btn.disabled = true;

  try {
    if (file?.size > 0) {
      const upForm = new FormData();
      upForm.append("file", file);
      upForm.append("upload_preset", "quiz_photo");
      const res = await fetch("https://api.cloudinary.com/v1_1/dbdf2gwc9/image/upload", { method: "POST", body: upForm });
      const data = await res.json();
      if (data.secure_url) formData.set("image_url", data.secure_url);
    }

    const api = id ? "update_question.php" : "insert_question.php";
    const res = await fetch(api, { method: "POST", body: formData });
    const result = await res.json();
    if (!res.ok) throw new Error(result.message || "Lỗi không xác định");

    alert(result.message);
    if (!id) $("questionForm").reset();
    resetPreview();
    refreshIframe();
    $("questionIframe").scrollIntoView({ behavior: "smooth" });
    formChanged = false;
  } catch (e) {
    alert("❌ " + (e.message || "Lỗi khi lưu câu hỏi."));
  } finally {
    btn.disabled = false;
  }
}

// ========== Delete ==========
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
        $("questionForm").reset();
        resetPreview();
        refreshIframe();
      }
    });
}

// ========== Excel Import/Export ==========
function exportToExcel() {
  const iframe = document.getElementById("questionIframe");
  const table = iframe.contentWindow.document.querySelector("#questionTable");
  if (!table) return alert("Không tìm thấy bảng.");

  const wb = XLSX.utils.book_new();
  const ws = XLSX.utils.table_to_sheet(table);
  XLSX.utils.book_append_sheet(wb, ws, "Danh sách câu hỏi");
  XLSX.writeFile(wb, "danh_sach_cau_hoi.xlsx");
}

function importExcel(file) {
  const reader = new FileReader();
  reader.onload = function (e) {
    const data = new Uint8Array(e.target.result);
    const workbook = XLSX.read(data, { type: "array" });
    const sheet = workbook.Sheets[workbook.SheetNames[0]];
    const rows = XLSX.utils.sheet_to_json(sheet, { header: 1 });

    rows.slice(1).forEach(row => {
      const [id, question, a1, a2, a3, a4, correct, topic] = row;
      if (question && correct) {
        fetch("insert_question.php", {
          method: "POST",
          body: new URLSearchParams({
            id: id || "",
            question, answer1: a1, answer2: a2, answer3: a3, answer4: a4,
            correct_answer: correct, topic
          })
        });
      }
    });

    alert("Đã nhập Excel. Hệ thống sẽ tự tải lại sau vài giây.");
    setTimeout(refreshIframe, 2000);
  };
  reader.readAsArrayBuffer(file);
}
