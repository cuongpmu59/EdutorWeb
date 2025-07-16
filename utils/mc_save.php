<?php
require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/../dotenv.php';

header('Content-Type: application/json');

function respond($success, $message = '', $extra = []) {
  echo json_encode(array_merge([
    'success' => $success,
    'message' => $message
  ], $extra));
  exit;
}

function uploadImageDirectly($filePath, $publicId) {
  $cloudName = $_ENV['CLOUDINARY_CLOUD_NAME'];
  $apiKey    = $_ENV['CLOUDINARY_API_KEY'];
  $apiSecret = $_ENV['CLOUDINARY_API_SECRET'];

  $timestamp = time();
  $params_to_sign = "public_id=$publicId&timestamp=$timestamp$apiSecret";
  $signature = sha1($params_to_sign);

  $post = [
    'file' => new CURLFile($filePath),
    'api_key' => $apiKey,
    'timestamp' => $timestamp,
    'public_id' => $publicId,
    'signature' => $signature
  ];

  $ch = curl_init("https://api.cloudinary.com/v1_1/$cloudName/image/upload");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  $result = curl_exec($ch);
  curl_close($ch);

  return json_decode($result, true);
}

if (!isset($_POST['mc_topic'], $_POST['mc_question'], $_POST['mc_answer1'], $_POST['mc_correct_answer'])) {
  respond(false, 'Thiếu dữ liệu bắt buộc.');
}

$mc_id     = $_POST['mc_id'] ?? '';
$mc_topic  = trim($_POST['mc_topic']);
$mc_q      = trim($_POST['mc_question']);
$a1        = trim($_POST['mc_answer1']);
$a2        = trim($_POST['mc_answer2']);
a3        = trim($_POST['mc_answer3']);
a4        = trim($_POST['mc_answer4']);
$correct   = $_POST['mc_correct_answer'];
$image_url = '';

try {
  if (!$conn) throw new Exception('Không kết nối được CSDL');

  $public_id = '';
  if (!empty($_FILES['mc_image']['tmp_name'])) {
    $public_id = $mc_id ? ('pic_' . $mc_id) : ('mc_temp_' . uniqid());
    $uploadRes = uploadImageDirectly($_FILES['mc_image']['tmp_name'], $public_id);

    if (empty($uploadRes['secure_url'])) {
      throw new Exception('Không upload được ảnh: ' . ($uploadRes['error']['message'] ?? 'Unknown error'));
    }

    $image_url = $uploadRes['secure_url'];
  }

  if ($mc_id === '') {
    $stmt = $conn->prepare("INSERT INTO mc_questions (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$mc_topic, $mc_q, $a1, $a2, $a3, $a4, $correct, $image_url]);
    $newId = $conn->lastInsertId();

    respond(true, 'Đã thêm mới', ['id' => $newId]);
  } else {
    if (!is_numeric($mc_id)) throw new Exception('ID không hợp lệ.');

    $stmt = $conn->prepare("UPDATE mc_questions SET mc_topic=?, mc_question=?, mc_answer1=?, mc_answer2=?, mc_answer3=?, mc_answer4=?, mc_correct_answer=? WHERE mc_id=?");
    $stmt->execute([$mc_topic, $mc_q, $a1, $a2, $a3, $a4, $correct, $mc_id]);

    if (!empty($image_url)) {
      $stmt = $conn->prepare("UPDATE mc_questions SET mc_image_url=? WHERE mc_id=?");
      $stmt->execute([$image_url, $mc_id]);
    }

    respond(true, 'Đã cập nhật');
  }

} catch (Exception $e) {
  respond(false, $e->getMessage());
}
