<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Upload & Delete Cloudinary (AJAX)</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .result { margin-top: 15px; padding: 10px; border: 1px solid #ccc; background: #f8f8f8; }
        img { max-width: 250px; margin-top: 10px; display: block; }
    </style>
</head>
<body>
    <h1>Upload ảnh lên Cloudinary</h1>
    <form id="uploadForm">
        <input type="file" name="image" required>
        <button type="submit">Upload</button>
    </form>
    <div id="uploadResult" class="result"></div>

    <h1>Xoá ảnh trên Cloudinary</h1>
    <form id="deleteForm">
        <input type="text" name="public_id_delete" id="public_id_delete" placeholder="Nhập Public ID" required>
        <button type="submit">Xoá ảnh</button>
    </form>
    <div id="deleteResult" class="result"></div>

    <script>
        // Xử lý upload
        document.getElementById('uploadForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('cloudinary_action.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                const resultDiv = document.getElementById('uploadResult');
                if (data.secure_url) {
                    resultDiv.innerHTML = `
                        ✅ Upload thành công!<br>
                        URL: <a href="${data.secure_url}" target="_blank">${data.secure_url}</a><br>
                        Public ID: ${data.public_id}
                        <img src="${data.secure_url}" alt="Uploaded Image">
                    `;
                    // 🆕 Auto điền Public ID vào form xoá
                    document.getElementById('public_id_delete').value = data.public_id;
                } else {
                    resultDiv.innerHTML = `❌ Lỗi upload: ${JSON.stringify(data)}`;
                }
            })
            .catch(err => {
                document.getElementById('uploadResult').innerHTML = `❌ Lỗi: ${err}`;
            });
        });

        // Xử lý delete
        document.getElementById('deleteForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('cloudinary_action.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                const resultDiv = document.getElementById('deleteResult');
                if (data.result === "ok") {
                    resultDiv.innerHTML = "✅ Ảnh đã xoá thành công!";
                } else {
                    resultDiv.innerHTML = `❌ Lỗi xoá: ${JSON.stringify(data)}`;
                }
            })
            .catch(err => {
                document.getElementById('deleteResult').innerHTML = `❌ Lỗi: ${err}`;
            });
        });
    </script>
</body>
</html>
