<?php
require 'db_connection.php';
require 'vendor/autoload.php'; // Gọi Cloudinary SDK
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

// ===== Lấy ảnh hiện tại từ DB =====
$image_url = '';
try {
    $stmt = $conn->prepare("SELECT image FROM questions WHERE id = ?");
    $stmt->execute([$id]);
    $image_url = $stmt->fetchColumn();

    if (!$image_url) {
        $image_url = '';
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi khi truy vấn ảnh: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ===== Xoá ảnh trên Cloudinary nếu có =====
if (!empty($image_url)) {
    \Cloudinary\Configuration\Configuration::instance([
        'cloud' => [
            'cloud_name' => 'dbdf2gwc9',
            'api_key'    => '451298475188791',
            'api_secret' => 'PK2QC' 
        ]
    ]);

    try {
        $publicId = "pic_$id";
        $result = \Cloudinary\Api\Upload::destroy($publicId);
        if (($result['result'] ?? '') !== 'ok') {
            error_log("⚠️ Không xoá được ảnh trên Cloudinary: pic_$id");
        }
    } catch (Exception $e) {
        error_log("❌ Lỗi khi xoá ảnh Cloudinary: " . $e->getMessage());
    }
}

// ===== Xoá câu hỏi trong DB =====
try {
    $stmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode([
        'status' => 'success',
        'message' => '✅ Đã xoá câu hỏi thành công.'
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => '❌ Lỗi khi xoá câu hỏi: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
