<?php
require_once 'config.php';
require_once 'vendor/autoload.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

header('Content-Type: application/json');

$imageUrl = $_POST['image_url'] ?? '';

if (!$imageUrl) {
    echo json_encode(['success' => false, 'message' => 'Thiếu URL ảnh']);
    exit;
}

// Tách public_id từ URL (giả định là https://res.cloudinary.com/.../image/upload/vxxx/pic_123.png)
$matches = [];
if (preg_match('~upload/.+?/([^/]+)\.(jpg|jpeg|png|gif|webp)$~', $imageUrl, $matches)) {
    $publicId = pathinfo($matches[1], PATHINFO_FILENAME);
} else {
    echo json_encode(['success' => false, 'message' => 'Không tìm được public_id']);
    exit;
}

// Cấu hình Cloudinary
Configuration::instance([
    'cloud' => [
        'cloud_name' => CLOUDINARY_CLOUD_NAME,
        'api_key'    => CLOUDINARY_API_KEY,
        'api_secret' => CLOUDINARY_API_SECRET,
    ],
    'url' => ['secure' => true]
]);

try {
    $result = (new UploadApi())->destroy($publicId);
    echo json_encode(['success' => $result['result'] === 'ok']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
