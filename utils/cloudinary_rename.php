<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../dotenv.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\Exception\ApiError;
use Cloudinary\Api\Upload\UploadApi;

header('Content-Type: application/json');

// Khởi tạo cấu hình Cloudinary
Configuration::instance([
  'cloud' => [
    'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
    'api_key'    => $_ENV['CLOUDINARY_API_KEY'],
    'api_secret' => $_ENV['CLOUDINARY_API_SECRET']
  ],
  'url' => ['secure' => true]
]);

// Đọc dữ liệu từ client
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

$oldPublicId = $data['old_public_id'] ?? null;
$mc_id = $data['mc_id'] ?? null;

if (!$oldPublicId || !$mc_id) {
  echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu']);
  exit;
}

// Tên mới
$newPublicId = 'mc_' . $mc_id;

try {
  $api = new UploadApi();
  $result = $api->rename($oldPublicId, $newPublicId, ['overwrite' => true]);

  echo json_encode([
    'success' => true,
    'new_url' => $result['secure_url'],
    'new_public_id' => $result['public_id']
  ]);
} catch (ApiError $e) {
  echo json_encode([
    'success' => false,
    'message' => 'Lỗi khi đổi tên ảnh: ' . $e->getMessage()
  ]);
}
