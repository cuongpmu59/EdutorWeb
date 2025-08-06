<?php
require_once __DIR__ . '/../../env/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['public_id'])) {
  echo json_encode(['success' => false, 'message' => '❌ public_id không được gửi']);
  exit;
}

$public_id = $_POST['public_id'];
$cloud_name = CLOUDINARY_CLOUD_NAME;
$api_key = CLOUDINARY_API_KEY;
$api_secret = CLOUDINARY_API_SECRET;

// Tạo signature
$timestamp = time();
$string_to_sign = "public_id={$public_id}&timestamp={$timestamp}{$api_secret}";
$signature = sha1($string_to_sign);

// Tạo dữ liệu POST
$post_fields = [
  'public_id' => $public_id,
  'api_key'   => $api_key,
  'timestamp' => $timestamp,
  'signature' => $signature
];

// Gửi yêu cầu tới Cloudinary
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/{$cloud_name}/image/destroy");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
$response = curl_exec($ch);
curl_close($ch);

// Trả kết quả
$result = json_decode($response, true);

if ($result['result'] === 'ok') {
  echo json_encode(['success' => true, 'message' => '✅ Ảnh đã được xóa']);
} else {
  echo json_encode(['success' => false, 'message' => '❌ Xóa không thành công', 'raw' => $result]);
}
