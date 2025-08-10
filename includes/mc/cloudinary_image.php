<?php
require_once __DIR__ . '/../../env/config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($action === 'upload' && isset($_FILES['file'])) {
    $timestamp = time();

    // Tạo signature
    $string_to_sign = "timestamp={$timestamp}" . CLOUDINARY_API_SECRET;
    $signature = sha1("timestamp={$timestamp}" . CLOUDINARY_API_SECRET);

    $file_path = $_FILES['file']['tmp_name'];

    $data = [
        'file' => new CURLFile($file_path),
        'api_key' => CLOUDINARY_API_KEY,
        'timestamp' => $timestamp,
        'signature' => $signature
    ];

    $url = "https://api.cloudinary.com/v1_1/" . CLOUDINARY_CLOUD_NAME . "/image/upload";

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    echo $response;
    exit;
}

if ($action === 'delete' && !empty($_POST['public_id'])) {
    $timestamp = time();
    $public_id = $_POST['public_id'];

    // Tạo signature cho xóa ảnh
    $string_to_sign = "public_id={$public_id}&timestamp={$timestamp}" . CLOUDINARY_API_SECRET;
    $signature = sha1("public_id={$public_id}&timestamp={$timestamp}" . CLOUDINARY_API_SECRET);

    $data = [
        'public_id' => $public_id,
        'api_key' => CLOUDINARY_API_KEY,
        'timestamp' => $timestamp,
        'signature' => $signature
    ];

    $url = "https://api.cloudinary.com/v1_1/" . CLOUDINARY_CLOUD_NAME . "/image/destroy";

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    echo $response;
    exit;
}

echo json_encode(['error' => 'Invalid request']);
