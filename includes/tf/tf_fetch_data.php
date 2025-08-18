<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../db_connection.php';

// Lấy các tham số từ DataTables
$draw  = intval($_POST['draw'] ?? 0);
$start = intval($_POST['start'] ?? 0);
$length= intval($_POST['length'] ?? 10);
$search= trim($_POST['search']['value'] ?? '');
$orderColIndex = intval($_POST['order'][0]['column'] ?? 0);
$orderDir      = in_array($_POST['order'][0]['dir'] ?? '', ['asc','desc']) ? $_POST['order'][0]['dir'] : 'asc';

// Các cột trong bảng True/False
$columns = [
    0 => 'tf_id',
    1 => 'tf_topic',
    2 => 'tf_question',
    3 => 'tf_correct_answer',
    4 => 'tf_image_url',
    5 => 'tf_created_at'
];

$orderColumn = $columns[$orderColIndex] ?? 'tf_id';

// Lọc chủ đề nếu có
$topicFilter = trim($_POST['columns'][1]['search']['value'] ?? '');

try {
    // Tổng số bản ghi
    $totalRecords = $conn->query("SELECT COUNT(*) FROM tf_questions")->fetchColumn();

    // Build WHERE clause
    $whereParts = [];
    $params = [];

    if ($topicFilter !== '') {
        $whereParts[] = "tf_topic LIKE :topic";
        $params[':topic'] = "%$topicFilter%";
    }

    if ($search !== '') {
        $searchParts = [];
        foreach ($columns as $col) {
            if ($col === 'tf_id' && ctype_digit($search)) {
                $searchParts[] = "$col = :id_search";
                $params[':id_search'] = intval($search);
            } elseif ($col !== 'tf_id') {
                $ph = ":search_$col";
                $searchParts[] = "$col LIKE $ph";
                $params[$ph] = "%$search%";
            }
        }
        if ($searchParts) {
            $whereParts[] = '(' . implode(' OR ', $searchParts) . ')';
        }
    }

    $where = $whereParts ? ' WHERE ' . implode(' AND ', $whereParts) : '';

    // Tổng số bản ghi sau filter
    $stmt = $conn->prepare("SELECT COUNT(*) FROM tf_questions $where");
    $stmt->execute($params);
    $totalFiltered = $stmt->fetchColumn();

    // Lấy dữ liệu với giới hạn, sắp xếp
    $sql = "SELECT * FROM tf_questions $where ORDER BY $orderColumn $orderDir LIMIT :start, :length";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':length', $length, PDO::PARAM_INT);

    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Trả về JSON
    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => intval($totalRecords),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi PDO: '.$e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
