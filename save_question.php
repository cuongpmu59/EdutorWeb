<?php
require 'db_connection.php'; // Kết nối CSDL

// Hàm lấy giá trị từ POST và làm sạch
function get_post($key) {
    return trim($_POST[$key] ?? '');
}

// 1. Lấy dữ liệu từ POST
$id          = get_post('question_id');
$topic       = get_post('topic');
$question    = get_post('question');
$answer1     = get_post('answer1');
$answer2     = get_post('answer2');
$answer3     = get_post('answer3');
$answer4     = get_post('answer4');
$correct     = get_post('correct_answer');
$image_url   = get_post('image_url');
$delete_image = get_post('delete_image');

// 2. Xử lý xoá ảnh nếu được chọn
if ($delete_image === '1') {
    $image_url = '';
}

// 3. Kiểm tra hợp lệ đầu vào
$errors = [];
if (!$question) $errors[] = "Câu hỏi không được để trống.";
if (!$answer1 || !$answer2 || !$answer3 || !$answer4) $errors[] = "Tất cả đáp án đều phải điền.";
if (!in_array($correct, ['A', 'B', 'C', 'D'])) $errors[] = "Đáp án đúng phải là A, B, C hoặc D.";

if ($errors) {
    http_response_code(400);
    echo implode("\n", $errors);
    exit;
}

// 4. Kiểm tra trùng lặp câu hỏi (chỉ khi thêm mới hoặc cập nhật nội dung khác)
try {
    $stmt = $conn->prepare("SELECT id FROM questions WHERE question = ?");
    if (!$stmt) throw new Exception("Lỗi prepare: " . $conn->error);
    $stmt->bind_param("s", $question);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($existing_id);
        $stmt->fetch();

        // Nếu thêm mới hoặc cập nhật sang câu hỏi đã tồn tại
        if (!$id || ($id && $existing_id != $id)) {
            http_response_code(409); // Conflict
            echo "Câu hỏi đã tồn tại trong cơ sở dữ liệu.";
            exit;
        }
    }
    $stmt->close();
} catch (Exception $e) {
    http_response_code(500);
    echo "Lỗi kiểm tra trùng lặp: " . $e->getMessage();
    exit;
}

// 5. Thêm hoặc cập nhật
try {
    if ($id) {
        // Cập nhật
        $stmt = $conn->prepare("UPDATE questions SET topic=?, question=?, answer1=?, answer2=?, answer3=?, answer4=?, correct_answer=?, image=? WHERE id=?");
        if (!$stmt) throw new Exception("Lỗi prepare: " . $conn->error);

        $stmt->bind_param("ssssssssi", $topic, $question, $answer1, $answer2, $answer3, $answer4, $correct, $image_url, $id);
        $stmt->execute();

        echo "✅ Cập nhật câu hỏi thành công.";
    } else {
        // Thêm mới
        $stmt = $conn->prepare("INSERT INTO questions (topic, question, answer1, answer2, answer3, answer4, correct_answer, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) throw new Exception("Lỗi prepare: " . $conn->error);

        $stmt->bind_param("ssssssss", $topic, $question, $answer1, $answer2, $answer3, $answer4, $correct, $image_url);
        $stmt->execute();

        echo "✅ Đã thêm câu hỏi mới.";
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "Lỗi lưu câu hỏi: " . $e->getMessage();
}
