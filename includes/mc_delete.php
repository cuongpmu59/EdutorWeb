<?php
require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Cloudinary\Cloudinary;
use Dotenv\Dotenv;

// Tải biến môi trường
$dotenv = Dotenv::createImmutable(__DIR__ . '/../env');
$dotenv->load();

// Cấu hình Cloudinary
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
        'api_key'    => $_ENV['CLOUDINARY_API_KEY'],
        'api_secret' => $_ENV['CLOUDINARY_API_SECRET'],
    ]
]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Truy cập không hợp lệ.']);
    exit;
}

$mc_id = isset($_POST['mc_id']) ? (int)$_POST['mc_id'] : 0;

if ($mc_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'mc_id không hợp lệ.']);
    exit;
}

try {
    // Lấy URL ảnh
    $stmt = $conn->prepare("SELECT mc_image_url FROM mc_questions WHERE mc_id = ?");
    $stmt->execute([$mc_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && !empty($row['mc_image_url'])) {
        $imageUrl = $row['mc_image_url'];

        if (strpos($imageUrl, 'res.cloudinary.com') !== false) {
            $publicId = extractCloudinaryPublicId($imageUrl);
            if ($publicId) {
                $cloudinary->uploadApi()->destroy($publicId);
            }
        } else {
            // Ảnh cục bộ
            $localPath = __DIR__ . '/../' . ltrim($imageUrl, '/');
            if (file_exists($localPath)) {
                unlink($localPath);
            }
        }
    }

    // Xoá bản ghi
    $stmt = $conn->prepare("DELETE FROM mc_questions WHERE mc_id = ?");
    $stmt->execute([$mc_id]);

    echo json_encode(['success' => true, 'message' => 'Đã xoá câu hỏi và ảnh.']);
    exit;

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi xoá: ' . $e->getMessage()]);
    exit;
}

// Hàm hỗ trợ
function extractCloudinaryPublicId($url) {
    $parts = parse_url($url);
    if (!isset($parts['path'])) return null;

    $path = $parts['path'];
    $segments = explode('/', $path);
    $filename = end($segments);
    return preg_replace('/\.[^.]+$/', '', $filename); // bỏ đuôi file
}
