// ========== 1. Utility Functions ==========
function getEl(id) {
  return document.getElementById(id);
}

function getFormData() {
  return new FormData(getEl("questionForm"));
}

function refreshIframe() {
  const iframe = getEl("questionIframe");
  if (iframe) {
    iframe.contentWindow.location.reload();
    iframe.onload = () => {
      iframe.contentWindow.MathJax?.typesetPromise();
    };
  }
}

function containsMath(text) {
  return /(\\\(.+?\\\))|(\\\[.+?\\\])|(\$\$.+?\$\$)|(\$.+?\$)/.test(text);
}

function wrapMathIfNeeded(text) {
  return containsMath(text) ? text : `\\(${text}\\)`;
}

function escapeHtml(str) {
  return str.replace(/[&<>"']/g, m => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;',
    '"': '&quot;', "'": '&#039;'
  })[m]);
}

let mathJaxTimer;
function debounceRenderMath(el) {
  clearTimeout(mathJaxTimer);
  mathJaxTimer = setTimeout(() => {
    if (window.MathJax && containsMath(el.innerHTML)) {
      MathJax.typesetPromise([el]);
    }
  }, 250);
}

function renderMathInPage() {
  if (window.MathJax && containsMath(document.body.innerText)) {
    MathJax.typesetPromise();
  }
}

// ========== 2. Preview ==========
function renderPreview(id) {
  const val = getEl(id).value;
  const preview = getEl("preview_" + id);
  preview.innerHTML = escapeHtml(val);
  debounceRenderMath(preview);
}

let previewTimer;
function debounceFullPreview() {
  clearTimeout(previewTimer);
  previewTimer = setTimeout(updateFullPreview, 300);
}

function updateFullPreview() {
  const q = getEl("question").value;
  const answers = ['answer1', 'answer2', 'answer3', 'answer4'].map(id => getEl(id).value);
  const correct = getEl("correct_answer").value;

  const html = `
    <p><strong>Câu hỏi:</strong> ${wrapMathIfNeeded(q)}</p>
    <ul>
      ${['A', 'B', 'C', 'D'].map((l, i) => `<li><strong>${l}.</strong> ${answers[i]}</li>`).join("")}
    </ul>
    <p><strong>Đáp án đúng:</strong> ${correct}</p>
  `;
  const preview = getEl("fullPreview");
  preview.innerHTML = html;
  debounceRenderMath(preview);
}

function togglePreviewBox(id, target) {
  const isChecked = getEl(id).checked;
  getEl(target).style.display = isChecked ? "block" : "none";
}

function resetPreview() {
  const img = getEl("imagePreview");
  const urlField = getEl("image_url");
  const deleteLabel = getEl("deleteImageLabel");
  const deleteCheckbox = getEl("delete_image");

  if (urlField.value) {
    img.src = urlField.value;
    img.classList.add("show");
    deleteLabel.style.display = "inline-block";
  } else {
    img.src = "";
    img.classList.remove("show");
    deleteLabel.style.display = "none";
  }

  urlField.value = "";
  deleteCheckbox.checked = false;
  debounceFullPreview();
}

// ========== 3. Save ==========
async function saveQuestion() {
  const id = getEl("question_id").value.trim();
  const formData = getFormData();
  formData.set("delete_image", getEl("delete_image").checked ? "1" : "0");

  const required = ["question", "answer1", "answer2", "answer3", "answer4", "correct_answer", "topic"];
  for (const field of required) {
    if (!formData.get(field)?.trim()) {
      return alert("Vui lòng điền đầy đủ thông tin.");
    }
  }

  const imageFile = formData.get("image");
  if (imageFile?.size > 0) {
    if (!imageFile.type.startsWith("image/")) return alert("Chỉ chấp nhận ảnh.");
    if (imageFile.size > 2 * 1024 * 1024) return alert("Ảnh quá lớn. < 2MB thôi.");
  }

  const btn = document.querySelector(".form-right button");
  btn.disabled = true;

  try {
    if (imageFile?.size > 0) {
      const uploadForm = new FormData();
      uploadForm.append("file", imageFile);
      uploadForm.append("upload_preset", "quiz_photo");

      const res = await fetch("https://api.cloudinary.com/v1_1/dbdf2gwc9/image/upload", { method: "POST", body: uploadForm });
      const data = await res.json();
      if (data.secure_url) formData.set("image_url", data.secure_url);
    }

    const api = id ? "update_question.php" : "insert_question.php";
    const res = await fetch(api, { method: "POST", body: formData });
    const result = await res.json();

    if (!res.ok) throw new Error(result.message || "Lỗi không xác định");

    alert(result.message);
    if (!id) getEl("questionForm").reset();
    resetPreview();
    refreshIframe();
    getEl("questionIframe").scrollIntoView({ behavior: "smooth" });
    formChanged = false;

  } catch (e) {
    alert("❌ " + (e.message || "Lỗi khi lưu câu hỏi."));
  } finally {
    btn.disabled = false;
  }
}

// ========== 4. Delete ==========
function deleteQuestion() {
  const id = getEl("question_id").value.trim();
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
        getEl("questionForm").reset();
        resetPreview();
        refreshIframe();
      }
    });
}

// ========== 5. Search ==========
function searchQuestion() {
  const keyword = prompt("Nhập từ khoá:");
  if (!keyword) return;

  fetch("search_question.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "keyword=" + encodeURIComponent(keyword)
  })
    .then(res => res.json())
    .then(data => {
      if (!data.length) alert("Không tìm thấy câu hỏi.");
      else showSearchModal(data);
    })
    .catch(err => alert("Lỗi tìm kiếm: " + err.message));
}

function showSearchModal(data) {
  const tbody = document.querySelector("#searchResultsTable tbody");
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

  getEl("searchModal").style.display = "flex";
}

function closeSearchModal() {
  getEl("searchModal").style.display = "none";
}

// ========== 6. Image Preview ==========
getEl("image").addEventListener("change", function () {
  const file = this.files[0];
  getEl("imageFileName").textContent = file?.name || "";
  const preview = getEl("imagePreview");
  const deleteCheckbox = getEl("delete_image");
  const deleteLabel = getEl("deleteImageLabel");

  if (file) {
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      preview.classList.add("show");
      deleteCheckbox.checked = false;
      deleteLabel.style.display = "inline-block";
    };
    reader.readAsDataURL(file);
  } else {
    preview.src = "";
    preview.classList.remove("show");
    deleteCheckbox.checked = false;
    deleteLabel.style.display = "none";
  }
});

// ========== 7. Đồng bộ từ bảng ==========
window.addEventListener("message", ({ data }) => {
  if (data.type === "fillForm") {
    const d = data.data;
    ["question_id", "topic", "question", "answer1", "answer2", "answer3", "answer4", "correct_answer"]
      .forEach(id => getEl(id).value = d[id]);

    const img = getEl("imagePreview");
    const label = getEl("deleteImageLabel");
    const url = getEl("image_url");

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

// ========== 8. Cảnh báo khi thoát ==========
let formChanged = false;
getEl("questionForm").addEventListener("input", () => formChanged = true);
window.addEventListener("beforeunload", e => {
  if (formChanged) {
    e.preventDefault();
    e.returnValue = "";
  }
});

// ========== 9. DOM Ready ==========
document.addEventListener('DOMContentLoaded', () => {
  const toggle = getEl("togglePreview");
  if (toggle) {
    toggle.addEventListener("change", () => togglePreviewBox("togglePreview", "previewBox"));
  }
});

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
      question: cells[1].innerText.trim(),
      answer1: cells[2].innerText.trim(),
      answer2: cells[3].innerText.trim(),
      answer3: cells[4].innerText.trim(),
      answer4: cells[5].innerText.trim(),
      correct_answer: cells[6].innerText.trim(),
      topic: cells[7].innerText.trim(),
      image: cells[8].querySelector("img")?.src?.replace(/upload\/c_fill,h_40,w_40\//, "upload/") || ""
    };

    parent.postMessage({ type: "fillForm", data: rowData }, window.location.origin);
  });
});
