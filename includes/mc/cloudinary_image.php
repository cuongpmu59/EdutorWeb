<?php
require_once __DIR__ . '/../env/dotenv.php';

// Lấy config từ .env
$cloudName     = env('CLOUDINARY_CLOUD_NAME');
$apiKey        = env('CLOUDINARY_API_KEY');
$apiSecret     = env('CLOUDINARY_API_SECRET');
$uploadPreset  = env('CLOUDINARY_UPLOAD_PRESET');

header('Content-Type: application/json; charset=utf-8');

// Kiểm tra phương thức
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$action = $_POST['action'] ?? '';

// ========== UPLOAD ẢNH ==========
if ($action === 'upload') {
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['error' => 'Không có file tải lên']);
        exit;
    }

    $uploadUrl = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";

    $postFields = [
        'file' => new CURLFile($_FILES['file']['tmp_name']),
        'upload_preset' => $uploadPreset
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $uploadUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

    $response = curl_exec($ch);
    curl_close($ch);

    echo $response;
    exit;
}

// ========== XÓA ẢNH ==========
if ($action === 'delete') {
    $publicId = $_POST['public_id'] ?? '';
    if (!$publicId) {
        echo json_encode(['error' => 'Thiếu public_id']);
        exit;
    }

    $deleteUrl = "https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy";

    // Cloudinary yêu cầu signature: sha1(string_to_sign + api_secret)
    $timestamp = time();
    $stringToSign = "public_id={$publicId}&timestamp={$timestamp}{$apiSecret}";
    $signature = sha1($stringToSign);

    $postFields = [
        'public_id' => $publicId,
        'api_key'   => $apiKey,
        'timestamp' => $timestamp,
        'signature' => $signature
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $deleteUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

    $response = curl_exec($ch);
    curl_close($ch);

    echo $response;
    exit;
}

echo json_encode(['error' => 'Hành động không hợp lệ']);
