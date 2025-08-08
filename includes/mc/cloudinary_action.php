<?php
require_once __DIR__ . '/../env/config.php'; // chứa CLOUDINARY_CLOUD_NAME & CLOUDINARY_UPLOAD_PRESET

header('Content-Type: application/json');

// ✅ Upload ảnh (unsigned)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    if (!isset($_FILES['image']['tmp_name']) || empty($_FILES['image']['tmp_name'])) {
        echo json_encode(['error' => '❌ Không có file nào được tải lên']);
        exit;
    }

    $filePath = $_FILES['image']['tmp_name'];

    // API unsigned upload
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

    if ($err) {
        echo json_encode(['error' => '❌ Lỗi CURL: ' . $err]);
    } else {
        echo $response; // Cloudinary trả về JSON
    }
    exit;
}

// ✅ Xóa ảnh (signed) - cần API Key & Secret
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['public_id'])) {
    $public_id = trim($_POST['public_id']);
    if ($public_id === '') {
        echo json_encode(['error' => '❌ public_id trống']);
        exit;
    }

    // Tạo signature
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

    if ($err) {
        echo json_encode(['error' => '❌ Lỗi CURL: ' . $err]);
    } else {
        echo $response; // Cloudinary trả về JSON
    }
    exit;
}

// ❌ Nếu không phải upload hay delete
echo json_encode(['error' => '❌ Request không hợp lệ']);
exit;
