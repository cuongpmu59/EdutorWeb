<?php
require 'vendor/autoload.php'; // Đảm bảo bạn đã cài Cloudinary SDK

\Cloudinary\Configuration\Configuration::instance([
  'cloud' => [
    'cloud_name' => 'dbdf2gwc9',
    'api_key'    => 'YOUR_API_KEY',
    'api_secret' => 'YOUR_API_SECRET',
  ],
  'url' => [
    'secure' => true
  ]
]);

header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"), true);
$publicId = $data['public_id'] ?? '';

if (!$publicId) {
  echo json_encode(['success' => false, 'message' => 'Thiếu public_id']);
  exit;
}

try {
  $result = \Cloudinary\Api\Upload::destroy($publicId);
  echo json_encode(['success' => true, 'result' => $result]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
