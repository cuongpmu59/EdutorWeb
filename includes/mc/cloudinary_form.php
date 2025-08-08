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
        contentType: false,
        dataType: 'json'   // jQuery tự parse JSON
    })
    .done(function (data) {
        console.log('Server response:', data);
        if (data.secure_url) {
            $('#preview').attr('src', data.secure_url);
            $('#btnDelete').show();
            currentPublicId = data.public_id;
            alert('✅ Upload thành công!');
        } else {
            alert(data.error || '❌ Lỗi không xác định khi upload');
        }
    })
    .fail(function () {
        alert('❌ Không thể kết nối server');
    })
    .always(function () {
        toggleButton('#btnUpload', false);
    });
});

/* Xóa ảnh */
$('#btnDelete').on('click', function () {
    if (!currentPublicId) return alert('❌ Chưa có ảnh để xóa');

    $.ajax({
        url: apiUrl,
        type: 'POST',
        data: { public_id: currentPublicId },
        dataType: 'json'   // jQuery tự parse JSON
    })
    .done(function (data) {
        console.log('Server response:', data);
        if (data.result === 'ok') {
            resetPreview();
            alert('✅ Ảnh đã được xóa');
        } else {
            alert(data.error || '❌ Lỗi khi xóa ảnh');
        }
    })
    .fail(function () {
        alert('❌ Không thể kết nối server');
    });
});
