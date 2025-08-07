<?php
require_once __DIR__ . '/../env/config.php';
require_once __DIR__ . '/../includes/db_connection.php';

header('Content-Type: application/json');

try {
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mc_id'])) {
    // 🔸 Load chi tiết 1 câu hỏi
    $mc_id = (int) $_POST['mc_id'];
    $stmt = $conn->prepare("SELECT * FROM mc_questions WHERE mc_id = ?");
    $stmt->execute([$mc_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
      echo json_encode(['error' => '❌ Không tìm thấy câu hỏi']);
      exit;
    }

    echo json_encode($data);
    exit;
  }

  // 🔸 Server-side DataTables
  $draw = intval($_GET['draw'] ?? 0);
  $start = intval($_GET['start'] ?? 0);
  $length = intval($_GET['length'] ?? 10);

  // Tổng số bản ghi
  $totalRecords = $conn->query("SELECT COUNT(*) FROM mc_questions")->fetchColumn();

  // Lấy dữ liệu giới hạn
  $stmt = $conn->prepare("SELECT * FROM mc_questions ORDER BY mc_id DESC LIMIT ?, ?");
  $stmt->bindValue(1, $start, PDO::PARAM_INT);
  $stmt->bindValue(2, $length, PDO::PARAM_INT);
  $stmt->execute();
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode([
    'draw' => $draw,
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $totalRecords,
    'data' => $data,
  ]);
} catch (Exception $e) {
  echo json_encode(['error' => '❌ Lỗi server: ' . $e->getMessage()]);
  http_response_code(500);
}
