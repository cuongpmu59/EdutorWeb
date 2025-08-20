<?php
// tf_form_image.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../env/config.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// === Cấu hình Cloudinary ===
Configuration::instance([
    'cloud' => [
        'cloud_name' => CLOUD_NAME,
        'api_key'    => CLOUD_API_KEY,
        'api_secret' => CLOUD_API_SECRET
    ],
    'url' => [
        'secure' => true
    ]
]);

try {
    // ==== Xử lý upload ảnh ====
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName    = $_FILES['file']['name'];

        if (!is_uploaded_file($fileTmpPath)) {
            echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy file tải lên.']);
            exit;
        }

        // Upload lên Cloudinary, đưa ảnh vào folder tf_questions
        $result = (new UploadApi())->upload($fileTmpPath, [
            'folder' => 'tf_questions'
        ]);

        echo json_encode([
            'status'      => 'success',
            'secure_url'  => $result['secure_url'],
            'public_id'   => $result['public_id']
        ]);
        exit;
    }

    // ==== Xử lý xóa ảnh ====
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'], $_POST['public_id'])) {
        $publicId = $_POST['public_id'];

        $result = (new UploadApi())->destroy($publicId);

        if ($result['result'] === 'ok') {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Không thể xóa ảnh.']);
        }
        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ.']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
