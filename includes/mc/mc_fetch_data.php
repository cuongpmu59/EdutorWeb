<?php
require_once __DIR__ . '/../db_connection.php';
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

try {
  $pdo = getPDOConnection();

  // ✅ Xóa bản ghi
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_mc_id'])) {
    $id = (int) $_POST['delete_mc_id'];
    $stmt = $pdo->prepare("DELETE FROM mc_questions WHERE mc_id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true]);
    exit;
  }

  // ✅ Lấy chi tiết 1 bản ghi
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mc_id'])) {
    $id = (int) $_POST['mc_id'];
    $stmt = $pdo->prepare("SELECT * FROM mc_questions WHERE mc_id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
      echo json_encode($row);
    } else {
      echo json_encode(['error' => 'Không tìm thấy dữ liệu.']);
    }
    exit;
  }

  // ✅ Thêm/Sửa bản ghi
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mc_topic'])) {
    $fields = [
      'mc_topic', 'mc_question',
      'mc_answer1', 'mc_answer2',
      'mc_answer3', 'mc_answer4',
      'mc_correct_answer'
    ];
    $data = [];
    foreach ($fields as $field) {
      $data[$field] = trim($_POST[$field] ?? '');
    }

    // Nếu có mc_id → Cập nhật, ngược lại → Thêm mới
    if (!empty($_POST['mc_id'])) {
      $id = (int) $_POST['mc_id'];
      $sql = "UPDATE mc_questions SET 
        mc_topic = :mc_topic,
        mc_question = :mc_question,
        mc_answer1 = :mc_answer1,
        mc_answer2 = :mc_answer2,
        mc_answer3 = :mc_answer3,
        mc_answer4 = :mc_answer4,
        mc_correct_answer = :mc_correct_answer
        WHERE mc_id = :mc_id";
      $stmt = $pdo->prepare($sql);
      $data['mc_id'] = $id;
      $stmt->execute($data);
      echo json_encode(['success' => true, 'action' => 'updated']);
    } else {
      $sql = "INSERT INTO mc_questions (
        mc_topic, mc_question,
        mc_answer1, mc_answer2,
        mc_answer3, mc_answer4,
        mc_correct_answer
      ) VALUES (
        :mc_topic, :mc_question,
        :mc_answer1, :mc_answer2,
        :mc_answer3, :mc_answer4,
        :mc_correct_answer)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute($data);
      echo json_encode(['success' => true, 'action' => 'inserted']);
    }
    exit;
  }

  // ✅ Trả dữ liệu cho DataTable (GET)
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT * FROM mc_questions ORDER BY mc_id DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['data' => $rows]);
    exit;
  }

  // Nếu không khớp yêu cầu nào
  echo json_encode(['error' => '❌ Yêu cầu không hợp lệ.']);
} catch (Exception $e) {
  echo json_encode(['error' => '❌ Lỗi máy chủ: ' . $e->getMessage()]);
}
