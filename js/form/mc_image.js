document.addEventListener('DOMContentLoaded', () => {
  const fileInput = document.getElementById('mc_image');
  const previewImage = document.getElementById('mc_image_preview');
  const imagePreviewContainer = document.querySelector('.mc-image-preview');
  const existingInput = document.getElementById('existing_image');
  const removeBtn = document.getElementById('mc_remove_image');

  // ===== Tạo label tên file nếu chưa có =====
  let filenameLabel = document.getElementById('image_filename');
  if (!filenameLabel) {
    filenameLabel = document.createElement('span');
    filenameLabel.id = 'image_filename';
    filenameLabel.className = 'image-filename';
    imagePreviewContainer.appendChild(filenameLabel);
  }

  // ===== Upload ảnh và xem trước =====
  fileInput.addEventListener('change', () => {
    const file = fileInput.files[0];
    if (!file) return;

    // Hiển thị tên file
    filenameLabel.textContent = file.name;

    const formData = new FormData();
    formData.append('image', file);

    const mcId = document.getElementById('mc_id')?.value;
    const oldUrl = document.getElementById('existing_image').value;
    const oldPublicId = document.getElementById('public_id')?.value;

    if (mcId) formData.append('mc_id', mcId);
    if (oldUrl) formData.append('existing_image', oldUrl);
    if (oldPublicId) formData.append('public_id', oldPublicId);

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

          // Cập nhật input ẩn
          document.getElementById('existing_image').value = data.url;
          document.getElementById('public_id').value = data.public_id;
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

  // ===== Xoá ảnh khỏi preview và reset input =====
  removeBtn?.addEventListener('click', () => {
    previewImage.src = '';
    previewImage.style.display = 'none';
    fileInput.value = '';
    document.getElementById('existing_image').value = '';
    document.getElementById('public_id').value = '';
    filenameLabel.textContent = '';
  });
});
