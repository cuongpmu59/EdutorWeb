<?php
// includes/mc/cloudinary_action.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// ===== Kết nối Cloudinary =====
require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/../config.php'; // chứa CLOUDINARY_CLOUD_NAME, API_KEY, API_SECRET
require_once __DIR__ . '/../../vendor/autoload.php';

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => CLOUDINARY_CLOUD_NAME,
        'api_key'    => CLOUDINARY_API_KEY,
        'api_secret' => CLOUDINARY_API_SECRET,
    ],
    'url' => ['secure' => true]
]);

// ===== XỬ LÝ REQUEST =====
try {
    // ==== UPLOAD ẢNH ====
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {

        // Kiểm tra lỗi upload
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['error' => '❌ Lỗi upload file']);
            exit;
        }

        // Giới hạn định dạng
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_ext)) {
            echo json_encode(['error' => '❌ Định dạng file không hợp lệ']);
            exit;
        }

        // Upload lên Cloudinary
        $result = $cloudinary->uploadApi()->upload(
            $_FILES['image']['tmp_name'],
            [
                'folder' => 'my_uploads', // thư mục trong Cloudinary
                'resource_type' => 'image'
            ]
        );

        echo json_encode([
            'secure_url' => $result['secure_url'],
            'public_id'  => $result['public_id']
        ]);
        exit;
    }

    // ==== XÓA ẢNH ====
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['public_id'])) {
        $publicId = trim($_POST['public_id']);
        if ($publicId === '') {
            echo json_encode(['error' => '❌ public_id rỗng']);
            exit;
        }

        $deleteResult = $cloudinary->uploadApi()->destroy($publicId);
        echo json_encode($deleteResult);
        exit;
    }

    // Nếu không đúng request
    echo json_encode(['error' => '❌ Request không hợp lệ']);
    exit;

} catch (Exception $e) {
    echo json_encode(['error' => '❌ Lỗi: ' . $e->getMessage()]);
    exit;
}
