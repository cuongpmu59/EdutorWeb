<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';

    if (!is_numeric($id)) {
        echo "❌ ID không hợp lệ.";
        exit;
    }

    // 1. Lấy URL ảnh từ DB
    $stmtGet = $conn->prepare("SELECT image FROM questions WHERE id = :id");
    $stmtGet->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtGet->execute();
    $imageUrl = $stmtGet->fetchColumn();

    // 2. Nếu có ảnh trên Cloudinary thì xoá
    if (!empty($imageUrl) && strpos($imageUrl, 'res.cloudinary.com') !== false) {
        $parsed = parse_url($imageUrl);
        $parts = explode('/', $parsed['path']);
        $filename = end($parts);
        $publicId = pathinfo($filename, PATHINFO_FILENAME); // bỏ phần mở rộng .jpg, .png

        // Cloudinary credentials
        $cloudName = 'dbdf2gwc9';
        $apiKey    = '451298475188791';
        $apiSecret = '***************'; // Đừng bao giờ công khai mã này

        $timestamp = time();
        $signature = sha1("public_id={$publicId}&timestamp={$timestamp}{$apiSecret}");

        $postData = [
            'public_id' => $publicId,
            'api_key' => $apiKey,
            'timestamp' => $timestamp,
            'signature' => $signature,
        ];

        $ch = curl_init("https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $response = curl_exec($ch);
        curl_close($ch);

        // (Tùy chọn) Ghi log nếu cần
        // file_put_contents('cloudinary_log.txt', $response . PHP_EOL, FILE_APPEND);
    }

    // 3. Xoá câu hỏi trong DB
    $stmt = $conn->prepare("DELETE FROM questions WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "✅ Đã xoá câu hỏi và ảnh minh hoạ (nếu có).";
    } else {
        echo "❌ Lỗi khi xoá câu hỏi.";
    }
} else {
    echo "❌ Phương thức không hợp lệ.";
}
?>
