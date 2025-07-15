<?php
require_once __DIR__ . '/../../../db_connection.php';
require_once __DIR__ . '/../../../dotenv.php';

$id = $_POST['mc_id'] ?? '';
if (!$id) exit("❌ Thiếu ID");

try {
  $stmt = $conn->prepare("SELECT mc_image_url FROM mc_questions WHERE mc_id = ?");
  $stmt->execute([$id]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$row || empty($row['mc_image_url'])) exit("⚠️ Không có ảnh để xoá");

  // Lấy public_id từ URL
  $url = $row['mc_image_url'];
  $parsed = parse_url($url);
  $parts = explode('/', $parsed['path']);
  $filename = end($parts);
  $publicId = 'mc_questions/' . pathinfo($filename, PATHINFO_FILENAME);

  // Xoá Cloudinary
  $cloudName = getenv('CLOUDINARY_CLOUD_NAME');
  $apiKey    = getenv('CLOUDINARY_API_KEY');
  $apiSecret = getenv('CLOUDINARY_API_SECRET');
  $timestamp = time();

  $signature = sha1("public_id=$publicId&timestamp=$timestamp$apiSecret");

  $postFields = http_build_query([
    'public_id' => $publicId,
    'timestamp' => $timestamp,
    'api_key'   => $apiKey,
    'signature' => $signature
  ]);

  $ch = curl_init("https://api.cloudinary.com/v1_1/$cloudName/image/destroy");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
  $response = curl_exec($ch);
  curl_close($ch);

  // Xoá trường ảnh trong DB
  $stmt = $conn->prepare("UPDATE mc_questions SET mc_image_url = NULL WHERE mc_id = ?");
  $stmt->execute([$id]);

  echo "✅ Đã xoá ảnh: $publicId";
} catch (Exception $e) {
  echo "❌ Lỗi: " . $e->getMessage();
}
