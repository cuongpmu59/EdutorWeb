<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/env/config.php';
require_once __DIR__ . '/vendor/autoload.php'; // Thư viện cloudinary PHP

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['error' => 'No file uploaded or upload error']);
        exit;
    }

    $fileTmpPath = $_FILES['image']['tmp_name'];

    try {
        // Upload ảnh lên Cloudinary
        $uploadResult = (new UploadApi())->upload($fileTmpPath, [
            'folder' => 'your_folder_name', // Thư mục trên Cloudinary (tuỳ chọn)
            'use_filename' => true,
            'unique_filename' => false,
            'overwrite' => true,
        ]);

        // $uploadResult chứa thông tin file vừa upload
        // Ví dụ: url, public_id, format, v.v.

        // Bạn có thể lưu $uploadResult['secure_url'] hoặc $uploadResult['public_id'] vào DB

        // Ví dụ giả định đã có kết nối DB $conn:
        /*
        $url = $uploadResult['secure_url'];
        $public_id = $uploadResult['public_id'];

        $stmt = $conn->prepare("INSERT INTO images (url, public_id) VALUES (?, ?)");
        $stmt->execute([$url, $public_id]);
        */

        echo json_encode([
            'success' => true,
            'url' => $uploadResult['secure_url'],
            'public_id' => $uploadResult['public_id'],
        ]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
