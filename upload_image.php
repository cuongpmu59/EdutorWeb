<?php
require 'dotenv.php';

header('Content-Type: application/json');

// Ghi log debug nếu có lỗi
function debugLog($message) {
    file_put_contents('upload_debug.log', date('c') . " - " . $message . "\n", FILE_APPEND);
}

// Kiểm tra file được gửi lên
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    debugLog('File upload failed or not set');
    echo json_encode(['success' => false, 'message' => '❌ Không tìm thấy file hợp lệ.']);
    exit;
}

$tempFile = $_FILES['file']['tmp_name'];

// Kiểm tra các biến môi trường
$cloudName = $_ENV['CLOUDINARY_CLOUD_NAME'] ?? '';
$apiKey = $_ENV['CLOUDINARY_API_KEY'] ?? '';
$apiSecret = $_ENV['CLOUDINARY_API_SECRET'] ?? '';

if (!$cloudName || !$apiKey || !$apiSecret) {
    debugLog('Thiếu thông tin Cloudinary từ .env');
    echo json_encode(['success' => false, 'message' => '❌ Thiếu thông tin cấu hình Cloudinary.']);
    exit;
}

$publicId = 'temp_' . time();
$timestamp = time();
$uploadUrl = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";
$signatureData = "public_id={$publicId}&timestamp={$timestamp}{$apiSecret}";
$signature = sha1($signatureData);

// Dữ liệu gửi đi
$postData = [
    'file' => new CURLFile($tempFile),
    'api_key' => $apiKey,
    'timestamp' => $timestamp,
    'public_id' => $publicId,
    'signature' => $signature
];

// Gửi đến Cloudinary
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $uploadUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Xử lý phản hồi
if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo json_encode([
        'success' => true,
        'secure_url' => $data['secure_url'] ?? '',
        'public_id' => $data['public_id'] ?? ''
    ]);
    debugLog("Upload OK: {$data['public_id']}");
} else {
    debugLog("Upload FAIL: HTTP $httpCode | $error | $response");
    echo json_encode([
        'success' => false,
        'message' => '❌ Lỗi upload: ' . $error . ' | Phản hồi: ' . $response
    ]);
}
