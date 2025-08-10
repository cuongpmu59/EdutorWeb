<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <title>Xóa ảnh Cloudinary</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <style>
    body { font-family: Arial, Helvetica, sans-serif; max-width:640px; margin:40px auto; padding:12px; }
    label, input, button, textarea { display:block; width:100%; margin-top:8px; }
    input[type="text"] { padding:8px; font-size:16px; }
    button { padding:10px; font-size:16px; cursor:pointer; margin-top:12px; }
    pre { background:#f6f6f6; padding:10px; border-radius:6px; white-space:pre-wrap; }
    .ok { color: #0a6; }
    .err { color: #c00; }
  </style>
</head>
<body>
  <h1>Xóa ảnh Cloudinary</h1>

  <label for="imageUrl">Dán URL ảnh Cloudinary (ví dụ: https://res.cloudinary.com/....png)</label>
  <input id="imageUrl" type="text" placeholder="https://res.cloudinary.com/..." />

  <label for="result">Kết quả</label>
  <pre id="result">Chưa có thao tác</pre>

  <button id="deleteBtn">Xóa ảnh</button>

  <script>
    const btn = document.getElementById('deleteBtn');
    const input = document.getElementById('imageUrl');
    const result = document.getElementById('result');

    btn.addEventListener('click', async () => {
      const url = input.value.trim();
      if (!url) {
        result.textContent = 'Vui lòng nhập URL ảnh.';
        result.className = 'err';
        return;
      }

      result.textContent = 'Đang gửi yêu cầu...';
      result.className = '';

      try {
        const resp = await fetch('delete_image.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({ image_url: url })
        });

        const text = await resp.text();
        // Try parse JSON, fallback to raw text
        try {
          const json = JSON.parse(text);
          if (json.success) {
            result.className = 'ok';
            result.textContent = 'Thành công:\n' + JSON.stringify(json, null, 2);
          } else {
            result.className = 'err';
            result.textContent = 'Lỗi:\n' + JSON.stringify(json, null, 2);
          }
        } catch (e) {
          result.className = resp.ok ? 'ok' : 'err';
          result.textContent = text;
        }
      } catch (err) {
        result.className = 'err';
        result.textContent = 'Lỗi kết nối: ' + err.message;
      }
    });
  </script>
</body>
</html>
