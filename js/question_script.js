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
  preview.innerHTML = wrapMath(val);
  debounceRender(preview);
  validateInput(id);  // <- thêm dòng này
}


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

  const content = `
    <strong>Chủ đề:</strong> ${topic}<br>
    <strong>Câu hỏi:</strong> ${question}<br>
    <strong>Đáp án A:</strong> ${a1}<br>
    <strong>Đáp án B:</strong> ${a2}<br>
    <strong>Đáp án C:</strong> ${a3}<br>
    <strong>Đáp án D:</strong> ${a4}<br>
    <strong>Đáp án đúng:</strong> ${correct}
  `;

  document.getElementById("fullPreview").innerHTML = content;
  if (window.MathJax) MathJax.typesetPromise(["#fullPreview"]);
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

// Hàm chính để xử lý lưu thêm/sửa
async function handleSaveQuestion(isEdit) {
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

  const buttons = document.querySelectorAll(".form-right button");
  buttons.forEach(btn => btn.disabled = true);

  try {
    // Upload ảnh nếu có
    if (file?.size > 0) {
      const upForm = new FormData();
      upForm.append("file", file);
      upForm.append("upload_preset", "quiz_photo");

      const res = await fetch("https://api.cloudinary.com/v1_1/dbdf2gwc9/image/upload", {
        method: "POST",
        body: upForm
      });

      const data = await res.json();
      if (!data.secure_url) {
        throw new Error("Lỗi upload ảnh: " + (data.error?.message || "Không rõ nguyên nhân."));
      }else{
        formData.set("image_url", data.secure_url);
      }
    }

    const api = isEdit ? "update_question.php" : "insert_question.php";
    const res = await fetch(api, {
      method: "POST",
      body: formData
    });

    const result = await res.json();
    if (!res.ok) throw new Error(result.message || "Lỗi không xác định");

    alert(result.message);

    // Đặt lại form nếu thêm mới, nếu sửa thì chỉ làm mới preview
    if (!isEdit) {
      resetForm();
    } else {
      resetPreview();
    }

    refreshIframe();
    $("questionIframe").scrollIntoView({ behavior: "smooth" });
    formChanged = false;

  } catch (e) {
    alert("❌ " + (e.message || "Lỗi khi lưu câu hỏi."));
  } finally {
    buttons.forEach(btn => btn.disabled = false);
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
  const label = document.getElementById("imageFileName");
  label.textContent = file ? file.name : "";
});

document.getElementById("image").addEventListener("change", function () {
  const file = this.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      const preview = document.getElementById("imagePreview");
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
    preview.style.border = "1px solid red";
    preview.title = "Công thức không hợp lệ";
    // Gợi ý: Thêm icon cảnh báo
    preview.innerHTML += '<span style="color:red;"> ⚠️</span>';
  } else {
    preview.style.border = "";
    preview.title = "";
  }
}

$(document).ready(function () {
  $('#questionTable').DataTable({
    dom: 'Bfrtip',
    buttons: [
      {
        extend: 'excelHtml5',
        text: '📥 Xuất Excel',
        className: 'btn-export-excel',
        title: 'Danh sách câu hỏi'
      },
      {
        extend: 'print',
        text: '🖨️ In bảng',
        className: 'btn-print',
        title: 'Danh sách câu hỏi'
      }
    ],
    pageLength: 20,
    lengthMenu: [10, 20, 50, 100],
    language: {
      search: "🔍 Tìm kiếm:",
      lengthMenu: "Hiển thị _MENU_ dòng",
      info: "Hiển thị _START_ đến _END_ trong _TOTAL_ dòng",
      zeroRecords: "Không tìm thấy kết quả phù hợp",
      infoEmpty: "Không có dữ liệu",
      paginate: {
        first: "«",
        last: "»",
        next: "›",
        previous: "‹"
      }
    },
    order: [[0, 'desc']]
  });
  
});

