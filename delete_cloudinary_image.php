<?php
require 'dotenv.php';           // Nạp biến môi trường từ .env
require 'vendor/autoload.php';  // Cloudinary SDK

use Cloudinary\Configuration\Configuration;
use Cloudinary\Uploader;

// Thiết lập cấu hình Cloudinary từ biến môi trường
Configuration::instance(getenv('CLOUDINARY_URL'));

header("Content-Type: application/json");

// Nhận dữ liệu JSON từ phía client (AJAX)
$data = json_decode(file_get_contents("php://input"), true);
$publicId = $data['public_id'] ?? '';

if (!$publicId) {
  echo json_encode([
    'success' => false,
    'message' => 'Thiếu public_id'
  ]);
  exit;
}

try {
  // Gọi Cloudinary để xóa ảnh
  $result = Uploader::destroy($publicId);

  echo json_encode([
    'success' => true,
    'result' => $result
  ]);
} catch (Exception $e) {
  echo json_encode([
    'success' => false,
    'message' => $e->getMessage()
  ]);
}
