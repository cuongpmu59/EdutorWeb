<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../includes/db_connection.php';
$conn = new mysqli("localhost", "root", "", "mydb");
if ($conn->connect_error) {
    die(json_encode(["error" => "DB connection failed"]));
}

$draw = intval($_POST['draw'] ?? 1);
$start = intval($_POST['start'] ?? 0);
$length = intval($_POST['length'] ?? 20);
$searchValue = $_POST['search']['value'] ?? '';

// Điều kiện tìm kiếm
$searchSql = '';
$params = [];
$types = '';
if ($searchValue) {
    $searchSql = "WHERE mc_topic LIKE ? OR mc_question LIKE ?";
    $params[] = "%$searchValue%";
    $params[] = "%$searchValue%";
    $types .= 'ss';
}

// Lấy tổng số bản ghi
$totalRecords = $conn->query("SELECT COUNT(*) AS cnt FROM mc_table")->fetch_assoc()['cnt'];

// Lấy tổng số bản ghi sau khi tìm kiếm
if ($searchValue) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM mc_table $searchSql");
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $filteredRecords = $stmt->get_result()->fetch_assoc()['cnt'];
} else {
    $filteredRecords = $totalRecords;
}

// Lấy dữ liệu thực tế
$sql = "SELECT mc_id, mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image
        FROM mc_table
        $searchSql
        ORDER BY mc_id DESC
        LIMIT ?, ?";
$params[] = $start;
$params[] = $length;
$types .= 'ii';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Trả về JSON
echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data
]);
