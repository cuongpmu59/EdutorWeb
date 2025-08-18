<?php
require_once __DIR__ . '/../../includes/db_connection.php';

// Thông tin Cloudinary (nên để trong biến môi trường .env thay vì hardcode)
$cloud_name     = "dbdf2gwc9"; 
$api_key        = "451298475188791";
$api_secret     = "e-lLavuDlEKvm3rg-Tg_P6yMM3o";
$upload_preset  = "my_exam_preset";

header('Content-Type: application/json');

// Hàm lấy public_id từ URL Cloudinary
function getPublicIdFromUrl($url) {
    $path = parse_url($url, PHP_URL_PATH);
    $parts = explode('/', $path);
    $uploadIndex = array_search('upload', $parts);
    if ($uploadIndex === false) return null;

    $publicParts = array_slice($parts, $uploadIndex + 1);
    if (preg_match('/^v\d+$/', $publicParts[0])) array_shift($publicParts);

    $filename = array_pop($publicParts);
    // Xoá đuôi .jpg/.png/.gif...
    $publicId = preg_replace('/\.[^.]+$/', '', $filename);
    return implode('/', array_merge($publicParts, [$publicId]));
}

// Hàm xoá ảnh Cloudinary
function deleteCloudinaryImage($publicId, $cloud_name, $api_key, $api_secret) {
    $timestamp = time();
    $stringToSign = "public_id={$publicId}&timestamp={$timestamp}" . $api_secret;
    $signature = sha1($stringToSign);

    $url = "https://api.cloudinary.com/v1_1/{$cloud_name}/image/destroy";
    $data = [
        'public_id' => $publicId,
        'api_key'   => $api_key,
        'timestamp' => $timestamp,
        'signature' => $signature
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $res = curl_exec($ch);
    curl_close($ch);

    return json_decode($res, true);
}

// Xử lý request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tf_id = filter_input(INPUT_POST, 'tf_id', FILTER_VALIDATE_INT);

    if (!$tf_id) {
        echo json_encode(['success' => false, 'message' => '❌ tf_id không hợp lệ.']);
        exit;
    }

    try {
        // 1. Lấy URL ảnh từ DB
        $stmt = $conn->prepare("SELECT tf_image_url FROM tf_questions WHERE tf_id = ?");
        $stmt->execute([$tf_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Nếu có ảnh thì xoá trên Cloudinary
        if (!empty($row['tf_image_url'])) {
            $publicId = getPublicIdFromUrl($row['tf_image_url']);
            if ($publicId) {
                $cloudRes = deleteCloudinaryImage($publicId, $cloud_name, $api_key, $api_secret);
                if (!isset($cloudRes['result']) || $cloudRes['result'] !== 'ok') {
                    error_log("⚠ Không thể xóa ảnh Cloudinary: " . json_encode($cloudRes));
                }
            }
        }

        // 2. Xoá câu hỏi trong DB (luôn thực hiện)
        $stmt = $conn->prepare("DELETE FROM tf_questions WHERE tf_id = ?");
        $stmt->execute([$tf_id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => '✅ Xoá thành công.']);
        } else {
            echo json_encode(['success' => false, 'message' => '❌ Không tìm thấy câu hỏi để xoá.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => '❌ Lỗi truy vấn: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => '❌ Phương thức không hợp lệ.']);
}
