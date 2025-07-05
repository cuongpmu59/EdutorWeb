<?php
require_once 'config.php';
require_once 'vendor/autoload.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

header('Content-Type: application/json');

// ===== Nhận thông tin từ POST =====
$tempPublicId = $_POST['temp_public_id'] ?? '';
$newPublicId  = $_POST['new_public_id'] ?? '';

if (!$tempPublicId || !$newPublicId) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu thông tin tên ảnh (temp_public_id hoặc new_public_id)'
    ]);
    exit;
}

// ===== Cấu hình Cloudinary =====
try {
    Configuration::instance([
        'cloud' => [
            'cloud_name' => CLOUDINARY_CLOUD_NAME,
            'api_key'    => CLOUDINARY_API_KEY,
            'api_secret' => CLOUDINARY_API_SECRET,
        ],
        'url' => ['secure' => true]
    ]);

    // ===== Đổi tên ảnh (rename) =====
    $result = (new UploadApi())->rename(
        $tempPublicId,
        $newPublicId,
        ['overwrite' => true]
    );

    $newUrl = $result['secure_url'] ?? '';

    if (!$newUrl) {
        echo json_encode([
            'success' => false,
            'message' => 'Không lấy được đường dẫn ảnh sau khi đổi tên'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'url' => $newUrl
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi Cloudinary: ' . $e->getMessage()
    ]);
}
