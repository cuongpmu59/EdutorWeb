<?php
require_once __DIR__ . '/env/config.php'; 

$image_url = "https://res.cloudinary.com/dbdf2gwc9/image/upload/v1754804130/h0s0yv4qpvy6j0iynwfk.png";

function getPublicIdFromUrl($url) {
    $path = parse_url($url, PHP_URL_PATH);
    $parts = explode('/', $path);
    $fileName = end($parts);
    return pathinfo($fileName, PATHINFO_FILENAME);
}

$public_id = getPublicIdFromUrl($image_url);

// Endpoint xóa ảnh
$delete_url = "https://api.cloudinary.com/v1_1/{$cloud_name}/resources/image/upload";

// Dữ liệu gửi kèm
$data = [
    'public_ids[]' => $public_id,
    'invalidate'   => 'true'
];

// cURL request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $delete_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($ch, CURLOPT_USERPWD, "{$api_key}:{$api_secret}");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 200) {
    echo "Ảnh đã được xóa thành công!\n";
    echo $response;
} else {
    echo "Lỗi khi xóa ảnh! Mã HTTP: $http_code\n";
    echo $response;
}
