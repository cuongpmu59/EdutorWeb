<?php
require 'db_connection.php';
header("Content-Type: application/json; charset=utf-8");

function get_post($key) {
    return trim($_POST[$key] ?? '');
}

$topic     = get_post('topic');
$question  = get_post('question');
$answer1   = get_post('answer1');
$answer2   = get_post('answer2');
$answer3   = get_post('answer3');
$answer4   = get_post('answer4');
$correct   = get_post('correct_answer');
$image_url = get_post('image_url');
$delete_image = get_post('delete_image');

if ($delete_image === '1') {
    $image_url = '';
}

// ==== Validation ====
$errors = [];
if (!$question) $errors[] = "Câu hỏi không được để trống.";
if (!$answer1 || !$answer2 || !$answer3 || !$answer4) $errors[] = "Tất cả đáp án đều phải điền.";
if (!in_array($correct, ['A', 'B', 'C', 'D'])) $errors[] = "Đáp án đúng phải là A, B, C hoặc D.";
if (!$topic) $errors[] = "Chủ đề không được để trống.";

if ($errors) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => implode(" ", $errors)], JSON_UNESCAPED_UNICODE);
    exit;
}

// ==== Kiểm tra trùng ====
$stmt = $conn->prepare("SELECT id FROM questions WHERE question = ?");
$stmt->bind_param("s", $question);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['status' => 'duplicate', 'message' => '⚠️ Câu hỏi đã tồn tại.'], JSON_UNESCAPED_UNICODE);
    exit;
}
$stmt->close();

// ==== Thêm ====
$stmt = $conn->prepare("INSERT INTO questions (topic, question, image, answer1, answer2, answer3, answer4, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $topic, $question, $image_url, $answer1, $answer2, $answer3, $answer4, $correct);
$stmt->execute();
$stmt->close();

echo json_encode(['status' => 'success', 'message' => '✅ Đã thêm câu hỏi mới.'], JSON_UNESCAPED_UNICODE);
