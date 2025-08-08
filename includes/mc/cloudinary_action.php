<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../env/config.php'; // chứa CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, CLOUDINARY_API_SECRET, CLOUDINARY_UPLOAD_PRESET
require_once __DIR__ . '/../../vendor/autoload.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Api;

try {
    Configuration::instance([
        'cloud' => [
            'cloud_name' => CLOUDINARY_CLOUD_NAME,
            'api_key'    => CLOUDINARY_API_KEY,
            'api_secret' => CLOUDINARY_API_SECRET,
        ],
        'url' => ['secure' => true]
    ]);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['error' => '❌ Yêu cầu phải là POST']);
        exit;
    }

    // Upload ảnh
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_path = $_FILES['image']['tmp_name'];

        $uploadApi = new UploadApi();
        $result = $uploadApi->upload($file_path, [
            'upload_preset' => CLOUDINARY_UPLOAD_PRESET,
            'folder' => 'examTest',
            //'public_id' => 'img_'.time(), // nếu muốn đặt tên tùy chỉnh
        ]);

        // Trả về JSON với dữ liệu cần thiết
        echo json_encode([
            'public_id' => $result['public_id'] ?? null,
            'secure_url' => $result['secure_url'] ?? null,
            'original_filename' => $result['original_filename'] ?? null,
            'format' => $result['format'] ?? null,
            'width' => $result['width'] ?? null,
            'height' => $result['height'] ?? null,
        ]);
        exit;
    }

    // Xóa ảnh
    if (isset($_POST['public_id']) && !empty($_POST['public_id'])) {
        $publicId = $_POST['public_id'];

        $api = new Api();
        $deleteResult = $api->deleteAssets([$publicId]);

        if (isset($deleteResult['deleted'][$publicId]) && $deleteResult['deleted'][$publicId] === 'deleted') {
            echo json_encode(['result' => 'ok']);
        } else {
            echo json_encode(['error' => '❌ Xóa ảnh thất bại']);
        }
        exit;
    }

    echo json_encode(['error' => '❌ Request không hợp lệ']);
    exit;

} catch (Exception $e) {
    echo json_encode(['error' => '❌ Lỗi: ' . $e->getMessage()]);
    exit;
}
