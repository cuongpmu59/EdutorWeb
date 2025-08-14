<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../db_connection.php'; // phải tạo $pdo = new PDO(...)

// Lấy params từ DataTables
$draw   = isset($_POST['draw']) ? (int)$_POST['draw'] : 0;
$start  = isset($_POST['start']) ? max(0, (int)$_POST['start']) : 0;
$length = isset($_POST['length']) ? (int)$_POST['length'] : 10;
$length = ($length < 1 || $length > 500) ? 10 : $length;

$searchValue = trim($_POST['search']['value'] ?? '');

// Mapping cột cho sort
$columnsMap = [
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

$orderCol = $columnsMap[$_POST['order'][0]['column'] ?? 0] ?? 'mc_id';
$orderDir = in_array(strtolower($_POST['order'][0]['dir'] ?? 'desc'), ['asc', 'desc']) 
            ? $_POST['order'][0]['dir'] 
            : 'desc';

// Base query
$sqlBase = "FROM mc_questions";
$where = "";
$params = [];

// Nếu có search
if ($searchValue !== '') {
    $where = " WHERE mc_topic LIKE :kw
               OR mc_question LIKE :kw
               OR mc_answer1 LIKE :kw
               OR mc_answer2 LIKE :kw
               OR mc_answer3 LIKE :kw
               OR mc_answer4 LIKE :kw
               OR mc_correct_answer LIKE :kw";
    $params[':kw'] = "%{$searchValue}%";
}

// Tổng records
$totalRecords = (int)$pdo->query("SELECT COUNT(*) FROM mc_questions")->fetchColumn();

// Tổng records sau lọc
if ($where) {
    $stmt = $pdo->prepare("SELECT COUNT(*) {$sqlBase} {$where}");
    $stmt->execute($params);
    $filteredRecords = (int)$stmt->fetchColumn();
} else {
    $filteredRecords = $totalRecords;
}

// Lấy dữ liệu
$stmt = $pdo->prepare("SELECT mc_id, mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url
                       {$sqlBase}
                       {$where}
                       ORDER BY {$orderCol} {$orderDir}
                       LIMIT :start, :length");
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v, PDO::PARAM_STR);
}
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':length', $length, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Trả JSON
echo json_encode([
    'draw' => $draw,
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $filteredRecords,
    'data' => $data
], JSON_UNESCAPED_UNICODE);
