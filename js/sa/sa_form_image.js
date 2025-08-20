const apiUrl = '../../includes/sa/sa_form_image.php';
const MAX_WIDTH = 1200;
const MAX_HEIGHT = 1200;
const QUALITY = 0.8;

// ==== Hàm hỗ trợ ====
function updateNoImageText() {
    const hasImage = Boolean($('#sa_preview_image').attr('src'));
    $('#noImageText').toggle(!hasImage);
}

function resetPreview() {
    $('#sa_preview_image').attr('src', '').hide();
    $('#sa_image').val('');
    $('#sa_image_url').val(''); // clear hidden input
    $('#statusMsg').html('');
    updateNoImageText();
}

function getPublicIdFromUrl(url) {
    try {
        const path = new URL(url).pathname;
        const parts = path.split('/');
        const uploadIndex = parts.indexOf('upload');
        if (uploadIndex === -1) return null;

        let publicPathParts = parts.slice(uploadIndex + 1);
        if (/^v\d+$/.test(publicPathParts[0])) publicPathParts.shift();

        const filename = publicPathParts.pop();
        const publicIdWithoutExt = filename.substring(0, filename.lastIndexOf('.'));
        return [...publicPathParts, publicIdWithoutExt].join('/');
    } catch {
        return null;
    }
}

// Nén ảnh bằng canvas
function compressImage(file, callback) {
    const reader = new FileReader();
    reader.onload = e => {
        const img = new Image();
        img.onload = () => {
            let { width, height } = img;
            if (width > MAX_WIDTH || height > MAX_HEIGHT) {
                if (width / height > MAX_WIDTH / MAX_HEIGHT) {
                    height *= MAX_WIDTH / width;
                    width = MAX_WIDTH;
                } else {
                    width *= MAX_HEIGHT / height;
                    height = MAX_HEIGHT;
                }
            }
            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);
            canvas.toBlob(blob => callback(blob), 'image/jpeg', QUALITY);
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

// ==== Sự kiện ====
// Upload ảnh
$(document).on('change', '#sa_image', function () {
    const file = this.files[0];
    if (!file) return;

    $('#statusMsg').css('color', '#333').html('⏳ Đang nén ảnh...');
    
    compressImage(file, compressedBlob => {
        if (!compressedBlob) {
            $('#statusMsg').css('color', 'red').html('❌ Lỗi khi nén ảnh.');
            return;
        }

        // Preview tạm từ ảnh nén
        const previewImage = $('#sa_preview_image');
        const previewReader = new FileReader();
        previewReader.onload = e => previewImage.attr('src', e.target.result).show();
        previewReader.readAsDataURL(compressedBlob);

        $('#statusMsg').css('color', '#333').html('⏳ Đang upload ảnh...');

        const formData = new FormData();
        formData.append('action', 'upload');
        formData.append('file', compressedBlob, file.name.replace(/\.[^/.]+$/, '.jpg'));

        $.ajax({
            url: apiUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: res => {
                if (res.secure_url) {
                    // ✅ Lưu URL thật từ Cloudinary vào hidden input
                    $('#sa_image_url').val(res.secure_url);
                    previewImage.attr('src', res.secure_url).show();
                    $('#statusMsg').css('color', 'green').html('✅ Upload thành công!');
                } else {
                    resetPreview();
                    $('#statusMsg').css('color', 'red').html('❌ Upload thất bại.');
                }
                updateNoImageText();
            },
            error: () => {
                resetPreview();
                $('#statusMsg').css('color', 'red').html('❌ Lỗi khi upload.');
            }
        });
    });
});

// Xóa ảnh
$(document).on('click', '#sa_clear_image', function () {
    const imgUrl = $('#sa_preview_image').attr('src');
    if (!imgUrl) {
        $('#statusMsg').css('color', 'red').html('❌ Không có ảnh để xóa.');
        return;
    }

    const public_id = getPublicIdFromUrl(imgUrl);
    if (!public_id) {
        $('#statusMsg').css('color', 'red').html('❌ Không thể lấy public_id.');
        return;
    }

    if (!confirm('Bạn có chắc muốn xóa ảnh này?')) return;

    $('#statusMsg').css('color', '#333').html('⏳ Đang xóa ảnh...');

    $.ajax({
        url: apiUrl,
        type: 'POST',
        data: { action: 'delete', public_id },
        dataType: 'json',
        success: res => {
            if (res.result === 'ok') {
                resetPreview();
                $('#statusMsg').css('color', 'green').html('🗑 Ảnh đã được xóa.');
            } else {
                $('#statusMsg').css('color', 'red').html('❌ Xóa thất bại.');
            }
        },
        error: () => {
            $('#statusMsg').css('color', 'red').html('❌ Lỗi khi xóa.');
        }
    });
});

// Khởi tạo
$(document).ready(updateNoImageText);
