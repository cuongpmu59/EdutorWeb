<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../../env/config.php';

try {
    // Chỉ xử lý nếu có file gửi lên
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {

        $filePath = $_FILES['file']['tmp_name'];

        if (!$filePath || !is_uploaded_file($filePath)) {
            throw new Exception('❌ Không tìm thấy file upload');
        }

        // Chuẩn bị dữ liệu gửi lên Cloudinary (unsigned)
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
        $error  = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception('❌ Lỗi CURL: ' . $error);
        }

        echo $result; // Cloudinary trả về JSON
        exit;
    }

    // Nếu không phải upload ảnh thì trả lỗi
    echo json_encode(['error' => '❌ Request không hợp lệ'], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
