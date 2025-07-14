<?php
require_once __DIR__ . '/../../dotenv.php';
require_once __DIR__ . '/../../db_connection.php';

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

header("X-Frame-Options: SAMEORIGIN");
header("Content-Type: text/html; charset=UTF-8");

try {
  if (!isset($conn)) throw new Exception("Không kết nối được CSDL");

  $id = $_POST['mc_id'] ?? '';
  $topic = $_POST['mc_topic'] ?? '';
  $question = $_POST['mc_question'] ?? '';
  $a1 = $_POST['mc_answer1'] ?? '';
  $a2 = $_POST['mc_answer2'] ?? '';
  $a3 = $_POST['mc_answer3'] ?? '';
  $a4 = $_POST['mc_answer4'] ?? '';
  $correct = $_POST['mc_correct_answer'] ?? '';

  // Cấu hình Cloudinary
  require_once __DIR__ . '/../../vendor/autoload.php';
  Configuration::instance([
    'cloud' => [
      'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
      'api_key'    => $_ENV['CLOUDINARY_API_KEY'],
      'api_secret' => $_ENV['CLOUDINARY_API_SECRET'],
    ],
    'url' => ['secure' => true]
  ]);
  $cloudinary = new Cloudinary();

  $imageUrl = '';
  $isNewImageUploaded = !empty($_FILES['mc_image']['tmp_name']);

  // === THÊM ===
  if ($id === '') {
    // Upload ảnh tạm nếu có
    if ($isNewImageUploaded) {
      $upload = $cloudinary->uploadApi()->upload($_FILES['mc_image']['tmp_name'], [
        'folder' => 'mc_images',
        'public_id' => uniqid("tmp_", true)
      ]);
      $imageUrl = $upload['secure_url'];
    }

    // Thêm câu hỏi
    $stmt = $conn->prepare("INSERT INTO mc_questions 
      (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url) 
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$topic, $question, $a1, $a2, $a3, $a4, $correct, $imageUrl]);
    $lastId = $conn->lastInsertId();

    // Nếu có ảnh tạm thì rename về pic_{id}
    if ($isNewImageUploaded && str_contains($imageUrl, 'tmp_')) {
      $oldPublicId = basename(parse_url($imageUrl, PHP_URL_PATH), '.' . pathinfo($imageUrl, PATHINFO_EXTENSION));
      $newPublicId = "mc_$lastId";

      $cloudinary->uploadApi()->rename("mc_images/$oldPublicId", "mc_images/$newPublicId");

      // Gán lại URL mới
      $imageUrl = "https://res.cloudinary.com/{$_ENV['CLOUDINARY_CLOUD_NAME']}/image/upload/v1/mc_images/$newPublicId";
      $stmt = $conn->prepare("UPDATE mc_questions SET mc_image_url=? WHERE mc_id=?");
      $stmt->execute([$imageUrl, $lastId]);
    }

  // === CẬP NHẬT ===
  } else {
    // Lấy ảnh cũ từ DB
    $stmt = $conn->prepare("SELECT mc_image_url FROM mc_questions WHERE mc_id = ?");
    $stmt->execute([$id]);
    $oldImageUrl = $stmt->fetchColumn();

    // Nếu có ảnh mới thì upload
    if ($isNewImageUploaded) {
      // Xóa ảnh cũ khỏi Cloudinary nếu có
      if (!empty($oldImageUrl)) {
        $oldPublicId = basename(parse_url($oldImageUrl, PHP_URL_PATH), '.' . pathinfo($oldImageUrl, PATHINFO_EXTENSION));
        $cloudinary->uploadApi()->destroy("mc_images/$oldPublicId");
      }

      // Upload ảnh mới với public_id = mc_{id}
      $upload = $cloudinary->uploadApi()->upload($_FILES['mc_image']['tmp_name'], [
        'folder' => 'mc_images',
        'public_id' => "mc_$id",
        'overwrite' => true
      ]);
      $imageUrl = $upload['secure_url'];
    }

    // Cập nhật nội dung
    $params = [$topic, $question, $a1, $a2, $a3, $a4, $correct];
    $sql = "UPDATE mc_questions SET mc_topic=?, mc_question=?, mc_answer1=?, mc_answer2=?, mc_answer3=?, mc_answer4=?, mc_correct_answer=?";
    if ($imageUrl !== '') {
      $sql .= ", mc_image_url=?";
      $params[] = $imageUrl;
    }
    $sql .= " WHERE mc_id=?";
    $params[] = $id;

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
  }

  echo "<script>window.parent.postMessage({type:'saved'}, '*');</script>";
} catch (Exception $e) {
  $msg = htmlspecialchars($e->getMessage());
  echo "<script>window.parent.postMessage({type:'error', message: '$msg'}, '*');</script>";
}
?>
