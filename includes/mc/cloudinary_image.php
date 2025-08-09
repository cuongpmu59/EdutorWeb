<?php
// ⚙️ Cấu hình Cloudinary
$cloud_name    = "dbdf2gwc9"; 
$api_key       = "451298475188791";
$api_secret    = "PK2QC"; // ⚠️ Đừng để lộ trên môi trường public
$upload_preset = "my_exam_preset"; 

// Cho phép CORS nếu test từ file HTML riêng
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

$action = $_POST['action'] ?? '';

if ($action === 'upload') {
    // Upload ảnh lên Cloudinary (unsigned)
    if (!isset($_FILES['file']['tmp_name'])) {
        echo json_encode(["error" => "Không có file tải lên"]);
        exit;
    }

    $file_path = $_FILES['file']['tmp_name'];
    $url = "https://api.cloudinary.com/v1_1/{$cloud_name}/image/upload";

    $data = [
        "file" => new CURLFile($file_path),
        "upload_preset" => $upload_preset
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo json_encode(["error" => curl_error($ch)]);
        curl_close($ch);
        exit;
    }
    curl_close($ch);

    echo $response;
    exit;
}

if ($action === 'delete') {
    $imageUrl = $_POST['image_url'] ?? '';

    if (!$imageUrl) {
        echo json_encode(['status' => 'error', 'message' => 'Không có URL ảnh để xoá']);
        exit;
    }
    // 🔹 Tách public_id từ URL
    $pathParts = parse_url($imageUrl, PHP_URL_PATH); // /demo/image/upload/v1691234567/folder/ten_anh.jpg
    $segments = explode('/', $pathParts);
    $lastPart = end($segments); // ten_anh.jpg

    $publicId = preg_replace('/\.[^.]+$/', '', $lastPart);

    // Nếu ảnh nằm trong folder, cần lấy từ sau "upload/"
    $uploadIndex = array_search('upload', $segments);
    if ($uploadIndex !== false) {
        $publicIdParts = array_slice($segments, $uploadIndex + 2); // bỏ 'upload' và version
        $publicId = implode('/', $publicIdParts);
        $publicId = preg_replace('/\.[^.]+$/', '', $publicId);
    }

    // 🔹 Gọi API xoá ảnh
    $timestamp = time();
    $stringToSign = "public_id={$publicId}&timestamp={$timestamp}" . CLOUDINARY_API_SECRET;
    $signature = sha1($stringToSign);

    $url = "https://api.cloudinary.com/v1_1/" . CLOUDINARY_CLOUD_NAME . "/image/destroy";

    $data = [
        'public_id' => $publicId,
        'timestamp' => $timestamp,
        'api_key' => CLOUDINARY_API_KEY,
        'signature' => $signature
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    echo $response;
    exit;
}