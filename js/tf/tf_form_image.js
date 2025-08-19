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
    $('#tf_image').val('');
    $('#tf_image_url').val('');
    $('#tf_statusMsg').html('');
    updateNoImageText();
}

function compressImage(file, callback) {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = event => {
        const img = new Image();
        img.src = event.target.result;
        img.onload = () => {
            let { width, height } = img;
            if (width > MAX_WIDTH || height > MAX_HEIGHT) {
                const ratio = Math.min(MAX_WIDTH / width, MAX_HEIGHT / height);
                width *= ratio;
                height *= ratio;
            }
            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);
            canvas.toBlob(
                blob => callback(blob),
                'image/jpeg',
                QUALITY
            );
        };
    };
}

// ==== Xử lý sự kiện khi DOM sẵn sàng ====
$(document).ready(function () {
    // Upload ảnh
    $('#tf_image').on('change', function () {
        const file = this.files[0];
        if (!file) return;

        compressImage(file, blob => {
            const formData = new FormData();
            formData.append('image', blob, file.name);

            $.ajax({
                url: apiUrl + '?action=upload',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status === 'success') {
                        $('#tf_preview_image').attr('src', response.url).show();
                        $('#tf_image_url').val(response.url);
                        $('#tf_statusMsg').html('<span style="color:green">Tải ảnh thành công!</span>');
                    } else {
                        $('#tf_statusMsg').html('<span style="color:red">' + response.message + '</span>');
                    }
                    updateNoImageText();
                },
                error: function () {
                    $('#tf_statusMsg').html('<span style="color:red">Lỗi kết nối server!</span>');
                }
            });
        });
    });

    // Xóa ảnh
    $('#tf_clear_image').on('click', function () {
        const url = $('#tf_image_url').val();
        if (!url) {
            resetPreview();
            return;
        }

        $.ajax({
            url: apiUrl + '?action=delete',
            type: 'POST',
            data: { url },
            success: function (response) {
                if (response.status === 'success') {
                    resetPreview();
                    $('#tf_statusMsg').html('<span style="color:green">Đã xóa ảnh!</span>');
                } else {
                    $('#tf_statusMsg').html('<span style="color:red">' + response.message + '</span>');
                }
            },
            error: function () {
                $('#tf_statusMsg').html('<span style="color:red">Lỗi kết nối server!</span>');
            }
        });
    });

    // Lần đầu load thì check trạng thái preview
    updateNoImageText();
});
