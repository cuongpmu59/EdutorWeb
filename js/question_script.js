
const $ = id => document.getElementById(id);
const $$ = selector => document.querySelector(selector);

// Upload ảnh tạm khi người dùng chọn
$("image").addEventListener("change", function () {
  const file = this.files[0];
  if (!file) return;

  const formData = new FormData();
  const tempName = "temp_" + Date.now();
  formData.append("file", file);
  formData.append("upload_preset", "your_unsigned_preset"); // nếu dùng unsigned preset
  formData.append("public_id", tempName);

  fetch("https://api.cloudinary.com/v1_1/your_cloud_name/image/upload", {
    method: "POST",
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      if (data.secure_url) {
        $("imagePreview").src = data.secure_url;
        $("imagePreview").style.display = "block";
        $("imagePreview").classList.add("show");
        $("image_url").value = data.secure_url;
        $("imageFileName").textContent = file.name;
        $("deleteImageLabel").style.display = "inline-block";
      } else {
        alert("Lỗi khi tải ảnh lên Cloudinary.");
      }
    })
    .catch(err => {
      console.error("Upload error:", err);
      alert("Không thể tải ảnh lên.");
    });
});

// Gửi form để thêm câu hỏi
function saveQuestion(action) {
  if (action !== 'add') return; // chỉ xử lý thêm mới ở đây

  const form = $("questionForm");
  const formData = new FormData(form);

  fetch("insert_question.php", {
    method: "POST",
    body: new URLSearchParams([...formData.entries()])
  })
    .then(res => res.json())
    .then(data => {
      if (data.status === "success") {
        alert("✅ Đã thêm câu hỏi!");
        resetForm();
        refreshIframe();
      } else {
        alert("❌ Lỗi: " + data.message);
      }
    })
    .catch(err => {
      alert("Không thể kết nối máy chủ.");
      console.error(err);
    });
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

// ========== Đồng bộ chọn dòng từ iframe ==========
window.addEventListener("message", function (event) {
  const data = event.data;
  if (!data || typeof data !== "object" || !data.id) return;

  $("question_id").value = data.id;
  $("topic").value = data.topic || "";
  $("question").value = data.question || "";
  $("answer1").value = data.answer1 || "";
  $("answer2").value = data.answer2 || "";
  $("answer3").value = data.answer3 || "";
  $("answer4").value = data.answer4 || "";
  $("correct_answer").value = data.correct_answer || "";

  if (data.image) {
    $("image_url").value = data.image;
    $("imagePreview").src = data.image;
    $("imagePreview").style.display = "block";
    $("imagePreview").classList.add("show");
    $("deleteImageLabel").style.display = "inline-block";
  } else {
    $("image_url").value = "";
    $("imagePreview").src = "";
    $("imagePreview").style.display = "none";
    $("imagePreview").classList.remove("show");
    $("deleteImageLabel").style.display = "none";
  }

  ["question", "answer1", "answer2", "answer3", "answer4"].forEach(renderPreview);
  debounceFullPreview();
  formChanged = false;
});

// ========== Arrow Up / Down ==========
document.addEventListener("keydown", function (e) {
  if (["ArrowUp", "ArrowDown"].includes(e.key)) {
    const iframe = $("questionIframe");
    if (!iframe) return;
    iframe.contentWindow.postMessage({ type: "navigate", direction: e.key }, "*");
    e.preventDefault();
  }
});
// ========== Xử lý nút Chọn ảnh ==========
$("select_image").addEventListener("click", () => {
  $("image").click();
});
