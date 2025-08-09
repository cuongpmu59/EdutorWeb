<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Upload Ảnh (Nén tự động) lên Cloudinary</title>
<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .preview { margin-top: 15px; }
    img { max-width: 300px; border: 1px solid #ccc; padding: 5px; border-radius: 8px; }
    .status { margin-top: 10px; font-weight: bold; color: green; }
</style>
</head>
<body>

<h2>📤 Upload ảnh (nén tự động)</h2>
<input type="file" id="fileInput" accept="image/*">
<button id="uploadBtn">Tải lên</button>

<div class="status" id="status"></div>
<div class="preview" id="preview"></div>

<script>
function getQualityBySize(fileSizeMB) {
    if (fileSizeMB > 5) return 0.5; // >5MB => 50% chất lượng
    if (fileSizeMB > 2) return 0.7; // 2-5MB => 70% chất lượng
    return 0.9;                     // <2MB => 90% chất lượng
}

function compressImage(file, quality, maxWidth = 1280) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = event => {
            const img = new Image();
            img.onload = () => {
                let width = img.width;
                let height = img.height;
                if (width > maxWidth) {
                    height *= maxWidth / width;
                    width = maxWidth;
                }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob(blob => {
                    resolve(blob);
                }, 'image/jpeg', quality);
            };
            img.onerror = () => reject("Không thể đọc ảnh");
            img.src = event.target.result;
        };
        reader.readAsDataURL(file);
    });
}

document.getElementById("uploadBtn").addEventListener("click", async function() {
    let file = document.getElementById("fileInput").files[0];
    if (!file) {
        alert("Vui lòng chọn ảnh trước khi tải lên!");
        return;
    }

    const fileSizeMB = file.size / (1024 * 1024);
    const quality = getQualityBySize(fileSizeMB);

    document.getElementById("status").textContent = `⏳ Đang nén ảnh (quality: ${quality * 100}%)...`;

    let compressedBlob;
    try {
        compressedBlob = await compressImage(file, quality, 1280);
    } catch (err) {
        document.getElementById("status").textContent = "❌ Lỗi nén ảnh: " + err;
        return;
    }

    let formData = new FormData();
    formData.append("file", compressedBlob, file.name);

    document.getElementById("status").textContent = "⏳ Đang tải lên...";

    fetch("upload_image.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.secure_url) {
            document.getElementById("status").textContent = "✅ Tải lên thành công!";
            document.getElementById("preview").innerHTML = `
                <p>Link ảnh: <a href="${data.secure_url}" target="_blank">${data.secure_url}</a></p>
                <img src="${data.secure_url}" alt="Uploaded Image">
            `;
        } else {
            document.getElementById("status").textContent = "❌ Lỗi: " + (data.error?.message || JSON.stringify(data.error));
        }
    })
    .catch(err => {
        document.getElementById("status").textContent = "❌ Lỗi: " + err;
    });
});
</script>

</body>
</html>
