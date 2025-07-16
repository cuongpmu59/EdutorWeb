<?php
require_once __DIR__ . '/../../db_connection.php';
require_once __DIR__ . '/../../dotenv.php';
require_once __DIR__ . '/cloudinary_upload.php';
require_once __DIR__ . '/cloudinary_rename.php';
require_once __DIR__ . '/delete_cloudinary_image.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/html; charset=UTF-8');

// 🧹 Làm sạch dữ liệu đầu vào
function sanitize($value) {
  return trim(htmlspecialchars($value));
}

// 📥 Nhận dữ liệu POST
$mc_id       = $_POST['mc_id'] ?? '';
$mc_topic    = sanitize($_POST['mc_topic'] ?? '');
$mc_question = $_POST['mc_question'] ?? '';
$mc_answer1  = $_POST['mc_answer1'] ?? '';
$mc_answer2  = $_POST['mc_answer2'] ?? '';
$mc_answer3  = $_POST['mc_answer3'] ?? '';
$mc_answer4  = $_POST['mc_answer4'] ?? '';
$mc_correct  = $_POST['mc_correct_answer'] ?? '';

$imageUrl = null;
$tempImageUploaded = false;
$publicId = null;

// 📤 Upload ảnh tạm nếu có
if (!empty($_FILES['mc_image']['tmp_name'])) {
  try {
    $uploadResult = uploadImageToCloudinary($_FILES['mc_image']['tmp_name'], 'mc_temp');
    if ($uploadResult['success']) {
      $imageUrl = $uploadResult['url'];
      $publicId = $uploadResult['public_id'];
      $tempImageUploaded = true;
    }
  } catch (Exception $e) {
    echo "<script>parent.postMessage({type: 'error', message: 'Lỗi upload ảnh: " . $e->getMessage() . "'}, '*');</script>";
    exit;
  }
}

try {
  if ($mc_id === '') {
    // ➕ Thêm mới
    $stmt = $conn->prepare("INSERT INTO mc_questions 
      (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url) 
      VALUES (?, ?, ?, ?, ?, ?, ?, '')");

    $stmt->execute([$mc_topic, $mc_question, $mc_answer1, $mc_answer2, $mc_answer3, $mc_answer4, $mc_correct]);

    $newId = $conn->lastInsertId();

    // 🔄 Nếu có ảnh -> đổi tên ảnh temp thành mc_{id}
    if ($tempImageUploaded) {
      $renameResult = renameImageOnCloudinary($publicId, 'mc_' . $newId);
      if ($renameResult['success']) {
        $finalUrl = $renameResult['url'];
        $updateStmt = $conn->prepare("UPDATE mc_questions SET mc_image_url = ? WHERE id = ?");
        $updateStmt->execute([$finalUrl, $newId]);
      }
    }

    echo "<script>parent.postMessage({type: 'saved'}, '*');</script>";

  } else {
    // 🔁 Cập nhật
    $imageClause = '';
    $params = [$mc_topic, $mc_question, $mc_answer1, $mc_answer2, $mc_answer3, $mc_answer4, $mc_correct];

    if ($tempImageUploaded) {
      // 🗂 Lấy URL ảnh cũ
      $stmtOld = $conn->prepare("SELECT mc_image_url FROM mc_questions WHERE id = ?");
      $stmtOld->execute([$mc_id]);
      $oldImage = $stmtOld->fetchColumn();

      // 🧹 Xoá ảnh cũ nếu có
      if ($oldImage && preg_match('/\/([^\/]+)\.(jpg|jpeg|png|gif|webp)$/', $oldImage, $matches)) {
        $oldPublicId = $matches[1] ?? null;
        if ($oldPublicId) deleteImageFromCloudinary($oldPublicId);
      }

      // 🔄 Đổi tên ảnh tạm sang mc_{id}
      $renameResult = renameImageOnCloudinary($publicId, 'mc_' . $mc_id);
      if ($renameResult['success']) {
        $imageUrl = $renameResult['url'];
        $imageClause = ", mc_image_url = ?";
        $params[] = $imageUrl;
      }
    }

    $params[] = $mc_id;

    $sql = "UPDATE mc_questions SET
              mc_topic = ?, mc_question = ?, mc_answer1 = ?, mc_answer2 = ?, 
              mc_answer3 = ?, mc_answer4 = ?, mc_correct_answer = ?
              $imageClause
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    echo "<script>parent.postMessage({type: 'saved'}, '*');</script>";
  }
} catch (Exception $e) {
  echo "<script>parent.postMessage({type: 'error', message: 'Lỗi CSDL: " . $e->getMessage() . "'}, '*');</script>";
}
