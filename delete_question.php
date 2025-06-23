<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';

    // 1. Lấy URL ảnh từ DB
    $stmtGet = $conn->prepare("SELECT image FROM questions WHERE id = :id");
    $stmtGet->bindParam(':id', $id);
    $stmtGet->execute();
    $imageUrl = $stmtGet->fetchColumn();

    // 2. Nếu có ảnh Cloudinary thì xoá
    if (!empty($imageUrl) && strpos($imageUrl, 'res.cloudinary.com') !== false) {
        // 👉 Trích xuất public_id từ URL
        $parts = explode('/', parse_url($imageUrl, PHP_URL_PATH));
        $filename = end($parts);
        $publicId = pathinfo($filename, PATHINFO_FILENAME); // bỏ đuôi .jpg, .png...

        // Cloudinary credentials
        $cloudName = 'dbdf2gwc9';
        $apiKey    = '451298475188791';
        $apiSecret = '*********************************';

        // 3. Tạo signature
        $timestamp = time();
        $signatureString = "public_id={$publicId}&timestamp={$timestamp}{$apiSecret}";
        $signature = sha1($signatureString);

        // 4. Gửi yêu cầu xoá ảnh
        $postData = [
            'public_id' => $publicId,
            'api_key' => $apiKey,
            'timestamp' => $timestamp,
            'signature' => $signature
        ];

        $ch = curl_init("https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $response = curl_exec($ch);
        curl_close($ch);

        // (Tùy chọn) Ghi log nếu cần
        // file_put_contents('cloudinary_log.txt', $response . PHP_EOL, FILE_APPEND);
    }

    // 5. Xoá câu hỏi trong DB
    $stmt = $conn->prepare("DELETE FROM questions WHERE id = :id");
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        echo "✅ Đã xoá câu hỏi và ảnh minh hoạ (nếu có).";
    } else {
        echo "❌ Lỗi khi xoá câu hỏi.";
    }
}
?>
