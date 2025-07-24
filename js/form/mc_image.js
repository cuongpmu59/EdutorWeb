document.addEventListener('DOMContentLoaded', function () {
  const fileInput = document.getElementById('mc_image');
  const imagePreviewContainer = document.querySelector('.mc-image-preview');
  const removeBtn = document.getElementById('mc_remove_image');

  // Xem trước ảnh khi chọn ảnh mới
  fileInput.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
      imagePreviewContainer.innerHTML = `<img src="${e.target.result}" alt="Ảnh xem trước">`;
    };
    reader.readAsDataURL(file);
  });

  // Xoá ảnh đang hiển thị
  removeBtn.addEventListener('click', function () {
    // Xoá ảnh trong preview
    imagePreviewContainer.innerHTML = '';

    // Xoá file input nếu có file mới đang chọn
    fileInput.value = '';

    // Xoá input hidden nếu đang sửa (có ảnh cũ)
    const hiddenInput = document.querySelector('input[name="existing_image"]');
    if (hiddenInput) {
      hiddenInput.remove();
    }
  });
});
