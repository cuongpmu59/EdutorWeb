<?php
// cloudinary_action.php
// Luôn bắt đầu bằng <?php không có gì trước đó

// Không in lỗi ra client — log vào error_log thay vì hiển thị
@ini_set('display_errors', '0');
@ini_set('log_errors', '1');
error_reporting(E_ALL);

// Luôn trả JSON
header('Content-Type: application/json; charset=utf-8');

// Ngăn output vô tình từ các include
ob_start();
require_once __DIR__ . '/env/dotenv.php';
require_once __DIR__ . '/env/config.php';
$preOutput = ob_get_clean();
if (!empty($preOutput)) {
    // Ghi log để debug; không trả thẳng cho client (tránh phá JSON)
    error_log("[cloudinary_action.php] Pre-output from includes: " . trim($preOutput));
}

// ----- Hàm upload -----
function uploadImageToCloudinary($filePath, $folder = "uploads") {
    $cloudName = defined('CLOUDINARY_CLOUD_NAME') ? CLOUDINARY_CLOUD_NAME : null;
    $apiKey    = defined('CLOUDINARY_API_KEY') ? CLOUDINARY_API_KEY : null;
    $apiSecret = defined('CLOUDINARY_API_SECRET') ? CLOUDINARY_API_SECRET : null;

    if (!$cloudName || !$apiKey || !$apiSecret) {
        return ['error' => 'Cloudinary config missing'];
    }

    $timestamp = time();
    $url = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";
    $params_to_sign = "folder={$folder}&timestamp={$timestamp}{$apiSecret}";
    $signature = sha1($params_to_sign);

    $postFields = [
        'file'      => new CURLFile($filePath),
        'api_key'   => $apiKey,
        'timestamp' => $timestamp,
        'folder'    => $folder,
        'signature' => $signature
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_TIMEOUT => 30
    ]);
    $response = curl_exec($ch);
    if ($response === false) {
        $err = curl_error($ch);
        curl_close($ch);
        error_log("[cloudinary upload] curl error: " . $err);
        return ['error' => 'cURL error: ' . $err];
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("[cloudinary upload] Invalid JSON response: " . substr($response,0,200));
        return ['error' => 'Invalid response from Cloudinary', 'raw' => substr($response,0,200)];
    }
    $decoded['http_code'] = $httpCode;
    return $decoded;
}

// ----- Hàm delete -----
function deleteImageFromCloudinary($publicId) {
    $cloudName = defined('CLOUDINARY_CLOUD_NAME') ? CLOUDINARY_CLOUD_NAME : null;
    $apiKey    = defined('CLOUDINARY_API_KEY') ? CLOUDINARY_API_KEY : null;
    $apiSecret = defined('CLOUDINARY_API_SECRET') ? CLOUDINARY_API_SECRET : null;

    if (!$cloudName || !$apiKey || !$apiSecret) {
        return ['error' => 'Cloudinary config missing'];
    }

    $timestamp = time();
    $url = "https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy";
    $params_to_sign = "public_id={$publicId}&timestamp={$timestamp}{$apiSecret}";
    $signature = sha1($params_to_sign);

    $postFields = [
        'public_id' => $publicId,
        'api_key'   => $apiKey,
        'timestamp' => $timestamp,
        'signature' => $signature
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_TIMEOUT => 20
    ]);
    $response = curl_exec($ch);
    if ($response === false) {
        $err = curl_error($ch);
        curl_close($ch);
        error_log("[cloudinary delete] curl error: " . $err);
        return ['error' => 'cURL error: ' . $err];
    }
    curl_close($ch);

    $decoded = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("[cloudinary delete] Invalid JSON response: " . substr($response,0,200));
        return ['error' => 'Invalid response from Cloudinary', 'raw' => substr($response,0,200)];
    }
    return $decoded;
}

// ----- Xử lý request -----
$result = ['error' => 'Yêu cầu không hợp lệ'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $res = uploadImageToCloudinary($_FILES['image']['tmp_name']);
        $result = $res;
    } elseif (!empty($_POST['public_id_delete'])) {
        $publicId = trim($_POST['public_id_delete']);
        $res = deleteImageFromCloudinary($publicId);
        $result = $res;
    } else {
        $result = ['error' => 'Thiếu tham số'];
    }
}

// Trả JSON an toàn
echo json_encode($result, JSON_UNESCAPED_UNICODE);
exit;
