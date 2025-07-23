document.addEventListener('DOMContentLoaded', function () {
  const fileInput = document.getElementById('mc_image');
  const imagePreviewContainer = document.querySelector('.mc-image-preview');
  const removeBtn = document.getElementById('mc_remove_image');

  // Xem trước ảnh khi chọn ảnh mới
  fileInput.addEventListener('change', function () {
    const file = this.files[0];
    if (!file || !file.type.startsWith('image/')) return;

    const reader = new FileReader();
    reader.onload = function (e) {
      const img = document.createElement('img');
      img.src = e.target.result;
      img.alt = 'Ảnh xem trước';
      img.classList.add('fade-in'); // Thêm hiệu ứng
      imagePreviewContainer.innerHTML = '';
      imagePreviewContainer.appendChild(img);
    };
    reader.readAsDataURL(file);
  });

  // Xoá ảnh đang hiển thị
  removeBtn.addEventListener('click', function () {
    imagePreviewContainer.innerHTML = '';
    fileInput.value = '';

    const hiddenInput = document.querySelector('input[name="existing_image"]');
    if (hiddenInput) hiddenInput.remove();
  });
});
