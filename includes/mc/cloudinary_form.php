<?php
// Đảm bảo file này được gọi từ trình duyệt
?>
<div style="max-width: 300px; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
    <h3>📤 Upload Ảnh</h3>

    <!-- Input chọn file -->
    <input type="file" id="uploadImage" accept="image/*">
    <br><br>

    <!-- Khung preview -->
    <div id="previewContainer" style="display:none; text-align:center;">
        <img id="preview" src="" 
             style="max-width:100%; max-height:200px; border:1px solid #ddd; padding:4px; border-radius:4px;">
        <br><br>
    </div>

    <!-- Nút thao tác -->
    <button id="btnUpload" style="margin-right:5px;">📤 Upload</button>
    <button id="btnDelete" style="display:none;">🗑 Xóa ảnh</button>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let currentPublicId = '';

/* ==== Xem trước ảnh khi chọn ==== */
$('#uploadImage').on('change', function() {
    let file = this.files[0];
    if (file) {
        let reader = new FileReader();
        reader.onload = function(e) {
            $('#preview').attr('src', e.target.result);
            $('#previewContainer').show();
        };
        reader.readAsDataURL(file);
    } else {
        $('#previewContainer').hide();
        $('#preview').attr('src', '');
    }
});

/* ==== Upload Ảnh ==== */
$('#btnUpload').on('click', function () {
    let file_data = $('#uploadImage').prop('files')[0];
    if (!file_data) {
        alert('❌ Vui lòng chọn ảnh!');
        return;
    }

    let form_data = new FormData();
    form_data.append('image', file_data);

    $.ajax({
        url: '../../includes/mc/cloudinary_action.php',
        type: 'POST',
        data: form_data,
        processData: false,
        contentType: false,
        success: function (res) {
            try {
                let data = typeof res === 'string' ? JSON.parse(res) : res;
                if (data.secure_url) {
                    $('#preview').attr('src', data.secure_url).show();
                    $('#btnDelete').show();
                    currentPublicId = data.public_id;
                    alert('✅ Upload thành công!');
                } else {
                    alert(data.error || '❌ Lỗi không xác định khi upload');
                }
            } catch (e) {
                alert('❌ Lỗi xử lý phản hồi từ server');
            }
        },
        error: function () {
            alert('❌ Không thể kết nối server');
        }
    });
});

/* ==== Xóa Ảnh ==== */
$('#btnDelete').on('click', function () {
    if (!currentPublicId) {
        alert('❌ Chưa có ảnh để xóa');
        return;
    }

    $.ajax({
        url: '../../includes/mc/cloudinary_action.php',
        type: 'POST',
        data: { public_id: currentPublicId },
        success: function (res) {
            try {
                let data = typeof res === 'string' ? JSON.parse(res) : res;
                if (data.result === 'ok') {
                    $('#previewContainer').hide();
                    $('#preview').attr('src', '');
                    $('#btnDelete').hide();
                    $('#uploadImage').val('');
                    currentPublicId = '';
                    alert('✅ Ảnh đã được xóa');
                } else {
                    alert(data.error || '❌ Lỗi khi xóa ảnh');
                }
            } catch (e) {
                alert('❌ Lỗi xử lý phản hồi từ server');
            }
        },
        error: function () {
            alert('❌ Không thể kết nối server');
        }
    });
});
</script>
