<?php
header('Content-Type: application/json; charset=utf-8');

$cloud_name = "dbdf2gwc9"; // thay bằng cloud_name
$upload_preset = "my_exam_preset"; // preset unsigned
$api_key = "451298475188791"; // để xóa ảnh
$api_secret = "PK2QC"; // để xóa ảnh

$action = $_GET['action'] ?? '';

if ($action === 'upload') {
    if (!isset($_FILES['image'])) {
        echo json_encode(['error' => 'Không có file tải lên']);
        exit;
    }

    $file_path = $_FILES['image']['tmp_name'];

    $url = "https://api.cloudinary.com/v1_1/$cloud_name/image/upload";

    $data = [
        'upload_preset' => $upload_preset,
        'file' => new CURLFile($file_path)
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

    $result = json_decode($res, true);
    if (isset($result['secure_url'])) {
        echo json_encode(['url' => $result['secure_url']]);
    } else {
        echo json_encode(['error' => 'Upload thất bại', 'res' => $result]);
    }
    exit;
}

if ($action === 'delete') {
    if (empty($_POST['public_id'])) {
        echo json_encode(['error' => 'Thiếu public_id']);
        exit;
    }

    $public_id = $_POST['public_id'];

    $timestamp = time();
    $string_to_sign = "public_id={$public_id}&timestamp={$timestamp}{$api_secret}";
    $signature = sha1($string_to_sign);

    $url = "https://api.cloudinary.com/v1_1/$cloud_name/image/destroy";

    $data = [
        'public_id' => $public_id,
        'timestamp' => $timestamp,
        'api_key' => $api_key,
        'signature' => $signature
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

    $result = json_decode($res, true);
    if (isset($result['result']) && $result['result'] === 'ok') {
        echo json_encode(['result' => 'ok']);
    } else {
        echo json_encode(['error' => 'Xóa ảnh thất bại', 'res' => $result]);
    }
    exit;
}

echo json_encode(['error' => 'Yêu cầu không hợp lệ']);
