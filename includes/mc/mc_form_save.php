<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Phương thức không hợp lệ.'
    ]);
    exit;
}

$mc_id            = isset($_POST['mc_id']) ? filter_var($_POST['mc_id'], FILTER_VALIDATE_INT) : null;
$mc_topic         = trim($_POST['mc_topic'] ?? '');
$mc_question      = trim($_POST['mc_question'] ?? '');
$mc_answer1       = trim($_POST['mc_answer1'] ?? '');
$mc_answer2       = trim($_POST['mc_answer2'] ?? '');
$mc_answer3       = trim($_POST['mc_answer3'] ?? '');
$mc_answer4       = trim($_POST['mc_answer4'] ?? '');
$mc_correct_answer= trim($_POST['mc_correct_answer'] ?? '');

if (
    $mc_topic === '' || $mc_question === '' ||
    $mc_answer1 === '' || $mc_answer2 === '' ||
    $mc_answer3 === '' || $mc_answer4 === '' ||
    $mc_correct_answer === ''
) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Vui lòng nhập đầy đủ thông tin.'
    ]);
    exit;
}

require_once __DIR__ . '/../../env/config.php'; 
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Kết nối CSDL thất bại: ' . $conn->connect_error
    ]);
    exit;
}
$conn->set_charset("utf8mb4");

// Lưu hoặc cập nhật
if ($mc_id) {
    $stmt = $conn->prepare("UPDATE mc_questions SET topic=?, question=?, answer1=?, answer2=?, answer3=?, answer4=?, correct_answer=? WHERE id=?");
    $stmt->bind_param("sssssssi", $mc_topic, $mc_question, $mc_answer1, $mc_answer2, $mc_answer3, $mc_answer4, $mc_correct_answer, $mc_id);
    $success = $stmt->execute();
    $stmt->close();
} else {
    $stmt = $conn->prepare("INSERT INTO mc_questions (topic, question, answer1, answer2, answer3, answer4, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $mc_topic, $mc_question, $mc_answer1, $mc_answer2, $mc_answer3, $mc_answer4, $mc_correct_answer);
    $success = $stmt->execute();
    $stmt->close();
}

$conn->close();

if ($success) {
    echo json_encode([
        'status' => 'success',
        'message' => $mc_id ? 'Cập nhật câu hỏi thành công.' : 'Thêm câu hỏi thành công.'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Không thể lưu dữ liệu.'
    ]);
}
