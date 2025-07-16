<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../dotenv.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

// ✅ Thiết lập cấu hình từ biến môi trường
Configuration::instance([
    'cloud' => [
        'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
        'api_key'    => $_ENV['CLOUDINARY_API_KEY'],
        'api_secret' => $_ENV['CLOUDINARY_API_SECRET']
    ],
    'url' => [
        'secure' => true
    ]
]);

header('Content-Type: application/json');

// 📥 Lấy dữ liệu POST
$input = json_decode(file_get_contents('php://input'), true);
$mc_id = $input['mc_id'] ?? null;

if (!$mc_id) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mc_id']);
    exit;
}

$publicId = 'mc_' . $mc_id; // Tên ảnh lưu theo chuẩn mc_{mc_id}

try {
    $result = (new UploadApi())->destroy($publicId);
    if ($result['result'] === 'ok') {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy ảnh cần xoá']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi Cloudinary: ' . $e->getMessage()]);
}
