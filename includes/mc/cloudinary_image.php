<?php
header("Content-Type: application/json");

$cloud_name = "dbdf2gwc9";
$api_key    = "451298475188791";
$api_secret = "PK2QC";
$upload_preset = "my_exam_preset";

$action = $_POST['action'] ?? '';

if ($action === 'upload') {
    if (!isset($_FILES['file'])) {
        echo json_encode(["error" => "Không có file tải lên"]);
        exit;
    }

    $file = $_FILES['file']['tmp_name'];
    $url = "https://api.cloudinary.com/v1_1/$cloud_name/image/upload";

    $data = [
        "file" => new CURLFile($file),
        "upload_preset" => $upload_preset
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
    $json = json_decode($res, true);

    if (isset($json['secure_url'])) {
        echo json_encode(["url" => $json['secure_url']]);
    } else {
        echo json_encode(["error" => $json['error']['message'] ?? "Upload thất bại"]);
    }
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["action"] === "delete") {
    if (empty($_POST["image_url"])) {
        echo json_encode(["error" => "Thiếu image_url"]);
        exit;
    }

    $image_url = $_POST["image_url"];

    // 🔹 Lấy public_id từ URL bằng regex
    if (preg_match("~upload/(?:v\d+/)?([^\.]+)~", $image_url, $matches)) {
        $public_id = $matches[1];
    } else {
        echo json_encode(["error" => "Không lấy được public_id"]);
        exit;
    }

    $timestamp = time();
    $string_to_sign = "public_id={$public_id}&timestamp={$timestamp}{$api_secret}";
    $signature = sha1($string_to_sign);

    $data = [
        "public_id" => $public_id,
        "timestamp" => $timestamp,
        "api_key"   => $api_key,
        "signature" => $signature
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/{$cloud_name}/image/destroy");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($ch);
    curl_close($ch);

    echo $response;
    exit;
}

echo json_encode(["error" => "Yêu cầu không hợp lệ"]);
