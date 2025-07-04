<?php
ob_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'db_connection.php';
require 'dotenv.php'; // Đảm bảo bạn đã có dotenv.php để load biến môi trường
header("Content-Type: application/json; charset=utf-8");

function get_post($key) {
    return trim($_POST[$key] ?? '');
}

$id         = get_post('question_id');
$topic      = get_post('topic');
$question   = get_post('question');
$answer1    = get_post('answer1');
$answer2    = get_post('answer2');
$answer3    = get_post('answer3');
$answer4    = get_post('answer4');
$correct    = get_post('correct_answer');
$image_url  = get_post('image_url');
$delete_img = get_post('delete_image');

$errors = [];
if (!$id) $errors[] = "ID không hợp lệ.";
if (!$topic) $errors[] = "Chủ đề không được để trống.";
if (!$question) $errors[] = "Câu hỏi không được để trống.";
if (!$answer1 || !$answer2 || !$answer3 || !$answer4) $errors[] = "Tất cả đáp án phải được điền.";
if (!in_array($correct, ['A', 'B', 'C', 'D'])) $errors[] = "Đáp án đúng phải là A, B, C hoặc D.";

if ($errors) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => implode(" ", $errors)], JSON_UNESCAPED_UNICODE);
    exit;
}

// Xử lý xoá ảnh nếu được yêu cầu
if ($delete_img === '1') {
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

    $image_url = ''; // Xoá đường dẫn ảnh khỏi DB
}

// Kiểm tra trùng câu hỏi (nếu khác ID hiện tại)
$stmt = $conn->prepare("SELECT id FROM questions WHERE question = ? AND id != ?");
$stmt->bind_param("si", $question, $id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['status' => 'duplicate', 'message' => '⚠️ Câu hỏi đã tồn tại.']);
    exit;
}
$stmt->close();

// Cập nhật câu hỏi
try {
    $stmt = $conn->prepare("UPDATE questions SET topic=?, question=?, answer1=?, answer2=?, answer3=?, answer4=?, correct_answer=?, image=? WHERE id=?");
    $stmt->bind_param("ssssssssi", $topic, $question, $answer1, $answer2, $answer3, $answer4, $correct, $image_url, $id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['status' => 'success', 'message' => '✅ Cập nhật câu hỏi thành công.', 'new_image_url' => $image_url]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => '❌ Lỗi cập nhật: ' . $e->getMessage()]);
}

// Kiểm tra rò rỉ ngoài JSON
$output = ob_get_clean();
if (strlen(trim($output)) > 0 && !str_starts_with(trim($output), '{')) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Có nội dung ngoài JSON: ' . $output], JSON_UNESCAPED_UNICODE);
}
