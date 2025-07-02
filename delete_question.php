<?php
require 'db_connection.php';
require 'vendor/autoload.php'; // Cloudinary SDK
header("Content-Type: application/json; charset=utf-8");

// ===== Lấy ID =====
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'ID không hợp lệ.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ===== Lấy ảnh hiện tại =====
$image_url = '';
$stmt = $conn->prepare("SELECT image FROM questions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($image_url);
if (!$stmt->fetch()) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'Không tìm thấy câu hỏi.'
    ], JSON_UNESCAPED_UNICODE);
    $stmt->close();
    exit;
}
$stmt->close();

// ===== Xoá ảnh trên Cloudinary nếu có =====
if (!empty($image_url)) {
    // Thiết lập thông tin Cloudinary
    \Cloudinary\Configuration\Configuration::instance([
        'cloud' => [
            'cloud_name' => 'dbdf2gwc9',
            'api_key'    => '451298475188791',
            'api_secret' => '*****************',
        ]
    ]);

    try {
        $publicId = "pic_$id";
        $result = \Cloudinary\Api\Upload::destroy($publicId);
        // Có thể kiểm tra: $result['result'] === 'ok'
    } catch (Exception $e) {
        error_log("❌ Lỗi xoá ảnh Cloudinary: " . $e->getMessage());
    }
}

// ===== Xoá câu hỏi =====
try {
    $stmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    echo json_encode([
        'status' => 'success',
        'message' => '✅ Đã xoá câu hỏi thành công.'
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi khi xoá: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
