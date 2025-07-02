<?php
ob_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'db_connection.php';
header("Content-Type: application/json; charset=utf-8");

function get_post($key) {
    return trim($_POST[$key] ?? '');
}

$id = get_post('question_id');
$topic = get_post('topic');
$question = get_post('question');
$answer1 = get_post('answer1');
$answer2 = get_post('answer2');
$answer3 = get_post('answer3');
$answer4 = get_post('answer4');
$correct = get_post('correct_answer');
$image_url = get_post('image_url');
$delete_image = get_post('delete_image');

if ($delete_image === '1' && $id) {
    require 'vendor/autoload.php';

    \Cloudinary\Configuration\Configuration::instance([
        'cloud' => [
            'cloud_name' => getenv('CLOUDINARY_CLOUD_NAME'),
            'api_key'    => getenv('CLOUDINARY_API_KEY'),
            'api_secret' => getenv('CLOUDINARY_API_SECRET'),
        ]
    ]);

    try {
        $publicId = "pic_$id";
        $result = \Cloudinary\Api\Upload::destroy($publicId);
        if (($result['result'] ?? '') !== 'ok') {
            error_log("⚠️ Không xoá được ảnh Cloudinary: $publicId");
        }
    } catch (Exception $e) {
        error_log("❌ Lỗi xoá ảnh Cloudinary: " . $e->getMessage());
    }

    $image_url = '';
}

$errors = [];
if (!$question) $errors[] = "Câu hỏi không được để trống.";
if (!$answer1 || !$answer2 || !$answer3 || !$answer4) $errors[] = "Tất cả đáp án đều phải điền.";
if (!in_array($correct, ['A', 'B', 'C', 'D'])) $errors[] = "Đáp án đúng phải là A, B, C hoặc D.";
if (!$topic) $errors[] = "Chủ đề không được để trống.";

if ($errors) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => implode(" ", $errors)
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Kiểm tra trùng
$stmt = $conn->prepare("SELECT id FROM questions WHERE question = ?");
$stmt->bind_param("s", $question);
$stmt->execute();
$existing_id = null;
$stmt->bind_result($existing_id);
$stmt->fetch();

$stmt->close();

if ($existing_id && (!$id || $existing_id != $id)) {
    http_response_code(409);
    echo json_encode(['status' => 'duplicate', 'message' => '⚠️ Câu hỏi đã tồn tại trong cơ sở dữ liệu.']);
    exit;
}

try {
    if ($id) {
        $stmt = $conn->prepare("UPDATE questions SET question=?, answer1=?, answer2=?, answer3=?, answer4=?, correct_answer=?, topic=?, image=? WHERE id=?");
        $stmt->bind_param("ssssssssi", $question, $answer1, $answer2, $answer3, $answer4, $correct, $topic, $image_url, $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success', 'message' => '✅ Cập nhật câu hỏi thành công.']);
    } else {
        $stmt = $conn->prepare("INSERT INTO questions (topic, question, image, answer1, answer2, answer3, answer4, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $topic, $question, $image_url, $answer1, $answer2, $answer3, $answer4, $correct);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status' => 'success', 'message' => '✅ Đã thêm câu hỏi mới.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Lỗi lưu câu hỏi: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}

// Đảm bảo không có gì ngoài JSON bị gửi
$output = ob_get_clean();
if (strlen(trim($output)) > 0 && !str_starts_with(trim($output), '{')) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Có nội dung ngoài JSON: ' . $output], JSON_UNESCAPED_UNICODE);
}

