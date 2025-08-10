<?php
// ===================== CẤU HÌNH CLOUDINARY =====================
$cloud_name = "dbdf2gwc9"; 
$api_key    = "451298475188791";
$api_secret = "e-lLavuDlEKvm3rg-Tg_P6yMM3o";

// ===================== XỬ LÝ XÓA ẢNH =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    $image_url = trim($_POST['image_url'] ?? '');
    if (!$image_url) {
        echo json_encode(['success' => false, 'message' => 'Thiếu image_url']);
        exit;
    }

    // Parse public_id từ URL Cloudinary
    $parsed_path = parse_url($image_url, PHP_URL_PATH);
    if (!$parsed_path || !str_contains($parsed_path, '/upload/')) {
        echo json_encode(['success' => false, 'message' => 'URL không hợp lệ hoặc không phải của Cloudinary']);
        exit;
    }

    $segments = explode('/', trim($parsed_path, '/'));
    $upload_index = array_search('upload', $segments);

    if ($upload_index === false || !isset($segments[$upload_index + 2])) {
        echo json_encode(['success' => false, 'message' => 'Không thể phân tích public_id']);
        exit;
    }

    // Lấy các phần còn lại làm public_id, bỏ phần mở rộng file
    $public_id_parts = array_slice($segments, $upload_index + 2);
    $public_id_with_ext = implode('/', $public_id_parts);
    $public_id = preg_replace('/\.[^.]+$/', '', $public_id_with_ext);

    // Gọi API xóa ảnh
    $timestamp = time();
    $signature = sha1("public_id={$public_id}&timestamp={$timestamp}{$api_secret}");

    $ch = curl_init("https://api.cloudinary.com/v1_1/{$cloud_name}/image/destroy");
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'public_id' => $public_id,
            'api_key'   => $api_key,
            'timestamp' => $timestamp,
            'signature' => $signature
        ],
        CURLOPT_RETURNTRANSFER => true
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $res_data = json_decode($response, true);

    if ($http_code === 200 && isset($res_data['result']) && $res_data['result'] === 'ok') {
        echo json_encode(['success' => true, 'message' => 'Xóa ảnh thành công!', 'public_id' => $public_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Xóa ảnh thất bại!', 'response' => $res_data]);
    }
    exit;
}
?>

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
        const res = await fetch(window.location.href, { method: 'POST', body: formData });
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
