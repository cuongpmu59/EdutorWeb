<?php 
require_once __DIR__ . '/../../env/config.php'; ?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Upload & Xóa Ảnh - Cloudinary</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .preview img { max-width: 200px; margin-top: 10px; display: block; }
        button { margin-top: 10px; padding: 6px 12px; }
    </style>
</head>
<body>
    <h2>📤 Upload ảnh</h2>
    <input type="file" id="fileInput">
    <button onclick="uploadImage()">Upload</button>

    <div class="preview" id="preview"></div>

    <script>
    async function uploadImage() {
        const file = document.getElementById("fileInput").files[0];
        if (!file) return alert("Vui lòng chọn ảnh");

        const formData = new FormData();
        formData.append("action", "upload");
        formData.append("file", file);

        let res = await fetch("../../cloudinary_image.php", { method: "POST", body: formData });
        let data = await res.json();

        if (data.url) {
            document.getElementById("preview").innerHTML = `
                <img src="${data.url}" alt="Uploaded">
                <button onclick="deleteImage('${data.public_id}')">🗑 Xóa ảnh</button>
            `;
        } else {
            alert("Lỗi upload: " + JSON.stringify(data));
        }
    }

    async function deleteImage(publicId) {
        const formData = new FormData();
        formData.append("action", "delete");
        formData.append("public_id", publicId);

        let res = await fetch("../../cloudinary_image.php", { method: "POST", body: formData });
        let data = await res.json();

        if (data.result === "ok") {
            document.getElementById("preview").innerHTML = "✅ Đã xóa ảnh";
        } else {
            alert("Lỗi xóa ảnh: " + JSON.stringify(data));
        }
    }
    </script>
</body>
</html>
