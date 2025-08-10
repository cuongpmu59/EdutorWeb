<?php
// cloudinary_form.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Upload & Delete Cloudinary</title>
<style>
    body { font-family: Arial, sans-serif; }
    .preview { margin-top: 10px; }
    img { max-width: 200px; display: block; margin-bottom: 5px; }
    button { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; }
    .btn-delete { background: #e74c3c; color: white; }
    .btn-upload { background: #3498db; color: white; }
</style>
</head>
<body>

<h2>Upload ảnh lên Cloudinary</h2>
<input type="file" id="fileInput">
<button class="btn-upload" onclick="uploadImage()">Upload</button>

<div id="preview" class="preview"></div>

<script>
function uploadImage() {
    const fileInput = document.getElementById('fileInput');
    if (!fileInput.files.length) {
        alert('Vui lòng chọn file');
        return;
    }
    const formData = new FormData();
    formData.append('action', 'upload');
    formData.append('file', fileInput.files[0]);

    fetch('cloudinary_image.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.secure_url) {
            document.getElementById('preview').innerHTML = `
                <img src="${data.secure_url}" alt="Ảnh đã upload">
                <button class="btn-delete" onclick="deleteImage('${data.secure_url}')">Xoá ảnh</button>
            `;
        } else {
            alert('Upload thất bại: ' + JSON.stringify(data));
        }
    });
}

function deleteImage(imageUrl) {
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('image_url', imageUrl);

    fetch('cloudinary_image.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.result === 'ok') {
            alert('Xoá ảnh thành công');
            document.getElementById('preview').innerHTML = '';
        } else {
            alert('Xoá ảnh thất bại: ' + JSON.stringify(data));
        }
    });
}
</script>
</body>
</html>
