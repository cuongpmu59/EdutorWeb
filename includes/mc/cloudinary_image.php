<?php
header("Content-Type: application/json; charset=utf-8");

// Cấu hình Cloudinary
$cloud_name = "dbdf2gwc9";
$api_key    = "451298475188791";
$api_secret = "PK2QC";
$upload_preset = "my_exam_preset";

$action = $_POST['action'] ?? '';

if ($action === 'upload') {
    if (!isset($_FILES['file'])) {
        echo json_encode(["error" => "Không có file tải lên"]);
        exit;
    }

    $file = $_FILES['file']['tmp_name'];
    $url = "https://api.cloudinary.com/v1_1/$cloud_name/image/upload";

    $data = [
        "file" => new CURLFile($file),
        "upload_preset" => $upload_preset
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_RETURNTRANSFER => true
    ]);

    $res = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($res, true);

    if (isset($json['secure_url'])) {
        echo json_encode(["url" => $json['secure_url']]);
    } else {
        echo json_encode(["error" => $json['error']['message'] ?? "Upload thất bại"]);
    }
    exit;
}

if ($action === 'delete') {
    $public_id = $_POST['public_id'] ?? '';
    if (!$public_id) {
        echo json_encode(["error" => "Thiếu public_id"]);
        exit;
    }

    $timestamp = time();
    $string_to_sign = "public_id={$public_id}&timestamp={$timestamp}{$api_secret}";
    $signature = sha1($string_to_sign);

    $url = "https://api.cloudinary.com/v1_1/$cloud_name/image/destroy";

    $data = [
        "public_id" => $public_id,
        "api_key" => $api_key,
        "timestamp" => $timestamp,
        "signature" => $signature
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_RETURNTRANSFER => true
    ]);

    $res = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($res, true);

    if (isset($json['result']) && $json['result'] === 'ok') {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => $json['error']['message'] ?? "Xoá thất bại"]);
    }
    exit;
}

echo json_encode(["error" => "Yêu cầu không hợp lệ"]);
