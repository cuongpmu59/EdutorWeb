document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('mcForm');
    const imageInput = document.getElementById('mc_image');
  
    form.addEventListener('submit', async function (e) {
      e.preventDefault();
  
      const formData = new FormData(form);
      const id = formData.get('mc_id');
      const isUpdate = id !== '';
  
      const url = isUpdate ? 'pages/utils/mc_update.php' : 'pages/utils/mc_insert.php';
  
      try {
        const response = await fetch(url, {
          method: 'POST',
          body: formData
        });
  
        const result = await response.json();
  
        if (result.success) {
          alert(isUpdate ? '✅ Cập nhật thành công!' : '✅ Đã thêm mới!');
          // 👉 Làm mới toàn bộ trang
          window.location.reload();
        } else {
          alert('❌ ' + (result.message || 'Lỗi không xác định'));
        }
  
      } catch (err) {
        alert('❌ Lỗi kết nối: ' + err.message);
      }
    });
  
    // Hiển thị ảnh minh hoạ
    imageInput.addEventListener('change', function () {
      const file = this.files?.[0];
      const preview = document.getElementById('mc_imagePreview');
  
      if (file) {
        const reader = new FileReader();
        reader.onload = e => {
          preview.src = e.target.result;
          preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
      } else {
        preview.src = '';
        preview.style.display = 'none';
      }
    });
  });
  