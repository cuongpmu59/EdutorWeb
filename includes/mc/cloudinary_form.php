<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Ảnh minh hoạ</title>
<style>
.preview-box {
    width: 300px; height: 200px;
    border: 2px dashed #ccc;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 10px; background: #f9f9f9;
}
.preview-box img { max-width: 100%; max-height: 100%; }
.btn {
    padding: 8px 12px; margin-right: 5px;
    border: none; cursor: pointer; border-radius: 4px;
}
.btn-upload { background: #28a745; color: white; }
.btn-delete { background: #dc3545; color: white; }
</style>
</head>
<body>

<h3>Ảnh minh hoạ</h3>

<div class="preview-box" id="previewBox">
    <img id="previewImage" src="" alt="Chưa có ảnh">
</div>

<input type="file" id="fileInput" style="display:none;" accept="image/*">
<button class="btn btn-upload" id="uploadBtn">Tải ảnh</button>
<button class="btn btn-delete" id="deleteBtn">Xoá ảnh</button>

<script>
// Nút tải ảnh
document.getElementById("uploadBtn").addEventListener("click", () => {
    document.getElementById("fileInput").click();
});

document.getElementById("fileInput").addEventListener("change", async function(){
    if (!this.files.length) return;
    let formData = new FormData();
    formData.append("file", this.files[0]);
    formData.append("action", "upload");

    let res = await fetch("cloudinary_image.php", { method: "POST", body: formData });
    let data = await res.json();

    if (data.url) {
        document.getElementById("previewImage").src = data.url;
    } else {
        alert("Lỗi tải ảnh: " + (data.error || "Không xác định"));
    }
});

// Nút xoá ảnh
document.getElementById("deleteBtn").addEventListener("click", async function(){
    let img = document.getElementById("previewImage");
    let src = img.src.trim();

    if (!src) return alert("Chưa có ảnh để xoá");

    // Regex lấy public_id từ URL
    let match = src.match(/upload\/(?:v\d+\/)?([^\.]+)/);
    if (!match) return alert("Không lấy được public_id");

    let public_id = match[1];

    let formData = new FormData();
    formData.append("action", "delete");
    formData.append("public_id", public_id);

    let res = await fetch("cloudinary_image.php", { method: "POST", body: formData });
    let data = await res.json();

    if (data.success) {
        img.src = "";
        alert("Xoá ảnh thành công");
    } else {
        alert("Lỗi xoá ảnh: " + (data.error || "Không xác định"));
    }
});
</script>

</body>
</html>
