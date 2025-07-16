<?php
require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/../dotenv.php';
require_once __DIR__ . '/cloudinary_upload.php';
require_once __DIR__ . '/cloudinary_rename.php';

header('Content-Type: application/json');

function respond($success, $message = '', $extra = []) {
  echo json_encode(array_merge([
    'success' => $success,
    'message' => $message
  ], $extra));
  exit;
}

if (!isset($_POST['mc_topic'], $_POST['mc_question'], $_POST['mc_answer1'], $_POST['mc_correct_answer'])) {
  respond(false, 'Thiếu dữ liệu bắt buộc.');
}

$mc_id     = $_POST['mc_id'] ?? '';
$mc_topic  = trim($_POST['mc_topic']);
$mc_q      = trim($_POST['mc_question']);
$a1        = trim($_POST['mc_answer1']);
$a2        = trim($_POST['mc_answer2']);
$a3        = trim($_POST['mc_answer3']);
$a4        = trim($_POST['mc_answer4']);
$correct   = $_POST['mc_correct_answer'];
$image_url = '';

try {
  if (!$conn) throw new Exception('Không kết nối được CSDL');

  // === UPLOAD ẢNH nếu có ===
  if (!empty($_FILES['mc_image']['tmp_name'])) {
    $uploadRes = uploadToCloudinary($_FILES['mc_image']['tmp_name'], 'mc_temp');
    if (!$uploadRes['secure_url']) throw new Exception('Không upload được ảnh.');
    $image_url = $uploadRes['secure_url'];
    $public_id = $uploadRes['public_id'];
  }

  // === THÊM MỚI ===
  if ($mc_id === '') {
    $stmt = $conn->prepare("INSERT INTO mc_questions (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$mc_topic, $mc_q, $a1, $a2, $a3, $a4, $correct, $image_url]);
    $newId = $conn->lastInsertId();

    // Đổi tên ảnh nếu có
    if (!empty($image_url)) {
      $newPublicId = 'pic_' . $newId;
      $renamedUrl = renameCloudinaryImage($public_id, $newPublicId);
      if ($renamedUrl) {
        $conn->prepare("UPDATE mc_questions SET mc_image_url = ? WHERE mc_id = ?")
             ->execute([$renamedUrl, $newId]);
      }
    }
    respond(true, 'Đã thêm mới', ['id' => $newId]);
  }

  // === CẬP NHẬT ===
  if (!is_numeric($mc_id)) throw new Exception('ID không hợp lệ.');
  $stmt = $conn->prepare("UPDATE mc_questions SET mc_topic=?, mc_question=?, mc_answer1=?, mc_answer2=?, mc_answer3=?, mc_answer4=?, mc_correct_answer=? WHERE mc_id=?");
  $stmt->execute([$mc_topic, $mc_q, $a1, $a2, $a3, $a4, $correct, $mc_id]);

  // Nếu có ảnh mới -> cập nhật lại ảnh
  if (!empty($image_url)) {
    $newPublicId = 'pic_' . $mc_id;
    $renamedUrl = renameCloudinaryImage($public_id, $newPublicId);
    if ($renamedUrl) {
      $conn->prepare("UPDATE mc_questions SET mc_image_url = ? WHERE mc_id = ?")
           ->execute([$renamedUrl, $mc_id]);
    }
  }

  respond(true, 'Đã cập nhật');

} catch (Exception $e) {
  respond(false, $e->getMessage());
}
