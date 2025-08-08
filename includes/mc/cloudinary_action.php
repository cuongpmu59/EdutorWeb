<?php
require_once __DIR__ . '/../env/config.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($action === 'upload' && isset($_FILES['image'])) {
    if (empty($_FILES['image']['tmp_name'])) {
        echo json_encode(['error' => '❌ Không có file nào được tải lên']);
        exit;
    }

    $filePath = $_FILES['image']['tmp_name'];
    $url = "https://api.cloudinary.com/v1_1/" . CLOUDINARY_CLOUD_NAME . "/image/upload";
    $data = [
        'upload_preset' => CLOUDINARY_UPLOAD_PRESET,
        'file' => new CURLFile($filePath)
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    echo $err ? json_encode(['error' => '❌ Lỗi CURL: ' . $err]) : $response;
    exit;
}

if ($action === 'delete' && !empty($_POST['public_id'])) {
    $public_id = trim($_POST['public_id']);
    $timestamp = time();
    $string_to_sign = "public_id={$public_id}&timestamp={$timestamp}" . CLOUDINARY_API_SECRET;
    $signature = sha1($string_to_sign);

    $url = "https://api.cloudinary.com/v1_1/" . CLOUDINARY_CLOUD_NAME . "/image/destroy";
    $data = [
        'public_id' => $public_id,
        'timestamp' => $timestamp,
        'api_key' => CLOUDINARY_API_KEY,
        'signature' => $signature
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    echo $err ? json_encode(['error' => '❌ Lỗi CURL: ' . $err]) : $response;
    exit;
}

echo json_encode(['error' => '❌ Request không hợp lệ']);
exit;
