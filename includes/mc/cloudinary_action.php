<?php
// Hiển thị lỗi (chỉ bật khi debug, khi chạy production nên tắt)
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/../../env/config.php';

// Kiểm tra method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => '❌ Chỉ chấp nhận phương thức POST']);
    exit;
}

// Kiểm tra file
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => '❌ Không tìm thấy file upload hoặc file bị lỗi']);
    exit;
}

// Lấy file tạm
$fileTmpPath = $_FILES['file']['tmp_name'];

// Upload unsigned lên Cloudinary
$cloudName = CLOUDINARY_CLOUD_NAME;
$uploadPreset = CLOUDINARY_UPLOAD_PRESET;

// Endpoint unsigned upload
$uploadUrl = "https://api.cloudinary.com/v1_1/{$cloudName}/auto/upload";

// Dùng CURL gửi file
$ch = curl_init();

$data = [
    'upload_preset' => $uploadPreset,
    'file' => new CURLFile($fileTmpPath, mime_content_type($fileTmpPath), $_FILES['file']['name'])
];

curl_setopt_array($ch, [
    CURLOPT_URL => $uploadUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $data
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false) {
    echo json_encode(['error' => '❌ Lỗi CURL: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

// Giải mã JSON từ Cloudinary
$result = json_decode($response, true);

if ($httpCode !== 200 || isset($result['error'])) {
    echo json_encode(['error' => '❌ Upload thất bại', 'details' => $result]);
    exit;
}

// Trả về link ảnh
echo json_encode([
    'success' => true,
    'url' => $result['secure_url'],
    'public_id' => $result['public_id']
]);
