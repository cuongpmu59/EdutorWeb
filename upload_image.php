<?php
// ⚙️ Cấu hình Cloudinary
$cloud_name    = "dbdf2gwc9"; // Thay bằng Cloud Name
$upload_preset = "my_exam_preset"; // Thay bằng Upload Preset (unsigned)

// Cho phép CORS nếu test từ file HTML riêng
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

// Kiểm tra file upload
if (!isset($_FILES['file']['tmp_name'])) {
    echo json_encode(["error" => "Không có file tải lên"]);
    exit;
}

// Chuẩn bị dữ liệu gửi
$file_path = $_FILES['file']['tmp_name'];
$url = "https://api.cloudinary.com/v1_1/{$cloud_name}/image/upload";

$data = [
    "file" => new CURLFile($file_path),
    "upload_preset" => $upload_preset
];

// Gửi request lên Cloudinary
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

// Trả về JSON từ Cloudinary
echo $response;
