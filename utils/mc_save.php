<?php
require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/../dotenv.php';
require_once __DIR__ . '/../cloudinary_upload.php';
require_once __DIR__ . '/../cloudinary_rename.php';

header("Content-Type: text/html; charset=UTF-8");
header("X-Frame-Options: SAMEORIGIN");

function sanitize($input) {
  return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

try {
  // ===== LẤY DỮ LIỆU TỪ FORM =====
  $mc_id             = isset($_POST['mc_id']) ? intval($_POST['mc_id']) : 0;
  $mc_topic          = sanitize($_POST['mc_topic'] ?? '');
  $mc_question       = sanitize($_POST['mc_question'] ?? '');
  $mc_answer1        = sanitize($_POST['mc_answer1'] ?? '');
  $mc_answer2        = sanitize($_POST['mc_answer2'] ?? '');
  $mc_answer3        = sanitize($_POST['mc_answer3'] ?? '');
  $mc_answer4        = sanitize($_POST['mc_answer4'] ?? '');
  $mc_correct_answer = sanitize($_POST['mc_correct_answer'] ?? '');

  if (!$mc_topic || !$mc_question || !$mc_answer1 || !$mc_answer2 || !$mc_answer3 || !$mc_answer4 || !$mc_correct_answer) {
    echo "<script>parent.postMessage({type:'error', message:'Vui lòng điền đầy đủ thông tin.'}, '*');</script>";
    exit;
  }

  $image_url = null;

  // ===== XỬ LÝ ẢNH MỚI =====
  if (isset($_FILES['mc_image']) && $_FILES['mc_image']['error'] === UPLOAD_ERR_OK) {
    $tempPath = $_FILES['mc_image']['tmp_name'];
    $publicIdTemp = 'temp_' . uniqid();
    $uploadResult = cloudinary_upload($tempPath, $publicIdTemp);

    if ($uploadResult && isset($uploadResult['secure_url'])) {
      $image_url = $uploadResult['secure_url'];
    }
  }

  // ===== THÊM MỚI =====
  if ($mc_id === 0) {
    $stmt = $conn->prepare("INSERT INTO mc_questions (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
      $mc_topic, $mc_question, $mc_answer1, $mc_answer2, $mc_answer3, $mc_answer4, $mc_correct_answer, $image_url
    ]);

    $newId = $conn->lastInsertId();

    // Nếu có ảnh thì rename thành pic_ID
    if ($image_url) {
      $renamedUrl = cloudinary_rename(basename($image_url), 'pic_' . $newId);
      if ($renamedUrl) {
        $conn->prepare("UPDATE mc_questions SET mc_image_url = ? WHERE mc_id = ?")
             ->execute([$renamedUrl, $newId]);
      }
    }

    echo "<script>parent.postMessage({type:'saved', id: $newId}, '*');</script>";
    exit;
  }

  // ===== CẬP NHẬT =====
  $sql = "UPDATE mc_questions SET 
            mc_topic = ?, 
            mc_question = ?, 
            mc_answer1 = ?, 
            mc_answer2 = ?, 
            mc_answer3 = ?, 
            mc_answer4 = ?, 
            mc_correct_answer = ?";

  $params = [$mc_topic, $mc_question, $mc_answer1, $mc_answer2, $mc_answer3, $mc_answer4, $mc_correct_answer];

  if ($image_url) {
    // Nếu upload ảnh mới khi cập nhật, thì ghi đè
    $sql .= ", mc_image_url = ?";
    $params[] = $image_url;
  }

  $sql .= " WHERE mc_id = ?";
  $params[] = $mc_id;

  $stmt = $conn->prepare($sql);
  $stmt->execute($params);

  // Nếu có ảnh, rename nó theo pic_ID
  if ($image_url) {
    $renamedUrl = cloudinary_rename(basename($image_url), 'pic_' . $mc_id);
    if ($renamedUrl) {
      $conn->prepare("UPDATE mc_questions SET mc_image_url = ? WHERE mc_id = ?")
           ->execute([$renamedUrl, $mc_id]);
    }
  }

  echo "<script>parent.postMessage({type:'saved', id: $mc_id}, '*');</script>";

} catch (Exception $e) {
  echo "<script>parent.postMessage({type:'error', message:'" . $e->getMessage() . "'}, '*');</script>";
}
