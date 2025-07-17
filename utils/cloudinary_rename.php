<?php
// utils/cloudinary_rename.php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../dotenv.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\Upload\UploadApi;

Configuration::instance([
  'cloud' => [
    'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
    'api_key'    => $_ENV['CLOUDINARY_API_KEY'],
    'api_secret' => $_ENV['CLOUDINARY_API_SECRET']
  ],
  'url' => [ 'secure' => true ]
]);

function renameCloudinaryImage($oldPublicId, $newPublicId) {
  try {
    $uploadApi = new UploadApi();
    $result = $uploadApi->rename($oldPublicId, $newPublicId, [
      'overwrite' => true
    ]);
    return $result['secure_url'] ?? '';
  } catch (Exception $e) {
    return '';
  }
}
