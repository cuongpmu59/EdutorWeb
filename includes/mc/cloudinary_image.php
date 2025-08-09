<?php
// includes/mc/cloudinary_image.php

require_once __DIR__ . '/../../env/config.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

$cloud_name    = CLOUDINARY_CLOUD_NAME;
$upload_preset = CLOUDINARY_UPLOAD_PRESET;
$api_key       = CLOUDINARY_API_KEY;
$api_secret    = CLOUDINARY_API_SECRET;

$action = $_POST['action'] ?? '';

if ($action === 'upload') {
    // Upload ảnh lên Cloudinary (unsigned)
    if (!isset($_FILES['file']['tmp_name'])) {
        echo json_encode(["error" => "Không có file tải lên"]);
        exit;
    }

    $file_path = $_FILES['file']['tmp_name'];
    $url = "https://api.cloudinary.com/v1_1/{$cloud_name}/image/upload";

    $data = [
        "file" => new CURLFile($file_path),
        "upload_preset" => $upload_preset
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo json_encode(["error" => curl_error($ch)]);
        curl_close($ch);
        exit;
    }
    curl_close($ch);

    echo $response;
    exit;
}

if ($action === 'delete') {
    // Xóa ảnh theo public_id
    $public_id = $_POST['public_id'] ?? '';
    if (!$public_id) {
        echo json_encode(["error" => "Thiếu public_id để xóa"]);
        exit;
    }

    $timestamp = time();
    $string_to_sign = "public_id={$public_id}&timestamp={$timestamp}{$api_secret}";
    $signature = sha1($string_to_sign);

    $url = "https://api.cloudinary.com/v1_1/{$cloud_name}/image/destroy";

    $data = [
        "public_id" => $public_id,
        "timestamp" => $timestamp,
        "api_key"   => $api_key,
        "signature" => $signature
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo json_encode(["error" => curl_error($ch)]);
        curl_close($ch);
        exit;
    }
    curl_close($ch);

    echo $response;
    exit;
}

echo json_encode(["error" => "Hành động không hợp lệ"]);
exit;
