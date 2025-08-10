<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Upload & Xóa ảnh Cloudinary</title>
    <style>
        .image-box { display: inline-block; margin: 10px; position: relative; }
        .delete-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: red;
            color: white;
            border: none;
            padding: 4px 8px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<h3>📤 Upload ảnh</h3>
<input type="file" id="fileInput" accept="image/*">
<button id="uploadBtn">Upload</button>

<div id="gallery"></div>

<script>
document.getElementById("uploadBtn").addEventListener("click", function() {
    const file = document.getElementById("fileInput").files[0];
    if (!file) {
        alert("Vui lòng chọn ảnh");
        return;
    }

    const formData = new FormData();
    formData.append("file", file);
    formData.append("upload_preset", "YOUR_UNSIGNED_PRESET"); // 🔹 thay bằng preset unsigned

    fetch("https://api.cloudinary.com/v1_1/YOUR_CLOUD_NAME/image/upload", { // 🔹 thay cloud name
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.secure_url) {
            const imgBox = document.createElement("div");
            imgBox.className = "image-box";
            imgBox.innerHTML = `
                <img src="${data.secure_url}" width="200">
                <button class="delete-btn" data-url="${data.secure_url}">X</button>
            `;
            document.getElementById("gallery").appendChild(imgBox);
        }
    })
    .catch(err => console.error("Upload lỗi:", err));
});

document.getElementById("gallery").addEventListener("click", function(e) {
    if (e.target.classList.contains("delete-btn")) {
        const imageUrl = e.target.dataset.url;
        fetch("cloudinary_image.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "action=delete&image_url=" + encodeURIComponent(imageUrl)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                e.target.parentElement.remove();
                alert("✅ Ảnh đã xóa thành công!");
            } else {
                alert("❌ Lỗi xóa ảnh: " + data.error);
            }
        })
        .catch(err => console.error("Xóa lỗi:", err));
    }
});
</script>
</body>
</html>
