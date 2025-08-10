<?php
// Khi bấm submit form, xử lý PHP xóa ảnh
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    $image_url = $_POST['image_url'] ?? '';

    if (empty($image_url)) {
        echo json_encode(["success" => false, "message" => "Thiếu image_url"]);
        exit;
    }

    // Lấy public_id từ URL ảnh
    $parsed_url = parse_url($image_url, PHP_URL_PATH);
    $parts = explode('/', trim($parsed_url, '/'));
    $public_id_with_ext = end($parts);
    $public_id = pathinfo($public_id_with_ext, PATHINFO_FILENAME);

    // Cấu hình Cloudinary
    $cloud_name = "dbdf2gwc9"; 
    $api_key    = "451298475188791";
    $api_secret = "e-lLavuDlEKvm3rg-Tg_P6yMM3o";

    $timestamp = time();
    $string_to_sign = "public_id={$public_id}&timestamp={$timestamp}{$api_secret}";
    $signature = sha1($string_to_sign);

    $url = "https://api.cloudinary.com/v1_1/{$cloud_name}/image/destroy";

    $data = [
        'public_id' => $public_id,
        'api_key' => $api_key,
        'timestamp' => $timestamp,
        'signature' => $signature
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        echo json_encode(["success" => true, "message" => "Ảnh đã được xóa", "response" => json_decode($response, true)]);
    } else {
        echo json_encode(["success" => false, "message" => "Lỗi khi xóa ảnh", "http_code" => $http_code, "response" => json_decode($response, true)]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Xóa ảnh Cloudinary</title>
<style>
    body { font-family: Arial; padding: 20px; }
    input[type="text"] { width: 100%; padding: 8px; }
    button { padding: 8px 15px; margin-top: 10px; }
</style>
</head>
<body>
    <h2>Nhập URL ảnh Cloudinary để xóa</h2>
    <form method="POST">
        <input type="text" name="image_url" placeholder="https://res.cloudinary.com/..." required>
        <br>
        <button type="submit">Xóa ảnh</button>
    </form>
</body>
</html>
