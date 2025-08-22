<?php
header('Content-Type: application/json; charset=utf-8');

// Chỉ chấp nhận POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Phương thức không hợp lệ.'
    ]);
    exit;
}

// Kết nối CSDL
require_once __DIR__ . '/../../includes/db_connection.php'; // chỉnh đường dẫn phù hợp

// Nhận raw JSON
$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, true);

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
    // Lấy từng trường, đặt default rỗng nếu không tồn tại
    $mc_topic = isset($row['mc_topic']) ? trim($row['mc_topic']) : '';
    $mc_question = isset($row['mc_question']) ? trim($row['mc_question']) : '';
    $mc_answer1 = isset($row['mc_answer1']) ? trim($row['mc_answer1']) : '';
    $mc_answer2 = isset($row['mc_answer2']) ? trim($row['mc_answer2']) : '';
    $mc_answer3 = isset($row['mc_answer3']) ? trim($row['mc_answer3']) : '';
    $mc_answer4 = isset($row['mc_answer4']) ? trim($row['mc_answer4']) : '';
    $mc_correct_answer = isset($row['mc_correct_answer']) ? trim($row['mc_correct_answer']) : '';
    $mc_image_url = isset($row['mc_image_url']) ? trim($row['mc_image_url']) : '';

    // Validation cơ bản
    if (!$mc_topic || !$mc_question || !$mc_answer1 || !$mc_answer2 || !$mc_correct_answer) {
        $errors[] = "Dòng " . ($index + 2) . " bị thiếu dữ liệu bắt buộc.";
        continue;
    }

    // Chuẩn bị query an toàn
    $stmt = $conn->prepare("INSERT INTO mc_questions 
        (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url, mc_created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");

    $stmt->bind_param(
        "ssssssss",
        $mc_topic,
        $mc_question,
        $mc_answer1,
        $mc_answer2,
        $mc_answer3,
        $mc_answer4,
        $mc_correct_answer,
        $mc_image_url
    );

    if ($stmt->execute()) {
        $successCount++;
    } else {
        $errors[] = "Dòng " . ($index + 2) . " không thể chèn: " . $stmt->error;
    }

    $stmt->close();
}

echo json_encode([
    'status' => 'success',
    'count' => $successCount,
    'errors' => $errors
]);
?>
