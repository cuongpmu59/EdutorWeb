<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../env/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'upload' && isset($_FILES['file'])) {
        $filePath = $_FILES['file']['tmp_name'];
        $timestamp = time();
        $signature = sha1("timestamp={$timestamp}" . CLOUDINARY_API_SECRET);

        $url = "https://api.cloudinary.com/v1_1/" . CLOUDINARY_CLOUD_NAME . "/image/upload";
        $data = [
            'file' => new CURLFile($filePath),
            'api_key' => CLOUDINARY_API_KEY,
            'timestamp' => $timestamp,
            'signature' => $signature
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $data
        ]);
        $res = curl_exec($ch);
        curl_close($ch);

        echo $res;
        exit;
    }

    if ($action === 'delete' && !empty($_POST['public_id'])) {
        $public_id = $_POST['public_id'];
        $timestamp = time();
        $signature = sha1("public_id={$public_id}&timestamp={$timestamp}" . CLOUDINARY_API_SECRET);

        $url = "https://api.cloudinary.com/v1_1/" . CLOUDINARY_CLOUD_NAME . "/image/destroy";
        $data = [
            'public_id' => $public_id,
            'api_key' => CLOUDINARY_API_KEY,
            'timestamp' => $timestamp,
            'signature' => $signature
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $data
        ]);
        $res = curl_exec($ch);
        curl_close($ch);

        echo $res;
        exit;
    }

    echo json_encode(['error' => '❌ Invalid action']);
    exit;
}

echo json_encode(['error' => '❌ Invalid request']);
