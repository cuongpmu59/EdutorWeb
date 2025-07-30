$(document).ready(function () {
  // Khi bấm nút tải ảnh
  $('#mc_upload_btn').on('click', function () {
    const fileInput = $('#mc_image_input')[0];
    const previewDiv = $('#mc_preview_image');

    if (fileInput.files && fileInput.files[0]) {
      const reader = new FileReader();

      // Xoá ảnh cũ nếu có
      previewDiv.empty();

      // Tải ảnh mới lên
      reader.onload = function (e) {
        const img = $('<img>', {
          src: e.target.result,
          alt: 'Ảnh đã chọn',
          class: 'img-fluid',
          style: 'max-height: 200px;' // tuỳ chỉnh nếu cần
        });
        previewDiv.append(img);
      };

      reader.readAsDataURL(fileInput.files[0]);
    } else {
      alert('📌 Vui lòng chọn một ảnh trước khi tải lên.');
    }
  });

  // Khi bấm nút xoá ảnh
  $('#mc_clear_btn').on('click', function () {
    const previewDiv = $('#mc_preview_image');

    if (previewDiv.children('img').length > 0) {
      previewDiv.empty(); // Xoá ảnh đang hiển thị
    } else {
      console.log('❌ Không có ảnh để xoá.');
      // Nếu muốn trả về null (ví dụ cập nhật 1 biến), bạn có thể gán:
      // imageData = null;
    }
  });
});
