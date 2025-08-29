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
    $sa_id = filter_var($data['sa_id'] ?? null, FILTER_VALIDATE_INT);

    $requiredFields = ['sa_topic', 'sa_question', 'sa_correct_answer'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            exit(json_encode(['status' => 'error', 'message' => 'Vui lòng nhập đầy đủ thông tin.']));
        }
    }

    // Cho phép để trống ảnh
    $sa_image_url = !empty($data['sa_image_url']) ? $data['sa_image_url'] : null;

    if ($sa_id) {
        $sql = "UPDATE sa_questions 
                SET sa_topic=?, sa_question=?, sa_correct_answer=?, sa_image_url=? 
                WHERE sa_id=?";
        $types = "ssssi";
        $params = [
            $data['sa_topic'], $data['sa_question'],
            $data['sa_correct_answer'], $sa_image_url, $sa_id
        ];
    } else {
        $sql = "INSERT INTO sa_questions 
                (sa_topic, sa_question, sa_correct_answer, sa_image_url) 
                VALUES (?, ?, ?, ?)";
        $types = "ssss";
        $params = [
            $data['sa_topic'], $data['sa_question'],
            $data['sa_correct_answer'], $sa_image_url
        ];
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    echo json_encode([
        'status' => 'success',
        'message' => $sa_id ? 'Cập nhật câu hỏi thành công.' : 'Thêm câu hỏi thành công.'
    ]);
} catch (mysqli_sql_exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
} finally {
    $conn?->close();
}
