<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Cho phép test từ nhiều domain (tùy chỉnh)
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../env/config.php';

// ================== CONFIG ==================
$cloud_name = CLOUDINARY_CLOUD_NAME;   // từ config.php
$upload_preset = 'unsigned_preset';    // preset phải được tạo trong Cloudinary và ở chế độ unsigned
$api_key = CLOUDINARY_API_KEY;
$api_secret = CLOUDINARY_API_SECRET;

// ================== UPLOAD ẢNH ==================
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['image']['tmp_name'];

    $url = "https://api.cloudinary.com/v1_1/{$cloud_name}/image/upload";

    $post_fields = [
        'file' => new CURLFile($file_tmp),
        'upload_preset' => $upload_preset
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $post_fields
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    echo $response;
    exit;
}

// ================== DELETE ẢNH ==================
if (isset($_POST['public_id']) && !empty($_POST['public_id'])) {
    $public_id = $_POST['public_id'];

    $timestamp = time();
    $string_to_sign = "public_id={$public_id}&timestamp={$timestamp}{$api_secret}";
    $signature = sha1($string_to_sign);

    $url = "https://api.cloudinary.com/v1_1/{$cloud_name}/image/destroy";

    $post_fields = [
        'public_id' => $public_id,
        'api_key' => $api_key,
        'timestamp' => $timestamp,
        'signature' => $signature
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $post_fields
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    echo $response;
    exit;
}

// ================== REQUEST KHÔNG HỢP LỆ ==================
echo json_encode(['error' => '❌ Request không hợp lệ']);
exit;
