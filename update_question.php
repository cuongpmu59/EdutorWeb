<?php
require 'db_connection.php';
header("Content-Type: application/json; charset=utf-8");

// ===== Hàm lấy dữ liệu =====
function get_post($key) {
    return trim($_POST[$key] ?? '');
}

// ===== Nhận dữ liệu =====
$id            = get_post('question_id');
$topic         = get_post('topic');
$question      = get_post('question');
$answer1       = get_post('answer1');
$answer2       = get_post('answer2');
$answer3       = get_post('answer3');
$answer4       = get_post('answer4');
$correct       = get_post('correct_answer');
$image_url     = get_post('image_url');
$delete_image  = get_post('delete_image');

if ($delete_image === '1') {
    $image_url = '';
}

// ===== Kiểm tra hợp lệ =====
$errors = [];
if (!$id || !is_numeric($id)) $errors[] = "ID câu hỏi không hợp lệ.";
if (!$question) $errors[] = "Câu hỏi không được để trống.";
if (!$answer1 || !$answer2 || !$answer3 || !$answer4) $errors[] = "Tất cả đáp án đều phải điền.";
if (!in_array($correct, ['A', 'B', 'C', 'D'])) $errors[] = "Đáp án đúng phải là A, B, C hoặc D.";
if (!$topic) $errors[] = "Chủ đề không được để trống.";

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => implode(" ", $errors)
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ===== Xử lý CSDL =====
try {
    // ==== Kiểm tra trùng câu hỏi khác (trừ chính nó) ====
    $stmt = $conn->prepare("SELECT id FROM questions WHERE question = ? AND id != ?");
    $stmt->execute([$question, $id]);

    if ($stmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode([
            'status' => 'duplicate',
            'message' => '⚠️ Câu hỏi đã tồn tại.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ==== Cập nhật dữ liệu ====
    $stmt = $conn->prepare("UPDATE questions 
        SET topic = ?, question = ?, image = ?, answer1 = ?, answer2 = ?, answer3 = ?, answer4 = ?, correct_answer = ?
        WHERE id = ?");
    $stmt->execute([$topic, $question, $image_url, $answer1, $answer2, $answer3, $answer4, $correct, $id]);

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => '✅ Cập nhật câu hỏi thành công.'
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => '❌ Lỗi CSDL: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
