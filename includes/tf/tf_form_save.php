<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit(json_encode(['status' => 'error', 'message' => 'Phương thức không hợp lệ.']));
}

require_once __DIR__ . '/../../includes/env/config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset("utf8mb4");

    $data = array_map('trim', $_POST);
    $tf_id = filter_var($data['tf_id'] ?? null, FILTER_VALIDATE_INT);

    $requiredFields = [
        'tf_topic', 'tf_question',
        'tf_statement1', 'tf_statement2', 'tf_statement3', 'tf_statement4',
        'tf_correct_answer1', 'tf_correct_answer2', 'tf_correct_answer3', 'tf_correct_answer4'
    ];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            exit(json_encode(['status' => 'error', 'message' => 'Vui lòng nhập đầy đủ thông tin.']));
        }
    }

    // Cho phép để trống ảnh
    $tf_image_url = !empty($data['tf_image_url']) ? $data['tf_image_url'] : null;

    if ($tf_id) {
        $sql = "UPDATE tf_questions 
                SET tf_topic=?, tf_question=?, 
                    tf_statement1=?, tf_statement2=?, tf_statement3=?, tf_statement4=?, 
                    tf_correct_answer1=?, tf_correct_answer2=?, tf_correct_answer3=?, tf_correct_answer4=?, 
                    tf_image_url=? 
                WHERE tf_id=?";
        $types = "sssssssssssi";
        $params = [
            $data['tf_topic'], $data['tf_question'],
            $data['tf_statement1'], $data['tf_statement2'], $data['tf_statement3'], $data['tf_statement4'],
            $data['tf_correct_answer1'], $data['tf_correct_answer2'], $data['tf_correct_answer3'], $data['tf_correct_answer4'],
            $tf_image_url, $tf_id
        ];
    } else {
        $sql = "INSERT INTO tf_questions 
                (tf_topic, tf_question, 
                 tf_statement1, tf_statement2, tf_statement3, tf_statement4, 
                 tf_correct_answer1, tf_correct_answer2, tf_correct_answer3, tf_correct_answer4, 
                 tf_image_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $types = "sssssssssss";
        $params = [
            $data['tf_topic'], $data['tf_question'],
            $data['tf_statement1'], $data['tf_statement2'], $data['tf_statement3'], $data['tf_statement4'],
            $data['tf_correct_answer1'], $data['tf_correct_answer2'], $data['tf_correct_answer3'], $data['tf_correct_answer4'],
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
