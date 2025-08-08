<?php
require_once __DIR__ . '/../env/config.php'; // chứa CLOUDINARY_CLOUD_NAME, CLOUDINARY_UPLOAD_PRESET, CLOUDINARY_API_KEY, CLOUDINARY_API_SECRET

header('Content-Type: application/json');

// Hàm gửi request CURL
function sendCurl($url, $data)
{
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
        exit;
    }
    echo $response; // Trả JSON Cloudinary
    exit;
}

// Nếu là upload ảnh (unsigned)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    if (empty($_FILES['image']['tmp_name'])) {
        echo json_encode(['error' => '❌ Không có file nào được tải lên']);
        exit;
    }

    $filePath = $_FILES['image']['tmp_name'];

    $url = "https://api.cloudinary.com/v1_1/" . CLOUDINARY_CLOUD_NAME . "/image/upload";

    $data = [
        'upload_preset' => CLOUDINARY_UPLOAD_PRESET, // unsigned preset
        'file' => new CURLFile($filePath)
    ];

    sendCurl($url, $data);
}

// Nếu là xóa ảnh (signed)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['public_id'])) {
    $public_id = trim($_POST['public_id']);
    if ($public_id === '') {
        echo json_encode(['error' => '❌ public_id trống']);
        exit;
    }

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

    sendCurl($url, $data);
}

// Nếu không đúng yêu cầu
echo json_encode(['error' => '❌ Request không hợp lệ']);
exit;
