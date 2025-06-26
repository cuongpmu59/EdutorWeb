<?php
require 'db_connection.php'; // Kết nối CSDL
header("Content-Type: application/json; charset=utf-8");

// Hàm lấy và làm sạch dữ liệu từ POST
function get_post($key) {
    return trim($_POST[$key] ?? '');
}

// Lấy dữ liệu từ form
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

// Nếu chọn xoá ảnh
if ($delete_image === '1') {
    $image_url = '';
}

// Kiểm tra hợp lệ dữ liệu
$errors = [];
if (!$question) $errors[] = "Câu hỏi không được để trống.";
if (!$answer1 || !$answer2 || !$answer3 || !$answer4) $errors[] = "Tất cả đáp án đều phải điền.";
if (!in_array($correct, ['A', 'B', 'C', 'D'])) $errors[] = "Đáp án đúng phải là A, B, C hoặc D.";
if (!$topic) $errors[] = "Chủ đề không được để trống.";

if ($errors) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => implode(" ", $errors)]);
    exit;
}

// Kiểm tra trùng lặp câu hỏi (chỉ khi thêm mới hoặc thay đổi nội dung)
try {
    $stmt = $conn->prepare("SELECT id FROM questions WHERE question = ?");
    if (!$stmt) throw new Exception("Lỗi prepare: " . $conn->error);

    $stmt->bind_param("s", $question);
    $stmt->execute();
    $stmt->bind_result($existing_id);
    $stmt->fetch();
    $stmt->close();

    // Nếu đã tồn tại và đang thêm mới, hoặc đang cập nhật sang một câu hỏi đã có
    if ($existing_id && (!$id || $existing_id != $id)) {
        http_response_code(409); // Conflict
        echo json_encode(['status' => 'duplicate', 'message' => '⚠️ Câu hỏi đã tồn tại trong cơ sở dữ liệu.']);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Lỗi kiểm tra trùng lặp: ' . $e->getMessage()]);
    exit;
}

// Thêm hoặc cập nhật
try {
    if ($id) {
        // Cập nhật
        $stmt = $conn->prepare("UPDATE questions SET topic=?, question=?, image=?, answer1=?, answer2=?, answer3=?, answer4=?, correct_answer=? WHERE id=?");
        if (!$stmt) throw new Exception("Lỗi prepare: " . $conn->error);

        $stmt->bind_param("ssssssssi", $topic, $question, $image_url, $answer1, $answer2, $answer3, $answer4, $correct, $id);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['status' => 'success', 'message' => '✅ Cập nhật câu hỏi thành công.']);
    } else {
        // Thêm mới
        $stmt = $conn->prepare("INSERT INTO questions (topic, question, image, answer1, answer2, answer3, answer4, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) throw new Exception("Lỗi prepare: " . $conn->error);

        $stmt->bind_param("sssssssss", $topic, $question, $image_url, $answer1, $answer2, $answer3, $answer4, $correct);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['status' => 'success', 'message' => '✅ Đã thêm câu hỏi mới.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Lỗi lưu câu hỏi: ' . $e->getMessage()]);
}
