document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('mcForm');

  // Nút Lưu
  document.getElementById('mc_save').addEventListener('click', function () {
    if (!form.reportValidity()) return;

    const formData = new FormData(form);

    fetch('../../includes/save_question.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert('Lưu câu hỏi thành công!');
        if (!form.mc_id?.value) form.reset();
      } else {
        alert('Lỗi khi lưu: ' + (data.message || 'Không xác định'));
      }
    })
    .catch(err => {
      alert('Lỗi hệ thống: ' + err);
    });
  });

  // Nút Xoá
  document.getElementById('mc_delete').addEventListener('click', function () {
    const id = document.getElementById('mc_id')?.value;
    if (!id || !confirm('Bạn có chắc muốn xóa câu hỏi này?')) return;

    fetch('../../includes/delete_question.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ mc_id: id })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert('Đã xóa thành công!');
        window.location.href = 'mc_list.php';
      } else {
        alert('Không thể xóa: ' + (data.message || 'Lỗi'));
      }
    })
    .catch(err => alert('Lỗi hệ thống: ' + err));
  });

  // Nút Làm lại
  document.getElementById('mc_reset').addEventListener('click', function () {
    if (confirm('Bạn có chắc muốn làm lại toàn bộ?')) {
      form.reset();

      // Reset xem trước nếu có
      document.querySelectorAll('.preview-box').forEach(box => box.style.display = 'none');
    }
  });

  // Nút Ẩn/Hiện danh sách
  document.getElementById("mc_view_list").addEventListener("click", function () {
  const tableWrapper = document.getElementById("mcTableWrapper");
  if (tableWrapper.style.display === "none" || tableWrapper.style.display === "") {
    tableWrapper.style.display = "block";
    this.textContent = "Ẩn danh sách";
  } else {
    tableWrapper.style.display = "none";
    this.textContent = "Hiện danh sách";
  }
});


  // Nút Làm đề
  document.getElementById('mc_preview_exam').addEventListener('click', function () {
    window.open('mc_exam_preview.php', '_blank');
  });
});

// Nút xử lý xem bảng câu hỏi
document.addEventListener("DOMContentLoaded", function () {
  const btnViewList = document.getElementById("mc_view_list");
  const tableWrapper = document.getElementById("mcTableWrapper");

  if (btnViewList && tableWrapper) {
    btnViewList.addEventListener("click", function () {
      if (tableWrapper.style.display === "none" || tableWrapper.style.display === "") {
        tableWrapper.style.display = "block";
        btnViewList.textContent = "Ẩn danh sách";
      } else {
        tableWrapper.style.display = "none";
        btnViewList.textContent = "Hiện danh sách";
      }
    });
  }
});


