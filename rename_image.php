<?php
require 'vendor/autoload.php';
require 'dotenv.php';

header("Content-Type: application/json; charset=utf-8");

$oldId = $_POST['old_public_id'] ?? '';
$newId = $_POST['new_public_id'] ?? '';

if (!$oldId || !$newId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Thiếu thông tin public_id.']);
    exit;
}

// Khởi tạo Cloudinary
\Cloudinary\Configuration\Configuration::instance([
    'cloud' => [
        'cloud_name' => getenv('CLOUDINARY_CLOUD_NAME'),
        'api_key'    => getenv('CLOUDINARY_API_KEY'),
        'api_secret' => getenv('CLOUDINARY_API_SECRET'),
    ]
]);

try {
    $result = \Cloudinary\Api\Upload::rename($oldId, $newId, ['overwrite' => true]);
    if (!empty($result['secure_url'])) {
        echo json_encode([
            'status' => 'success',
            'message' => '✅ Đã đổi tên ảnh thành công.',
            'url' => $result['secure_url']
        ], JSON_UNESCAPED_UNICODE);
    } else {
        throw new Exception("Không nhận được secure_url từ Cloudinary.");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => '❌ Lỗi khi đổi tên ảnh: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
