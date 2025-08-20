<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../env/config.php';

$cloud_name     = CLOUDINARY_CLOUD_NAME;
$api_key        = CLOUDINARY_API_KEY;
$api_secret     = CLOUDINARY_API_SECRET;
$upload_preset  = CLOUDINARY_UPLOAD_PRESET;

$action = $_POST['action'] ?? '';

if ($action === 'upload') {
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['error' => 'Không có file tải lên']);
        exit;
    }

    $fileTmpPath = $_FILES['file']['tmp_name'];
    $uploadUrl = "https://api.cloudinary.com/v1_1/{$cloud_name}/image/upload";

    $data = [
        'upload_preset' => $upload_preset, // unsigned preset
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

    echo $response; // Cloudinary trả JSON về cho JS
    exit;
}

if ($action === 'delete') {
    $publicId = $_POST['public_id'] ?? '';
    if (!$publicId) {
        echo json_encode(['error' => 'Thiếu public_id']);
        exit;
    }

    $deleteUrl = "https://api.cloudinary.com/v1_1/{$cloud_name}/resources/image/upload";

    $data = [
        'public_ids[]' => $publicId,
        'invalidate'   => 'true'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $deleteUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_USERPWD, "{$api_key}:{$api_secret}");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        echo json_encode(['result' => 'ok', 'cloudinary_response' => json_decode($response, true)]);
    } else {
        echo json_encode(['error' => "Xóa thất bại", 'status' => $http_code, 'cloudinary_response' => $response]);
    }
    exit;
}

// Nếu action không hợp lệ
echo json_encode(['error' => 'Yêu cầu không hợp lệ']);
