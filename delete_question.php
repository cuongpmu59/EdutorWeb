<?php
require 'db_connection.php';
require 'dotenv.php';

header('Content-Type: application/json');

$id = $_POST['id'] ?? '';
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID không hợp lệ']);
    exit;
}

try {
    // Tìm ảnh trước khi xoá
    $stmt = $conn->prepare("SELECT image FROM questions WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetchColumn();

    // Xóa ảnh Cloudinary nếu có
    if ($image) {
        $publicId = pathinfo(parse_url($image, PHP_URL_PATH), PATHINFO_FILENAME);
        if ($publicId) {
            $cloudName = getenv('CLOUDINARY_CLOUD_NAME');
            $apiKey = getenv('CLOUDINARY_API_KEY');
            $apiSecret = getenv('CLOUDINARY_API_SECRET');
            $url = "https://api.cloudinary.com/v1_1/$cloudName/image/destroy";

            $timestamp = time();
            $signature = sha1("public_id=$publicId&timestamp=$timestamp$apiSecret");

            $data = [
                'public_id' => $publicId,
                'api_key' => $apiKey,
                'timestamp' => $timestamp,
                'signature' => $signature
            ];

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($data)
            ]);
            curl_exec($ch);
            curl_close($ch);
        }
    }

    // Xoá câu hỏi
    $stmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['status' => 'success', 'message' => '✅ Đã xoá câu hỏi']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => '❌ Lỗi: ' . $e->getMessage()]);
}
