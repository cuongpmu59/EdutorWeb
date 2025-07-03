<?php
require 'db_connection.php';
require 'config.php'; // Load .env biến môi trường
header('Content-Type: application/json; charset=utf-8');

// Nhận ID từ POST
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID không hợp lệ']);
    exit;
}

try {
    // Lấy URL ảnh nếu có
    $stmt = $conn->prepare("SELECT image_url FROM questions WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy câu hỏi']);
        exit;
    }

    $imageUrl = $row['image_url'];

    // Xoá câu hỏi khỏi DB
    $delStmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
    $delStmt->execute([$id]);

    // Nếu có ảnh => gọi API Cloudinary để xoá
    if (!empty($imageUrl)) {
        $publicId = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_FILENAME);
        $timestamp = time();

        // Lấy thông tin từ biến môi trường
        $cloudName = $_ENV['CLOUDINARY_CLOUD_NAME'];
        $apiKey = $_ENV['CLOUDINARY_API_KEY'];
        $apiSecret = $_ENV['CLOUDINARY_API_SECRET'];

        $signatureString = "public_id=$publicId&timestamp=$timestamp$apiSecret";
        $signature = sha1($signatureString);

        $postData = [
            'public_id' => $publicId,
            'api_key' => $apiKey,
            'timestamp' => $timestamp,
            'signature' => $signature
        ];

        $ch = curl_init("https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy");
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
