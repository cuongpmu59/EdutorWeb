<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../env/config.php'; // chứa CLOUDINARY_CLOUD_NAME, CLOUDINARY_UPLOAD_PRESET

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['error' => '❌ Lỗi upload file']);
            exit;
        }

        $file_path = $_FILES['image']['tmp_name'];

        // Dữ liệu post gửi đến Cloudinary
        $post_fields = [
            'file' => new CURLFile($file_path),
            'upload_preset' => CLOUDINARY_UPLOAD_PRESET,
        ];

        $url = "https://api.cloudinary.com/v1_1/" . CLOUDINARY_CLOUD_NAME . "/image/upload";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            echo json_encode(['error' => '❌ CURL Error: ' . $error]);
            exit;
        }

        echo $response;
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['public_id'])) {
        // Unsigned upload KHÔNG hỗ trợ delete qua API
        echo json_encode(['error' => '❌ Unsigned upload không hỗ trợ xóa ảnh qua API']);
        exit;
    }

    echo json_encode(['error' => '❌ Request không hợp lệ']);
    exit;

} catch (Exception $e) {
    echo json_encode(['error' => '❌ Lỗi: ' . $e->getMessage()]);
    exit;
}
