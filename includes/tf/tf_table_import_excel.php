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
    $tf_topic = $row['tf_topic'] ?? '';
    $tf_question = $row['tf_question'] ?? '';
    $tf_image_url = $row['tf_image_url'] ?? '';

    // Statements & Answers
    $statements = [];
    $corrects = [];
    for ($i=1; $i<=4; $i++) {
        $statements[$i] = $row["tf_statement$i"] ?? '';
        $corrects[$i]   = strtoupper($row["tf_correct_answer$i"] ?? '');
    }

    // Validation cơ bản
    if (!$tf_topic || !$tf_question || !$statements[1] || !$corrects[1]) {
        $errors[] = "Dòng " . ($index + 2) . " thiếu dữ liệu bắt buộc (topic, question, statement1 hoặc answer1).";
        continue;
    }

    // Validate correct answers (chỉ nhận TRUE/FALSE)
    foreach ($corrects as $i => $val) {
        if ($val && !in_array($val, ['TRUE','FALSE'])) {
            $errors[] = "Dòng " . ($index + 2) . " đáp án đúng cho statement$i không hợp lệ (TRUE/FALSE).";
            continue 2; // bỏ qua dòng này
        }
    }

    // Chuẩn bị query PDO
    $stmt = $conn->prepare("
        INSERT INTO tf_questions
        (tf_topic, tf_question,
         tf_statement1, tf_correct_answer1,
         tf_statement2, tf_correct_answer2,
         tf_statement3, tf_correct_answer3,
         tf_statement4, tf_correct_answer4,
         tf_image_url, tf_created_at)
        VALUES
        (:topic, :question,
         :s1, :c1,
         :s2, :c2,
         :s3, :c3,
         :s4, :c4,
         :image, NOW())
    ");

    try {
        $stmt->execute([
            ':topic'    => $tf_topic,
            ':question' => $tf_question,
            ':s1'       => $statements[1],
            ':c1'       => $corrects[1],
            ':s2'       => $statements[2],
            ':c2'       => $corrects[2],
            ':s3'       => $statements[3],
            ':c3'       => $corrects[3],
            ':s4'       => $statements[4],
            ':c4'       => $corrects[4],
            ':image'    => $tf_image_url
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
