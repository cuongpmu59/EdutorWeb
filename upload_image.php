<?php
require 'dotenv.php'; // để lấy biến môi trường từ .env

header('Content-Type: application/json');

// Kiểm tra file được gửi lên
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => '❌ Không tìm thấy file hợp lệ.']);
    exit;
}

// Lấy file tạm thời
$tempFile = $_FILES['file']['tmp_name'];
$cloudName = $_ENV['CLOUDINARY_CLOUD_NAME'];
$apiKey = $_ENV['CLOUDINARY_API_KEY'];
$apiSecret = $_ENV['CLOUDINARY_API_SECRET'];

// Tạo public_id tạm
$publicId = 'temp_' . time();

// Endpoint upload của Cloudinary
$uploadUrl = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";

// Tạo timestamp và signature
$timestamp = time();
$signatureData = "public_id={$publicId}&timestamp={$timestamp}{$apiSecret}";
$signature = sha1($signatureData);

// Dữ liệu POST
$postData = [
    'file' => new CURLFile($tempFile),
    'api_key' => $apiKey,
    'timestamp' => $timestamp,
    'public_id' => $publicId,
    'signature' => $signature
];

// Gửi request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $uploadUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Phân tích kết quả
if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo json_encode([
        'success' => true,
        'secure_url' => $data['secure_url'],
        'public_id' => $data['public_id']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => '❌ Lỗi upload: ' . $error . ' | Phản hồi: ' . $response
    ]);
}
