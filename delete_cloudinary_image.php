<?php
require 'dotenv.php';
require 'vendor/autoload.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Uploader;

// Thiết lập header để trả JSON luôn
header("Content-Type: application/json; charset=utf-8");

try {
    // Cấu hình Cloudinary từ biến môi trường
    Configuration::instance(getenv('CLOUDINARY_URL'));

    // Đọc JSON từ input
    $data = json_decode(file_get_contents("php://input"), true);
    $publicId = $data['public_id'] ?? '';

    if (!$publicId) {
        http_response_code(400); // Bad request
        echo json_encode([
            'success' => false,
            'message' => 'Thiếu public_id'
        ]);
        exit;
    }

    // Gọi Cloudinary để xóa ảnh
    $result = Uploader::destroy($publicId);

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'result' => $result
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
