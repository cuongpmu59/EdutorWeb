<?php
require 'vendor/autoload.php'; // Đảm bảo bạn đã chạy: composer require cloudinary/cloudinary_php

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

// Cấu hình Cloudinary
Configuration::instance([
  'cloud' => [
    'cloud_name' => 'YOUR_CLOUD_NAME',
    'api_key'    => 'YOUR_API_KEY',
    'api_secret' => 'YOUR_API_SECRET'
  ],
  'url' => [
    'secure' => true
  ]
]);

// Kiểm tra yêu cầu POST và file ảnh
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
  $id = $_POST['id'] ?? '';

  if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID câu hỏi.']);
    exit;
  }

  $tmpPath = $_FILES['image']['tmp_name'];
  $publicId = "pic_" . $id;

  try {
    // Tải ảnh lên Cloudinary, ghi đè nếu trùng
    $result = (new UploadApi())->upload($tmpPath, [
      'public_id' => $publicId,
      'overwrite' => true,
      'folder' => 'question_images' // Tùy chọn: thư mục lưu trên Cloudinary
    ]);

    echo json_encode([
      'success' => true,
      'url' => $result['secure_url']
    ]);
  } catch (Exception $e) {
    echo json_encode([
      'success' => false,
      'message' => $e->getMessage()
    ]);
  }
} else {
  echo json_encode([
    'success' => false,
    'message' => 'Yêu cầu không hợp lệ.'
  ]);
}
