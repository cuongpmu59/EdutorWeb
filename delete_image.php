<?php
header('Content-Type: application/json; charset=utf-8');

// require_once __DIR__ . '/env/config.php';
$cloud_name = "dbdf2gwc9"; 
$api_key    = "451298475188791";
$api_secret = "e-lLavuDlEKvm3rg-Tg_P6yMM3o";

$image_url = trim($_POST['image_url'] ?? '');
if ($image_url === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Thiếu image_url']);
    exit;
}

function getPublicIdFromUrl($url) {
    $path = parse_url($url, PHP_URL_PATH);
    if ($path === null) return null;

    $parts = array_values(array_filter(explode('/', $path), 'strlen'));
    $uploadIndex = array_search('upload', $parts, true);
    if ($uploadIndex === false) {
        $fileParts = $parts;
    } else {
        $fileParts = array_slice($parts, $uploadIndex + 1);
    }

    if (empty($fileParts)) return null;
    if (preg_match('/^v\d+$/', $fileParts[0])) {
        array_shift($fileParts);
    }

    $filePath = implode('/', $fileParts);
    $basename = pathinfo($filePath, PATHINFO_FILENAME);

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

$delete_url = "https://api.cloudinary.com/v1_1/{$cloud_name}/resources/image/upload";

$data = [
    'public_ids[]' => $public_id,
    'invalidate'   => 'true'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $delete_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($ch, CURLOPT_USERPWD, "{$api_key}:{$api_secret}");
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

$decoded = json_decode($response, true);
if ($http_code >= 200 && $http_code < 300) {
    echo json_encode(['success' => true, 'http_code' => $http_code, 'response' => $decoded ?? $response]);
} else {
    http_response_code($http_code);
    echo json_encode(['success' => false, 'http_code' => $http_code, 'response' => $decoded ?? $response]);
}
