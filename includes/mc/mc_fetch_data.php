<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- Kết nối CSDL ---
// mc_fetch_data.php ở /includes/mc/
// db_connection.php ở /includes/
require_once __DIR__ . '/../db_connection.php'; // biến PDO là $conn

// --- Lấy tham số DataTables ---
$draw    = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start   = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length  = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search  = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
$orderColIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir      = isset($_POST['order'][0]['dir']) && in_array($_POST['order'][0]['dir'], ['asc','desc'])
                  ? $_POST['order'][0]['dir'] : 'asc';

// --- Map DataTables index → cột DB ---
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
    $totalRecords = $conn->query("SELECT COUNT(*) FROM mc_questions")->fetchColumn();

    // 2. Tổng số bản ghi lọc
    $where = '';
    $params = [];
    if ($search !== '') {
        $parts = [];
        foreach ($columns as $col) $parts[] = "$col LIKE :search";
        $where = ' WHERE ' . implode(' OR ', $parts);
        $params[':search'] = "%$search%";
    }

    $totalFiltered = $conn->prepare("SELECT COUNT(*) FROM mc_questions" . $where);
    $totalFiltered->execute($params);
    $totalFiltered = $totalFiltered->fetchColumn();

    // 3. Lấy dữ liệu thực tế
    $sql = "SELECT * FROM mc_questions" . $where . " ORDER BY $orderColumn $orderDir LIMIT :start,:length";
    $stmt = $conn->prepare($sql);
    foreach ($params as $k=>$v) $stmt->bindValue($k,$v,PDO::PARAM_STR);
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':length', $length, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Trả JSON cho DataTables
    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => intval($totalRecords),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data
    ], JSON_UNESCAPED_UNICODE);

} catch(PDOException $e){
    http_response_code(500);
    echo json_encode(['status'=>'error','message'=>'Lỗi PDO: '.$e->getMessage()], JSON_UNESCAPED_UNICODE);
}
