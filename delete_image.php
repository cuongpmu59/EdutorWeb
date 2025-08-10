<?php
// delete_image.php
header('Content-Type: application/json; charset=utf-8');

// Load Cloudinary credentials from env/config.php
// file must define: $cloud_name, $api_key, $api_secret
require_once __DIR__ . '/env/config.php';

// get and validate input
$image_url = trim($_POST['image_url'] ?? '');
if ($image_url === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Thiếu image_url']);
    exit;
}

// extract public_id (preserve folders if any)
function getPublicIdFromUrl($url) {
    $path = parse_url($url, PHP_URL_PATH);
    if ($path === null) return null;

    // explode và tìm 'upload' (bỏ phần trước: /<cloud_name>/image/upload)
    $parts = array_values(array_filter(explode('/', $path), 'strlen'));
    // tìm 'upload' trong mảng
    $uploadIndex = array_search('upload', $parts, true);
    if ($uploadIndex === false) {
        // URL không có segment 'upload' -> có thể đã là trực tiếp public_id dạng folder/xxx.png
        $fileParts = $parts;
    } else {
        // lấy phần sau 'upload'
        $fileParts = array_slice($parts, $uploadIndex + 1);
    }

    if (empty($fileParts)) return null;

    // nếu first part là version v12345, bỏ nó
    if (preg_match('/^v\d+$/', $fileParts[0])) {
        array_shift($fileParts);
    }

    // nối lại để có path/to/file.png
    $filePath = implode('/', $fileParts);

    // lấy tên file không phần mở rộng
    $basename = pathinfo($filePath, PATHINFO_FILENAME);

    // nếu hình ở trong folder, filename trả ra sẽ mất folder — ta cần giữ folder(s)/basename
    // pathinfo chỉ lấy basename; để giữ folders, ta lấy dirname (nếu tồn tại)
    $dir = pathinfo($filePath, PATHINFO_DIRNAME);
    if ($dir === '.' || $dir === '') {
        return $basename;
    } else {
        return $dir . '/' . $basename;
    }
}

$public_id = getPublicIdFromUrl($image_url);
if ($public_id === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Không thể phân tích URL để lấy public_id']);
    exit;
}

// Cloudinary delete endpoint (bulk delete supports public_ids[])
$delete_url = "https://api.cloudinary.com/v1_1/{$cloud_name}/resources/image/upload";

// prepare form data and send DELETE with urlencoded body
$data = [
    'public_ids[]' => $public_id,
    'invalidate'   => 'true'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $delete_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($ch, CURLOPT_USERPWD, "{$api_key}:{$api_secret}");
// IMPORTANT: encode form data as application/x-www-form-urlencoded
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_err = curl_error($ch);
curl_close($ch);

if ($response === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi cURL', 'error' => $curl_err]);
    exit;
}

// Try decode JSON
$decoded = json_decode($response, true);
if ($http_code >= 200 && $http_code < 300) {
    echo json_encode(['success' => true, 'http_code' => $http_code, 'response' => $decoded ?? $response]);
} else {
    http_response_code($http_code);
    echo json_encode(['success' => false, 'http_code' => $http_code, 'response' => $decoded ?? $response]);
}
