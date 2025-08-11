<?php
header('Content-Type: application/json; charset=utf-8');
// require_once __DIR__ . '/../../env/config.php';
$cloud_name = "dbdf2gwc9"; 
$api_key    = "451298475188791";
$api_secret = "e-lLavuDlEKvm3rg-Tg_P6yMM3o";
$upload_preset = "my_exam_preset";

$action = $_POST['action'] ?? '';

if ($action === 'upload' && isset($_FILES['file'])) {
    $file = $_FILES['file']['tmp_name'];
    $upload_url = "https://api.cloudinary.com/v1_1/{$cloud_name}/image/upload";

    $data = [
        'file' => new CURLFile($file),
        'upload_preset' => $upload_preset
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $upload_url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_RETURNTRANSFER => true
    ]);
    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (!empty($response['secure_url'])) {
        echo json_encode(['success' => true, 'url' => $response['secure_url']]);
    } else {
        echo json_encode(['success' => false, 'message' => $response['error']['message'] ?? 'Upload thất bại']);
    }
    exit;
}

if ($action === 'delete' && !empty($_POST['image_url'])) {
    $image_url = $_POST['image_url'];
    $parts = explode('/', parse_url($image_url, PHP_URL_PATH));
    $public_id_with_ext = end($parts);
    $public_id = pathinfo($public_id_with_ext, PATHINFO_FILENAME);

    $timestamp = time();
    $string_to_sign = "public_id={$public_id}&timestamp={$timestamp}{$api_secret}";
    $signature = sha1($string_to_sign);

    $delete_url = "https://api.cloudinary.com/v1_1/{$cloud_name}/image/destroy";
    $data = [
        'public_id' => $public_id,
        'api_key' => $api_key,
        'timestamp' => $timestamp,
        'signature' => $signature
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $delete_url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_RETURNTRANSFER => true
    ]);
    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (!empty($response['result']) && $response['result'] === 'ok') {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $response['error']['message'] ?? 'Xóa thất bại']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
