document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('mcForm');
  if (!form) {
    console.warn("Không tìm thấy form #mcForm");
    return;
  }

  // === Nút Lưu ===
  const btnSave = document.getElementById('mc_save');
  if (btnSave) {
    btnSave.addEventListener('click', function () {
      if (!form.reportValidity()) return;

      const formData = new FormData(form);

      fetch('../../includes/mc_save.php', {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert('Lưu câu hỏi thành công!');

            // Nếu là thêm mới (mc_id rỗng), thì reset form
            if (!form.mc_id?.value) {
              form.reset();

              const imagePreview = document.querySelector('.mc-image-preview');
              if (imagePreview) imagePreview.innerHTML = '';

              const existing = form.querySelector('input[name="existing_image"]');
              if (existing) existing.remove();

              document.querySelectorAll('.preview-box').forEach(box => box.style.display = 'none');
            }

            // Gửi thông báo reload bảng
            const iframe = document.getElementById("mcTableFrame");
            if (iframe?.contentWindow) {
              iframe.contentWindow.postMessage({ type: 'mc_reload' }, '*');
            }
          } else {
            alert('Lỗi khi lưu: ' + (data.message || 'Không xác định'));
          }
        })
        .catch(err => {
          alert('Lỗi hệ thống: ' + err);
        });
    });
  }

  // === Nút Xoá ===
  const btnDelete = document.getElementById('mc_delete');
  if (btnDelete) {
    btnDelete.addEventListener('click', function () {
      const id = document.getElementById('mc_id')?.value;
      if (!id || !confirm('Bạn có chắc muốn xóa câu hỏi này?')) return;

      fetch('../../includes/mc_delete.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ mc_id: id })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert('Đã xóa thành công!');
            window.location.href = 'mc_form.php';
          } else {
            alert('Không thể xóa: ' + (data.message || 'Lỗi'));
          }
        })
        .catch(err => alert('Lỗi hệ thống: ' + err));
    });
  }

  // === Nút Làm lại ===
  const btnReset = document.getElementById('mc_reset');
  if (btnReset) {
    btnReset.addEventListener('click', function () {
      if (confirm('Bạn có chắc muốn làm lại toàn bộ?')) {
        form.reset();

        const imagePreview = document.querySelector('.mc-image-preview');
        if (imagePreview) imagePreview.innerHTML = '';

        const existing = form.querySelector('input[name="existing_image"]');
        if (existing) existing.remove();

        document.querySelectorAll('.preview-box').forEach(box => box.style.display = 'none');
      }
    });
  }

  // === Nút Ẩn/Hiện danh sách ===
  const btnViewList = document.getElementById("mc_view_list");
  const tableWrapper = document.getElementById("mcTableFrame");

  if (btnViewList && tableWrapper) {
    btnViewList.addEventListener("click", function () {
      const isHidden = tableWrapper.style.display === "none" || getComputedStyle(tableWrapper).display === "none";
      tableWrapper.style.display = isHidden ? "block" : "none";
      this.textContent = isHidden ? "Ẩn danh sách" : "Hiện danh sách";
    });
  } else {
    console.warn("Không tìm thấy nút hoặc iframe bảng (mc_view_list hoặc mcTableFrame)");
  }

  // === Nút Làm đề ===
  const btnExam = document.getElementById('mc_preview_exam');
  if (btnExam) {
    btnExam.addEventListener('click', function () {
      window.open('mc_exam_preview.php', '_blank');
    });
  }
});
