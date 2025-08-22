<?php
header('Content-Type: application/json; charset=utf-8');

// --- Kết nối CSDL ---
require_once __DIR__ . '/../../includes/db_connection.php';

// Nhận JSON raw
$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, true);

// Log dữ liệu để kiểm tra
file_put_contents(__DIR__ . '/import_debug.log', print_r($data, true));

if (!$data || !isset($data['rows']) || !is_array($data['rows'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Dữ liệu không hợp lệ.'
    ]);
    exit;
}

$rows = $data['rows'];
$successCount = 0;
$errors = [];

foreach ($rows as $index => $row) {
    $mc_topic = $row['mc_topic'] ?? '';
    $mc_question = $row['mc_question'] ?? '';
    $mc_answer1 = $row['mc_answer1'] ?? '';
    $mc_answer2 = $row['mc_answer2'] ?? '';
    $mc_correct_answer = $row['mc_correct_answer'] ?? '';

    // Check bắt buộc
    if (!$mc_topic || !$mc_question || !$mc_answer1 || !$mc_answer2 || !$mc_correct_answer) {
        $errors[] = "Dòng " . ($index + 2) . " thiếu dữ liệu.";
        continue;
    }

    $successCount++;
}

echo json_encode([
    'status' => 'success',
    'count' => $successCount,
    'errors' => $errors
]);
?>
