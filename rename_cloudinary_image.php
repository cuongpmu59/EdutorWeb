<?php
require_once 'config.php';
require_once 'vendor/autoload.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\Upload\UploadApi;

header('Content-Type: application/json');

$tempPublicId = $_POST['temp_public_id'] ?? '';
$newPublicId = $_POST['new_public_id'] ?? '';

if (!$tempPublicId || !$newPublicId) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin tên ảnh']);
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
    // Đổi tên (move)
    $result = (new UploadApi())->rename($tempPublicId, $newPublicId, ['overwrite' => true]);

    // Trả về URL mới
    $url = $result['secure_url'] ?? '';
    echo json_encode(['success' => true, 'url' => $url]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
