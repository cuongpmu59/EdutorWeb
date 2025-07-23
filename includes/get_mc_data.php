<?php
require_once __DIR__ . '/db_connection.php';

header('Content-Type: application/json');

if (isset($_GET['mc_id'])) {
    // Giao diện form: trả dữ liệu câu hỏi theo ID
    $mc_id = (int)$_GET['mc_id'];

    $stmt = $conn->prepare("SELECT * FROM mc_questions WHERE mc_id = ?");
    $stmt->execute([$mc_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy câu hỏi']);
    }
    exit;
}

// Dưới đây là phần cho bảng DataTables
$searchValue = $_GET['search']['value'] ?? '';
$start       = (int)($_GET['start'] ?? 0);
$length      = (int)($_GET['length'] ?? 10);
$orderColumn = $_GET['order'][0]['column'] ?? 0;
$orderDir    = $_GET['order'][0]['dir'] ?? 'asc';

$columns = ['mc_id', 'mc_topic', 'mc_question', 'mc_correct_answer', 'mc_image_url'];
$orderBy = $columns[$orderColumn] ?? 'mc_id';
$orderDir = strtolower($orderDir) === 'desc' ? 'DESC' : 'ASC';

// Đếm tổng số bản ghi
$totalQuery = $conn->query("SELECT COUNT(*) FROM mc_questions");
$totalRecords = $totalQuery->fetchColumn();

// Lọc theo từ khóa tìm kiếm
$where = '';
$params = [];

if (!empty($searchValue)) {
    $where = "WHERE mc_topic LIKE ? OR mc_question LIKE ?";
    $params[] = "%$searchValue%";
    $params[] = "%$searchValue%";
}

$dataQuery = $conn->prepare("
    SELECT * FROM mc_questions
    $where
    ORDER BY $orderBy $orderDir
    LIMIT $start, $length
");
$dataQuery->execute($params);
$data = $dataQuery->fetchAll(PDO::FETCH_ASSOC);

// Đếm số bản ghi sau khi lọc
if ($where) {
    $filteredQuery = $conn->prepare("SELECT COUNT(*) FROM mc_questions $where");
    $filteredQuery->execute($params);
    $filtered = $filteredQuery->fetchColumn();
} else {
    $filtered = $totalRecords;
}

// Trả về kết quả cho DataTables
echo json_encode([
    'draw' => (int)($_GET['draw'] ?? 0),
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $filtered,
    'data' => $data
]);
