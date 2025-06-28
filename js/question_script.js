// ========== 1. Thêm câu hỏi ==========
export async function addQuestion() {
  const form = document.getElementById("questionForm");
  const formData = new FormData(form);

  if (form.image.files[0]) {
    const url = await uploadImage(form.image.files[0]);
    if (!url) return alert("Tải ảnh thất bại!");
    formData.set("image_url", url);
  }

  fetch("insert_question.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.text())
    .then((msg) => {
      alert(msg);
      form.reset();
      refreshIframe();
    })
    .catch(() => alert("Lỗi khi thêm câu hỏi!"));
}

// ========== 2. Cập nhật câu hỏi ==========
export async function updateQuestion() {
  const form = document.getElementById("questionForm");
  const formData = new FormData(form);

  if (form.image.files[0]) {
    const url = await uploadImage(form.image.files[0]);
    if (!url) return alert("Tải ảnh thất bại!");
    formData.set("image_url", url);
  }

  fetch("update_question.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.text())
    .then((msg) => {
      alert(msg);
      refreshIframe();
    })
    .catch(() => alert("Lỗi khi cập nhật!"));
}

// ========== 3. Xoá câu hỏi ==========
export function deleteQuestion() {
  const id = document.getElementById("question_id").value;
  if (!id || !confirm("Bạn chắc chắn xoá?")) return;

  fetch("delete_question.php", {
    method: "POST",
    body: new URLSearchParams({ id }),
  })
    .then((res) => res.text())
    .then((msg) => {
      alert(msg);
      document.getElementById("questionForm").reset();
      refreshIframe();
    })
    .catch(() => alert("Lỗi khi xoá câu hỏi!"));
}

// ========== 4. Làm mới iframe ==========
function refreshIframe() {
  const iframe = document.getElementById("questionIframe");
  if (iframe) {
    iframe.contentWindow.location.reload();
    iframe.onload = () => {
      if (iframe.contentWindow.MathJax) {
        iframe.contentWindow.MathJax.typesetPromise();
      }
    };
  }
}

// ========== 5. Xem trước toàn bộ ==========
export function previewFull() {
  const q = (id) => document.getElementById(id).value;
  document.getElementById("pv_id").innerText = q("question_id");
  document.getElementById("pv_topic").innerText = q("topic");
  document.getElementById("pv_question").innerText = q("question");
  document.getElementById("pv_a").innerText = q("answer1");
  document.getElementById("pv_b").innerText = q("answer2");
  document.getElementById("pv_c").innerText = q("answer3");
  document.getElementById("pv_d").innerText = q("answer4");
  document.getElementById("pv_correct").innerText = q("correct_answer");

  const img = document.getElementById("pv_image");
  const url = q("image_url");
  if (url) {
    img.src = url;
    img.style.display = "block";
  } else {
    img.style.display = "none";
  }

  if (window.MathJax) MathJax.typesetPromise();
}

// ========== 6. Zoom ảnh ==========
export function zoomImage(img) {
  const modal = document.getElementById("imageModal");
  const modalImg = document.getElementById("modalImage");
  modal.style.display = "block";
  modalImg.src = img.src;
}

// ========== 7. Modal tìm kiếm ==========
export function openSearchModal() {
  document.getElementById("searchModal").style.display = "block";
}
export function closeSearchModal() {
  document.getElementById("searchModal").style.display = "none";
}

// ========== 8. Tìm kiếm câu hỏi ==========
export function searchQuestion() {
  const keyword = document.getElementById("searchKeyword").value.trim().toLowerCase();
  const tableBody = document.querySelector("#searchResultTable tbody");
  const iframe = document.getElementById("questionIframe");
  const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

  const rows = iframeDoc.querySelectorAll("tbody tr");
  tableBody.innerHTML = "";

  rows.forEach((row) => {
    const cols = row.querySelectorAll("td");
    const text = Array.from(cols).map((c) => c.textContent.toLowerCase()).join(" ");
    if (text.includes(keyword)) {
      const newRow = document.createElement("tr");
      newRow.innerHTML = `
        <td>${cols[0].textContent}</td>
        <td>${cols[7].textContent}</td>
        <td>${cols[1].textContent}</td>
      `;
      newRow.onclick = () => {
        row.click();
        closeSearchModal();
      };
      tableBody.appendChild(newRow);
    }
  });
}

// ========== 9. Nhập CSV ==========
export function importCSV(event) {
  const file = event.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = () => {
    const lines = reader.result.split("\n").filter(Boolean);
    const headers = lines[0].split(",");

    for (let i = 1; i < lines.length; i++) {
      const cols = lines[i].split(",");
      const data = {};
      headers.forEach((h, idx) => data[h.trim()] = cols[idx]?.trim() || "");

      fetch("insert_question.php", {
        method: "POST",
        body: new URLSearchParams(data),
      });
    }

    alert("Đã nhập từ CSV");
    refreshIframe();
  };
  reader.readAsText(file);
}

// ========== 10. Upload ảnh lên Cloudinary ==========
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

// ========== 11. Xem trước từng phần ==========
["question", "answer1", "answer2", "answer3", "answer4"].forEach((id) => {
  document.getElementById(id).addEventListener("input", () => {
    document.getElementById("preview_" + id).textContent = document.getElementById(id).value;
    if (window.MathJax) MathJax.typesetPromise();
  });
});

// ========== 12. Toggle xem trước ==========
document.getElementById("togglePreview").addEventListener("change", (e) => {
  document.getElementById("previewBox").style.display = e.target.checked ? "block" : "none";
});

// ========== 13. Dark Mode ==========
document.getElementById("toggleDarkMode").addEventListener("change", function () {
  document.body.classList.toggle("dark-mode", this.checked);
});

// ========== 14. Cảnh báo rời trang khi chưa lưu ==========
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

// ========== 15. Nhận dữ liệu từ iframe và điền vào form ==========
window.addEventListener("message", (event) => {
  if (event.data?.type === "fillForm") {
    const data = event.data.data;
    document.getElementById("question_id").value = data.id || "";
    document.getElementById("topic").value = data.topic || "";
    document.getElementById("question").value = data.question || "";
    document.getElementById("answer1").value = data.answer1 || "";
    document.getElementById("answer2").value = data.answer2 || "";
    document.getElementById("answer3").value = data.answer3 || "";
    document.getElementById("answer4").value = data.answer4 || "";
    document.getElementById("correct_answer").value = data.correct_answer || "";
    document.getElementById("image_url").value = data.image || "";

    const img = document.getElementById("previewImage");
    if (data.image) {
      img.src = data.image;
      img.style.display = "block";
      document.getElementById("deleteImageLabel").style.display = "inline";
    } else {
      img.src = "";
      img.style.display = "none";
      document.getElementById("deleteImageLabel").style.display = "none";
    }

    previewFull();
  }
});

document.getElementById("xlsxUploadForm").addEventListener("submit", function (e) {
  e.preventDefault();
  const formData = new FormData(this);
  fetch("get_question.php", {
    method: "POST",
    body: formData,
  })
    .then(res => res.json())
    .then(data => {
      alert(`✅ Đã thêm: ${data.inserted}, Bỏ qua (trùng): ${data.skipped}`);
      this.reset();
      bootstrap.Modal.getInstance(document.getElementById("xlsxModal")).hide();
      refreshIframe();
    })
    .catch(() => alert("❌ Lỗi khi tải lên Excel"));
});
