function getFormData() {
  const form = document.getElementById("questionForm");
  const formData = new FormData(form);
  return formData;
}

function saveQuestion() {
  const id = document.getElementById("question_id").value.trim();
  const form = document.getElementById("questionForm");
  const formData = new FormData(form);

  const question = formData.get("question")?.trim();
  const answer1 = formData.get("answer1")?.trim();
  const answer2 = formData.get("answer2")?.trim();
  const answer3 = formData.get("answer3")?.trim();
  const answer4 = formData.get("answer4")?.trim();
  const correctAnswer = formData.get("correct_answer")?.trim();

  if (!question || !answer1 || !answer2 || !answer3 || !answer4 || !correctAnswer) {
    alert("Vui lòng điền đầy đủ thông tin câu hỏi và đáp án.");
    return;
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

  // ✅ Nếu là thêm mới (không có ID), kiểm tra trùng lặp
  if (!id) {
    fetch("check_duplicate.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "question=" + encodeURIComponent(question)
    })
    .then(res => res.json())
    .then(data => {
      if (data.exists) {
        alert("Câu hỏi này đã tồn tại trong hệ thống.");
        return;
      } else {
        submitQuestion(formData, form, id);
      }
    })
    .catch(err => {
      console.error("Lỗi kiểm tra trùng lặp:", err);
      alert("Không thể kiểm tra trùng lặp.");
    });
  } else {
    // ✅ Nếu là cập nhật, bỏ qua kiểm tra trùng
    submitQuestion(formData, form, id);
  }
}

function submitQuestion(formData, form, id) {
  const url = id ? "update_question.php" : "insert_question.php";

  fetch(url, {
    method: "POST",
    body: formData
  })
  .then(res => res.text())
  .then(response => {
    alert(response);
    refreshIframe();
    if (!id) form.reset();
    resetPreview();
  })
  .catch(error => {
    console.error("Lỗi:", error);
    alert("Đã xảy ra lỗi khi lưu câu hỏi.");
  });
}

function deleteQuestion() {
  const id = document.getElementById("question_id").value.trim();
  if (!id) {
    alert("Vui lòng chọn câu hỏi cần xoá.");
    return;
  }

  if (!confirm("Bạn có chắc muốn xoá câu hỏi này?")) return;

  fetch("delete_question.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "id=" + encodeURIComponent(id)
  })
  .then(res => res.text())
  .then(response => {
    alert(response);
    document.getElementById("questionForm").reset();
    const imgPreview = document.getElementById("imagePreview");
    if(imgPreview) imgPreview.style.display = "none";
    refreshIframe();
  })
  .catch(error => {
    console.error("Lỗi:", error);
    alert("Không thể xoá câu hỏi.");
  });
}

function resetPreview() {
  const imgPreview = document.getElementById("imagePreview");
  imgPreview.src = "";
  imgPreview.style.display = "none";
}

function togglePreview() {
  const isChecked = document.getElementById("togglePreview").checked;
  const previews = document.querySelectorAll(".latex-preview");
  previews.forEach(div => {
    div.style.display = isChecked ? "block" : "none";
  });
}

function updateFullPreview() {
  const question = document.getElementById("question").value;
  const a = document.getElementById("answer1").value;
  const b = document.getElementById("answer2").value;
  const c = document.getElementById("answer3").value;
  const d = document.getElementById("answer4").value;
  const correct = document.getElementById("correct_answer").value;

  const html = `
    <p><strong>Câu hỏi:</strong> ${question}</p>
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
  if (window.MathJax) {
    MathJax.typesetPromise([preview]);
  }
}

function renderPreview(fieldId) {
  const value = document.getElementById(fieldId).value;
  const previewDiv = document.getElementById("preview_" + fieldId);
  previewDiv.innerHTML = value;

  if (window.MathJax) {
    MathJax.typesetPromise([previewDiv]);
  }
    // Cập nhật xem trước toàn bộ
  updateFullPreview();
}

function searchQuestion() {
  const keyword = prompt("Nhập từ khóa cần tìm:");
  if (!keyword) return;

  fetch("search_question.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "keyword=" + encodeURIComponent(keyword)
  })
  .then(res => res.json())
  .then(data => {
    if (data.length === 0) {
      alert("Không tìm thấy câu hỏi nào.");
    } else {
      alert("Tìm thấy " + data.length + " câu hỏi.");
      // Bạn có thể hiển thị dữ liệu trong modal hoặc bảng riêng tùy ý
      console.log(data);
    }
  })
  .catch(error => {
    console.error("Lỗi:", error);
    alert("Tìm kiếm thất bại.");
  });
}

function refreshIframe() {
  const iframe = document.getElementById("questionIframe");
  if (iframe) {
    iframe.contentWindow.location.reload();

    // Sau khi reload xong, gọi MathJax render lại (cần bên iframe có MathJax được tải)
    iframe.onload = function () {
      if (iframe.contentWindow.MathJax) {
        iframe.contentWindow.MathJax.typesetPromise();
      }
    };
  }
}

// Hàm tự động gọi MathJax render trong trang chính (nếu bạn dùng bảng không qua iframe)
function renderMathInPage() {
  if (window.MathJax) {
    MathJax.typesetPromise();
  }
}

// Nếu bạn có bảng trực tiếp trong trang chính, gọi renderMathInPage() sau khi tải dữ liệu bảng.

// Ví dụ gọi refreshIframe() hoặc renderMathInPage() sau khi cập nhật xong để hiển thị công thức LaTeX.

document.getElementById("image").addEventListener("change", function() {
  const file = this.files[0];
  if (file) {
    if (!file.type.startsWith("image/")) {
      alert("Chỉ chấp nhận file ảnh!");
      this.value = "";
      return;
    }
    if (file.size > 2 * 1024 * 1024) { // 2MB
      alert("Ảnh quá lớn. Vui lòng chọn ảnh dưới 2MB.");
      this.value = "";
      return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
      const preview = document.getElementById("imagePreview");
      preview.src = e.target.result;
      preview.style.display = "block";
    };
    reader.readAsDataURL(file);
  }
});



