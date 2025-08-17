<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../db_connection.php'; // $conn là PDO

// --- Lấy tham số DataTables ---
$draw  = intval($_POST['draw'] ?? 0);
$start = intval($_POST['start'] ?? 0);
$length= intval($_POST['length'] ?? 10);
$search= trim($_POST['search']['value'] ?? '');
$orderColIndex = intval($_POST['order'][0]['column'] ?? 0);
$orderDir      = in_array($_POST['order'][0]['dir'] ?? '', ['asc','desc']) ? $_POST['order'][0]['dir'] : 'asc';

// --- Map cột DataTables sang cột DB ---
$columns = [
    0 => 'mc_id', 1 => 'mc_topic', 2 => 'mc_question', 3 => 'mc_answer1',
    4 => 'mc_answer2', 5 => 'mc_answer3', 6 => 'mc_answer4',
    7 => 'mc_correct_answer', 8 => 'mc_image_url', 9 => 'mc_created_at'
];
$orderColumn = $columns[$orderColIndex] ?? 'mc_id';

// --- Filter chủ đề từ cột 1 ---
$topicFilter = trim($_POST['columns'][1]['search']['value'] ?? '');

try {
    // 1. Tổng số bản ghi
    $totalRecords = $conn->query("SELECT COUNT(*) FROM mc_questions")->fetchColumn();

    // 2. Build WHERE
    $whereParts = [];
    $params = [];

    // Filter chủ đề
    if ($topicFilter !== '') {
        $whereParts[] = "mc_topic = :topic";
        $params[':topic'] = $topicFilter;
    }

    // Search toàn bộ cột
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
            if (!isset($params[':id_search'])) $params[':search'] = "%$search%";
        }
    }

    $where = $whereParts ? ' WHERE '.implode(' AND ', $whereParts) : '';

    // 3. Tổng số bản ghi sau filter
    $stmt = $conn->prepare("SELECT COUNT(*) FROM mc_questions $where");
    $stmt->execute($params);
    $totalFiltered = $stmt->fetchColumn();

    // 4. Lấy dữ liệu
    $sql = "SELECT * FROM mc_questions $where ORDER BY $orderColumn $orderDir LIMIT :start, :length";
    $stmt = $conn->prepare($sql);

    // Bind tham số
    foreach ($params as $k=>$v) { $stmt->bindValue($k,$v); }
    $stmt->bindValue(':start',$start,PDO::PARAM_INT);
    $stmt->bindValue(':length',$length,PDO::PARAM_INT);

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
