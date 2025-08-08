<input type="file" id="uploadImage">
<button id="btnUpload">📤 Upload</button>
<br><br>
<img id="preview" src="" style="max-width:200px; display:none;">
<br>
<button id="btnDelete" style="display:none;">🗑 Xóa ảnh</button>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let currentPublicId = '';

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
        url: 'cloudinary_action.php',
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
        url: 'cloudinary_action.php',
        type: 'POST',
        data: { public_id: currentPublicId },
        success: function (res) {
            try {
                let data = typeof res === 'string' ? JSON.parse(res) : res;
                if (data.result === 'ok') {
                    $('#preview').hide().attr('src', '');
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
