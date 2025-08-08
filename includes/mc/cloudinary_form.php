<?php // form_upload.php ?>
<div style="max-width: 300px; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
    <h3>📤 Upload Ảnh</h3>

    <input type="file" id="uploadImage" accept="image/*">
    <br><br>

    <div id="previewContainer" style="display:none; text-align:center;">
        <img id="preview" src="" style="max-width:100%; max-height:200px; border:1px solid #ddd; padding:4px; border-radius:4px;">
        <br><br>
    </div>

    <button id="btnUpload" style="margin-right:5px;">📤 Upload</button>
    <button id="btnDelete" style="display:none;">🗑 Xóa ảnh</button>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let currentPublicId = '';
const apiUrl = '../../includes/mc/cloudinary_action.php';

/* Xem trước ảnh */
$('#uploadImage').on('change', function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#preview').attr('src', e.target.result);
            $('#previewContainer').show();
            currentPublicId = ''; // reset khi chọn ảnh mới
            $('#btnDelete').hide();
        };
        reader.readAsDataURL(file);
    } else {
        resetPreview();
    }
});

/* Upload ảnh */
$('#btnUpload').on('click', function () {
    const file = $('#uploadImage').prop('files')[0];
    if (!file) return alert('❌ Vui lòng chọn ảnh!');

    const formData = new FormData();
    formData.append('image', file);

    toggleButton('#btnUpload', true);

    $.ajax({
        url: apiUrl,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false
    }).done(function (res) {
        console.log('Server response:', res);
        try {
            const data = JSON.parse(res);
            if (data.secure_url) {
                $('#preview').attr('src', data.secure_url);
                $('#btnDelete').show();
                currentPublicId = data.public_id;
                alert('✅ Upload thành công!');
            } else {
                alert(data.error || '❌ Lỗi không xác định khi upload');
            }
        } catch (err) {
            console.error(err);
            alert('❌ Phản hồi không hợp lệ từ server');
        }
    }).fail(function () {
        alert('❌ Không thể kết nối server');
    }).always(function () {
        toggleButton('#btnUpload', false);
    });
});

/* Xóa ảnh */
$('#btnDelete').on('click', function () {
    if (!currentPublicId) return alert('❌ Chưa có ảnh để xóa');

    $.post(apiUrl, { public_id: currentPublicId })
    .done(function (res) {
        console.log('Server response:', res);
        try {
            const data = JSON.parse(res);
            if (data.result === 'ok') {
                resetPreview();
                alert('✅ Ảnh đã được xóa');
            } else {
                alert(data.error || '❌ Lỗi khi xóa ảnh');
            }
        } catch (err) {
            console.error(err);
            alert('❌ Phản hồi không hợp lệ từ server');
        }
    }).fail(function () {
        alert('❌ Không thể kết nối server');
    });
});

/* Hàm reset preview */
function resetPreview() {
    $('#previewContainer').hide();
    $('#preview').attr('src', '');
    $('#uploadImage').val('');
    $('#btnDelete').hide();
    currentPublicId = '';
}

/* Khóa/mở nút */
function toggleButton(selector, disabled) {
    $(selector).prop('disabled', disabled).text(disabled ? '⏳ Đang xử lý...' : '📤 Upload');
}
</script>
