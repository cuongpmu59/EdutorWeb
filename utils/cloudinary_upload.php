<?php
// utils/cloudinary_upload.php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../dotenv.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

Configuration::instance([
  'cloud' => [
    'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
    'api_key'    => $_ENV['CLOUDINARY_API_KEY'],
    'api_secret' => $_ENV['CLOUDINARY_API_SECRET']
  ],
  'url' => [
    'secure' => true
  ]
]);

function uploadToCloudinary($tmpFilePath, $publicId = null) {
  try {
    $publicId = $publicId ?: 'mc_temp_' . uniqid();

    $result = (new UploadApi())->upload($tmpFilePath, [
      'public_id' => $publicId,
      'overwrite' => true
      // 'folder' => 'mc_questions' // optional: add a folder if needed
    ]);

    return [
      'secure_url' => $result['secure_url'] ?? '',
      'public_id'  => $result['public_id'] ?? ''
    ];

  } catch (Exception $e) {
    return ['error' => $e->getMessage()];
  }
}