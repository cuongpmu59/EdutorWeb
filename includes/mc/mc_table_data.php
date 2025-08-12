<?php
header('Content-Type: application/json; charset=utf-8');

// Kết nối PDO
require_once __DIR__ . '/../../includes/db_connection.php';

// Lấy dữ liệu từ DataTables gửi lên
$draw   = isset($_GET['draw']) ? (int)$_GET['draw'] : 1;
$start  = isset($_GET['start']) ? (int)$_GET['start'] : 0;
$length = isset($_GET['length']) ? (int)$_GET['length'] : 10;
$searchValue = isset($_GET['search']['value']) ? trim($_GET['search']['value']) : "";

// Sắp xếp
$orderColumnIndex = $_GET['order'][0]['column'] ?? 0;
$orderDir = $_GET['order'][0]['dir'] ?? 'asc';
$columns = ['mc_id', 'mc_topic', 'mc_question', 'mc_a', 'mc_b', 'mc_c', 'mc_d', 'mc_answer', 'mc_image'];
$orderColumn = $columns[$orderColumnIndex] ?? 'mc_id';

// ==========================
// Tổng số bản ghi
// ==========================
$totalQuery = $pdo->query("SELECT COUNT(*) FROM mc_table");
$totalRecords = (int) $totalQuery->fetchColumn();

// ==========================
// Truy vấn dữ liệu
// ==========================
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
    $params[':search'] = "%" . $searchValue . "%";
}

// Tổng số bản ghi sau khi lọc
$stmtFiltered = $pdo->prepare(str_replace("SELECT mc_id, mc_topic, mc_question, mc_a, mc_b, mc_c, mc_d, mc_answer, mc_image", "SELECT COUNT(*)", $sql));
$stmtFiltered->execute($params);
$filteredRecords = (int) $stmtFiltered->fetchColumn();

// Thêm sắp xếp và phân trang
$sql .= " ORDER BY $orderColumn $orderDir LIMIT :start, :length";

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':length', $length, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ==========================
// Trả dữ liệu JSON
// ==========================
echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data
], JSON_UNESCAPED_UNICODE);

