<?php
require_once __DIR__ . '/../db_connection.php';

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

// ─────────────────────────────────────────────────────────────
// 1️⃣ ƯU TIÊN: Trường hợp POST để lấy 1 câu hỏi chi tiết
// ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mc_id'])) {
  // ✅ Validate
  $mc_id = filter_input(INPUT_POST, 'mc_id', FILTER_VALIDATE_INT);
  if (!$mc_id) {
    http_response_code(400);
    echo json_encode(['error' => '❌ mc_id không hợp lệ']);
    exit;
  }

  // ✅ Truy vấn
  $stmt = $conn->prepare('SELECT * FROM mc_questions WHERE mc_id = ?');
  $stmt->execute([$mc_id]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  // ✅ Phản hồi
  echo $row ? json_encode($row) : json_encode(['error' => '❌ Không tìm thấy câu hỏi']);
  exit;
}

// ─────────────────────────────────────────────────────────────
// 2️⃣ Trường hợp GET dùng cho DataTables server-side
// ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  // ✅ Lấy tham số từ DataTables
  $draw            = (int)($_GET['draw'] ?? 1);
  $start           = (int)($_GET['start'] ?? 0);
  $length          = (int)($_GET['length'] ?? 10);
  $searchValue     = trim($_GET['search']['value'] ?? '');
  $orderColumnIdx  = (int)($_GET['order'][0]['column'] ?? 0);
  $orderDir        = ($_GET['order'][0]['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';

  // ✅ Danh sách cột tương ứng với DataTables
  $columns = ['mc_id', 'mc_topic', 'mc_question', 'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4', 'mc_correct_answer', 'mc_image_url'];
  $orderColumn = $columns[$orderColumnIdx] ?? 'mc_id';

  // ✅ WHERE nếu có tìm kiếm
  $where = '';
  $params = [];

  if ($searchValue !== '') {
    $where = "WHERE mc_question LIKE :kw OR mc_topic LIKE :kw";
    $params[':kw'] = '%' . $searchValue . '%';
  }

  // ✅ Tổng bản ghi (không lọc)
  $recordsTotal = $conn->query("SELECT COUNT(*) FROM mc_questions")->fetchColumn();

  // ✅ Tổng bản ghi (có lọc)
  $filteredStmt = $conn->prepare("SELECT COUNT(*) FROM mc_questions $where");
  $filteredStmt->execute($params);
  $recordsFiltered = $filteredStmt->fetchColumn();

  // ✅ Truy vấn chính (có phân trang)
  $sql = "SELECT * FROM mc_questions $where ORDER BY $orderColumn $orderDir LIMIT :start, :length";
  $stmt = $conn->prepare($sql);

  // Bind params tìm kiếm
  foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val, PDO::PARAM_STR);
  }
  // Bind LIMIT
  $stmt->bindValue(':start', $start, PDO::PARAM_INT);
  $stmt->bindValue(':length', $length, PDO::PARAM_INT);

  // ✅ Lấy dữ liệu
  $stmt->execute();
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // ✅ JSON phản hồi cho DataTables
  echo json_encode([
    'draw' => $draw,
    'recordsTotal' => $recordsTotal,
    'recordsFiltered' => $recordsFiltered,
    'data' => $data
  ]);
  exit;
}

// ─────────────────────────────────────────────────────────────
// 3️⃣ Trường hợp không hợp lệ
// ─────────────────────────────────────────────────────────────
http_response_code(400);
echo json_encode(['error' => '❌ Yêu cầu không hợp lệ']);
exit;
