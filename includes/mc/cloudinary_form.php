<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Test Cloudinary Upload & Delete</title>
<style>
    body { font-family: Arial; padding: 20px; }
    input, button { margin: 5px; padding: 5px; }
    img { max-width: 300px; display: block; margin-top: 10px; }
</style>
</head>
<body>

<h2>Upload ảnh lên Cloudinary</h2>
<input type="file" id="imageInput">
<button id="uploadBtn">Upload</button>

<div id="result"></div>

<h2>Xoá ảnh</h2>
<input type="text" id="publicIdInput" placeholder="Nhập public_id để xoá">
<button id="deleteBtn">Delete</button>

<script>
const resultDiv = document.getElementById('result');

// UPLOAD
document.getElementById('uploadBtn').addEventListener('click', () => {
    const fileInput = document.getElementById('imageInput');
    if (!fileInput.files.length) {
        alert("Vui lòng chọn ảnh!");
        return;
    }

    const formData = new FormData();
    formData.append('image', fileInput.files[0]);

    fetch('../../includes/mc/cloudinary_action.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        console.log("Upload response:", data);
        if (data.secure_url) {
            resultDiv.innerHTML = `
                <p>✅ Upload thành công!</p>
                <img src="${data.secure_url}" alt="Uploaded image">
                <p>public_id: <strong>${data.public_id}</strong></p>
            `;
            document.getElementById('publicIdInput').value = data.public_id;
        } else {
            resultDiv.innerHTML = `<p>❌ Lỗi upload</p><pre>${JSON.stringify(data, null, 2)}</pre>`;
        }
    })
    .catch(err => {
        console.error(err);
        resultDiv.innerHTML = "<p>❌ Lỗi kết nối</p>";
    });
});

// DELETE
document.getElementById('deleteBtn').addEventListener('click', () => {
    const publicId = document.getElementById('publicIdInput').value.trim();
    if (!publicId) {
        alert("Vui lòng nhập public_id!");
        return;
    }

    const formData = new FormData();
    formData.append('public_id', publicId);

    fetch('../../includes/mc/cloudinary_action.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        console.log("Delete response:", data);
        if (data.result === 'ok') {
            resultDiv.innerHTML = `<p>✅ Xoá thành công ảnh có public_id: ${publicId}</p>`;
        } else {
            resultDiv.innerHTML = `<p>❌ Lỗi xoá</p><pre>${JSON.stringify(data, null, 2)}</pre>`;
        }
    })
    .catch(err => {
        console.error(err);
        resultDiv.innerHTML = "<p>❌ Lỗi kết nối</p>";
    });
});
</script>

</body>
</html>
