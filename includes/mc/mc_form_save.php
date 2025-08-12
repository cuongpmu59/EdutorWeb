<?php
header('Content-Type: application/json; charset=utf-8');

// Chỉ cho phép POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không hợp lệ.']);
    exit;
}

require_once __DIR__ . '/../../env/config.php'; 

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Bật chế độ ném lỗi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn->set_charset("utf8mb4");

try {
    // Lấy dữ liệu & loại bỏ khoảng trắng
    $data = array_map('trim', $_POST);
    $mc_id = filter_var($data['mc_id'] ?? null, FILTER_VALIDATE_INT);

    // Danh sách trường bắt buộc
    $requiredFields = ['mc_topic', 'mc_question', 'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4', 'mc_correct_answer'];

    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng nhập đầy đủ thông tin.']);
            exit;
        }
    }

    // Xác định câu SQL và tham số
    if ($mc_id) {
        $sql = "UPDATE mc_questions 
                SET topic=?, question=?, answer1=?, answer2=?, answer3=?, answer4=?, correct_answer=? 
                WHERE id=?";
        $params = [$data['mc_topic'], $data['mc_question'], $data['mc_answer1'], $data['mc_answer2'], $data['mc_answer3'], $data['mc_answer4'], $data['mc_correct_answer'], $mc_id];
        $types = "sssssssi";
    } else {
        $sql = "INSERT INTO mc_questions (topic, question, answer1, answer2, answer3, answer4, correct_answer) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $params = [$data['mc_topic'], $data['mc_question'], $data['mc_answer1'], $data['mc_answer2'], $data['mc_answer3'], $data['mc_answer4'], $data['mc_correct_answer']];
        $types = "sssssss";
    }

    // Thực thi câu lệnh
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $stmt->close();

    echo json_encode([
        'status' => 'success',
        'message' => $mc_id ? 'Cập nhật câu hỏi thành công.' : 'Thêm câu hỏi thành công.'
    ]);

} catch (mysqli_sql_exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
} finally {
    $conn->close();
}
