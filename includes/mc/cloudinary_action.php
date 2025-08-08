<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../env/config.php'; // Chứa CLOUDINARY_CLOUD_NAME, API_KEY, API_SECRET
require_once __DIR__ . '/../../vendor/autoload.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Api\Admin\AdminApi;

// 🔹 Cấu hình Cloudinary
Configuration::instance([
    'cloud' => [
        'cloud_name' => CLOUDINARY_CLOUD_NAME,
        'api_key'    => CLOUDINARY_API_KEY,
        'api_secret' => CLOUDINARY_API_SECRET
    ],
    'url' => [
        'secure' => true
    ]
]);

try {
    // ========================
    // 1️⃣ UPLOAD (unsigned)
    // ========================
    if (!empty($_FILES['image'])) {
        $fileTmp = $_FILES['image']['tmp_name'];

        // Gọi API upload unsigned
        $uploadResult = (new UploadApi())->unsignedUpload(
            $fileTmp,
            'mc_unsigned_preset', // Tên upload preset bạn tạo trong Cloudinary
            [
                'folder' => 'mc_uploads'
            ]
        );

        echo json_encode([
            'secure_url' => $uploadResult['secure_url'] ?? null,
            'public_id'  => $uploadResult['public_id'] ?? null
        ]);
        exit;
    }

    // ========================
    // 2️⃣ DELETE
    // ========================
    if (!empty($_POST['public_id'])) {
        $publicId = $_POST['public_id'];

        $deleteResult = (new UploadApi())->destroy($publicId);

        echo json_encode($deleteResult);
        exit;
    }

    // Nếu không phải upload hoặc delete
    echo json_encode(['error' => '❌ Request không hợp lệ']);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
