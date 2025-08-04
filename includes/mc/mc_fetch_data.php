<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/db_connection.php';
require_once __DIR__ . '/../../env/config.php';
require_once __DIR__ . '/../../vendor/autoload.php';
use Cloudinary\Uploader;

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

// Hàm hỗ trợ lấy public_id từ URL Cloudinary
function getPublicIdFromUrl($url) {
  $parsed = parse_url($url);
  $parts = explode('/', $parsed['path']);
  $filename = end($parts);
  $public_id = pathinfo($filename, PATHINFO_FILENAME);
  $folder = implode('/', array_slice($parts, array_search('upload', $parts) + 2, -1));
  return $folder ? $folder . '/' . $public_id : $public_id;
}

// Hàm hỗ trợ kiểm tra định dạng ảnh
function isValidImage($file) {
  $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
  return in_array($file['type'], $allowedTypes);
}

try {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $imageChanged = !empty($_FILES['image']['tmp_name']);

    // ================= INSERT =================
    if ($action === 'insert') {
      $image_url = null;
      if ($imageChanged) {
        if (!isValidImage($_FILES['image'])) {
          http_response_code(400);
          echo json_encode(['error' => '❌ Định dạng ảnh không hợp lệ']);
          exit;
        }

        $uploadResult = Uploader::upload($_FILES['image']['tmp_name'], [
          'folder' => 'mc_images',
          'invalidate' => true
        ]);
        $image_url = $uploadResult['secure_url'] ?? null;
      }

      $stmt = $conn->prepare("INSERT INTO mc_questions (
        mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4,
        mc_correct_answer, mc_image_url
      ) VALUES (
        :topic, :question, :a1, :a2, :a3, :a4, :correct, :image_url
      )");

      $stmt->execute([
        ':topic' => $_POST['mc_topic'] ?? '',
        ':question' => $_POST['mc_question'] ?? '',
        ':a1' => $_POST['mc_answer1'] ?? '',
        ':a2' => $_POST['mc_answer2'] ?? '',
        ':a3' => $_POST['mc_answer3'] ?? '',
        ':a4' => $_POST['mc_answer4'] ?? '',
        ':correct' => $_POST['mc_correct_answer'] ?? '',
        ':image_url' => $image_url
      ]);

      echo json_encode(['success' => true]);
      exit;
    }

    // ================= UPDATE =================
    if ($action === 'update') {
      $mc_id = filter_input(INPUT_POST, 'mc_id', FILTER_VALIDATE_INT);
      if (!$mc_id) {
        http_response_code(400);
        echo json_encode(['error' => '❌ mc_id không hợp lệ']);
        exit;
      }

      $stmt = $conn->prepare("SELECT mc_image_url FROM mc_questions WHERE mc_id = :id");
      $stmt->execute([':id' => $mc_id]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $old_image_url = $row['mc_image_url'] ?? null;
      $new_image_url = $old_image_url;

      // Nếu có ảnh mới → Xoá ảnh cũ rồi upload mới
      if ($imageChanged) {
        if (!isValidImage($_FILES['image'])) {
          http_response_code(400);
          echo json_encode(['error' => '❌ Định dạng ảnh không hợp lệ']);
          exit;
        }

        if (!empty($old_image_url)) {
          $public_id = getPublicIdFromUrl($old_image_url);
          $result = Uploader::destroy($public_id, ['invalidate' => true]);
        }

        $uploadResult = Uploader::upload($_FILES['image']['tmp_name'], [
          'folder' => 'mc_images',
          'invalidate' => true
        ]);
        $new_image_url = $uploadResult['secure_url'] ?? null;
      }

      // Nếu chọn xoá ảnh mà không có ảnh mới
      if (!$imageChanged && ($_POST['delete_image'] ?? '') === 'true') {
        if (!empty($old_image_url)) {
          $public_id = getPublicIdFromUrl($old_image_url);
          $result = Uploader::destroy($public_id, ['invalidate' => true]);
        }
        $new_image_url = null;
      }

      $stmt = $conn->prepare("UPDATE mc_questions SET 
        mc_topic = :topic, mc_question = :question, mc_answer1 = :a1, 
        mc_answer2 = :a2, mc_answer3 = :a3, mc_answer4 = :a4, 
        mc_correct_answer = :correct, mc_image_url = :image_url 
        WHERE mc_id = :id
      ");

      $stmt->execute([
        ':id' => $mc_id,
        ':topic' => $_POST['mc_topic'] ?? '',
        ':question' => $_POST['mc_question'] ?? '',
        ':a1' => $_POST['mc_answer1'] ?? '',
        ':a2' => $_POST['mc_answer2'] ?? '',
        ':a3' => $_POST['mc_answer3'] ?? '',
        ':a4' => $_POST['mc_answer4'] ?? '',
        ':correct' => $_POST['mc_correct_answer'] ?? '',
        ':image_url' => $new_image_url
      ]);

      echo json_encode(['success' => true]);
      exit;
    }

    // ================= DELETE RECORD =================
    if ($action === 'delete' && isset($_POST['delete_mc_id'])) {
      $mc_id = filter_input(INPUT_POST, 'delete_mc_id', FILTER_VALIDATE_INT);
      if (!$mc_id) {
        http_response_code(400);
        echo json_encode(['error' => '❌ delete_mc_id không hợp lệ']);
        exit;
      }

      $stmt = $conn->prepare("SELECT mc_image_url FROM mc_questions WHERE mc_id = :mc_id");
      $stmt->execute(['mc_id' => $mc_id]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($row && !empty($row['mc_image_url'])) {
        $public_id = getPublicIdFromUrl($row['mc_image_url']);
        $result = Uploader::destroy($public_id, ['invalidate' => true]);
      }

      $stmt = $conn->prepare("DELETE FROM mc_questions WHERE mc_id = :mc_id");
      $stmt->execute(['mc_id' => $mc_id]);

      echo json_encode(['success' => true]);
      exit;
    }

    // ================= DELETE IMAGE ONLY =================
    if ($action === 'delete_image' && isset($_POST['mc_id'])) {
      $mc_id = filter_input(INPUT_POST, 'mc_id', FILTER_VALIDATE_INT);
      if (!$mc_id) {
        http_response_code(400);
        echo json_encode(['error' => '❌ mc_id không hợp lệ']);
        exit;
      }

      $stmt = $conn->prepare("SELECT mc_image_url FROM mc_questions WHERE mc_id = :id");
      $stmt->execute([':id' => $mc_id]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($row && !empty($row['mc_image_url'])) {
        $public_id = getPublicIdFromUrl($row['mc_image_url']);
        $result = Uploader::destroy($public_id, ['invalidate' => true]);
      }

      $stmt = $conn->prepare("UPDATE mc_questions SET mc_image_url = NULL WHERE mc_id = :id");
      $stmt->execute([':id' => $mc_id]);

      echo json_encode(['success' => true]);
      exit;
    }

    // ================= GET SINGLE =================
    if ($action === 'get_single' && isset($_POST['mc_id'])) {
      $mc_id = filter_input(INPUT_POST, 'mc_id', FILTER_VALIDATE_INT);
      if (!$mc_id) {
        http_response_code(400);
        echo json_encode(['error' => '❌ mc_id không hợp lệ']);
        exit;
      }

      $stmt = $conn->prepare("
        SELECT mc_id, mc_topic, mc_question, 
               mc_answer1, mc_answer2, mc_answer3, mc_answer4, 
               mc_correct_answer, mc_image_url
        FROM mc_questions
        WHERE mc_id = :mc_id
        LIMIT 1
      ");
      $stmt->execute(['mc_id' => $mc_id]);
      $data = $stmt->fetch(PDO::FETCH_ASSOC);

      echo $data ? json_encode($data) : json_encode(['error' => '❌ Không tìm thấy dữ liệu']);
      exit;
    }
  }

  // ================= GET ALL =================
  $stmt = $conn->query("
    SELECT mc_id, mc_topic, mc_question, 
           mc_answer1, mc_answer2, mc_answer3, mc_answer4, 
           mc_correct_answer, mc_image_url
    FROM mc_questions
    ORDER BY mc_id DESC
  ");
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode(['data' => $rows]);
  exit;

} catch (Exception $e) {
  http_response_code(500);
  echo json_encode([
    'error' => '❌ Lỗi hệ thống: ' . $e->getMessage()
  ]);
  exit;
}
