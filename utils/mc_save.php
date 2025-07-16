<?php
require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/../env/dotenv.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

function respond($success, $message = '', $extra = []) {
  echo json_encode(array_merge([
    'success' => $success,
    'message' => $message
  ], $extra));
  exit;
}

function uploadToCloudinary($filePath, $publicId) {
  $cloudName = env('CLOUDINARY_CLOUD_NAME');
  $apiKey    = env('CLOUDINARY_API_KEY');
  $apiSecret = env('CLOUDINARY_API_SECRET');

  if (!$cloudName || !$apiKey || !$apiSecret) {
    throw new Exception("Thiếu cấu hình Cloudinary trong .env");
  }

  $timestamp = time();
  $toSign    = "public_id=$publicId&timestamp=$timestamp";
  $signature = sha1($toSign . $apiSecret);

  $post = [
    'file'      => new CURLFile($filePath),
    'api_key'   => $apiKey,
    'timestamp' => $timestamp,
    'public_id' => $publicId,
    'signature' => $signature
  ];

  $ch = curl_init("https://api.cloudinary.com/v1_1/$cloudName/image/upload");
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $post
  ]);

  $res = curl_exec($ch);
  if ($res === false) {
    throw new Exception('Lỗi cURL: ' . curl_error($ch));
  }

  curl_close($ch);
  $result = json_decode($res, true);

  if (!isset($result['secure_url'])) {
    $err = $result['error']['message'] ?? 'Không rõ lỗi';
    throw new Exception("Không upload được ảnh: $err");
  }

  return $result['secure_url'];
}

// ===== NHẬN DỮ LIỆU =====
$mc_id     = $_POST['mc_id'] ?? '';
$mc_topic  = trim($_POST['mc_topic'] ?? '');
$mc_q      = trim($_POST['mc_question'] ?? '');
$a1        = trim($_POST['mc_answer1'] ?? '');
$a2        = trim($_POST['mc_answer2'] ?? '');
$a3        = trim($_POST['mc_answer3'] ?? '');
$a4        = trim($_POST['mc_answer4'] ?? '');
$correct   = $_POST['mc_correct_answer'] ?? '';

if (!$mc_topic || !$mc_q || !$a1 || !$correct) {
  respond(false, 'Thiếu dữ liệu bắt buộc.');
}

$image_url = '';

try {
  if (!$conn) throw new Exception("Không kết nối được CSDL.");

  // === Upload ảnh nếu có ===
  if (!empty($_FILES['mc_image']['tmp_name'])) {
    $publicId  = $mc_id ? 'pic_' . $mc_id : 'mc_temp_' . uniqid();
    $image_url = uploadToCloudinary($_FILES['mc_image']['tmp_name'], $publicId);
  }

  // === THÊM MỚI ===
  if ($mc_id === '') {
    $stmt = $conn->prepare("INSERT INTO mc_questions 
      (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$mc_topic, $mc_q, $a1, $a2, $a3, $a4, $correct, $image_url]);
    $newId = $conn->lastInsertId();
    respond(true, "Đã thêm mới", ['id' => $newId]);
  }

  // === CẬP NHẬT ===
  if (!is_numeric($mc_id)) throw new Exception("ID không hợp lệ.");

  $stmt = $conn->prepare("UPDATE mc_questions SET 
    mc_topic=?, mc_question=?, mc_answer1=?, mc_answer2=?, mc_answer3=?, mc_answer4=?, mc_correct_answer=? 
    WHERE mc_id=?");
  $stmt->execute([$mc_topic, $mc_q, $a1, $a2, $a3, $a4, $correct, $mc_id]);

  if ($image_url) {
    $stmt = $conn->prepare("UPDATE mc_questions SET mc_image_url=? WHERE mc_id=?");
    $stmt->execute([$image_url, $mc_id]);
  }

  respond(true, "Đã cập nhật");

} catch (Exception $e) {
  respond(false, $e->getMessage());
}
