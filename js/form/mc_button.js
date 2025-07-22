document.addEventListener("DOMContentLoaded", function () {
  const btnViewList = document.getElementById("mc_view_list");
  const tableWrapper = document.getElementById("mcTableWrapper");
  const btnReset = document.getElementById("mc_reset");
  const form = document.getElementById("mcForm");
  const imagePreview = document.querySelector(".mc-image-preview img");
  const previewFull = document.getElementById("mcPreviewContent");
  const btnSave = document.getElementById("mc_save");
  const btnDelete = document.getElementById("mc_delete");

  // 1. Ẩn/hiện bảng danh sách
  if (btnViewList && tableWrapper) {
    btnViewList.addEventListener("click", function () {
      const isHidden = getComputedStyle(tableWrapper).display === "none";
      tableWrapper.style.display = isHidden ? "block" : "none";
      btnViewList.textContent = isHidden ? "Ẩn danh sách" : "Hiện danh sách";
    });
  }

  // 2. Làm lại form
  if (btnReset && form) {
    btnReset.addEventListener("click", function () {
      form.reset();

      // Xoá ảnh minh hoạ nếu có
      if (imagePreview) {
        imagePreview.src = "";
      }

      // Xoá xem trước toàn bộ nếu có
      if (previewFull) {
        previewFull.innerHTML = "";
      }

      // Ẩn xem trước toàn bộ
      const previewZone = document.getElementById("mcPreview");
      if (previewZone) {
        previewZone.style.display = "none";
      }

      // Focus vào input đầu tiên
      const firstInput = form.querySelector("input, textarea, select");
      if (firstInput) firstInput.focus();
    });
  }

  // 3. Lưu câu hỏi
  if (btnSave && form) {
    btnSave.addEventListener("click", function () {
      // Bạn có thể thêm kiểm tra hợp lệ ở đây nếu cần
      form.submit();
    });
  }

  // 4. Xoá câu hỏi
  if (btnDelete) {
    btnDelete.addEventListener("click", function () {
      const mcIdField = document.getElementById("mc_id");
      if (!mcIdField || !mcIdField.value) {
        alert("Không có câu hỏi nào để xoá.");
        return;
      }

      if (confirm("Bạn có chắc muốn xoá câu hỏi này không?")) {
        const formData = new FormData();
        formData.append("delete_mc_id", mcIdField.value);

        fetch("mc_delete.php", {
          method: "POST",
          body: formData
        })
        .then(response => response.text())
        .then(result => {
          alert("Câu hỏi đã được xoá.");
          window.location.href = "mc_form.php";
        })
        .catch(error => {
          console.error("Lỗi khi xoá:", error);
          alert("Đã xảy ra lỗi khi xoá.");
        });
      }
    });
  }
});
