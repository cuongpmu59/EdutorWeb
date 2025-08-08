<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../env/config.php'; // chứa CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, CLOUDINARY_API_SECRET, CLOUDINARY_UPLOAD_PRESET

require_once __DIR__ . '/../../vendor/autoload.php'; // thư viện Cloudinary

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

// Cấu hình Cloudinary
Configuration::instance([
    'cloud' => [
        'cloud_name' => CLOUDINARY_CLOUD_NAME,
        'api_key'    => CLOUDINARY_API_KEY,
        'api_secret' => CLOUDINARY_API_SECRET,
    ],
    'url' => [
        'secure' => true
    ]
]);

$cloudinary = new Cloudinary();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Upload ảnh
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file_path = $_FILES['image']['tmp_name'];

            $uploadApi = new UploadApi();
            $result = $uploadApi->upload($file_path, [
                'upload_preset' => CLOUDINARY_UPLOAD_PRESET,
                // 'folder' => 'your_folder', // nếu muốn lưu vào folder cụ thể
            ]);

            echo json_encode($result);
            exit;
        }

        // Xóa ảnh theo public_id
        if (isset($_POST['public_id']) && !empty($_POST['public_id'])) {
            $publicId = $_POST['public_id'];

            $api = new \Cloudinary\Api();

            $deleteResult = $api->deleteAssets([$publicId]);

            // deleteAssets trả về một mảng chứa kết quả
            // Ví dụ: ['deleted' => ['public_id' => 'deleted'], 'deleted_count' => 1]

            if (isset($deleteResult['deleted'][$publicId]) && $deleteResult['deleted'][$publicId] === 'deleted') {
                echo json_encode(['result' => 'ok']);
            } else {
                echo json_encode(['error' => '❌ Xóa ảnh thất bại']);
            }
            exit;
        }
    }

    echo json_encode(['error' => '❌ Request không hợp lệ']);
    exit;

} catch (Exception $e) {
    echo json_encode(['error' => '❌ Lỗi: ' . $e->getMessage()]);
    exit;
}
