<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit(json_encode(['status' => 'error', 'message' => 'Phương thức không hợp lệ.']));
}

require_once __DIR__ . '/../../env/config.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset("utf8mb4");

    $data = array_map('trim', $_POST);
    $tf_id = filter_var($data['tf_id'] ?? null, FILTER_VALIDATE_INT);

    // Bắt buộc nhập chủ đề + câu hỏi + ít nhất 1 phát biểu
    $requiredFields = ['tf_topic', 'tf_question', 'tf_statement1', 'tf_correct_answer1'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field]) && $data[$field] !== "0") {
            exit(json_encode(['status' => 'error', 'message' => 'Vui lòng nhập đầy đủ thông tin bắt buộc.']));
        }
    }

    // Cho phép để trống ảnh
    $tf_image_url = !empty($data['tf_image_url']) ? $data['tf_image_url'] : null;

    if ($tf_id) {
        // UPDATE
        $sql = "UPDATE tf_questions 
                SET tf_topic=?, tf_question=?, 
                    tf_statement1=?, tf_correct_answer1=?, 
                    tf_statement2=?, tf_correct_answer2=?, 
                    tf_statement3=?, tf_correct_answer3=?, 
                    tf_statement4=?, tf_correct_answer4=?, 
                    tf_image_url=? 
                WHERE tf_id=?";
        $types = "sssisisiissi";
        $params = [
            $data['tf_topic'], $data['tf_question'],
            $data['tf_statement1'], (int)$data['tf_correct_answer1'],
            $data['tf_statement2'] ?? null, isset($data['tf_correct_answer2']) ? (int)$data['tf_correct_answer2'] : null,
            $data['tf_statement3'] ?? null, isset($data['tf_correct_answer3']) ? (int)$data['tf_correct_answer3'] : null,
            $data['tf_statement4'] ?? null, isset($data['tf_correct_answer4']) ? (int)$data['tf_correct_answer4'] : null,
            $tf_image_url,
            $tf_id
        ];
    } else {
        // INSERT
        $sql = "INSERT INTO tf_questions 
                (tf_topic, tf_question, 
                 tf_statement1, tf_correct_answer1, 
                 tf_statement2, tf_correct_answer2, 
                 tf_statement3, tf_correct_answer3, 
                 tf_statement4, tf_correct_answer4, 
                 tf_image_url, tf_created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $types = "sssisisiiss";
        $params = [
            $data['tf_topic'], $data['tf_question'],
            $data['tf_statement1'], (int)$data['tf_correct_answer1'],
            $data['tf_statement2'] ?? null, isset($data['tf_correct_answer2']) ? (int)$data['tf_correct_answer2'] : null,
            $data['tf_statement3'] ?? null, isset($data['tf_correct_answer3']) ? (int)$data['tf_correct_answer3'] : null,
            $data['tf_statement4'] ?? null, isset($data['tf_correct_answer4']) ? (int)$data['tf_correct_answer4'] : null,
            $tf_image_url
        ];
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    echo json_encode([
        'status' => 'success',
        'message' => $tf_id ? 'Cập nhật câu hỏi thành công.' : 'Thêm câu hỏi thành công.'
    ]);
} catch (mysqli_sql_exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
} finally {
    $conn?->close();
}
