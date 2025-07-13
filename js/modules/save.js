document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('mcForm');
  const imageInput = document.getElementById('mc_image');
  const imagePreview = document.getElementById('mc_imagePreview');

  // ===== XỬ LÝ GỬI FORM =====
  form.addEventListener('submit', async function (e) {
    e.preventDefault();

    const formData = new FormData(form);
    const id = formData.get('mc_id');
    const isUpdate = id && id.trim() !== '';
    const url = isUpdate ? 'pages/utils/mc_update.php' : 'pages/utils/mc_insert.php';

    try {
      const response = await fetch(url, {
        method: 'POST',
        body: formData
      });

      const result = await response.json();

      if (result.success) {
        alert(isUpdate ? '✅ Cập nhật thành công!' : '✅ Đã thêm mới!');
        
        // Làm mới iframe bảng danh sách
        const iframe = document.getElementById('mcIframe');
        if (iframe?.contentWindow) iframe.contentWindow.location.reload();

        // Reset form (trừ khi là cập nhật)
        if (!isUpdate) {
          form.reset();
          document.getElementById('mc_id').value = result.id || '';
          if (imagePreview) {
            imagePreview.src = '';
            imagePreview.style.display = 'none';
          }
        }

      } else {
        alert('❌ ' + (result.message || 'Lỗi không xác định'));
      }

    } catch (err) {
      alert('❌ Lỗi kết nối: ' + err.message);
    }
  });

  // ===== XEM TRƯỚC ẢNH MINH HỌA =====
  imageInput.addEventListener('change', function () {
    const file = this.files?.[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = e => {
        imagePreview.src = e.target.result;
        imagePreview.style.display = 'block';
      };
      reader.readAsDataURL(file);
    } else {
      imagePreview.src = '';
      imagePreview.style.display = 'none';
    }
  });
});
