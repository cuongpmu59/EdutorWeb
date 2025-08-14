<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- Kết nối CSDL ---
// Chỉnh đường dẫn tới file db_connection.php thật chính xác
// Nếu db_connection.php nằm ở /includes/db_connection.php thì:
require_once __DIR__ . '/../db_connection.php'; // mc_fetch_data.php ở /includes/mc/

// Biến PDO trong db_connection.php là $conn

// --- Lấy tham số từ DataTables ---
$draw    = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start   = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length  = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search  = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
$orderColIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir      = isset($_POST['order'][0]['dir']) && in_array($_POST['order'][0]['dir'], ['asc','desc'])
                  ? $_POST['order'][0]['dir'] : 'asc';

// --- Map index DataTables → cột DB ---
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

try {
    // 1. Tổng số bản ghi
    $totalRecordsStmt = $conn->query("SELECT COUNT(*) FROM mc_questions");
    $totalRecords = $totalRecordsStmt->fetchColumn();

    // 2. Tổng số bản ghi đã lọc
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

    $stmtFiltered = $conn->prepare("SELECT COUNT(*) FROM mc_questions" . $where);
    $stmtFiltered->execute($params);
    $totalFiltered = $stmtFiltered->fetchColumn();

    // 3. Lấy dữ liệu thực tế
    $sqlData = "SELECT * FROM mc_questions" . $where . " ORDER BY $orderColumn $orderDir LIMIT :start, :length";
    $stmtData = $conn->prepare($sqlData);

    foreach ($params as $key => $val) {
        $stmtData->bindValue($key, $val, PDO::PARAM_STR);
    }
    $stmtData->bindValue(':start', $start, PDO::PARAM_INT);
    $stmtData->bindValue(':length', $length, PDO::PARAM_INT);

    $stmtData->execute();
    $data = $stmtData->fetchAll(PDO::FETCH_ASSOC);

    // 4. Trả JSON cho DataTables
    echo json_encode([
        "draw"            => $draw,
        "recordsTotal"    => intval($totalRecords),
        "recordsFiltered" => intval($totalFiltered),
        "data"            => $data
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Lỗi PDO: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
