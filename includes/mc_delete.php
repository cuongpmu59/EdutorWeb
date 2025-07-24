<?php
require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/../vendor/autoload.php'; // nếu dùng composer

use Cloudinary\Cloudinary;

// Cấu hình Cloudinary
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'YOUR_CLOUD_NAME',
        'api_key'    => 'YOUR_API_KEY',
        'api_secret' => 'YOUR_API_SECRET',
    ]
]);

// Chỉ xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mc_id = isset($_POST['mc_id']) ? (int)$_POST['mc_id'] : 0;

    if ($mc_id > 0) {
        try {
            // 1. Lấy đường dẫn ảnh từ CSDL
            $stmt = $conn->prepare("SELECT mc_image_url FROM mc_questions WHERE mc_id = ?");
            $stmt->execute([$mc_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && !empty($row['mc_image_url'])) {
                $imageUrl = $row['mc_image_url'];

                // 2. Xoá ảnh Cloudinary nếu là ảnh Cloudinary
                if (strpos($imageUrl, 'res.cloudinary.com') !== false) {
                    // Trích xuất public_id từ URL
                    $publicId = extractCloudinaryPublicId($imageUrl);
                    if ($publicId) {
                        $cloudinary->uploadApi()->destroy($publicId);
                    }
                }

                // 3. (Tuỳ chọn) Xoá file ảnh cục bộ nếu không phải Cloudinary
                if (strpos($imageUrl, 'res.cloudinary.com') === false) {
                    $localPath = __DIR__ . '/../' . ltrim($imageUrl, '../');
                    if (file_exists($localPath)) {
                        unlink($localPath);
                    }
                }
            }

            // 4. Xoá bản ghi khỏi CSDL
            $stmt = $conn->prepare("DELETE FROM mc_questions WHERE mc_id = ?");
            $stmt->execute([$mc_id]);

            echo json_encode(['success' => true, 'message' => 'Đã xoá câu hỏi và ảnh.']);
            exit;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi khi xoá: ' . $e->getMessage()]);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'mc_id không hợp lệ.']);
        exit;
    }
}

// Hàm trích xuất public_id từ URL Cloudinary
function extractCloudinaryPublicId($url) {
    // Ví dụ URL: https://res.cloudinary.com/demo/image/upload/v1234567890/mc_123.jpg
    $parts = parse_url($url);
    if (!isset($parts['path'])) return null;

    $path = $parts['path'];
    $segments = explode('/', $path);
    $filename = end($segments);
    $filename = preg_replace('/\.[^.]+$/', '', $filename); // Bỏ đuôi .jpg, .png

    return $filename;
}
?>
