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
const wrapMath = text => containsMath(text) ? text : `\\(${text})\\`;

const escapeHtml = str => str.replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[m]);

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
  preview.innerHTML = wrapMath(val);
  debounceRender(preview);
  validateInput(id);  // <- thêm dòng này
}

let previewTimeout;
function debounceFullPreview() {
  clearTimeout(previewTimeout);
  previewTimeout = setTimeout(() => {
    updateFullPreview();
    adjustFullPreviewHeight(); // ✅ Gọi thêm ở đây
  }, 300);
}

function updateFullPreview() {
  const topic = document.getElementById("topic").value;
  const question = document.getElementById("question").value;
  const a1 = document.getElementById("answer1").value;
  const a2 = document.getElementById("answer2").value;
  const a3 = document.getElementById("answer3").value;
  const a4 = document.getElementById("answer4").value;
  const correct = document.getElementById("correct_answer").value;

  const escape = escapeHtml;
  const content = `
  <strong>Chủ đề:</strong> ${escape(topic)}<br>
  <strong>Câu hỏi:</strong> ${escape(question)}<br>
  <strong>Đáp án A:</strong> ${escape(a1)}<br>
  <strong>Đáp án B:</strong> ${escape(a2)}<br>
  <strong>Đáp án C:</strong> ${escape(a3)}<br>
  <strong>Đáp án D:</strong> ${escape(a4)}<br>
  <strong>Đáp án đúng:</strong> ${escape(correct)}
`;


  document.getElementById("fullPreview").innerHTML = content;
  if (window.MathJax) MathJax.typesetPromise(["#fullPreview"]);
}

function togglePreview() {
  const show = document.getElementById("togglePreview").checked;
  document.querySelectorAll(".latex-preview").forEach(div => {
    div.style.display = show ? "block" : "none";
  });
}

function adjustFullPreviewHeight() {
  const box = document.getElementById("fullPreviewBox");
  if (box && box.style.display !== "none") {
    box.style.height = "auto"; // Reset trước
    box.style.height = box.scrollHeight + "px"; // Tự điều chỉnh theo nội dung
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

  // Nếu người dùng đã chọn "xoá ảnh", thì ẩn luôn ảnh
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
    // gọi hàm thêm mới
  } else if (action === 'edit') {
    // gọi hàm cập nhật
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

document.getElementById("image").addEventListener("change", function () {
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
document.getElementById("questionForm").addEventListener("input", () => {
  formChanged = true;
});
window.addEventListener("beforeunload", function (e) {
  if (formChanged) {
    e.preventDefault();
    e.returnValue = "";
  }
});

function resetForm() {
  // Reset toàn bộ form về trạng thái ban đầu
  const form = $("questionForm");
  form.reset();

  // Reset ID để tránh nhầm lẫn giữa thêm và sửa
  $("question_id").value = "";

  // Ẩn ảnh xem trước
  const img = $("imagePreview");
  img.src = "";
  img.style.display = "none";
  img.classList.remove("show");

  // Ẩn nhãn xoá ảnh nếu có
  $("deleteImageLabel").style.display = "none";
  $("delete_image").checked = false;

  // Reset trường ảnh URL (ẩn)
  $("image_url").value = "";
  $("imageFileName").textContent = "";

  // Làm mới xem trước công thức toàn bộ
  debounceFullPreview();

  // Đánh dấu form chưa thay đổi
  formChanged = false;
}

function isValidMath(text) {
  if (!text.trim()) return true;
  try {
    MathJax.tex2chtml(text); // nếu sai cú pháp sẽ throw
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
  
};

