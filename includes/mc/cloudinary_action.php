<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../env/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_FILES['file']['tmp_name'])) {
        $url = 'https://api.cloudinary.com/v1_1/' . CLOUDINARY_CLOUD_NAME . '/image/upload';
        $timestamp = time();
        $signature = hash_hmac('sha1', "timestamp={$timestamp}" . CLOUDINARY_API_SECRET, CLOUDINARY_API_SECRET);

        $data = [
            'file' => new CURLFile($_FILES['file']['tmp_name']),
            'api_key' => CLOUDINARY_API_KEY,
            'timestamp' => $timestamp,
            'signature' => $signature,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        ob_clean(); // tránh rác output
        echo $response; // Cloudinary trả về JSON gốc
        exit;
    }
}

echo json_encode(['error' => 'No action performed']);
