const imgInput = document.getElementById('mc_image');
const previewZone = document.querySelector('.mc-image-preview');
const removeBtn = document.getElementById('mc_remove_image');

imgInput.addEventListener('change', () => {
  const file = imgInput.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    previewZone.innerHTML = `<img src="${e.target.result}">`;
  };
  reader.readAsDataURL(file);
});

removeBtn.addEventListener('click', () => {
  previewZone.innerHTML = '';
  imgInput.value = '';
  // xóa trên cloud + database nếu đã lưu
  const mcId = document.getElementById('mc_id')?.value;
  if (mcId) {
    fetch('api/remove_image.php', {
      method: 'POST',
      body: JSON.stringify({ mc_id: mcId }),
      headers: {'Content-Type': 'application/json'}
    }).then(() => alert('Đã xóa ảnh'));
  }
});
