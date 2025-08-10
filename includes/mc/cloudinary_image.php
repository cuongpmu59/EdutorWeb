<?php
header('Content-Type: application/json; charset=utf-8');

// Cấu hình Cloudinary
$cloud_name    = "dbdf2gwc9"; // thay bằng cloud_name của bạn
$upload_preset = "my_exam_preset"; // thay bằng upload_preset unsigned
$api_key       = "451298475188791";   // chỉ dùng cho xóa
$api_secret    = "PK2QC"; // chỉ dùng cho xóa

// Hàm upload ảnh (unsigned)
function uploadImage($filePath, $cloud_name, $upload_preset) {
    $url = "https://api.cloudinary.com/v1_1/{$cloud_name}/image/upload";
    $data = [
        'upload_preset' => $upload_preset,
        'file' => new CURLFile($filePath)
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_RETURNTRANSFER => true
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}


// Hàm xóa ảnh (signed)
function deleteImage($public_id, $cloud_name, $api_key, $api_secret) {
    $timestamp = time();
    $string_to_sign = "public_id={$public_id}&timestamp={$timestamp}{$api_secret}";
    $signature = sha1($string_to_sign);

    $url = "https://api.cloudinary.com/v1_1/{$cloud_name}/image/destroy";
    $data = [
        'public_id' => $public_id,
        'timestamp' => $timestamp,
        'api_key' => $api_key,
        'signature' => $signature
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_RETURNTRANSFER => true
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

// Xử lý request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Upload ảnh
    if (isset($_FILES['image'])) {
        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $res = uploadImage($_FILES['image']['tmp_name'], $cloud_name, $upload_preset);
            echo json_encode($res);
        } else {
            echo json_encode(['error' => 'Không thể tải file lên']);
        }
        exit;
    }

    // Xóa ảnh
    if (isset($_POST['image_url'])) {
        // Lấy public_id từ URL bằng regex
        if (preg_match('#/upload/(?:v\d+/)?([^\.]+)#', $_POST['image_url'], $matches)) {
            $public_id = $matches[1];
            $res = deleteImage($public_id, $cloud_name, $api_key, $api_secret);
            echo json_encode($res);
        } else {
            echo json_encode(['error' => 'Không tìm thấy public_id']);
        }
        exit;
    }

    echo json_encode(['error' => 'Yêu cầu không hợp lệ']);
}
?>
