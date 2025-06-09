function getFormData() {
    const form = document.getElementById("questionForm");
    const formData = new FormData(form);
    return formData;
  }
  
  function saveQuestion() {
    const id = document.getElementById("question_id").value.trim();
    const form = document.getElementById("questionForm");
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
        const imagePreview = document.getElementById("imagePreview");
        if(imagePreview) imagePreview.style.display = "none";
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
            // Có thể mở một modal hoặc hiện trong bảng khác tùy nhu cầu
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
    }
  }
  
  // Hàm toggle ẩn/hiện bảng câu hỏi
  function toggleQuestionTable() {
    const iframe = document.getElementById("questionIframe");
    if (!iframe) return;
  
    if (iframe.style.display === "none" || iframe.style.display === "") {
      iframe.style.display = "block";
    } else {
      iframe.style.display = "none";
    }
  }
  