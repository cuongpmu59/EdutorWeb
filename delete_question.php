<?php
require 'db_connection.php';
header('Content-Type: application/json; charset=utf-8');

// Nhận ID
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID không hợp lệ']);
    exit;
}

// Lấy đường dẫn ảnh nếu có
try {
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

    // Xoá ảnh khỏi Cloudinary nếu có
    if (!empty($imageUrl)) {
        $publicId = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_FILENAME);

        // Gọi API Cloudinary để xoá
        $timestamp = time();
        $apiKey = '451298475188791';
        $apiSecret = 'PK2QC';
        $cloudName = 'dbdf2gwc9';

        $stringToSign = "public_id=$publicId&timestamp=$timestamp$apiSecret";
        $signature = sha1($stringToSign);

        $postData = [
            'public_id' => $publicId,
            'api_key' => $apiKey,
            'timestamp' => $timestamp,
            'signature' => $signature
        ];

        $ch = curl_init("https://api.cloudinary.com/v1_1/dbdf2gwc9/image/destroy");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $response = curl_exec($ch);
        curl_close($ch);
    }

    echo json_encode(['status' => 'success', 'message' => '✅ Đã xoá câu hỏi']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi: ' . $e->getMessage()]);
}
