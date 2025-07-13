<?php
require_once __DIR__ . '/../../db_connection.php';
require_once __DIR__ . '/../../dotenv.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// ⚠️ Kiểm tra phương thức gửi
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  exit('❌ Chỉ chấp nhận POST');
}

// === Lấy dữ liệu từ form ===
$topic     = $_POST['mc_topic'] ?? '';
$question  = $_POST['mc_question'] ?? '';
$answer1   = $_POST['mc_answer1'] ?? '';
$answer2   = $_POST['mc_answer2'] ?? '';
$answer3   = $_POST['mc_answer3'] ?? '';
$answer4   = $_POST['mc_answer4'] ?? '';
$correct   = $_POST['mc_correct_answer'] ?? '';
$imageUrl  = ''; // Mặc định ảnh rỗng

// === Upload ảnh nếu có ===
if (isset($_FILES['mc_image']) && $_FILES['mc_image']['error'] === UPLOAD_ERR_OK) {
  $tmpName = $_FILES['mc_image']['tmp_name'];
  $imageData = base64_encode(file_get_contents($tmpName));
  $mimeType = mime_content_type($tmpName);

  // Gửi đến Cloudinary
  $cloudName = getenv('CLOUDINARY_CLOUD_NAME');
  $uploadPreset = getenv('CLOUDINARY_UPLOAD_PRESET');
  $apiUrl = "https://api.cloudinary.com/v1_1/$cloudName/image/upload";

  $postData = [
    'file' => "data:$mimeType;base64,$imageData",
    'upload_preset' => $uploadPreset,
    'folder' => 'mc_questions'
  ];

  $ch = curl_init($apiUrl);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
  $response = curl_exec($ch);
  curl_close($ch);

  $result = json_decode($response, true);
  if (isset($result['secure_url'])) {
    $imageUrl = $result['secure_url'];
  }
}

// === Chèn vào CSDL ===
try {
  $stmt = $conn->prepare("
    INSERT INTO mc_questions (
      mc_topic, mc_question,
      mc_answer1, mc_answer2, mc_answer3, mc_answer4,
      mc_correct_answer, mc_image_url
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
  ");
  $stmt->execute([
    $topic, $question,
    $answer1, $answer2, $answer3, $answer4,
    $correct, $imageUrl
  ]);

  echo '✅ Câu hỏi đã được lưu thành công.';
} catch (PDOException $e) {
  echo '❌ Lỗi khi lưu vào CSDL: ' . $e->getMessage();
}
?>
