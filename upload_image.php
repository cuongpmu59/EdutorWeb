<?php
require 'vendor/autoload.php';
require 'dotenv.php';

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

header('Content-Type: application/json');

// Kiểm tra file hợp lệ
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => 'Không tìm thấy file hợp lệ.']);
    exit;
}

// Lấy file từ form
$tempFile = $_FILES['file']['tmp_name'];

// Khởi tạo Cloudinary
$cloudinary = new Cloudinary(
    [
        'cloud' => [
            'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
            'api_key'    => $_ENV['CLOUDINARY_API_KEY'],
            'api_secret' => $_ENV['CLOUDINARY_API_SECRET'],
        ],
        'url' => [
            'secure' => true
        ]
    ]
);

// Tạo tên tạm thời
$tempName = 'temp_' . time();

try {
    // Upload ảnh với tên tạm
    $uploadResult = $cloudinary->uploadApi()->upload($tempFile, [
        'public_id' => $tempName,
        'folder' => '', // hoặc 'true_false' nếu bạn muốn nhóm thư mục
        'overwrite' => true,
        'resource_type' => 'image'
    ]);

    echo json_encode([
        'secure_url' => $uploadResult['secure_url'],
        'public_id' => $uploadResult['public_id']
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Lỗi khi upload ảnh: ' . $e->getMessage()]);
}
