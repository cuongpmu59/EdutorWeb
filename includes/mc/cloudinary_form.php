<?php // includes/mc/cloudinary_form.php ?>
<div style="max-width: 320px; padding: 15px; border: 1px solid #ccc; border-radius: 6px;">
    <h3>📤 Upload Ảnh lên Cloudinary</h3>

    <input type="file" id="uploadImage" accept="image/*" />
    <br><br>

    <div id="previewContainer" style="display:none; text-align:center;">
        <img id="preview" src="" alt="Preview" style="max-width:100%; max-height:200px; border:1px solid #ddd; padding:6px; border-radius:6px;">
        <br><br>
    </div>

    <button id="btnUpload" style="margin-right:8px;">📤 Upload</button>
    <button id="btnDelete" style="display:none;">🗑 Xóa ảnh</button>

    <div id="statusMsg" style="margin-top:10px; font-size:14px; color:#333;"></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
const apiUrl = '../../includes/mc/cloudinary_image.php';
let currentPublicId = '';

// Reset preview và trạng thái
function resetPreview() {
    $('#previewContainer').hide();
    $('#preview').attr('src', '');
    $('#uploadImage').val('');
    $('#btnDelete').hide();
    $('#statusMsg').html('');
    currentPublicId = '';
}

// Bật/tắt nút và đổi text
function toggleButton(selector, disabled, loadingText = '') {
    $(selector).prop('disabled', disabled);
    if (loadingText) {
        $(selector).text(disabled ? loadingText : $(selector).data('default-text'));
    }
}

// Gán text gốc vào data để khi đổi lại không bị sai
$('#btnUpload').data('default-text', '📤 Upload');
$('#btnDelete').data('default-text', '🗑 Xóa ảnh');

// Preview ảnh khi chọn file
$('#uploadImage').on('change', function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#preview').attr('src', e.target.result);
            $('#previewContainer').show();
            $('#btnDelete').hide();
            $('#statusMsg').html('');
            currentPublicId = '';
        };
        reader.readAsDataURL(file);
    } else {
        resetPreview();
    }
});

// Upload ảnh
$('#btnUpload').on('click', function () {
    const file = $('#uploadImage')[0].files[0];
    if (!file) {
        $('#statusMsg').css('color', 'red').html('❌ Vui lòng chọn ảnh trước.');
        return;
    }

    toggleButton('#btnUpload', true, '⏳ Đang tải...');
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
                $('#btnDelete').show();
                currentPublicId = res.public_id;
                $('#statusMsg').css('color', 'green').html('✅ Upload thành công!');
            } else {
                $('#statusMsg').css('color', 'red').html('❌ Upload thất bại.');
            }
        },
        error: function() {
            $('#statusMsg').css('color', 'red').html('❌ Lỗi khi upload.');
        },
        complete: function() {
            toggleButton('#btnUpload', false);
        }
    });
});

// Xóa ảnh
$('#btnDelete').on('click', function () {
    if (!currentPublicId) {
        $('#statusMsg').css('color', 'red').html('❌ Không có ảnh để xóa.');
        return;
    }

    if (!confirm('Bạn có chắc muốn xóa ảnh này?')) return;

    toggleButton('#btnDelete', true, '⏳ Đang xóa...');
    $('#statusMsg').css('color', '#333').html('⏳ Đang xóa ảnh...');

    $.ajax({
        url: apiUrl,
        type: 'POST',
        data: { action: 'delete', public_id: currentPublicId },
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
        },
        complete: function() {
            toggleButton('#btnDelete', false);
        }
    });
});
</script>
