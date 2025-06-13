function getFormData() {
  const form = document.getElementById("questionForm");
  const formData = new FormData(form);
  return formData;
}

function saveQuestion() {
  const id = document.getElementById("question_id").value.trim();
  const formData = getFormData();
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

