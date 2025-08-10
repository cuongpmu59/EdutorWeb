<?php
header('Content-Type: application/json; charset=utf-8');

// Tải biến môi trường từ .env
require_once __DIR__ . '/../../env/config.php'; 

$action = $_POST['action'] ?? '';

if ($action === 'upload') {
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['error' => 'Không có file tải lên']);
        exit;
    }

    $fileTmpPath = $_FILES['file']['tmp_name'];
    $uploadUrl = "https://api.cloudinary.com/v1_1/" . CLOUDINARY_CLOUD_NAME . "/image/upload";

    $data = [
        'upload_preset' => CLOUDINARY_UPLOAD_PRESET, // unsigned preset
        'file' => new CURLFile($fileTmpPath)
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $uploadUrl,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $data
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    echo $response;
    exit;
}

if ($action === 'delete') {
    $publicId = $_POST['public_id'] ?? '';
    if (!$publicId) {
        echo json_encode(['error' => 'Thiếu public_id']);
        exit;
    }

    // Tạo signature để xóa
    $timestamp = time();
    $stringToSign = "public_id={$publicId}&timestamp={$timestamp}" . CLOUDINARY_API_SECRET;
    $signature = sha1($stringToSign);

    $deleteUrl = "https://api.cloudinary.com/v1_1/" . CLOUDINARY_CLOUD_NAME . "/image/destroy";

    $data = [
        'public_id' => $publicId,
        'api_key' => CLOUDINARY_API_KEY,
        'timestamp' => $timestamp,
        'signature' => $signature
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $deleteUrl,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $data
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    echo $response;
    exit;
}

echo json_encode(['error' => 'Yêu cầu không hợp lệ']);
