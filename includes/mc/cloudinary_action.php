<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../env/config.php'; // Load biến môi trường

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['action'])) {
        echo json_encode(['error' => 'No action provided']);
        exit;
    }

    $action = $_POST['action'];

    // ✅ UPLOAD
    if ($action === 'upload' && isset($_FILES['file'])) {
        $url = "https://api.cloudinary.com/v1_1/" . CLOUDINARY_CLOUD_NAME . "/image/upload";
        $timestamp = time();
        $params = [
            'timestamp' => $timestamp,
            'api_key'   => CLOUDINARY_API_KEY
        ];
        $toSign = http_build_query($params) . CLOUDINARY_API_SECRET;
        $signature = sha1($toSign);

        $data = [
            'file'       => new CURLFile($_FILES['file']['tmp_name']),
            'api_key'    => CLOUDINARY_API_KEY,
            'timestamp'  => $timestamp,
            'signature'  => $signature
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        echo $result;
        exit;
    }

    // ✅ DELETE
    if ($action === 'delete' && isset($_POST['public_id'])) {
        $url = "https://api.cloudinary.com/v1_1/" . CLOUDINARY_CLOUD_NAME . "/image/destroy";
        $timestamp = time();
        $params = [
            'public_id' => $_POST['public_id'],
            'timestamp' => $timestamp
        ];
        $toSign = http_build_query($params) . CLOUDINARY_API_SECRET;
        $signature = sha1($toSign);

        $data = [
            'public_id' => $_POST['public_id'],
            'api_key'   => CLOUDINARY_API_KEY,
            'timestamp' => $timestamp,
            'signature' => $signature
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        echo $result;
        exit;
    }
}

echo json_encode(['error' => 'No action performed']);
