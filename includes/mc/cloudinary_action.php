<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../../env/config.php'; 
// Trong config.php cần có:
// define('CLOUDINARY_CLOUD_NAME', 'ten_cloud');
// define('CLOUDINARY_UPLOAD_PRESET', 'ten_preset_unsigned');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
        // ==== UNSIGNED UPLOAD ====
        $filePath = $_FILES['file']['tmp_name'];

        if (!$filePath || !is_uploaded_file($filePath)) {
            throw new Exception('❌ Không tìm thấy file upload');
        }

        $postFields = [
            'file'          => new CURLFile($filePath),
            'upload_preset' => CLOUDINARY_UPLOAD_PRESET
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/" . CLOUDINARY_CLOUD_NAME . "/image/upload");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        echo $result; // Cloudinary trả JSON
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['public_id'])) {
        // ❌ Với unsigned upload thì KHÔNG xoá được ảnh qua API
        echo json_encode(['error' => '❌ Unsigned upload không hỗ trợ xoá qua API'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode(['error' => '❌ Request không hợp lệ'], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
