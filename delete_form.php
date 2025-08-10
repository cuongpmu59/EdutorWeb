<?php
// ===================== CẤU HÌNH CLOUDINARY =====================
$cloud_name = "dbdf2gwc9"; 
$api_key    = "451298475188791";
$api_secret = "e-lLavuDlEKvm3rg-Tg_P6yMM3o";

// ===================== XỬ LÝ XÓA ẢNH =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image_url'])) {
    header('Content-Type: application/json; charset=utf-8');
    $image_url = trim($_POST['image_url']);

    if (empty($image_url)) {
        echo json_encode(['success' => false, 'message' => 'Thiếu image_url']);
        exit;
    }

    // Lấy public_id từ URL (kể cả ảnh nằm trong folder)
    $parsed_url = parse_url($image_url, PHP_URL_PATH);
    $parts = explode('/', trim($parsed_url, '/'));
    $upload_index = array_search('upload', $parts);

    if ($upload_index !== false) {
        // +2 để bỏ "upload" và "version" (vd: v1720000000)
        $public_id_parts = array_slice($parts, $upload_index + 2);
        $public_id_with_ext = implode('/', $public_id_parts);
        $public_id = preg_replace('/\.[^.]+$/', '', $public_id_with_ext);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể phân tích public_id']);
        exit;
    }

    // Gọi API xóa ảnh trên Cloudinary
    $timestamp = time();
    $signature = sha1("public_id={$public_id}&timestamp={$timestamp}{$api_secret}");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/{$cloud_name}/image/destroy");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'public_id' => $public_id,
        'api_key'   => $api_key,
        'timestamp' => $timestamp,
        'signature' => $signature
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        $res_data = json_decode($result, true);
        if (isset($res_data['result']) && $res_data['result'] === 'ok') {
            echo json_encode(['success' => true, 'message' => 'Xóa ảnh thành công!', 'public_id' => $public_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Xóa ảnh thất bại!', 'response' => $res_data]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Lỗi HTTP {$http_code}", 'response' => $result]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Xóa ảnh Cloudinary</title>
<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    input { width: 80%; padding: 8px; margin-bottom: 10px; }
    button { padding: 8px 15px; cursor: pointer; }
    .result { margin-top: 15px; }
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
document.getElementById('deleteForm').addEventListener('submit', function(e) {
    e.preventDefault();

    let formData = new FormData();
    formData.append('image_url', document.getElementById('image_url').value);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        let resultDiv = document.getElementById('result');
        if (data.success) {
            resultDiv.innerHTML = `<p style="color:green;">✅ ${data.message}</p>`;
        } else {
            resultDiv.innerHTML = `<p style="color:red;">❌ ${data.message}</p><pre>${JSON.stringify(data.response || {}, null, 2)}</pre>`;
        }
    })
    .catch(err => {
        document.getElementById('result').innerHTML = `<p style="color:red;">Lỗi: ${err}</p>`;
    });
});
</script>

</body>
</html>
