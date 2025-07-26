<?php
require_once __DIR__ . '/load_env.php';
loadEnv();

$api_secret = $_ENV['CLOUDINARY_API_SECRET'];
$api_key = $_ENV['CLOUDINARY_API_KEY'];
$cloud_name = $_ENV['CLOUDINARY_CLOUD_NAME'];

header('Content-Type: application/json');

$body = json_decode(file_get_contents("php://input"), true);
$public_id = $body['public_id'] ?? 'mc_' . time();
$timestamp = time();

$params_to_sign = [
    'public_id' => $public_id,
    'timestamp' => $timestamp
];

ksort($params_to_sign);
$signature_base = urldecode(http_build_query($params_to_sign)) . $api_secret;
$signature = sha1($signature_base);

echo json_encode([
    'signature' => $signature,
    'timestamp' => $timestamp,
    'public_id' => $public_id,
    'api_key' => $api_key,
    'cloud_name' => $cloud_name
]);
