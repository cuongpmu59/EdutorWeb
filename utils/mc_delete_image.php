<?php
// utils/mc_delete_image.php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../dotenv.php';
require_once __DIR__ . '/../db_connection.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

header('Content-Type: application/json');

Configuration::instance([
  'cloud' => [
    'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
    'api_key'    => $_ENV['CLOUDINARY_API_KEY'],
    'api_secret' => $_ENV['CLOUDINARY_API_SECRET']
  ],
  'url' => [ 'secure' => true ]
]);

function respond($success, $message = '') {
  echo json_encode(['success' => $success, 'message' => $message]);
  exit;
}

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);
$mc_id = $data['mc_id'] ?? '';

if (!$mc_id || !is_numeric($mc_id)) {
  respond(false, 'ID không hợp lệ.');
}

try {
  if (!$conn) throw new Exception("Không kết nối CSDL");

  $stmt = $conn->prepare("SELECT mc_image_url FROM mc_questions WHERE mc_id = ?");
  $stmt->execute([$mc_id]);
  $url = $stmt->fetchColumn();

  if (!$url) respond(false, 'Không tìm thấy ảnh để xoá.');

  $publicId = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_FILENAME);

  $uploadApi = new UploadApi();
  $uploadApi->destroy($publicId, [ 'invalidate' => true ]);

  $conn->prepare("UPDATE mc_questions SET mc_image_url = NULL WHERE mc_id = ?")
       ->execute([$mc_id]);

  respond(true, 'Đã xoá ảnh.');

} catch (Exception $e) {
  respond(false, 'Lỗi: ' . $e->getMessage());
}
