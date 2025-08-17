<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../db_connection.php'; // $conn là PDO

// --- Lấy tham số DataTables ---
$draw         = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start        = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length       = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search       = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
$orderColIndex= isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir     = isset($_POST['order'][0]['dir']) && in_array($_POST['order'][0]['dir'], ['asc','desc'])
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
    8 => 'mc_image_url',
    9 => 'mc_created_at'
];
$orderColumn = $columns[$orderColIndex] ?? 'mc_id';

// --- Filter chủ đề từ column search ---
$topicFilter = $_POST['columns'][1]['search']['value'] ?? '';

try {
    // 1. Tổng số bản ghi
    $totalRecords = $conn->query("SELECT COUNT(*) FROM mc_questions")->fetchColumn();

    // 2. Điều kiện WHERE
    $whereParts = [];
    $params = [];

    if ($topicFilter !== '') {
        $whereParts[] = "mc_topic = :topic";
        $params[':topic'] = $topicFilter;
    }

    if ($search !== '') {
        $searchParts = [];
        foreach ($columns as $col) {
            if ($col === 'mc_id' && ctype_digit($search)) {
                $searchParts[] = "$col = :id_search";
                $params[':id_search'] = intval($search);
            } elseif ($col !== 'mc_id') {
                $searchParts[] = "$col LIKE :search";
            }
        }
        if ($searchParts) {
            $whereParts[] = '(' . implode(' OR ', $searchParts) . ')';
            if (!isset($params[':id_search'])) {
                $params[':search'] = "%$search%";
            }
        }
    }

    $where = $whereParts ? ' WHERE ' . implode(' AND ', $whereParts) : '';

    // 3. Tổng số bản ghi sau khi lọc
    $stmt = $conn->prepare("SELECT COUNT(*) FROM mc_questions $where");
    $stmt->execute($params);
    $totalFiltered = $stmt->fetchColumn();

    // 4. Lấy dữ liệu thực tế
    $start  = max(0, $start);
    $length = max(1, $length);

    $sql = "SELECT * FROM mc_questions $where ORDER BY $orderColumn $orderDir LIMIT $start, $length";
    $stmt = $conn->prepare($sql);

    // Bind các tham số filter/search
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }

    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 5. Trả JSON cho DataTables
    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => intval($totalRecords),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data
    ], JSON_UNESCAPED_UNICODE);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status'=>'error',
        'message'=>'Lỗi PDO: '.$e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
