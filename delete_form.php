<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>🗑️ Xóa ảnh Cloudinary</title>
<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    input { width: 80%; padding: 8px; margin-bottom: 10px; }
    button { padding: 8px 15px; cursor: pointer; }
    .result { margin-top: 15px; }
    pre { background: #f4f4f4; padding: 8px; white-space: pre-wrap; }
</style>
</head>
<body>

<h2>🗑️ Xóa ảnh trên Cloudinary</h2>
<form id="deleteForm">
    <input type="text" name="image_url" id="image_url" placeholder="Nhập URL ảnh Cloudinary..." required>
    <button type="submit">Xóa ảnh</button>
</form>

<div class="result" id="result"></div>

<script>
document.getElementById('deleteForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append('image_url', document.getElementById('image_url').value);

    try {
        const res = await fetch('delete.php', { method: 'POST', body: formData });
        const data = await res.json();
        const resultDiv = document.getElementById('result');

        if (data.success) {
            resultDiv.innerHTML = `<p style="color:green;">✅ ${data.message}</p>`;
        } else {
            resultDiv.innerHTML = `<p style="color:red;">❌ ${data.message}</p><pre>${JSON.stringify(data.response || {}, null, 2)}</pre>`;
        }
    } catch (err) {
        document.getElementById('result').innerHTML = `<p style="color:red;">Lỗi: ${err}</p>`;
    }
});
</script>

</body>
</html>
