// tf_form_image.js

const apiUrl = '../../includes/tf/tf_form_image.php';
const MAX_WIDTH = 1200;
const MAX_HEIGHT = 1200;
const QUALITY = 0.8;

// ==== Hàm hỗ trợ ====
function updateNoImageText() {
  const hasImage = Boolean($('#tf_preview_image').attr('src'));
  $('#tf_noImageText').toggle(!hasImage);
}

function resetPreview() {
  $('#tf_preview_image').attr('src', '').hide();
  $('#tf_image_url').val('');
  updateNoImageText();
}

function resizeImage(file, callback) {
  const reader = new FileReader();
  reader.onload = e => {
    const img = new Image();
    img.onload = () => {
      const canvas = document.createElement('canvas');
      let width = img.width;
      let height = img.height;

      // Giữ tỉ lệ, giới hạn kích thước
      if (width > height && width > MAX_WIDTH) {
        height *= MAX_WIDTH / width;
        width = MAX_WIDTH;
      } else if (height > MAX_HEIGHT) {
        width *= MAX_HEIGHT / height;
        height = MAX_HEIGHT;
      }

      canvas.width = width;
      canvas.height = height;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(img, 0, 0, width, height);

      canvas.toBlob(blob => {
        callback(blob);
      }, 'image/jpeg', QUALITY);
    };
    img.src = e.target.result;
  };
  reader.readAsDataURL(file);
}

function getPublicIdFromUrl(url) {
  try {
    const parts = url.split('/');
    const fileName = parts[parts.length - 1];
    return fileName.split('.')[0];
  } catch (e) {
    return null;
  }
}

// ==== Xử lý sự kiện ====
$(document).ready(function () {
  const fileInput = $('#tf_image');
  const previewImage = $('#tf_preview_image');
  const clearBtn = $('#tf_clear_image');
  const hiddenInput = $('#tf_image_url');
  const statusMsg = $('#tf_statusMsg');

  // Upload ảnh
  fileInput.on('change', function () {
    const file = this.files[0];
    if (!file) return;

    // Kiểm tra loại file
    if (!file.type.match(/^image\//)) {
      statusMsg.css('color', 'red').html('❌ Vui lòng chọn file ảnh hợp lệ.');
      this.value = '';
      return;
    }

    // Preview ảnh tạm
    const previewReader = new FileReader();
    previewReader.onload = e => {
      previewImage.attr('src', e.target.result).show();
      updateNoImageText();
    };
    previewReader.readAsDataURL(file);

    // Hiện status
    statusMsg.css('color', 'blue').html('⏳ Đang nén và tải ảnh...');

    // Nén ảnh rồi upload
    resizeImage(file, blob => {
      const formData = new FormData();
      formData.append('file', blob, file.name);

      $.ajax({
        url: apiUrl,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
          if (response.status === 'success') {
            hiddenInput.val(response.secure_url);
            previewImage.attr('src', response.secure_url).show();
            updateNoImageText();
            statusMsg.css('color', 'green').html('✅ Ảnh đã tải thành công.');
          } else {
            resetPreview();
            statusMsg.css('color', 'red').html('❌ Lỗi: ' + response.message);
          }
        },
        error: function () {
          resetPreview();
          statusMsg.css('color', 'red').html('❌ Không thể tải ảnh.');
        }
      });
    });
  });

  // Xóa ảnh
  clearBtn.on('click', function () {
    if (!confirm('Bạn có chắc muốn xóa ảnh này?')) return;

    const imageUrl = hiddenInput.val();
    if (!imageUrl) {
      resetPreview();
      statusMsg.css('color', 'green').html('🗑️ Ảnh đã được xóa.');
      return;
    }

    const publicId = getPublicIdFromUrl(imageUrl);
    if (!publicId) {
      resetPreview();
      statusMsg.css('color', 'orange').html('⚠️ Không tìm thấy public_id.');
      return;
    }

    $.ajax({
      url: apiUrl,
      type: 'POST',
      data: { delete: true, public_id: publicId },
      success: function (response) {
        resetPreview();
        if (response.status === 'success') {
          statusMsg.css('color', 'green').html('🗑️ Ảnh đã được xóa thành công.');
        } else {
          statusMsg.css('color', 'red').html('❌ Lỗi khi xóa ảnh: ' + response.message);
        }
      },
      error: function () {
        resetPreview();
        statusMsg.css('color', 'red').html('❌ Không thể xóa ảnh.');
      }
    });
  });

  updateNoImageText();
});
