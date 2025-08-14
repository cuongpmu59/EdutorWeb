<?php
header('Content-Type: application/json; charset=utf-8');

// Kết nối CSDL
require_once __DIR__ . '/../db_connection.php'; // Đảm bảo file này tạo $pdo

// Lấy tham số từ DataTables gửi lên
$draw    = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start   = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length  = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search  = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
$orderColIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir      = isset($_POST['order'][0]['dir']) && in_array($_POST['order'][0]['dir'], ['asc', 'desc'])
                  ? $_POST['order'][0]['dir'] : 'asc';

// Map cột index → tên cột trong DB
$columns = [
    0 => 'mc_id',
    1 => 'mc_topic',
    2 => 'mc_question',
    3 => 'mc_answer1',
    4 => 'mc_answer2',
    5 => 'mc_answer3',
    6 => 'mc_answer4',
    7 => 'mc_correct_answer',
    8 => 'mc_image_url'
];
$orderColumn = $columns[$orderColIndex] ?? 'mc_id';

// 1. Lấy tổng số bản ghi (chưa lọc)
$totalRecordsStmt = $pdo->query("SELECT COUNT(*) FROM mc_questions");
$totalRecords = $totalRecordsStmt->fetchColumn();

// 2. Lấy tổng số bản ghi (đã lọc)
$sqlFiltered = "SELECT COUNT(*) FROM mc_questions";
$where = '';
$params = [];

if ($search !== '') {
    $whereParts = [];
    foreach ($columns as $col) {
        $whereParts[] = "$col LIKE :search";
    }
    $where = ' WHERE ' . implode(' OR ', $whereParts);
    $params[':search'] = "%$search%";
}

$stmtFiltered = $pdo->prepare($sqlFiltered . $where);
$stmtFiltered->execute($params);
$totalFiltered = $stmtFiltered->fetchColumn();

// 3. Lấy dữ liệu thực tế
$sqlData = "SELECT * FROM mc_questions" . $where .
           " ORDER BY $orderColumn $orderDir LIMIT :start, :length";
$stmtData = $pdo->prepare($sqlData);

// Bind giá trị tìm kiếm
foreach ($params as $key => $value) {
    $stmtData->bindValue($key, $value, PDO::PARAM_STR);
}

// Bind start, length (phải bind kiểu int)
$stmtData->bindValue(':start', $start, PDO::PARAM_INT);
$stmtData->bindValue(':length', $length, PDO::PARAM_INT);

$stmtData->execute();
$data = $stmtData->fetchAll(PDO::FETCH_ASSOC);

// 4. Trả JSON cho DataTables
echo json_encode([
    "draw"            => $draw,
    "recordsTotal"    => $totalRecords,
    "recordsFiltered" => $totalFiltered,
    "data"            => $data
], JSON_UNESCAPED_UNICODE);
