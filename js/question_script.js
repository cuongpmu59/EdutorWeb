// ========== 1. Utility Functions ==========
function getFormData() {
  return new FormData(document.getElementById("questionForm"));
}

function refreshIframe() {
  const iframe = document.getElementById("questionIframe");
  if (iframe) {
    iframe.contentWindow.location.reload();
    iframe.onload = function () {
      if (iframe.contentWindow.MathJax) {
        iframe.contentWindow.MathJax.typesetPromise();
      }
    };
  }
}

function containsMath(content) {
  return /(\\\(.+?\\\))|(\\\[.+?\\\])|(\$\$.+?\$\$)|(\$.+?\$)/.test(content);
}


let mathJaxTimer;
function debounceRenderMath(element) {
  clearTimeout(mathJaxTimer);
  mathJaxTimer = setTimeout(() => {
    if (window.MathJax && containsMath(element.innerHTML)) {
      MathJax.typesetPromise([element]);
    }
  }, 250);
}

function renderMathInPage() {
  if (!window.MathJax) {
    console.warn("MathJax chưa được tải.");
    return;
  }
  
  if (containsMath(document.body.innerText)) {
    MathJax.typesetPromise();
  }
}

function escapeHtml(str) {
  return str.replace(/[&<>"']/g, function (m) {
    return ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    })[m];
  });
}

// ========== 2. Preview ==========
function renderPreview(fieldId) {
  const value = document.getElementById(fieldId).value;
  const previewDiv = document.getElementById("preview_" + fieldId);
  previewDiv.innerHTML = escapeHtml(value);
  debounceRenderMath(previewDiv);
  
}

let previewTimer;
function debounceFullPreview() {
  clearTimeout(previewTimer);
  previewTimer = setTimeout(updateFullPreview, 300);
}

function updateFullPreview() {
  const q = document.getElementById("question").value;
  const a = document.getElementById("answer1").value;
  const b = document.getElementById("answer2").value;
  const c = document.getElementById("answer3").value;
  const d = document.getElementById("answer4").value;
  const correct = document.getElementById("correct_answer").value;

  const html = `
    <p><strong>Câu hỏi:</strong> \\(${q}\\)</p>
    <ul>
      <li><strong>A.</strong> ${a}</li>
      <li><strong>B.</strong> ${b}</li>
      <li><strong>C.</strong> ${c}</li>
      <li><strong>D.</strong> ${d}</li>
    </ul>
    <p><strong>Đáp án đúng:</strong> ${correct}</p>
  `;
  const preview = document.getElementById("fullPreview");
  preview.innerHTML = html;
  debounceRenderMath(preview);
}

function togglePreview() {
  const isChecked = document.getElementById("togglePreview").checked;
  document.querySelectorAll(".latex-preview").forEach(div => {
    div.style.display = isChecked ? "block" : "none";
  });
}

function toggleFullPreview() {
  const isChecked = document.getElementById("toggleFullPreview").checked;
  document.getElementById("fullPreview").style.display = isChecked ? "block" : "none";
}

function resetPreview() {
  document.getElementById("imagePreview").classList.remove("show");
  document.getElementById("image_url").value = "";
  document.getElementById("delete_image").checked = false;
  document.getElementById("deleteImageLabel").style.display = "none";
  debounceFullPreview();
}

// ========== 3. Save Question ==========
async function saveQuestion() {
  const id = document.getElementById("question_id").value.trim();
  const formData = getFormData();
  formData.set("delete_image", document.getElementById("delete_image").checked ? "1" : "0");

  const required = ["question", "answer1", "answer2", "answer3", "answer4", "correct_answer", "topic"];
  for (let field of required) {
    if (!formData.get(field)?.trim()) {
      alert("Vui lòng điền đầy đủ thông tin câu hỏi, đáp án và chủ đề.");
      return;
    }
  }

  const imageFile = formData.get("image");
  if (imageFile && imageFile.size > 0) {
    if (!imageFile.type.startsWith("image/")) {
      alert("Chỉ chấp nhận file ảnh!");
      return;
    }
    if (imageFile.size > 2 * 1024 * 1024) {
      alert("Ảnh quá lớn. Vui lòng chọn ảnh dưới 2MB.");
      return;
    }
  }

  const saveBtn = document.querySelector(".form-right button:nth-child(1)");
  saveBtn.disabled = true;

  // Upload ảnh lên Cloudinary
  if (imageFile && imageFile.size > 0) {
    const cloudForm = new FormData();
    cloudForm.append("file", imageFile);
    cloudForm.append("upload_preset", "quiz_photo");
    try {
      const res = await fetch("https://api.cloudinary.com/v1_1/dbdf2gwc9/image/upload", {
        method: "POST",
        body: cloudForm,
      });
      const data = await res.json();
      if (data.secure_url) formData.set("image_url", data.secure_url);
    } catch (err) {
      alert("Không thể tải ảnh lên Cloudinary: " + err.message);
      saveBtn.disabled = false;
      return;
    }
  }

  // 🔄 Dùng file phù hợp
  const apiUrl = id ? "update_question.php" : "insert_question.php";

  try {
    const res = await fetch(apiUrl, {
      method: "POST",
      body: formData
    });
  
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || "Lỗi không rõ");
  
    alert(data.message);
    if (!id) document.getElementById("questionForm").reset();
    resetPreview();
    refreshIframe();
    document.getElementById("questionIframe").scrollIntoView({ behavior: "smooth" });
    formChanged = false;
    setTimeout(() => formChanged = false, 100);

  } catch (err) {
    alert("❌ " + (err.message || "Không thể xử lý phản hồi từ máy chủ."));
  } finally {
    saveBtn.disabled = false;
  }
  
}

// ========== 4. Delete ==========
function deleteQuestion() {
  const id = document.getElementById("question_id").value.trim();
  if (!id) return alert("Chọn câu hỏi cần xoá.");
  if (!confirm("Bạn có chắc muốn xoá?")) return;

  fetch("delete_question.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "id=" + encodeURIComponent(id)
  })
  .then(res => res.json())
  .then(data => {
    alert(data.message);
    if (data.status === "success") {
      document.getElementById("questionForm").reset();
      resetPreview();
      refreshIframe();
    }
  });
  
}

// ========== 5. Search ==========
function searchQuestion() {
  const keyword = prompt("Nhập từ khoá cần tìm:");
  if (!keyword) return;

  fetch("search_question.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "keyword=" + encodeURIComponent(keyword)
  })
    .then(res => res.json())
    .then(data => {
      if (data.length === 0) alert("Không tìm thấy câu hỏi nào.");
      else showSearchModal(data);
    })
    .catch(err => alert("Lỗi tìm kiếm: " + err.message));
}

function showSearchModal(data) {
  const modal = document.getElementById("searchModal");
  const tbody = document.querySelector("#searchResultsTable tbody");
  tbody.innerHTML = "";
  data.forEach(item => {
    const row = document.createElement("tr");
    row.innerHTML = `
  <td>${item.id}</td>
  <td>${item.topic}</td>
  <td>${item.question}</td>
  <td>${item.correct_answer}</td>
  <td>
    ${item.image
      ? `<img src="${item.image}" alt="img" style="max-height:60px; border-radius:4px;">`
      : ""}
  </td>
`;
    row.onclick = () => {
      window.postMessage({ type: "fillForm", data: item }, "*");
      row.style.backgroundColor = "#e0f7fa";
      closeSearchModal();
    };
    tbody.appendChild(row);
  });
  modal.style.display = "flex";
}

function closeSearchModal() {
  document.getElementById("searchModal").style.display = "none";
}

// ========== 6. Image Preview ==========
document.getElementById("image").addEventListener("change", function () {
  const file = this.files[0];
  document.getElementById("imageFileName").textContent = file ? file.name : "";

  const preview = document.getElementById("imagePreview");
  const deleteCheckbox = document.getElementById("delete_image");
  const deleteLabel = document.getElementById("deleteImageLabel");

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
window.addEventListener("message", function (event) {
  if (event.data.type === "fillForm") {
    const data = event.data.data;
    document.getElementById("question_id").value = data.id;
    document.getElementById("topic").value = data.topic;
    document.getElementById("question").value = data.question;
    document.getElementById("answer1").value = data.answer1;
    document.getElementById("answer2").value = data.answer2;
    document.getElementById("answer3").value = data.answer3;
    document.getElementById("answer4").value = data.answer4;
    document.getElementById("correct_answer").value = data.correct_answer;

    if (data.image) {
      const img = document.getElementById("imagePreview");
      img.src = data.image;
      img.classList.add("show");
      document.getElementById("image_url").value = data.image;
      document.getElementById("deleteImageLabel").style.display = "inline-block";
    } else {
      document.getElementById("imagePreview").src = document.getElementById("image_url").value || "";
      document.getElementById("imagePreview").classList.remove("show");
      document.getElementById("image_url").value = "";
      document.getElementById("deleteImageLabel").style.display = "none";
    }

    ["question", "answer1", "answer2", "answer3", "answer4"].forEach(renderPreview);
    debounceFullPreview();
    formChanged = false;
  }
});

// ========== 8. Cảnh báo khi thoát ==========
let formChanged = false;
document.getElementById("questionForm").addEventListener("input", () => {
  formChanged = true;
});
window.addEventListener("beforeunload", (e) => {
  if (formChanged) {
    e.preventDefault();
    e.returnValue = "";
  }
});

// Toggle ẩn/hiện khối xem trước toàn bộ
document.addEventListener('DOMContentLoaded', function () {
  const toggle = document.getElementById("togglePreview");
  const previewBox = document.getElementById("previewBox");

  if (toggle && previewBox) {
    toggle.addEventListener("change", function () {
      previewBox.style.display = toggle.checked ? "block" : "none";
    });
  }
});

let currentRow = null; // Thêm dòng này ở đầu script hoặc trong $(document).ready
$(document).ready(function () {
  const table = $('#questionTable').DataTable({
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
    },
    pageLength: 10,
    lengthChange: true,
    columnDefs: [
      { orderable: false, targets: [8] }
    ]
  });

  // === Lọc theo chủ đề ===
  $('#filterTopic').on('change', function () {
    const selected = $(this).val();
    table.column(7).search(selected).draw();
  });


  // Khi click vào hàng → postMessage + tô sáng
  $('#questionTable tbody').on('click', 'tr', function () {
    if (currentRow) $(currentRow).removeClass('selected-row');
    currentRow = this;
    $(this).addClass('selected-row');

    const data = table.row(this).data();
    if (!data) return;

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
      image: cells[8].querySelector('img')?.getAttribute('src')?.replace(/upload\/c_fill,h_40,w_40\//, 'upload/') || ""
    };
    parent.postMessage({ type: "fillForm", data: rowData }, window.location.origin);
  });
});




