document.addEventListener('DOMContentLoaded', function () {
  const fileInput = document.getElementById('mc_image');
  const imagePreviewContainer = document.querySelector('.mc-image-preview');
  const removeBtn = document.getElementById('mc_remove_image');

  fileInput.addEventListener('change', function () {
    const file = this.files[0];
    if (!file || !file.type.startsWith('image/')) return;

    const reader = new FileReader();
    reader.onload = function (e) {
      const img = document.createElement('img');
      img.src = e.target.result;
      img.alt = 'Ảnh xem trước';
      img.classList.add('fade-in');

      const nameDiv = document.createElement('div');
      nameDiv.className = 'image-name';
      nameDiv.textContent = file.name;

      imagePreviewContainer.innerHTML = ''; // Xoá ảnh cũ
      imagePreviewContainer.appendChild(img);
      imagePreviewContainer.appendChild(nameDiv); // Thêm tên ảnh
    };
    reader.readAsDataURL(file);
  });

  removeBtn.addEventListener('click', function () {
    imagePreviewContainer.innerHTML = '';
    fileInput.value = '';

    const hiddenInput = document.querySelector('input[name="existing_image"]');
    if (hiddenInput) hiddenInput.remove();
  });
});
