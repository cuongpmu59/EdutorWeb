<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/env/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

Configuration::instance([
    'cloud' => [
        'cloud_name' => CLOUDINARY_CLOUD_NAME,
        'api_key'    => CLOUDINARY_API_KEY,
        'api_secret' => CLOUDINARY_API_SECRET,
    ],
    'url' => ['secure' => true],
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['error' => 'No file uploaded or upload error']);
        exit;
    }

    $fileTmpPath = $_FILES['image']['tmp_name'];

    try {
        $uploadResult = (new UploadApi())->upload($fileTmpPath, [
            'folder' => 'your_folder_name',
            'use_filename' => true,
            'unique_filename' => false,
            'overwrite' => true,
        ]);

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
