<?php
require 'dotenv.php';

header('Content-Type: application/json');

// Kiểm tra yêu cầu POST và file upload
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu tệp ảnh hoặc sai phương thức POST.']);
    exit;
}

// Lấy file từ form
$file = $_FILES['file']['tmp_name'] ?? null;
if (!$file || !is_uploaded_file($file)) {
    echo json_encode(['success' => false, 'message' => 'Tệp không hợp lệ hoặc không được tải lên đúng cách.']);
    exit;
}

// Lấy biến môi trường
$cloud_name = getenv('CLOUDINARY_CLOUD_NAME');
$api_key = getenv('CLOUDINARY_API_KEY');
$api_secret = getenv('CLOUDINARY_API_SECRET');
$upload_preset = getenv('CLOUDINARY_UPLOAD_PRESET');

if (!$cloud_name || !$api_key || !$api_secret || !$upload_preset) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin cấu hình môi trường.']);
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

// Chuẩn bị dữ liệu gửi đến Cloudinary
$postfields = [
    'file' => new CURLFile($file),
    'api_key' => $api_key,
    'timestamp' => $timestamp,
    'upload_preset' => $upload_preset,
    'signature' => $signature
];

$upload_url = "https://api.cloudinary.com/v1_1/$cloud_name/image/upload";

// Thực hiện cURL upload
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $upload_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postfields,
    CURLOPT_SSL_VERIFYPEER => true, // nên giữ true trên máy chủ thật để bảo mật
]);

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

// Xử lý phản hồi
if ($response !== false) {
    $json = json_decode($response, true);
    if (isset($json['secure_url']) && isset($json['public_id'])) {
        echo json_encode([
            'success' => true,
            'secure_url' => $json['secure_url'],
            'public_id' => $json['public_id']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Phản hồi không hợp lệ từ Cloudinary.', 'debug' => $json]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'cURL Error: ' . $error]);
}
