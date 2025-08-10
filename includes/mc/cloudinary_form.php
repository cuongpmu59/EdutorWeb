<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Ảnh minh họa</title>
<style>
    .preview-container {
        width: 300px;
        height: 200px;
        border: 2px dashed #ccc;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        margin-bottom: 10px;
    }
    .preview-container img {
        max-width: 100%;
        max-height: 100%;
    }
    button {
        padding: 8px 14px;
        margin: 5px;
        border: none;
        cursor: pointer;
        border-radius: 4px;
        font-size: 14px;
    }
    .upload-btn { background-color: #4CAF50; color: white; }
    .delete-btn { background-color: #f44336; color: white; }
</style>
</head>
<body>

<h3>Ảnh minh họa</h3>

<div class="preview-container" id="preview">
    <span>Chưa có ảnh</span>
</div>

<!-- Input file ẩn -->
<input type="file" id="fileInput" accept="image/*" style="display:none;">

<button class="upload-btn" id="uploadBtn">Tải ảnh</button>
<button class="delete-btn" id="deleteBtn">Xóa ảnh</button>

<script>
const fileInput = document.getElementById('fileInput');
const preview = document.getElementById('preview');
const uploadBtn = document.getElementById('uploadBtn');
const deleteBtn = document.getElementById('deleteBtn');

uploadBtn.addEventListener('click', () => fileInput.click());

fileInput.addEventListener('change', () => {
    const file = fileInput.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append("image", file);

    fetch("cloudinary_image.php?action=upload", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.url) {
            preview.innerHTML = `<img src="${data.url}" alt="Ảnh minh họa">`;
        } else {
            alert(data.error || "Tải ảnh thất bại");
        }
    });
});

deleteBtn.addEventListener('click', () => {
    const img = preview.querySelector('img');
    if (!img) {
        alert("Không có ảnh để xóa");
        return;
    }
    const src = img.src;
    const match = src.match(/\/upload\/(?:v\d+\/)?([^\.]+)/); // Lấy public_id
    if (!match) {
        alert("Không lấy được public_id");
        return;
    }
    const publicId = match[1];

    fetch("cloudinary_image.php?action=delete", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "public_id=" + encodeURIComponent(publicId)
    })
    .then(res => res.json())
    .then(data => {
        if (data.result === "ok") {
            preview.innerHTML = "<span>Chưa có ảnh</span>";
        } else {
            alert(data.error || "Xóa ảnh thất bại");
        }
    });
});
</script>

</body>
</html>
