<?php
header('Content-Type: application/json');
require_once __DIR__ . '/env/dotenv.php';
require_once __DIR__ . '/env/config.php';

// ===== Hàm Upload Ảnh =====
function uploadImageToCloudinary($filePath, $folder = "uploads") {
    $cloudName = CLOUDINARY_CLOUD_NAME;
    $apiKey    = CLOUDINARY_API_KEY;
    $apiSecret = CLOUDINARY_API_SECRET;

    $timestamp = time();
    $url = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";

    $params_to_sign = "folder={$folder}&timestamp={$timestamp}{$apiSecret}";
    $signature = sha1($params_to_sign);

    $postFields = [
        'file'      => new CURLFile($filePath),
        'api_key'   => $apiKey,
        'timestamp' => $timestamp,
        'folder'    => $folder,
        'signature' => $signature
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postFields
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// ===== Hàm Xoá Ảnh =====
function deleteImageFromCloudinary($publicId) {
    $cloudName = CLOUDINARY_CLOUD_NAME;
    $apiKey    = CLOUDINARY_API_KEY;
    $apiSecret = CLOUDINARY_API_SECRET;

    $timestamp = time();
    $url = "https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy";

    $params_to_sign = "public_id={$publicId}&timestamp={$timestamp}{$apiSecret}";
    $signature = sha1($params_to_sign);

    $postFields = [
        'public_id' => $publicId,
        'api_key'   => $apiKey,
        'timestamp' => $timestamp,
        'signature' => $signature
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postFields
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// ===== Xử lý Request =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload = uploadImageToCloudinary($_FILES['image']['tmp_name']);
        echo json_encode($upload);
        exit;
    }

    // Delete
    if (!empty($_POST['public_id_delete'])) {
        $delete = deleteImageFromCloudinary($_POST['public_id_delete']);
        echo json_encode($delete);
        exit;
    }
}

echo json_encode(['error' => 'Yêu cầu không hợp lệ']);
