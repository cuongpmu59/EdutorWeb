<?php // includes/mc/cloudinary_form.php ?>
<style>
.image-preview {
    width: 100%;
    aspect-ratio: 4/3;
    border: 1px solid #ccc;
    border-radius: 6px;
    background-color: #f8f8f8;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
}
.image-preview img {
    max-width: 100%;
    max-height: 100%;
}
#noImageText {
    color: #888;
    position: absolute;
}
.image-buttons {
    margin-top: 10px;
    display: flex;
    gap: 10px;
}
.btn-upload, .btn-delete {
    flex: 1;
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.btn-upload {
    background-color: #4CAF50;
    color: white;
}
.btn-delete {
    background-color: #f44336;
    color: white;
}
#statusMsg {
    margin-top: 10px;
    font-size: 14px;
    color: #333;
}
</style>

<div style="max-width: 320px; padding: 15px; border: 1px solid #ccc; border-radius: 6px;">
    <h3>📤 Ảnh minh hoạ</h3>
    <div class="image-preview">
        <img id="preview" src="" alt="">
        <span id="noImageText">Chưa có ảnh</span>
    </div>
    <div class="image-buttons">
        <label class="btn-upload">
            Tải ảnh
            <input type="file" id="uploadImage" name="image" accept="image/*" hidden>
        </label>
        <button type="button" class="btn-delete" id="btnDelete">Xóa ảnh</button>
    </div>
    <div id="statusMsg"></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
const apiUrl = '../../includes/mc/cloudinary_image.php';

// Hiển thị hoặc ẩn chữ “Chưa có ảnh”
function updateNoImageText() {
    if ($('#preview').attr('src')) {
        $('#noImageText').hide();
    } else {
        $('#noImageText').show();
    }
}

// Reset preview
function resetPreview() {
    $('#preview').attr('src', '');
    $('#uploadImage').val('');
    $('#statusMsg').html('');
    updateNoImageText();
}

// Lấy public_id từ URL Cloudinary
function getPublicIdFromUrl(url) {
    try {
        let path = new URL(url).pathname; // /<...>/upload/v1234567/folder/file.jpg
        let parts = path.split('/');
        let uploadIndex = parts.indexOf('upload');
        if (uploadIndex === -1) return null;

        // Lấy tất cả sau 'upload' và bỏ version (v123456...)
        let publicPathParts = parts.slice(uploadIndex + 1);
        if (publicPathParts[0].match(/^v[0-9]+$/)) {
            publicPathParts.shift();
        }

        // Bỏ phần đuôi file
        let filename = publicPathParts.pop();
        let publicIdWithoutExt = filename.split('.')[0];

        // Ghép lại folder + public_id
        return [...publicPathParts, publicIdWithoutExt].join('/');
    } catch (e) {
        return null;
    }
}

// Khi chọn file => preview + upload
$('#uploadImage').on('change', function () {
    const file = this.files[0];
    if (!file) return;

    // Preview ảnh tạm thời
    const reader = new FileReader();
    reader.onload = function(e) {
        $('#preview').attr('src', e.target.result);
        updateNoImageText();
    };
    reader.readAsDataURL(file);

    // Gửi upload
    $('#statusMsg').css('color', '#333').html('⏳ Đang upload ảnh...');
    const formData = new FormData();
    formData.append('action', 'upload');
    formData.append('file', file);

    $.ajax({
        url: apiUrl,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
            if (res.secure_url) {
                $('#preview').attr('src', res.secure_url);
                updateNoImageText();
                $('#statusMsg').css('color', 'green').html('✅ Upload thành công!');
            } else {
                $('#statusMsg').css('color', 'red').html('❌ Upload thất bại.');
            }
        },
        error: function() {
            $('#statusMsg').css('color', 'red').html('❌ Lỗi khi upload.');
        }
    });
});

// Nút xóa ảnh
$('#btnDelete').on('click', function () {
    let imgUrl = $('#preview').attr('src');
    if (!imgUrl) {
        $('#statusMsg').css('color', 'red').html('❌ Không có ảnh để xóa.');
        return;
    }

    let public_id = getPublicIdFromUrl(imgUrl);
    if (!public_id) {
        $('#statusMsg').css('color', 'red').html('❌ Không thể lấy public_id.');
        return;
    }

    if (!confirm('Bạn có chắc muốn xóa ảnh này?')) return;

    $('#statusMsg').css('color', '#333').html('⏳ Đang xóa ảnh...');

    $.ajax({
        url: apiUrl,
        type: 'POST',
        data: { action: 'delete', public_id: public_id },
        dataType: 'json',
        success: function(res) {
            if (res.result === 'ok') {
                resetPreview();
                $('#statusMsg').css('color', 'green').html('🗑 Ảnh đã được xóa.');
            } else {
                $('#statusMsg').css('color', 'red').html('❌ Xóa thất bại.');
            }
        },
        error: function() {
            $('#statusMsg').css('color', 'red').html('❌ Lỗi khi xóa.');
        }
    });
});

// Khởi tạo
updateNoImageText();
</script>
