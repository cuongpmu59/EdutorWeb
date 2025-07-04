<?php
ob_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'db_connection.php';
require 'dotenv.php';
require 'vendor/autoload.php';

header("Content-Type: application/json; charset=utf-8");

$id = trim($_POST['id'] ?? '');

if (!$id || !is_numeric($id)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Thiếu hoặc sai ID câu hỏi.']);
    exit;
}

// Lấy thông tin ảnh trước khi xoá
$stmt = $conn->prepare("SELECT image FROM questions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($image_url);
$stmt->fetch();
$stmt->close();

// Xoá ảnh trên Cloudinary nếu có
if (!empty($image_url)) {
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
        error_log("❌ Lỗi khi xoá ảnh Cloudinary: " . $e->getMessage());
    }
}

// Xoá câu hỏi khỏi cơ sở dữ liệu
try {
    $stmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['status' => 'success', 'message' => '✅ Đã xoá câu hỏi.']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => '❌ Lỗi xoá câu hỏi: ' . $e->getMessage()]);
}

// Dọn dẹp buffer
$output = ob_get_clean();
if (strlen(trim($output)) > 0 && !str_starts_with(trim($output), '{')) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Có nội dung ngoài JSON: ' . $output
    ]);
}
