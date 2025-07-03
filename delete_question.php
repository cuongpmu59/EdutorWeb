<?php
require 'db_connection.php';
require_once 'dotenv.php'; // Tự viết hoặc tải từ nguồn nhẹ

header('Content-Type: application/json; charset=utf-8');

// Đọc biến môi trường từ .env
$env = parse_ini_file(__DIR__ . '/.env');

$cloudName = $env['CLOUD_NAME'] ?? '';
$apiKey = $env['API_KEY'] ?? '';
$apiSecret = $env['API_SECRET'] ?? '';

// Nhận ID
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID không hợp lệ']);
    exit;
}

try {
    // Lấy ảnh nếu có
    $stmt = $conn->prepare("SELECT image_url FROM questions WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy câu hỏi']);
        exit;
    }

    $imageUrl = $row['image_url'];

    // Xoá câu hỏi
    $delStmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
    $delStmt->execute([$id]);

    // Nếu có ảnh thì xoá trên Cloudinary
    if (!empty($imageUrl) && $cloudName && $apiKey && $apiSecret) {
        $publicId = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_FILENAME);
        $timestamp = time();

        $stringToSign = "public_id=$publicId&timestamp=$timestamp$apiSecret";
        $signature = sha1($stringToSign);

        $postData = [
            'public_id' => $publicId,
            'api_key' => $apiKey,
            'timestamp' => $timestamp,
            'signature' => $signature
        ];

        $ch = curl_init("https://api.cloudinary.com/v1_1/$cloudName/image/destroy");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $response = curl_exec($ch);
        curl_close($ch);

        // Ghi log nếu cần kiểm tra
        // file_put_contents("cloudinary_delete.log", $response . "\n", FILE_APPEND);
    }

    echo json_encode(['status' => 'success', 'message' => '✅ Đã xoá câu hỏi']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi: ' . $e->getMessage()]);
}
