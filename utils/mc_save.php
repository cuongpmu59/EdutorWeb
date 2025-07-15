<?php
require_once __DIR__ . '/../../db_connection.php';
require_once __DIR__ . '/../../dotenv.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Chỉ chấp nhận POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  exit('❌ Chỉ chấp nhận POST');
}

// Nhận dữ liệu
$id       = $_POST['mc_id'] ?? '';
$topic    = $_POST['mc_topic'] ?? '';
$question = $_POST['mc_question'] ?? '';
$answer1  = $_POST['mc_answer1'] ?? '';
$answer2  = $_POST['mc_answer2'] ?? '';
$answer3  = $_POST['mc_answer3'] ?? '';
$answer4  = $_POST['mc_answer4'] ?? '';
$correct  = $_POST['mc_correct_answer'] ?? '';
$imageUrl = '';
$publicId = '';

// === Upload ảnh tạm nếu có ===
if (isset($_FILES['mc_image']) && $_FILES['mc_image']['error'] === UPLOAD_ERR_OK) {
  $tmpName   = $_FILES['mc_image']['tmp_name'];
  $imageData = base64_encode(file_get_contents($tmpName));
  $mimeType  = mime_content_type($tmpName);

  $cloudName    = getenv('CLOUDINARY_CLOUD_NAME');
  $uploadPreset = getenv('CLOUDINARY_UPLOAD_PRESET');
  $uploadUrl    = "https://api.cloudinary.com/v1_1/$cloudName/image/upload";

  $postData = [
    'file' => "data:$mimeType;base64,$imageData",
    'upload_preset' => $uploadPreset,
    'folder' => 'mc_questions'
  ];

  $ch = curl_init($uploadUrl);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
  $response = curl_exec($ch);
  curl_close($ch);

  $result = json_decode($response, true);
  if (isset($result['secure_url'])) {
    $imageUrl = $result['secure_url'];
    $publicId = $result['public_id']; // Tên gốc trên Cloudinary
  }
}

try {
  // === Cập nhật ===
  if ($id) {
    $stmt = $conn->prepare("
      UPDATE mc_questions SET
        mc_topic = ?, mc_question = ?,
        mc_answer1 = ?, mc_answer2 = ?, mc_answer3 = ?, mc_answer4 = ?,
        mc_correct_answer = ?, mc_image_url = ?
      WHERE mc_id = ?
    ");
    $stmt->execute([
      $topic, $question,
      $answer1, $answer2, $answer3, $answer4,
      $correct, $imageUrl, $id
    ]);
  } else {
    // === Thêm mới ===
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

    // Lấy ID vừa chèn
    $id = $conn->lastInsertId();

    // === Đổi tên ảnh nếu có ===
    if (!empty($publicId)) {
      $newPublicId = 'mc_questions/mc_' . $id;

      $timestamp = time();
      $apiKey    = getenv('CLOUDINARY_API_KEY');
      $apiSecret = getenv('CLOUDINARY_API_SECRET');

      $signatureData = "from_public_id=$publicId&to_public_id=$newPublicId&timestamp=$timestamp$apiSecret";
      $signature = sha1($signatureData);

      $renameData = [
        'from_public_id' => $publicId,
        'to_public_id'   => $newPublicId,
        'timestamp'      => $timestamp,
        'api_key'        => $apiKey,
        'signature'      => $signature
      ];

      $renameUrl = "https://api.cloudinary.com/v1_1/$cloudName/image/rename";

      $ch2 = curl_init($renameUrl);
      curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch2, CURLOPT_POST, true);
      curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query($renameData));
      $renameResponse = curl_exec($ch2);
      curl_close($ch2);

      $renameResult = json_decode($renameResponse, true);

      if (!empty($renameResult['secure_url'])) {
        $imageUrl = $renameResult['secure_url'];

        // Cập nhật lại URL trong bảng
        $conn->prepare("UPDATE mc_questions SET mc_image_url = ? WHERE mc_id = ?")
             ->execute([$imageUrl, $id]);
      }
    }
  }

  // ✅ Thành công
  echo '<script>parent.postMessage({ type: "saved" }, "*");</script>';
} catch (PDOException $e) {
  echo '<script>parent.postMessage({ type: "error", message: "' . $e->getMessage() . '" }, "*");</script>';
}
