$(document).ready(function () {
  const fileInput = $('#mc_image');
  const previewImage = $('#mc_preview_image');

  // Khi chọn ảnh từ ổ đĩa
  fileInput.on('change', function () {
    const file = this.files[0];

    if (file) {
      const reader = new FileReader();

      reader.onload = function (e) {
        previewImage.attr('src', e.target.result).show(); // Gán ảnh và hiển thị
      };

      reader.readAsDataURL(file);
    }
  });

  // Khi bấm nút xoá ảnh
  $('#mc_clear_image').on('click', function () {
    if (previewImage.attr('src')) {
      previewImage.attr('src', '').hide(); // Ẩn ảnh và xoá link
      fileInput.val(''); // Reset input file
    } else {
      console.log('❌ Không có ảnh để xoá.');
    }
  });
});
