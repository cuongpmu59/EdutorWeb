<?php
ob_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'db_connection.php';
header("Content-Type: application/json; charset=utf-8");

// Hàm tiện ích
function get_post($key) {
    return trim($_POST[$key] ?? '');
}

// Lấy dữ liệu POST
$topic     = get_post('topic');
$question  = get_post('question');
$answer1   = get_post('answer1');
$answer2   = get_post('answer2');
$answer3   = get_post('answer3');
$answer4   = get_post('answer4');
$correct   = get_post('correct_answer');

// Kiểm tra dữ liệu
$errors = [];
if (!$topic)       $errors[] = "Chủ đề không được để trống.";
if (!$question)    $errors[] = "Câu hỏi không được để trống.";
if (!$answer1 || !$answer2 || !$answer3 || !$answer4) $errors[] = "Tất cả đáp án phải được điền.";
if (!in_array($correct, ['A', 'B', 'C', 'D'])) $errors[] = "Đáp án đúng phải là A, B, C hoặc D.";

if ($errors) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => implode(" ", $errors)], JSON_UNESCAPED_UNICODE);
    exit;
}

// Kiểm tra trùng câu hỏi
$stmt = $conn->prepare("SELECT id FROM questions WHERE question = ?");
$stmt->bind_param("s", $question);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['status' => 'duplicate', 'message' => '⚠️ Câu hỏi đã tồn tại trong cơ sở dữ liệu.']);
    exit;
}
$stmt->close();

// Thêm câu hỏi mới (chưa có ảnh)
try {
    $stmt = $conn->prepare("INSERT INTO questions (topic, question, answer1, answer2, answer3, answer4, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $topic, $question, $answer1, $answer2, $answer3, $answer4, $correct);
    $stmt->execute();

    $new_id = $stmt->insert_id;
    $stmt->close();

    echo json_encode([
        'status' => 'success',
        'message' => '✅ Đã thêm câu hỏi mới.',
        'id' => $new_id
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => '❌ Lỗi khi thêm câu hỏi: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

// Xoá bất kỳ nội dung không mong muốn trong buffer
$output = ob_get_clean();
if (strlen(trim($output)) > 0 && !str_starts_with(trim($output), '{')) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Có nội dung ngoài JSON: ' . $output], JSON_UNESCAPED_UNICODE);
}
