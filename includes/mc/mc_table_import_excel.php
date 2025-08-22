<?php
header('Content-Type: application/json; charset=utf-8');

// Kết nối PDO
require_once __DIR__ . '/../../includes/db_connection.php'; // đường dẫn tới file PDO

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
    $mc_topic = $row['mc_topic'] ?? '';
    $mc_question = $row['mc_question'] ?? '';
    $mc_answer1 = $row['mc_answer1'] ?? '';
    $mc_answer2 = $row['mc_answer2'] ?? '';
    $mc_answer3 = $row['mc_answer3'] ?? '';
    $mc_answer4 = $row['mc_answer4'] ?? '';
    $mc_correct_answer = strtoupper($row['mc_correct_answer'] ?? '');
    $mc_image_url = $row['mc_image_url'] ?? '';

    // Validation cơ bản
    if (!$mc_topic || !$mc_question || !$mc_answer1 || !$mc_answer2 || !$mc_correct_answer) {
        $errors[] = "Dòng " . ($index + 2) . " thiếu dữ liệu bắt buộc.";
        continue;
    }

    if (!in_array($mc_correct_answer, ['A','B','C','D'])) {
        $errors[] = "Dòng " . ($index + 2) . " đáp án đúng không hợp lệ (A/B/C/D).";
        continue;
    }

    // Chuẩn bị query PDO
    $stmt = $conn->prepare("
        INSERT INTO mc_questions 
        (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url, mc_created_at)
        VALUES (:topic, :question, :a1, :a2, :a3, :a4, :correct, :image, NOW())
    ");

    try {
        $stmt->execute([
            ':topic'   => $mc_topic,
            ':question'=> $mc_question,
            ':a1'      => $mc_answer1,
            ':a2'      => $mc_answer2,
            ':a3'      => $mc_answer3,
            ':a4'      => $mc_answer4,
            ':correct' => $mc_correct_answer,
            ':image'   => $mc_image_url
        ]);
        $successCount++;
    } catch (PDOException $e) {
        $errors[] = "Dòng " . ($index + 2) . " không thể chèn: " . $e->getMessage();
    }
}

// Trả về JSON cho client
echo json_encode([
    'status' => 'success',
    'count'  => $successCount,
    'errors' => $errors
]);
