<?php
require 'db_connection.php';
header('Content-Type: application/json; charset=utf-8');

// ===== Tải biến môi trường từ file .env =====
function loadEnv($file = '.env') {
    if (!file_exists($file)) return;
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (trim($line) === '' || str_starts_with(trim($line), '#')) continue;
        [$name, $value] = explode('=', $line, 2);
        putenv(trim($name) . '=' . trim($value));
        $_ENV[trim($name)] = trim($value);
    }
}
loadEnv();

// ===== Lấy ID =====
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID không hợp lệ']);
    exit;
}

try {
    // ===== Lấy đường dẫn ảnh từ DB =====
    $stmt = $conn->prepare("SELECT image_url FROM questions WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy câu hỏi']);
        exit;
    }

    $imageUrl = $row['image_url'];

    // ===== Xoá câu hỏi =====
    $delStmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
    $delStmt->execute([$id]);

    // ===== Nếu có ảnh thì xoá khỏi Cloudinary =====
    if (!empty($imageUrl)) {
        $parsedUrl = parse_url($imageUrl);
        $publicId = pathinfo($parsedUrl['path'], PATHINFO_FILENAME);

        $cloudName = getenv('CLOUDINARY_CLOUD_NAME');
        $apiKey    = getenv('CLOUDINARY_API_KEY');
        $apiSecret = getenv('CLOUDINARY_API_SECRET');
        $timestamp = time();

        $stringToSign = "public_id=$publicId&timestamp=$timestamp$apiSecret";
        $signature = sha1($stringToSign);

        $postData = [
            'public_id' => $publicId,
            'api_key'   => $apiKey,
            'timestamp' => $timestamp,
            'signature' => $signature,
        ];

        $ch = curl_init("https://api.cloudinary.com/v1_1/$cloudName/image/destroy");
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
