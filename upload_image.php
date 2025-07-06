<?php
require 'dotenv.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu tệp ảnh hoặc sai phương thức POST.']);
    exit;
}

$file = $_FILES['file']['tmp_name'];

$cloud_name = getenv('CLOUDINARY_CLOUD_NAME');
$api_key = getenv('CLOUDINARY_API_KEY');
$api_secret = getenv('CLOUDINARY_API_SECRET');
$upload_preset = getenv('CLOUDINARY_UPLOAD_PRESET');

if (!$cloud_name || !$api_key || !$api_secret || !$upload_preset) {
    echo json_encode(['success' => false, 'message' => 'Thiếu cấu hình môi trường.']);
    exit;
}

// Tạo chữ ký bảo mật (signature)
$timestamp = time();
$params_to_sign = [
    'timestamp' => $timestamp,
    'upload_preset' => $upload_preset
];
ksort($params_to_sign);
$signature_base = http_build_query($params_to_sign, '', '&', PHP_QUERY_RFC3986);
$signature = hash_hmac('sha1', urldecode($signature_base), $api_secret);

// Chuẩn bị dữ liệu gửi đi
$postfields = [
    'file' => new CURLFile($file),
    'api_key' => $api_key,
    'timestamp' => $timestamp,
    'upload_preset' => $upload_preset,
    'signature' => $signature
];

$upload_url = "https://api.cloudinary.com/v1_1/$cloud_name/image/upload";

// Gửi yêu cầu tới Cloudinary
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $upload_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Optional: Bỏ xác thực SSL nếu máy chủ không hỗ trợ
$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($response !== false) {
    echo $response;
} else {
    echo json_encode(['success' => false, 'message' => 'cURL Error: ' . $error]);
}
