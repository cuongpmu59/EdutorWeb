<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../includes/db_connection.php';

try {
    // Lấy tham số từ DataTables
    $draw   = isset($_GET['draw']) ? (int)$_GET['draw'] : 1;
    $start  = isset($_GET['start']) ? (int)$_GET['start'] : 0;
    $length = isset($_GET['length']) ? (int)$_GET['length'] : 10;
    $searchValue = isset($_GET['search']['value']) ? trim($_GET['search']['value']) : "";

    $orderColumnIndex = $_GET['order'][0]['column'] ?? 0;
    $orderDir = ($_GET['order'][0]['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';

    // Các cột cho ORDER BY
    $columns = ['mc_id', 'mc_topic', 'mc_question', 'mc_a', 'mc_b', 'mc_c', 'mc_d', 'mc_answer', 'mc_image'];
    $orderColumn = $columns[$orderColumnIndex] ?? 'mc_id';

    // Đếm tổng số bản ghi
    $stmtTotal = $pdo->query("SELECT COUNT(*) FROM mc_table");
    $recordsTotal = (int)$stmtTotal->fetchColumn();

    // Tạo câu query chính
    $sql = "SELECT mc_id, mc_topic, mc_question, mc_a, mc_b, mc_c, mc_d, mc_answer, mc_image 
            FROM mc_table";
    $params = [];

    if ($searchValue !== "") {
        $sql .= " WHERE mc_topic LIKE :search
                  OR mc_question LIKE :search
                  OR mc_a LIKE :search
                  OR mc_b LIKE :search
                  OR mc_c LIKE :search
                  OR mc_d LIKE :search";
        $params[':search'] = "%{$searchValue}%";
    }

    // Đếm số bản ghi sau khi lọc
    $sqlCount = "SELECT COUNT(*) FROM mc_table";
    if ($searchValue !== "") {
        $sqlCount .= " WHERE mc_topic LIKE :search
                       OR mc_question LIKE :search
                       OR mc_a LIKE :search
                       OR mc_b LIKE :search
                       OR mc_c LIKE :search
                       OR mc_d LIKE :search";
    }
    $stmtFiltered = $pdo->prepare($sqlCount);
    $stmtFiltered->execute($params);
    $recordsFiltered = (int)$stmtFiltered->fetchColumn();

    // Thêm ORDER BY + LIMIT
    $sql .= " ORDER BY $orderColumn $orderDir LIMIT {$start}, {$length}";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val, PDO::PARAM_STR);
    }
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Trả dữ liệu JSON
    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => $recordsTotal,
        "recordsFiltered" => $recordsFiltered,
        "data" => $data
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => $e->getMessage()
    ]);
}
