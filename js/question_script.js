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

const containsMath = text => /(\\\(.+?\\\))|(\\\[.+?\\\])|(\$\$.+?\$\$)|(\$.+?\$)/.test(text);
const wrapMath = text => containsMath(text) ? text : `\\(${text}\\)`;

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

// ========== Search ==========
function searchQuestion() {
  const keyword = prompt("Nhập từ khoá:");
  if (!keyword) return;

  fetch("search_question.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "keyword=" + encodeURIComponent(keyword)
  })
    .then(res => res.json())
    .then(data => data.length ? showSearchModal(data) : alert("Không tìm thấy câu hỏi."))
    .catch(err => alert("Lỗi tìm kiếm: " + err.message));
}

function showSearchModal(data) {
  const tbody = $$("#searchResultsTable tbody");
  tbody.innerHTML = "";
  data.forEach(item => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${item.id}</td>
      <td>${item.topic}</td>
      <td>${item.question}</td>
      <td>${item.correct_answer}</td>
      <td>${item.image ? `<img src="${item.image}" style="max-height:60px;border-radius:4px;">` : ""}</td>
    `;
    row.onclick = () => {
      window.postMessage({ type: "fillForm", data: item }, "*");
      closeSearchModal();
    };
    tbody.appendChild(row);
  });
  $("searchModal").style.display = "flex";
}

function closeSearchModal() {
  $("searchModal").style.display = "none";
}

// ========== Image Preview ==========
$("image").addEventListener("change", function () {
  const file = this.files[0];
  $("imageFileName").textContent = file?.name || "";
  const preview = $("imagePreview"), delChk = $("delete_image"), delLbl = $("deleteImageLabel");
  if (file) {
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      preview.classList.add("show");
      delChk.checked = false;
      delLbl.style.display = "inline-block";
    };
    reader.readAsDataURL(file);
  } else {
    preview.src = "";
    preview.classList.remove("show");
    delChk.checked = false;
    delLbl.style.display = "none";
  }
});

// ========== Sync from Table ==========
window.addEventListener("message", ({ data }) => {
  if (data.type === "fillForm") {
    const d = data.data;
    ["question_id", "topic", "question", "answer1", "answer2", "answer3", "answer4", "correct_answer"].forEach(id => $(id).value = d[id]);
    const img = $("imagePreview"), label = $("deleteImageLabel"), url = $("image_url");
    if (d.image) {
      img.src = d.image;
      img.classList.add("show");
      url.value = d.image;
      label.style.display = "inline-block";
    } else {
      img.src = "";
      img.classList.remove("show");
      url.value = "";
      label.style.display = "none";
    }
    ["question", "answer1", "answer2", "answer3", "answer4"].forEach(renderPreview);
    debounceFullPreview();
    formChanged = false;
  }
});

// ========== Exit Warning ==========
let formChanged = false;
$("questionForm").addEventListener("input", () => formChanged = true);
window.addEventListener("beforeunload", e => {
  if (formChanged) {
    e.preventDefault();
    e.returnValue = "";
  }
});

// ========== DOM Ready ==========
document.addEventListener('DOMContentLoaded', () => {
  const toggle = $("togglePreview");
  if (toggle) toggle.addEventListener("change", () => togglePreviewBox("togglePreview", "previewBox"));
});

// ========== DataTable Interaction ==========
let currentRow = null;
$(document).ready(() => {
  const table = $('#questionTable').DataTable({
    language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json' },
    pageLength: 10,
    columnDefs: [{ orderable: false, targets: [8] }]
  });

  $('#filterTopic').on('change', function () {
    table.column(7).search($(this).val()).draw();
  });

  $('#questionTable tbody').on('click', 'tr', function () {
    if (currentRow) $(currentRow).removeClass('selected-row');
    currentRow = this;
    $(this).addClass('selected-row');

    const cells = this.querySelectorAll("td");
    const rowData = {
      id: cells[0].innerText.trim(),
      topic: cells[1].innerText.trim(),
      question: cells[2].innerText.trim(),
      answer1: cells[3].innerText.trim(),
      answer2: cells[4].innerText.trim(),
      answer3: cells[5].innerText.trim(),
      answer4: cells[6].innerText.trim(),
      correct_answer: cells[7].innerText.trim(),
      image: cells[8].querySelector("img")?.src?.replace(/upload\/c_fill,h_40,w_40\//, "upload/") || ""
    };
    parent.postMessage({ type: "fillForm", data: rowData }, window.location.origin);
  });
});
