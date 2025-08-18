<?php
header('Content-Type: application/json; charset=utf-8');

// Chỉ cho phép GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không hợp lệ']);
    exit;
}

require_once __DIR__ . '/../db_connection.php'; // biến kết nối là $conn (PDO)

$draw   = intval($_GET['draw'] ?? 0);
$start  = intval($_GET['start'] ?? 0);
$length = intval($_GET['length'] ?? 10);
$search = $_GET['search']['value'] ?? '';

$orderColumnIndex = $_GET['order'][0]['column'] ?? 0;
$orderDir         = $_GET['order'][0]['dir'] ?? 'asc';

// Mapping cột DataTables -> cột DB
$columns = [
    0 => 'tf_id',
    1 => 'tf_topic',
    2 => 'tf_question',
    3 => 'tf_statement1',
    4 => 'tf_correct_answer1',
    5 => 'tf_statement2',
    6 => 'tf_correct_answer2',
    7 => 'tf_statement3',
    8 => 'tf_correct_answer3',
    9 => 'tf_statement4',
    10 => 'tf_correct_answer4',
    11 => 'tf_image_url',
    12 => 'created_at'
];
$orderColumn = $columns[$orderColumnIndex] ?? 'tf_id';

$where = '';
$params = [];
if ($search !== '') {
    $where = "WHERE tf_topic LIKE :search 
              OR tf_question LIKE :search 
              OR tf_statement1 LIKE :search 
              OR tf_statement2 LIKE :search 
              OR tf_statement3 LIKE :search 
              OR tf_statement4 LIKE :search";
    $params[':search'] = "%$search%";
}

try {
    // Tổng số bản ghi
    $stmtTotal = $conn->prepare("SELECT COUNT(*) FROM tf_questions");
    $stmtTotal->execute();
    $recordsTotal = $stmtTotal->fetchColumn();

    // Số bản ghi lọc
    $stmtFiltered = $conn->prepare("SELECT COUNT(*) FROM tf_questions $where");
    $stmtFiltered->execute($params);
    $recordsFiltered = $stmtFiltered->fetchColumn();

    // Lấy dữ liệu
    $sql = "SELECT * FROM tf_questions $where ORDER BY $orderColumn $orderDir LIMIT :start, :length";
    $stmt = $conn->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v, PDO::PARAM_STR);
    }
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':length', $length, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => intval($recordsTotal),
        "recordsFiltered" => intval($recordsFiltered),
        "data" => $data
    ]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
