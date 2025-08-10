<?php
// ===================== CẤU HÌNH CLOUDINARY =====================
$cloud_name = "dbdf2gwc9"; 
$api_key    = "451298475188791";
$api_secret = "e-lLavuDlEKvm3rg-Tg_P6yMM3o";

// ===================== XỬ LÝ XÓA ẢNH =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    $image_url = trim($_POST['image_url'] ?? '');
    if (!$image_url) {
        echo json_encode(['success' => false, 'message' => 'Thiếu image_url']);
        exit;
    }

    // Parse public_id từ URL Cloudinary
    $parsed_path = parse_url($image_url, PHP_URL_PATH);
    if (!$parsed_path || !str_contains($parsed_path, '/upload/')) {
        echo json_encode(['success' => false, 'message' => 'URL không hợp lệ hoặc không phải của Cloudinary']);
        exit;
    }

    $segments = explode('/', trim($parsed_path, '/'));
    $upload_index = array_search('upload', $segments);

    if ($upload_index === false || !isset($segments[$upload_index + 2])) {
        echo json_encode(['success' => false, 'message' => 'Không thể phân tích public_id']);
        exit;
    }

    // Lấy public_id
    $public_id_parts = array_slice($segments, $upload_index + 2);
    $public_id_with_ext = implode('/', $public_id_parts);
    $public_id = preg_replace('/\.[^.]+$/', '', $public_id_with_ext);

    // Tạo signature
    $timestamp = time();
    $signature = sha1("public_id={$public_id}&timestamp={$timestamp}{$api_secret}");

    // Gọi API xóa ảnh
    $ch = curl_init("https://api.cloudinary.com/v1_1/{$cloud_name}/image/destroy");
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'public_id' => $public_id,
            'api_key'   => $api_key,
            'timestamp' => $timestamp,
            'signature' => $signature
        ],
        CURLOPT_RETURNTRANSFER => true
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $res_data = json_decode($response, true);

    if ($http_code === 200 && isset($res_data['result']) && $res_data['result'] === 'ok') {
        echo json_encode(['success' => true, 'message' => 'Xóa ảnh thành công!', 'public_id' => $public_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Xóa ảnh thất bại!', 'response' => $res_data]);
    }
}
