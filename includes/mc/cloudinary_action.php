<?php
require_once __DIR__ . '/../../env/config.php'; // chứa CLOUDINARY_* từ .env

header('Content-Type: application/json; charset=utf-8');

// ======= HÀM UPLOAD ẢNH =======
function uploadImageToCloudinary($filePath) {
    $cloud_name = CLOUDINARY_CLOUD_NAME;
    $api_key    = CLOUDINARY_API_KEY;
    $api_secret = CLOUDINARY_API_SECRET;

    $url = "https://api.cloudinary.com/v1_1/{$cloud_name}/image/upload";
    $timestamp = time();
    $signature = sha1("timestamp={$timestamp}{$api_secret}");

    $data = [
        'file' => new CURLFile($filePath),
        'api_key' => $api_key,
        'timestamp' => $timestamp,
        'signature' => $signature
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result, true);
}

// ======= HÀM XOÁ ẢNH =======
function deleteImageFromCloudinary($public_id) {
    $cloud_name = CLOUDINARY_CLOUD_NAME;
    $api_key    = CLOUDINARY_API_KEY;
    $api_secret = CLOUDINARY_API_SECRET;

    $url = "https://api.cloudinary.com/v1_1/{$cloud_name}/image/destroy";
    $timestamp = time();
    $signature = sha1("public_id={$public_id}&timestamp={$timestamp}{$api_secret}");

    $data = [
        'public_id' => $public_id,
        'api_key' => $api_key,
        'timestamp' => $timestamp,
        'signature' => $signature
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result, true);
}

// ======= XỬ LÝ YÊU CẦU =======
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image'])) {
        $upload = uploadImageToCloudinary($_FILES['image']['tmp_name']);
        echo json_encode($upload);
        exit;
    }
    if (isset($_POST['public_id'])) {
        $delete = deleteImageFromCloudinary($_POST['public_id']);
        echo json_encode($delete);
        exit;
    }
}

echo json_encode(['error' => 'No action performed']);
