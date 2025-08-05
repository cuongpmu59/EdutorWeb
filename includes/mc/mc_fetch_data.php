<?php
require_once __DIR__ . '/../../includes/db_connection.php';
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

try {
  // ✅ DELETE - Nếu có POST delete_mc_id
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_mc_id'])) {
    $mc_id = filter_input(INPUT_POST, 'delete_mc_id', FILTER_VALIDATE_INT);

    if (!$mc_id) {
      echo json_encode(['error' => '❌ delete_mc_id không hợp lệ']);
      http_response_code(400);
      exit;
    }

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

// ✅ Xử lý Lưu
try {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Kiểm tra các trường bắt buộc
    $requiredFields = ['mc_topic', 'mc_question', 'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4', 'mc_correct_answer'];
    foreach ($requiredFields as $field) {
      if (empty($_POST[$field])) {
        echo json_encode(['error' => "❌ Thiếu trường bắt buộc: $field"]);
        http_response_code(400);
        exit;
      }
    }

    // Lấy dữ liệu form
    $data = [
      'mc_topic' => trim($_POST['mc_topic']),
      'mc_question' => trim($_POST['mc_question']),
      'mc_answer1' => trim($_POST['mc_answer1']),
      'mc_answer2' => trim($_POST['mc_answer2']),
      'mc_answer3' => trim($_POST['mc_answer3']),
      'mc_answer4' => trim($_POST['mc_answer4']),
      'mc_correct_answer' => trim($_POST['mc_correct_answer']),
    ];

    // Xử lý ảnh
    $image_url = null;

    // Nếu có ảnh mới upload
    if (isset($_FILES['mc_image']) && $_FILES['mc_image']['error'] === UPLOAD_ERR_OK) {
      $uploadDir = __DIR__ . '/../../uploads/';
      if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
      }

      $filename = time() . '_' . basename($_FILES['mc_image']['name']);
      $targetPath = $uploadDir . $filename;

      if (move_uploaded_file($_FILES['mc_image']['tmp_name'], $targetPath)) {
        $image_url = 'uploads/' . $filename;
      } else {
        echo json_encode(['error' => '❌ Không thể lưu ảnh lên server']);
        http_response_code(500);
        exit;
      }
    }
    // Nếu có ảnh cũ giữ lại
    elseif (!empty($_POST['mc_image_url'])) {
      $image_url = trim($_POST['mc_image_url']);
    }

    $data['mc_image'] = $image_url;

    // Nếu có save_mc_id → UPDATE
    if (!empty($_POST['save_mc_id'])) {
      $mc_id = filter_input(INPUT_POST, 'save_mc_id', FILTER_VALIDATE_INT);
      if (!$mc_id) {
        echo json_encode(['error' => '❌ save_mc_id không hợp lệ']);
        http_response_code(400);
        exit;
      }

      $data['mc_id'] = $mc_id;

      $sql = "UPDATE mc_questions
              SET mc_topic = :mc_topic,
                  mc_question = :mc_question,
                  mc_answer1 = :mc_answer1,
                  mc_answer2 = :mc_answer2,
                  mc_answer3 = :mc_answer3,
                  mc_answer4 = :mc_answer4,
                  mc_correct_answer = :mc_correct_answer,
                  mc_image = :mc_image
              WHERE mc_id = :mc_id";

      $stmt = $conn->prepare($sql);
      $stmt->execute($data);

      echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);
      exit;
    }

    // Nếu không có save_mc_id → INSERT
    else {
      $sql = "INSERT INTO mc_questions (
                mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3,
                mc_answer4, mc_correct_answer, mc_image
              ) VALUES (
                :mc_topic, :mc_question, :mc_answer1, :mc_answer2, :mc_answer3,
                :mc_answer4, :mc_correct_answer, :mc_image
              )";

      $stmt = $conn->prepare($sql);
      $stmt->execute($data);

      echo json_encode(['success' => true, 'message' => 'Thêm mới thành công']);
      exit;
    }
  }
} catch (Exception $e) {
  echo json_encode(['error' => '❌ Lỗi server: ' . $e->getMessage()]);
  http_response_code(500);
  exit;
}
