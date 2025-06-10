function getFormData() {
  const form = document.getElementById("questionForm");
  return new FormData(form);
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
    if (!id) document.getElementById("questionForm").reset();
    document.getElementById("imagePreview").style.display = "none";
  })
  .catch(error => {
    console.error("Lỗi:", error);
    alert("Đã xảy ra lỗi khi lưu câu hỏi.");
  });
}

function deleteQuestion() {
  const id = document.getElementById("question_id").value.trim();
  if (!id) return alert("Vui lòng chọn câu hỏi cần xoá.");
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
    document.getElementById("imagePreview").style.display = "none";
    refreshIframe();
  })
  .catch(error => {
    console.error("Lỗi:", error);
    alert("Không thể xoá câu hỏi.");
  });
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
      console.log(data); // Có thể hiển thị bảng riêng nếu muốn
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

    iframe.onload = function () {
      if (iframe.contentWindow.MathJax) {
        iframe.contentWindow.MathJax.typesetPromise();
      }
    };
  }
}

function renderMathInPage() {
  if (window.MathJax) {
    MathJax.typesetPromise();
  }
}

// Xem trước ảnh khi chọn file
document.getElementById("image").addEventListener("change", function () {
  const file = this.files[0];
  const preview = document.getElementById("imagePreview");

  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      preview.src = e.target.result;
      preview.style.display = "block";
    };
    reader.readAsDataURL(file);
  } else {
    preview.style.display = "none";
  }
});

// Đồng bộ dữ liệu từ bảng về form chính
window.addEventListener("message", function (event) {
  if (event.data && event.data.type === 'selectQuestion') {
    const q = event.data.data;
    document.getElementById("question_id").value = q.id;
    document.getElementById("question").value = q.question;
    document.getElementById("answer1").value = q.answer1;
    document.getElementById("answer2").value = q.answer2;
    document.getElementById("answer3").value = q.answer3;
    document.getElementById("answer4").value = q.answer4;
    document.getElementById("correct_answer").value = q.correct_answer;

    const imgPreview = document.getElementById("imagePreview");
    if (q.image && q.image.trim() !== "") {
      imgPreview.src = q.image;
      imgPreview.style.display = "block";
    } else {
      imgPreview.style.display = "none";
    }

    if (window.MathJax) MathJax.typesetPromise();
  }
});
