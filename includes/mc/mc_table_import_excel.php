<?php
header('Content-Type: application/json; charset=utf-8');

// Kết nối CSDL
require_once __DIR__ . '/../../includes/db_connection.php';

// Nhận JSON từ client
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
    $mc_topic = isset($row['mc_topic']) ? trim($row['mc_topic']) : '';
    $mc_question = isset($row['mc_question']) ? trim($row['mc_question']) : '';
    $mc_answer1 = isset($row['mc_answer1']) ? trim($row['mc_answer1']) : '';
    $mc_answer2 = isset($row['mc_answer2']) ? trim($row['mc_answer2']) : '';
    $mc_answer3 = isset($row['mc_answer3']) ? trim($row['mc_answer3']) : '';
    $mc_answer4 = isset($row['mc_answer4']) ? trim($row['mc_answer4']) : '';
    $mc_correct_answer = isset($row['mc_correct_answer']) ? strtoupper(trim($row['mc_correct_answer'])) : '';
    $mc_image_url = isset($row['mc_image_url']) ? trim($row['mc_image_url']) : '';

    // Validation bắt buộc
    if (!$mc_topic || !$mc_question || !$mc_answer1 || !$mc_answer2 || !$mc_correct_answer) {
        $errors[] = "Dòng " . ($index + 2) . " thiếu dữ liệu bắt buộc.";
        continue;
    }

    // Kiểm tra đáp án hợp lệ
    if (!in_array($mc_correct_answer, ['A','B','C','D'])) {
        $errors[] = "Dòng " . ($index + 2) . " đáp án đúng không hợp lệ (A/B/C/D).";
        continue;
    }

    // Insert an toàn
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

// Trả về JSON cho client
echo json_encode([
    'status' => 'success',
    'count' => $successCount,
    'errors' => $errors
]);
?>
