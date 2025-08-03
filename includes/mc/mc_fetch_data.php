<?php
require_once __DIR__ . '/../../includes/db_connection.php';
require_once __DIR__ . '/../env/config.php'; // Kết nối Cloudinary
require_once __DIR__ . '/../vendor/autoload.php';
use Cloudinary\Uploader;

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

try {

// ✅ INSERT hoặc UPDATE

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  require_once __DIR__ . '/../includes/db_connection.php';

  $action = $_POST['action'] ?? '';

  if ($action === 'insert') {
    // Lấy dữ liệu từ POST
    $topic = $_POST['mc_topic'] ?? '';
    $question = $_POST['mc_question'] ?? '';
    // ... các trường khác
    $image_url = $_POST['image_url'] ?? '';

    $stmt = $conn->prepare("INSERT INTO mc_questions (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url) VALUES (:topic, :question, :a1, :a2, :a3, :a4, :correct, :image_url)");
    $stmt->execute([
      ':topic' => $topic,
      ':question' => $question,
      ':a1' => $_POST['mc_answer1'] ?? '',
      ':a2' => $_POST['mc_answer2'] ?? '',
      ':a3' => $_POST['mc_answer3'] ?? '',
      ':a4' => $_POST['mc_answer4'] ?? '',
      ':correct' => $_POST['mc_correct_answer'] ?? '',
      ':image_url' => $image_url,
    ]);

    echo json_encode(['success' => true]);
    exit;
  }

  if ($action === 'update') {
    $mc_id = filter_input(INPUT_POST, 'mc_id', FILTER_VALIDATE_INT);
    if (!$mc_id) {
      echo json_encode(['error' => 'ID không hợp lệ']);
      exit;
    }

    $stmt = $conn->prepare("UPDATE mc_questions SET mc_topic = :topic, mc_question = :question, mc_answer1 = :a1, mc_answer2 = :a2, mc_answer3 = :a3, mc_answer4 = :a4, mc_correct_answer = :correct, mc_image_url = :image_url WHERE mc_id = :id");
    $stmt->execute([
      ':id' => $mc_id,
      ':topic' => $_POST['mc_topic'] ?? '',
      ':question' => $_POST['mc_question'] ?? '',
      ':a1' => $_POST['mc_answer1'] ?? '',
      ':a2' => $_POST['mc_answer2'] ?? '',
      ':a3' => $_POST['mc_answer3'] ?? '',
      ':a4' => $_POST['mc_answer4'] ?? '',
      ':correct' => $_POST['mc_correct_answer'] ?? '',
      ':image_url' => $_POST['image_url'] ?? '',
    ]);

    echo json_encode(['success' => true]);
    exit;
  }
}

  // ✅ DELETE - Nếu có POST delete_mc_id

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_mc_id'])) {
    $mc_id = filter_input(INPUT_POST, 'delete_mc_id', FILTER_VALIDATE_INT);
  
    if (!$mc_id) {
      echo json_encode(['error' => '❌ delete_mc_id không hợp lệ']);
      http_response_code(400);
      exit;
    }
  
    // 🔍 Truy vấn ảnh liên quan (nếu có)
    $stmt = $conn->prepare("SELECT mc_image_url FROM mc_questions WHERE mc_id = :mc_id");
    $stmt->execute(['mc_id' => $mc_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
  
    if ($row && !empty($row['mc_image_url'])) {
      $image_url = $row['mc_image_url'];
      $parsed_url = parse_url($image_url);
      $path_parts = explode('/', $parsed_url['path']); // ex: /db/image/upload/v1234/folder/image.png
      $filename = end($path_parts); // image.png
      $public_id = pathinfo($filename, PATHINFO_FILENAME);
  
      // Nếu ảnh nằm trong thư mục (ví dụ: mc_temp/image.png)
      $folder_parts = array_slice($path_parts, array_search('upload', $path_parts) + 2, -1); // sau "upload", bỏ "v..."
      if (!empty($folder_parts)) {
        $public_id = implode('/', $folder_parts) . '/' . $public_id;
      }
  
      // 🗑 Xoá ảnh Cloudinary (nếu có)
      try {
        $cloudResult = Uploader::destroy($public_id, ['invalidate' => true]);
      } catch (Exception $e) {
        // Ghi log nếu cần
      }
    }
  
    // 🧹 Xoá khỏi DB
    $stmt = $conn->prepare("DELETE FROM mc_questions WHERE mc_id = :mc_id");
    $stmt->execute(['mc_id' => $mc_id]);
  
    if ($stmt->rowCount() > 0) {
      echo json_encode(['success' => true]);
    } else {
      echo json_encode(['error' => '❌ Không tìm thấy câu hỏi để xoá']);
      http_response_code(404);
    }
    exit;
  }

  // ✅ GET một bản ghi nếu có POST mc_id
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mc_id'])) {
    $mc_id = filter_input(INPUT_POST, 'mc_id', FILTER_VALIDATE_INT);

    if (!$mc_id) {
      echo json_encode(['success' => false, 'message' => '❌ delete_mc_id không hợp lệ']);
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

    if ($data) {
      echo json_encode($data);
    } else {
      echo json_encode(['error' => '❌ Không tìm thấy dữ liệu']);
      http_response_code(404);
    }
    exit;
  }

  // ✅ GET toàn bộ danh sách (nếu không có POST mc_id hoặc delete_mc_id)
  $stmt = $conn->query("
    SELECT mc_id, mc_topic, mc_question, 
           mc_answer1, mc_answer2, mc_answer3, mc_answer4, 
           mc_correct_answer, mc_image_url
    FROM mc_questions
    ORDER BY mc_id DESC
  ");
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode(['data' => $rows]);

} catch (PDOException $e) {
  echo json_encode(['data' => [], 'error' => '❌ Lỗi truy vấn: ' . $e->getMessage()]);
  http_response_code(500);
}

