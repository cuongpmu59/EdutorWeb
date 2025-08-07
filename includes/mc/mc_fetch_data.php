<?php
require_once __DIR__ . '/../db_connection.php';

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

try {
  // Nếu có POST mc_id → Trả về dữ liệu 1 dòng chi tiết (form)
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mc_id'])) {
    $mc_id = filter_input(INPUT_POST, 'mc_id', FILTER_VALIDATE_INT);
    if (!$mc_id) {
      echo json_encode(['error' => '❌ mc_id không hợp lệ.']);
      exit;
    }

    $stmt = $conn->prepare("SELECT * FROM multiple_choice WHERE mc_id = ?");
    $stmt->execute([$mc_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
      echo json_encode($row);
    } else {
      echo json_encode(['error' => '❌ Không tìm thấy dữ liệu.']);
    }
    exit;
  }

  // Nếu là POST dùng cho DataTables → trả về danh sách
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->query("SELECT mc_id, mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url FROM multiple_choice ORDER BY mc_id DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['data' => $rows]);
    exit;
  }

  // Nếu không đúng POST → trả về lỗi
  echo json_encode(['error' => '❌ Phương thức không được hỗ trợ.']);
  http_response_code(405);

} catch (Exception $e) {
  echo json_encode(['error' => '❌ Lỗi máy chủ: ' . $e->getMessage()]);
  http_response_code(500);
}
