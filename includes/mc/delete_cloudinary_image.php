<?php
require_once '../../env/dotenv.php';
require_once '../../env/config.php';

header('Content-Type: application/json');

$image_url = $_POST['image_url'] ?? null;

if (!$image_url) {
  echo json_encode(['success' => false, 'error' => 'Thiếu image_url']);
  exit;
}

// Tách public_id từ image_url
$parts = explode('/upload/', $image_url);
if (count($parts) < 2) {
  echo json_encode(['success' => false, 'error' => 'Không tách được public_id']);
  exit;
}

$public_id_with_ext = $parts[1]; // vd: mc_images/example_image.jpg
$public_id = preg_replace('/\.[^.]+$/', '', $public_id_with_ext); // Bỏ .jpg

// Gửi xoá đến Cloudinary
$cloud_name = getenv('CLOUDINARY_CLOUD_NAME');
$api_key = getenv('CLOUDINARY_API_KEY');
$api_secret = getenv('CLOUDINARY_API_SECRET');
$timestamp = time();
$string_to_sign = "public_id={$public_id}&timestamp={$timestamp}{$api_secret}";
$signature = sha1($string_to_sign);

$data = [
  'public_id' => $public_id,
  'api_key' => $api_key,
  'timestamp' => $timestamp,
  'signature' => $signature
];

$ch = curl_init("https://api.cloudinary.com/v1_1/{$cloud_name}/image/destroy");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if ($result['result'] === 'ok') {
  // Nếu cần, bạn có thể xoá mc_image_url trong DB tại đây
  $stmt = $pdo->prepare("UPDATE mc_questions SET mc_image_url = NULL WHERE mc_image_url = ?");
  $stmt->execute([$image_url]);

  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'error' => 'Xoá ảnh thất bại']);
}
