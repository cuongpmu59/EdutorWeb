document.addEventListener('DOMContentLoaded', () => {
  const fileInput = document.getElementById('mc_image');
  const previewImage = document.getElementById('mc_image_preview');
  const imagePreviewContainer = document.querySelector('.mc-image-preview');
  const existingInput = document.getElementById('existing_image');
  const publicIdInput = document.getElementById('public_id');
  const removeBtn = document.getElementById('mc_remove_image');

  // Tạo label hiển thị tên file nếu chưa có
  let filenameLabel = document.getElementById('image_filename');
  if (!filenameLabel) {
    filenameLabel = document.createElement('span');
    filenameLabel.id = 'image_filename';
    filenameLabel.className = 'image-filename';
    imagePreviewContainer.appendChild(filenameLabel);
  }

  // ===== Xem trước và upload ảnh lên server =====
  fileInput.addEventListener('change', () => {
    const file = fileInput.files[0];
    if (!file) return;

    // Hiển thị tên file
    filenameLabel.textContent = file.name;

    const formData = new FormData();
    formData.append('image', file);

    const mcId = document.getElementById('mc_id')?.value;
    if (mcId) formData.append('mc_id', mcId);

    const oldUrl = existingInput.value;
    const oldId = publicIdInput.value;

    if (oldUrl) formData.append('existing_image', oldUrl);
    if (oldId) formData.append('public_id', oldId);

    fetch('includes/mc_image.php', {
      method: 'POST',
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          previewImage.src = data.url;
          previewImage.style.display = 'block';
          filenameLabel.textContent = file.name;
          existingInput.value = data.url;
          publicIdInput.value = data.public_id;
        } else {
          alert('❌ Upload ảnh thất bại!');
          previewImage.style.display = 'none';
          filenameLabel.textContent = '';
        }
      })
      .catch(() => {
        alert('❌ Lỗi khi upload ảnh!');
        previewImage.style.display = 'none';
        filenameLabel.textContent = '';
      });
  });

  // ===== Xoá ảnh khỏi preview và input =====
  removeBtn?.addEventListener('click', () => {
    previewImage.src = '';
    previewImage.style.display = 'none';
    fileInput.value = '';
    existingInput.value = '';
    publicIdInput.value = '';
    filenameLabel.textContent = '';
  });
});
