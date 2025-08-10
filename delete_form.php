<?php
// ================== CONFIG ==================
// Lấy thông tin từ biến môi trường hoặc cấu hình
$CLOUDINARY_CLOUD_NAME = "your_cloud_name"; // Thay bằng Cloud Name
$CLOUDINARY_UPLOAD_PRESET = "your_unsigned_preset"; // Thay bằng Upload Preset (unsigned)

// ================== XỬ LÝ UPLOAD ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['image_url'])) {
    $image_url = trim($_POST['image_url']);

    if (filter_var($image_url, FILTER_VALIDATE_URL)) {
        $api_url = "https://api.cloudinary.com/v1_1/{$CLOUDINARY_CLOUD_NAME}/image/upload";

        // Dữ liệu gửi lên Cloudinary
        $post_fields = [
            'file' => $image_url,
            'upload_preset' => $CLOUDINARY_UPLOAD_PRESET
        ];

        // Gửi request bằng cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $http_code === 200,
            'http_code' => $http_code,
            'response' => json_decode($response, true)
        ]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'URL ảnh không hợp lệ']);
        exit;
    }
}
?>

<!-- ================== HTML FORM ================== -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Upload Ảnh từ URL - Cloudinary</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: auto; padding: 20px; }
        input[type=text] { width: 100%; padding: 10px; margin-top: 5px; }
        button { margin-top: 10px; padding: 10px 20px; background: #00a1ff; color: white; border: none; cursor: pointer; }
        button:hover { background: #007acc; }
        pre { background: #f4f4f4; padding: 10px; white-space: pre-wrap; }
    </style>
</head>
<body>
    <h2>📤 Upload Ảnh từ URL</h2>
    <form method="post" action="">
        <label>Nhập URL ảnh:</label>
        <input type="text" name="image_url" placeholder="https://example.com/image.jpg" required>
        <button type="submit">Upload</button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <h3>Kết quả:</h3>
        <pre><?php echo htmlspecialchars($response ?? ''); ?></pre>
    <?php endif; ?>
</body>
</html>
