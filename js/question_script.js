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

const containsMath = text => /(\\\(.+?\\\)|\\\[.+?\\\]|\\begin\{.+?\\end\{.+?\})/.test(text);

// ✅ CHÍNH: Phân tích văn bản có cả công thức và text thường
function processContent(raw) {
  if (!raw || typeof raw !== "string") return "";

  if (!containsMath(raw)) return raw;

  const parts = raw.split(/(\\\(.+?\\\)|\\\[.+?\\\]|\\begin\{[\s\S]+?\\end\{[\s\S]+?\})/g);

  return parts.map(part => {
    if (!part) return "";
    if (/^\\\(.+\\\)$/.test(part)) return part; // inline math
    if (/^\\\[.+\\\]$/.test(part) || /^\\begin\{/.test(part)) return `<div>${part}</div>`; // display math
    return part; // giữ nguyên text, không escape
  }).join("");
}

let mathTimer;
function debounceRender(el) {
  clearTimeout(mathTimer);
  mathTimer = setTimeout(() => {
    if (window.MathJax && containsMath(el.innerHTML)) {
      MathJax.typesetPromise([el]);
    }
  }, 300);
}

function renderMathPage() {
  if (window.MathJax && containsMath(document.body.innerText)) MathJax.typesetPromise();
}

// ========== Preview ==========
function renderPreview(id) {
  const val = $(id).value;
  const preview = $("preview_" + id);
  preview.innerHTML = processContent(val);
  debounceRender(preview);
  validateInput(id);
}

let previewTimeout;
function debounceFullPreview() {
  clearTimeout(previewTimeout);
  previewTimeout = setTimeout(() => {
    updateFullPreview();
    adjustFullPreviewHeight();
  }, 300);
}

function updateFullPreview() {
  const topic = $("topic").value;
  const question = $("question").value;
  const a1 = $("answer1").value;
  const a2 = $("answer2").value;
  const a3 = $("answer3").value;
  const a4 = $("answer4").value;
  const correct = $("correct_answer").value;

  const content = `
    <strong>Chủ đề:</strong> ${topic}<br>
    <strong>Câu hỏi:</strong> ${processContent(question)}<br>
    <strong>Đáp án A:</strong> ${processContent(a1)}<br>
    <strong>Đáp án B:</strong> ${processContent(a2)}<br>
    <strong>Đáp án C:</strong> ${processContent(a3)}<br>
    <strong>Đáp án D:</strong> ${processContent(a4)}<br>
    <strong>Đáp án đúng:</strong> ${correct}
  `;

  $("fullPreview").innerHTML = content;
  if (window.MathJax) MathJax.typesetPromise(["#fullPreview"]);
}

function togglePreview() {
  const show = $("togglePreview").checked;
  document.querySelectorAll(".latex-preview").forEach(div => {
    div.style.display = show ? "block" : "none";
  });
}

function adjustFullPreviewHeight() {
  const box = $("fullPreviewBox");
  if (box && box.style.display !== "none") {
    box.style.height = "auto";
    box.style.height = box.scrollHeight + "px";
  }
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

  if (delChk.checked) {
    img.src = "";
    img.style.display = "none";
  }

  url.value = "";
  delChk.checked = false;
  debounceFullPreview();
}

// ========== Save ==========
function saveQuestion(action) {
  if (action === 'add') {
    // Gọi hàm thêm mới
  } else if (action === 'edit') {
    // Gọi hàm cập nhật
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
        resetForm();
        refreshIframe();
      }
    });
}

// ========== Excel Import/Export ==========
function exportToExcel() {
  const iframe = $("questionIframe");
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

$("image").addEventListener("change", function () {
  const file = this.files[0];
  const label = $("imageFileName");
  label.textContent = file ? file.name : "";

  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      const preview = $("imagePreview");
      preview.src = e.target.result;
      preview.style.display = "block";
      preview.style.maxWidth = "100%";
    };
    reader.readAsDataURL(file);
  }
});

let formChanged = false;
$("questionForm").addEventListener("input", () => {
  formChanged = true;
});

window.addEventListener("beforeunload", function (e) {
  if (formChanged) {
    e.preventDefault();
    e.returnValue = "";
  }
});

function resetForm() {
  const form = $("questionForm");
  form.reset();
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
  formChanged = false;
}

function deleteImage() {
  if (!confirm("Bạn có chắc muốn xoá ảnh minh hoạ?")) return;

  const id = $("question_id").value.trim();
  if (!id) return alert("Bạn cần chọn một câu hỏi đã có để xoá ảnh.");

  const publicId = `pic_${id}`;

  fetch("delete_cloudinary_image.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ public_id: publicId })
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert("Đã xoá ảnh khỏi Cloudinary.");
        $("imagePreview").style.display = "none";
        $("imagePreview").src = "";
        $("image").value = "";
        $("image_url").value = "";
        $("image").setAttribute("data-delete", "1");
        $("deleteImageBtn").style.display = "none";
      } else {
        alert("Không thể xoá ảnh: " + data.message);
      }
    })
    .catch(err => {
      alert("Lỗi khi xoá ảnh: " + err.message);
    });
}

function isValidMath(text) {
  if (!text.trim()) return true;
  try {
    MathJax.tex2chtml(text);
    return true;
  } catch (e) {
    console.warn("Math invalid:", text, e.message);
    return false;
  }
}

window.addEventListener("load", () => {
  if (!window.MathJax || !MathJax.typesetPromise) {
    console.error("❌ MathJax chưa sẵn sàng!");
    return;
  }
  ["question", "answer1", "answer2", "answer3", "answer4"].forEach(renderPreview);
});

function validateInput(id) {
  const el = $(id);
  const preview = $("preview_" + id);

  if (!isValidMath(el.value)) {
    preview.classList.add("invalid-math");
    preview.title = "Công thức không hợp lệ";
  } else {
    preview.classList.remove("invalid-math");
    preview.title = "";
  }
}
