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

try {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $imageChanged = !empty($_FILES['image']['tmp_name']);

    // ================= INSERT =================
    if ($action === 'insert') {
      $image_url = null;
      if ($imageChanged) {
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
        echo json_encode(['error' => 'ID không hợp lệ']);
        exit;
      }

      $stmt = $conn->prepare("SELECT mc_image_url FROM mc_questions WHERE mc_id = :id");
      $stmt->execute([':id' => $mc_id]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $old_image_url = $row['mc_image_url'] ?? null;

      $new_image_url = $old_image_url;

      // Nếu có ảnh mới → xóa ảnh cũ, upload mới
      if ($imageChanged) {
        if (!empty($old_image_url)) {
          $parsed = parse_url($old_image_url);
          $parts = explode('/', $parsed['path']);
          $filename = end($parts);
          $public_id = pathinfo($filename, PATHINFO_FILENAME);
          $folder = implode('/', array_slice($parts, array_search('upload', $parts) + 2, -1));
          if ($folder) $public_id = $folder . '/' . $public_id;
          Uploader::destroy($public_id, ['invalidate' => true]);
        }

        $uploadResult = Uploader::upload($_FILES['image']['tmp_name'], [
          'folder' => 'mc_images',
          'invalidate' => true
        ]);
        $new_image_url = $uploadResult['secure_url'] ?? null;
      }

      // Nếu không có ảnh mới nhưng user chọn xoá ảnh cũ
      if (!$imageChanged && isset($_POST['delete_image']) && $_POST['delete_image'] === 'true') {
        if (!empty($old_image_url)) {
          $parsed = parse_url($old_image_url);
          $parts = explode('/', $parsed['path']);
          $filename = end($parts);
          $public_id = pathinfo($filename, PATHINFO_FILENAME);
          $folder = implode('/', array_slice($parts, array_search('upload', $parts) + 2, -1));
          if ($folder) $public_id = $folder . '/' . $public_id;
          Uploader::destroy($public_id, ['invalidate' => true]);
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
    if (isset($_POST['delete_mc_id'])) {
      $mc_id = filter_input(INPUT_POST, 'delete_mc_id', FILTER_VALIDATE_INT);
      if (!$mc_id) {
        echo json_encode(['error' => '❌ delete_mc_id không hợp lệ']);
        http_response_code(400);
        exit;
      }

      // Xoá ảnh nếu có
      $stmt = $conn->prepare("SELECT mc_image_url FROM mc_questions WHERE mc_id = :mc_id");
      $stmt->execute(['mc_id' => $mc_id]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($row && !empty($row['mc_image_url'])) {
        $image_url = $row['mc_image_url'];
        $parsed_url = parse_url($image_url);
        $path_parts = explode('/', $parsed_url['path']);
        $filename = end($path_parts);
        $public_id = pathinfo($filename, PATHINFO_FILENAME);
        $folder_parts = array_slice($path_parts, array_search('upload', $path_parts) + 2, -1);
        if (!empty($folder_parts)) {
          $public_id = implode('/', $folder_parts) . '/' . $public_id;
        }
        Uploader::destroy($public_id, ['invalidate' => true]);
      }

      // Xoá dòng DB
      $stmt = $conn->prepare("DELETE FROM mc_questions WHERE mc_id = :mc_id");
      $stmt->execute(['mc_id' => $mc_id]);

      echo json_encode(['success' => true]);
      exit;
    }

    // ================= DELETE IMAGE ONLY =================
    if (isset($_POST['delete_image']) && $_POST['delete_image'] === 'true' && isset($_POST['mc_id'])) {
      $mc_id = filter_input(INPUT_POST, 'mc_id', FILTER_VALIDATE_INT);
      if (!$mc_id) {
        echo json_encode(['error' => '❌ mc_id không hợp lệ']);
        exit;
      }

      $stmt = $conn->prepare("SELECT mc_image_url FROM mc_questions WHERE mc_id = :id");
      $stmt->execute([':id' => $mc_id]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($row && !empty($row['mc_image_url'])) {
        $image_url = $row['mc_image_url'];
        $parsed = parse_url($image_url);
        $parts = explode('/', $parsed['path']);
        $filename = end($parts);
        $public_id = pathinfo($filename, PATHINFO_FILENAME);
        $folder = implode('/', array_slice($parts, array_search('upload', $parts) + 2, -1));
        if ($folder) $public_id = $folder . '/' . $public_id;
        Uploader::destroy($public_id, ['invalidate' => true]);
      }

      $stmt = $conn->prepare("UPDATE mc_questions SET mc_image_url = NULL WHERE mc_id = :id");
      $stmt->execute([':id' => $mc_id]);

      echo json_encode(['success' => true]);
      exit;
    }

    // ================= GET SINGLE =================
    if (isset($_POST['mc_id'])) {
      $mc_id = filter_input(INPUT_POST, 'mc_id', FILTER_VALIDATE_INT);
      if (!$mc_id) {
        echo json_encode(['error' => '❌ mc_id không hợp lệ']);
        http_response_code(400);
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
  echo json_encode([
    'error' => '❌ Lỗi hệ thống: ' . $e->getMessage()
  ]);
  http_response_code(500);
  exit;
}
