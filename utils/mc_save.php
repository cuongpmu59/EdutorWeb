<?php
require_once __DIR__ . '/../../db_connection.php';
require_once __DIR__ . '/../../dotenv.php';
require_once __DIR__ . '/cloudinary_upload.php';

header('Content-Type: text/html; charset=UTF-8');

function sanitize($value) {
  return trim(htmlspecialchars($value));
}

$mc_id = $_POST['mc_id'] ?? '';
$mc_topic = sanitize($_POST['mc_topic'] ?? '');
$mc_question = $_POST['mc_question'] ?? '';
$mc_answer1 = $_POST['mc_answer1'] ?? '';
$mc_answer2 = $_POST['mc_answer2'] ?? '';
$mc_answer3 = $_POST['mc_answer3'] ?? '';
$mc_answer4 = $_POST['mc_answer4'] ?? '';
$mc_correct = $_POST['mc_correct_answer'] ?? '';

$imageUrl = null;
$tempImageUploaded = false;

// Nếu có ảnh được upload mới
if (!empty($_FILES['mc_image']['tmp_name'])) {
  $uploadResult = uploadImageToCloudinary($_FILES['mc_image']['tmp_name'], 'mc_temp');
  if ($uploadResult['success']) {
    $imageUrl = $uploadResult['url'];
    $publicId = $uploadResult['public_id'];
    $tempImageUploaded = true;
  }
}

try {
  if ($mc_id === '') {
    // THÊM MỚI
    $stmt = $conn->prepare("INSERT INTO mc_questions (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image)
                            VALUES (?, ?, ?, ?, ?, ?, ?, '')");
    $stmt->execute([$mc_topic, $mc_question, $mc_answer1, $mc_answer2, $mc_answer3, $mc_answer4, $mc_correct]);

    $newId = $conn->lastInsertId();

    // Nếu có ảnh thì đổi tên và lưu URL mới
    if ($tempImageUploaded) {
      $renameResult = renameImageOnCloudinary($publicId, 'mc_' . $newId);  // ✅ đổi tên thành mc_{mc_id}
      if ($renameResult['success']) {
        $finalUrl = $renameResult['url'];
        $updateStmt = $conn->prepare("UPDATE mc_questions SET mc_image = ? WHERE id = ?");
        $updateStmt->execute([$finalUrl, $newId]);
      }
    }

    echo "<script>parent.postMessage({type: 'saved'}, '*');</script>";
  } else {
    // CẬP NHẬT
    $imageClause = '';
    $params = [$mc_topic, $mc_question, $mc_answer1, $mc_answer2, $mc_answer3, $mc_answer4, $mc_correct];

    if ($tempImageUploaded) {
      $renameResult = renameImageOnCloudinary($publicId, 'mc_' . $mc_id);  // ✅ đổi tên thành mc_{mc_id}
      if ($renameResult['success']) {
        $imageUrl = $renameResult['url'];
        $imageClause = ", mc_image = ?";
        $params[] = $imageUrl;
      }
    }

    $params[] = $mc_id;

    $sql = "UPDATE mc_questions SET
              mc_topic = ?, mc_question = ?, mc_answer1 = ?, mc_answer2 = ?, mc_answer3 = ?, mc_answer4 = ?, mc_correct_answer = ?
              $imageClause
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    echo "<script>parent.postMessage({type: 'saved'}, '*');</script>";
  }
} catch (Exception $e) {
  echo "<script>parent.postMessage({type: 'error', message: 'Lỗi CSDL: " . $e->getMessage() . "'}, '*');</script>";
}
