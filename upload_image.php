<?php
// Cấu hình Cloudinary
$cloud_name    = "dbdf2gwc9"; // Thay bằng Cloud Name
$upload_preset = "my_exam_preset"; // Thay bằng Upload Preset (unsigned)

// Trả kết quả JSON
header('Content-Type: application/json; charset=utf-8');

// Kiểm tra xem có file được gửi không
if (!isset($_FILES['file']['tmp_name'])) {
    echo json_encode(["error" => "Không có file tải lên"]);
    exit;
}

// Chuẩn bị dữ liệu upload
$file_path = $_FILES['file']['tmp_name'];
$url = "https://api.cloudinary.com/v1_1/{$cloud_name}/image/upload";

// Tạo POST request
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

// Trả về kết quả từ Cloudinary
echo $response;
