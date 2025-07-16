<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../dotenv.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

header('Content-Type: application/json');

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

if (!isset($_FILES['image'])) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy file ảnh']);
    exit;
}

// Lấy file tạm
$tmpFile = $_FILES['image']['tmp_name'];

// Tùy chọn đặt tên ảnh theo mc_id
$mc_id = $_POST['mc_id'] ?? null;

// Nếu có mc_id → dùng tên cố định
$publicId = $mc_id ? 'mc_' . $mc_id : 'mc_temp_' . uniqid();

// Thực hiện upload
try {
    $result = (new UploadApi())->upload($tmpFile, [
        'public_id' => $publicId,
        'overwrite' => true,
        'folder'    => '', // hoặc thêm folder nếu muốn (vd: 'mc_questions/')
    ]);

    echo json_encode([
        'success' => true,
        'url' => $result['secure_url'],
        'public_id' => $result['public_id']
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi upload: ' . $e->getMessage()]);
}
