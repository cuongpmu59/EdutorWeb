// ========== Tiện ích chung ==========
function getForm() {
  return document.getElementById("questionForm");
}
function getValue(id) {
  return document.getElementById(id).value;
}
function resetForm() {
  getForm().reset();
  document.getElementById("previewImage").style.display = "none";
  document.getElementById("deleteImageLabel").style.display = "none";
  previewFull();
  formChanged = false;
}
function setPreviewImage(url) {
  const img = document.getElementById("previewImage");
  if (url) {
    img.src = url;
    img.style.display = "block";
    document.getElementById("deleteImageLabel").style.display = "inline";
  } else {
    img.src = "";
    img.style.display = "none";
    document.getElementById("deleteImageLabel").style.display = "none";
  }
}

// ========== Upload ảnh lên Cloudinary ==========
async function uploadImage(file) {
  const form = new FormData();
  form.append("file", file);
  form.append("upload_preset", "quiz_photo");
  form.append("cloud_name", "dbdf2gwc9");

  const res = await fetch("https://api.cloudinary.com/v1_1/dbdf2gwc9/image/upload", {
    method: "POST",
    body: form,
  });
  const data = await res.json();
  return data.secure_url || "";
}

// ========== Thêm / Sửa câu hỏi ==========
async function submitQuestion(endpoint) {
  const form = getForm();
  const formData = new FormData(form);

  if (form.image.files[0]) {
    const url = await uploadImage(form.image.files[0]);
    if (!url) return alert("Tải ảnh thất bại!");
    formData.set("image_url", url);
  }

  try {
    const res = await fetch(endpoint, { method: "POST", body: formData });
    const msg = await res.text();
    alert(msg);
    resetForm();
    refreshIframe();
  } catch {
    alert("Lỗi khi gửi dữ liệu!");
  }
}

export function addQuestion() {
  submitQuestion("insert_question.php");
}

export function updateQuestion() {
  submitQuestion("update_question.php");
}

// ========== Xoá câu hỏi ==========
export async function deleteQuestion() {
  const id = getValue("question_id");
  if (!id || !confirm("Bạn chắc chắn xoá?")) return;

  try {
    const res = await fetch("delete_question.php", {
      method: "POST",
      body: new URLSearchParams({ id }),
    });
    const msg = await res.text();
    alert(msg);
    resetForm();
    refreshIframe();
  } catch {
    alert("Lỗi khi xoá câu hỏi!");
  }
}

// ========== Làm mới iframe ==========
function refreshIframe() {
  const iframe = document.getElementById("questionIframe");
  if (!iframe) return;
  iframe.contentWindow.location.reload();
  iframe.onload = () => {
    if (iframe.contentWindow.MathJax) {
      iframe.contentWindow.MathJax.typesetPromise();
    }
  };
}

// ========== Xem trước toàn bộ ==========
export function previewFull() {
  const q = (id) => getValue(id);
  const ids = {
    pv_id: "question_id",
    pv_topic: "topic",
    pv_question: "question",
    pv_a: "answer1",
    pv_b: "answer2",
    pv_c: "answer3",
    pv_d: "answer4",
    pv_correct: "correct_answer",
  };

  for (const [targetId, sourceId] of Object.entries(ids)) {
    document.getElementById(targetId).innerText = q(sourceId);
  }

  const url = q("image_url");
  setPreviewImage(url);
  document.getElementById("pv_image").style.display = url ? "block" : "none";

  if (window.MathJax) MathJax.typesetPromise();
}

// ========== Zoom ảnh ==========
export function zoomImage(img) {
  document.getElementById("imageModal").style.display = "block";
  document.getElementById("modalImage").src = img.src;
}

// ========== Modal tìm kiếm ==========
export function openSearchModal() {
  document.getElementById("searchModal").style.display = "block";
}
export function closeSearchModal() {
  document.getElementById("searchModal").style.display = "none";
}

// ========== Tìm kiếm ==========
export function searchQuestion() {
  const keyword = getValue("searchKeyword").trim().toLowerCase();
  const tableBody = document.querySelector("#searchResultTable tbody");
  const iframeDoc = document.getElementById("questionIframe").contentDocument;

  tableBody.innerHTML = "";
  iframeDoc.querySelectorAll("tbody tr").forEach((row) => {
    const cols = row.querySelectorAll("td");
    const text = Array.from(cols).map((c) => c.textContent.toLowerCase()).join(" ");
    if (text.includes(keyword)) {
      const newRow = document.createElement("tr");
      newRow.innerHTML = `
        <td>${cols[0].textContent}</td>
        <td>${cols[7].textContent}</td>
        <td>${cols[1].textContent}</td>`;
      newRow.onclick = () => {
        row.click();
        closeSearchModal();
      };
      tableBody.appendChild(newRow);
    }
  });
}

// ========== Nhập CSV ==========
export function importCSV(event) {
  const file = event.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = async () => {
    const lines = reader.result.split("\n").filter(Boolean);
    const headers = lines[0].split(",").map((h) => h.trim());

    const promises = lines.slice(1).map((line) => {
      const cols = line.split(",");
      const data = {};
      headers.forEach((h, i) => (data[h] = cols[i]?.trim() || ""));
      return fetch("insert_question.php", {
        method: "POST",
        body: new URLSearchParams(data),
      });
    });

    await Promise.all(promises);
    alert("Đã nhập từ CSV");
    refreshIframe();
  };
  reader.readAsText(file);
}

// ========== Xem trước từng phần ==========
["question", "answer1", "answer2", "answer3", "answer4"].forEach((id) => {
  document.getElementById(id).addEventListener("input", () => {
    document.getElementById("preview_" + id).textContent = getValue(id);
    if (window.MathJax) MathJax.typesetPromise();
  });
});

// ========== Toggle xem trước ==========
document.getElementById("togglePreview").addEventListener("change", (e) => {
  document.getElementById("previewBox").style.display = e.target.checked ? "block" : "none";
});

// ========== Dark Mode ==========
document.getElementById("toggleDarkMode").addEventListener("change", function () {
  document.body.classList.toggle("dark-mode", this.checked);
});

// ========== Cảnh báo rời trang ==========
let formChanged = false;
getForm().addEventListener("input", () => (formChanged = true));
window.addEventListener("beforeunload", (e) => {
  if (formChanged) {
    e.preventDefault();
    e.returnValue = "";
  }
});

// ========== Nhận dữ liệu từ iframe ==========
window.addEventListener("message", (event) => {
  if (event.data?.type === "fillForm") {
    const d = event.data.data;
    const fields = ["question_id", "topic", "question", "answer1", "answer2", "answer3", "answer4", "correct_answer", "image_url"];
    fields.forEach((f) => (document.getElementById(f).value = d[f] || ""));

    setPreviewImage(d.image);
    previewFull();
    formChanged = false;
  }
});

// ========== Gửi file Excel ==========
document.getElementById("xlsxUploadForm").addEventListener("submit", async function (e) {
  e.preventDefault();
  const formData = new FormData(this);
  try {
    const res = await fetch("get_question.php", { method: "POST", body: formData });
    const data = await res.json();
    alert(`✅ Đã thêm: ${data.inserted}, Bỏ qua (trùng): ${data.skipped}`);
    this.reset();
    bootstrap.Modal.getInstance(document.getElementById("xlsxModal")).hide();
    refreshIframe();
  } catch {
    alert("❌ Lỗi khi tải lên Excel");
  }
});
