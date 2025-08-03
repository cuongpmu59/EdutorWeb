<?php
require_once __DIR__ . '/../../includes/db_connection.php';
require_once __DIR__ . '/../../env/config.php';
require_once __DIR__ . '/../../vendor/autoload.php';

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

try {
  // ✅ DELETE - Nếu có POST delete_mc_id
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_mc_id'])) {
    $mc_id = filter_input(INPUT_POST, 'delete_mc_id', FILTER_VALIDATE_INT);

    if (!$mc_id) {
      echo json_encode(['error' => '❌ delete_mc_id không hợp lệ']);
      http_response_code(400);
      exit;
    }

    // Nếu có public_id → Xoá ảnh Cloudinary
    if (!empty($_POST['public_id'])) {
      // Cấu hình Cloudinary từ biến môi trường
      Configuration::instance([
        'cloud' => [
          'cloud_name' => $cloudinary_cloud_name,
          'api_key'    => $cloudinary_api_key,
          'api_secret' => $cloudinary_api_secret,
        ],
        'url' => ['secure' => true]
      ]);

      try {
        $result = (new UploadApi())->destroy($_POST['public_id']);
        // Optional: Ghi log hoặc xử lý kết quả nếu cần
      } catch (Exception $e) {
        // Không chặn xoá DB nếu lỗi Cloudinary
        error_log("❌ Cloudinary delete error: " . $e->getMessage());
      }
    }

    // Xoá trong database
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
      echo json_encode(['success' => false, 'message' => '❌ mc_id không hợp lệ']);
      http_response_code(400);
      exit;
    }

    $stmt = $conn->prepare("
      SELECT mc_id, mc_topic, mc_question, 
             mc_answer1, mc_answer2, mc_answer3, mc_answer4, 
             mc_correct_answer, mc_image_url, mc_image_public_id
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
