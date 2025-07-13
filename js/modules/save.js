document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('mcForm');
  
    if (!form) return;
  
    form.addEventListener('submit', function (e) {
      e.preventDefault(); // Ngăn form gửi mặc định
  
      const formData = new FormData(form);
      const mc_id = formData.get('mc_id').trim();
  
      // Gửi tới file PHP thích hợp
      const endpoint = mc_id ? 'mc_update.php' : 'mc_insert.php';
  
      fetch(endpoint, {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert(mc_id ? '✅ Cập nhật câu hỏi thành công!' : '✅ Đã thêm câu hỏi mới!');
            form.reset();
  
            // Ẩn ảnh xem trước nếu có
            const img = document.getElementById('mc_imagePreview');
            if (img) {
              img.src = '';
              img.style.display = 'none';
            }
  
            // Làm mới bảng trong iframe
            const iframe = document.getElementById('mcIframe');
            if (iframe?.contentWindow) {
              iframe.contentWindow.location.reload();
            }
  
          } else {
            alert('❌ Lỗi: ' + (data.message || 'Không thể lưu câu hỏi.'));
          }
        })
        .catch(error => {
          alert('❌ Lỗi khi gửi yêu cầu đến máy chủ.');
          console.error(error);
        });
    });
  });
  